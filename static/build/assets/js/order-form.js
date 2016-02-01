var TC_HOUR = TC_HOUR || 0;
var TC_NAMES = TC_NAMES || {};
var TC_DEF_DAY = TC_DEF_DAY || 0;
var TC_DEF_HOUR = TC_DEF_HOUR || 0;
var AppOrderRequest = AppOrderRequest || {};
AppOrderRequest.data = {};
AppOrderRequest.closeRequestModal = function ($form) {
	var $modal = $form.parents('.modal');
	if ($modal.length > 0) {
		$modal.modal('hide');
	}
};
AppOrderRequest.AppAccount = {
	alertError: function (text) {
		return AppHelpers.message(text);
	},
	ajaxError: function () {
	},
	alertAjaxError: function () {
	},
	alertMessage: function (text) {
		return AppHelpers.message(text);
	}
};

function initCreateOrderForm(AppAccount) {

	// это будет заглушка с фронта, если форма не загружена в ЛК
	AppAccount = AppAccount || AppOrderRequest.AppAccount;

	// type = new это новый клиент
	// type = account это авторизованный клиент
	var $form = $('#new-order-form');
	var type = $form.data('type');
	sendEvent('OrderRequestForm', 'init', type);

	if (!$form || $form.length == 0) {
		return;
	}

	$.fn.serializeObject = function () {
		var o = {};
		var a = this.serializeArray();
		$.each(a, function () {
			if (o[this.name]) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};


	var $button = $form.find('button[type=submit]');

	$form.off('submit').on('submit', function () {
		var data = $(this).serializeObject();
		data.type = type;
		sendEvent('OrderRequestForm', 'submit', type);

		/**
		 * @namespace data.address1
		 * @namespace data.address2
		 * @namespace data.day1
		 * @namespace data.day2
		 * @namespace data.time1
		 * @namespace data.time2
		 */

		// от новых клиентов нам нужны телефон и емейл
		if (type == 'new') {
			if (!data.phone) {
				AppAccount.alertError(TRANS['request.phoneRequired']);
				sendEventInvalid('phone');
				return;
			}
			if (!data.email) {
				AppAccount.alertError(TRANS['request.emailRequired']);
				sendEventInvalid('email');
				return;
			}
		}

		if (!data.address1) {
			AppAccount.alertError(TRANS['request.addressFromRequired']);
			sendEventInvalid('address1');
			return;
		}
		if (!data.address2) {
			AppAccount.alertError(TRANS['request.addressToRequired']);
			sendEventInvalid('address2');
			return;
		}

		if ($options.notFromDt()) {
			AppAccount.alertError(TRANS['request.fromDateTime']);
			sendEventInvalid('dateTimeFrom');
			return;
		}
		if ($options.notToDt()) {
			AppAccount.alertError(TRANS['request.toDateTime']);
			sendEventInvalid('dateTimeTo');
			return;
		}

		data.orderText = $options.orderText();

		var url = API_BASE_URL + 'customer/request';
		$button.button('loading');

		AppAccount.ajaxError = function (q) {
			AppAccount.alertAjaxError(q);
			$button.button('reset');
		};

		// форма заказа
		$.ajax(
			url,
			{
				type: 'POST',
				data: data,
				crossDomain: true,
				xhrFields: {
					withCredentials: true
				},
				success: function (res) {
					sendEvent('OrderRequestForm', 'result', 'success');
					AppOrderRequest.closeRequestModal($form);

					$button.button('reset');
					// это когда заявка отправлена авторизованным клиентом
					var m = TRANS['request.orderAccepted'];

					if (type == 'new') {

						// передаем данные в форму регистрации в случае с новыми клиентами
						// если эта форма есть, то она добавит в объект AppOrderRequest функцию onResponse
						AppOrderRequest.data = res.data;
						if (typeof AppOrderRequest.onResponse == 'function') {
							AppOrderRequest.onResponse();
						}
						// открываем сразу окно регистрации
						if(typeof AppOrderRequest.openNewUserRegisterModal == 'function'){
							AppOrderRequest.openNewUserRegisterModal(TRANS['request.registrationTitle'], TRANS['request.registrationComment']);
						}
						return;

					}

					AppAccount.alertMessage(m);

				},
				error: function (q) {
					$button.button('reset');
					var message = (q.responseJSON && q.responseJSON.errors && q.responseJSON.errors[0]) || (q.responseJSON.message) || 'Sorry... Unknown server error';
					sendEvent('OrderRequestForm', 'result', 'error-' + message);
					AppAccount.alertError(message);
				}
			}
		);

		function sendEventInvalid(field){
			sendEvent('OrderRequestForm', 'result', 'invalid-' + field);
		}

		return false;
	});


	var $doDoubleAddress = true;
	var $addr1 = $form.find('input[name=address1]');
	var $addr2 = $form.find('input[name=address2]');
	// при вводе в адрес1 содержимое повторяется в адресе2
	$addr1.off('keyup').on('keyup', function(){
		if($addr2.val() == ''){
			$doDoubleAddress = true;
		}
		if($doDoubleAddress){
			$addr2.val($addr1.val());
		}
	});
	// если что-то вводили в адрес2, он больше не копирует адрес1
	$addr2.off('keyup').on('keyup', function () {
		$doDoubleAddress = false;
	});



	// логика опций заказа
	var $options = {

		notWantDescription: true,
		strictDelivery: false,
		mode: 'standard',

		fromText: '',
		toText: '',

		allText: [],

		init: function(){
			$(this.leftDays[0]).trigger('click');
			if(TC_DEF_DAY > 0 && TC_DEF_HOUR > 0){
				$form.find('.on-day-left[data-key="' + TC_DEF_DAY + '"]').click();
				$form.find('.on-time-left[data-key="' + TC_DEF_HOUR + '"]').click();
			}
		},

		notFromDt: function(){
			return this.mode == 'standard' && (!this.leftActive() || !this.leftTimeActive());
		},

		notToDt: function(){
			return this.mode == 'standard' && (!this.rightActive() || !this.rightTimeActive());
		},

		changed: function(){

			this.allText = [];

			var $nwdText = $form.find('.nwdText');
			if(this.notWantDescription == false){
				$nwdText.show();
				this.allText.push($nwdText.html());
			}else{
				$nwdText.hide();
			}

			var $strictText = $form.find('.strictText');
			if(this.strictDelivery == true){
				$strictText.show();
				this.allText.push($strictText.html());
			}else{
				$strictText.hide();
			}


			var fromDay = '';
			this.leftDays.each(function(key, item){
				if($(item).parent().hasClass('active')){
					fromDay = TC_NAMES[$(item).data('key')];
				}
			});

			var toDay = '';
			this.rightDays.each(function(key, item){
				if($(item).parent().hasClass('active')){
					toDay = TC_NAMES[$(item).data('key')];
				}
			});

			var fromTime = '';
			this.leftTimes.each(function(key, item){
				if($(item).parent().hasClass('active')){
					fromTime = $(item).parent().data('label');
				}
			});

			var toTime = '';
			this.rightTimes.each(function(key, item){
				if($(item).parent().hasClass('active')){
					toTime = $(item).parent().data('label');
				}
			});

			if(fromDay){
				this.fromText = fromDay;
				if(fromTime){
					this.fromText = this.fromText + ', ' + fromTime;
				}
			}

			if(toDay){
				this.toText = toDay;
				if(toTime){
					this.toText = this.toText + ', ' + toTime;
				}
			}

			if(this.mode == 'standard') {

				$form.find('.fastText').hide();
				$form.find('.order-standard-only').show();
				var text;

				if (this.fromText) {
					text = TRANS['request.weGet'] + ': ' + this.fromText;
					$form.find('.fromText').html(text).show();
					this.allText.push(text);
				} else {
					$form.find('.fromText').hide();
				}
				if (this.toText) {
					text = TRANS['request.weReturn'] + ': ' + this.toText;
					$form.find('.toText').html(text).show();
					this.allText.push(text);
				} else {
					$form.find('.toText').hide();
				}

			}else{

				$form.find('.order-standard-only').hide();
				$form.find('.fromText').hide();
				$form.find('.toText').hide();

				$form.find('.fastText').each(function(key, item){
					$options.allText.push($(item).html());
				});
				$form.find('.fastText').show();

			}

		},

		orderText: function(){
			return '<p>' + this.allText.join('</p><p>')  + '</p>';
		},

		standard: function($btn){

			var $buttons = $form.find('.select-mode');
			$buttons.removeClass('selected').find('i').removeClass('fa-check-circle').addClass('fa-circle-o');
			$btn.addClass('selected').find('i').removeClass('fa-circle-o').addClass('fa-check-circle');

			this.mode = 'standard';
			this.fromText = '';
			this.toText = '';
			this.changed();

			sendEvent('OrderRequestForm', 'choose', 'standard');
		},

		fast: function($btn){

			var $buttons = $form.find('.select-mode');
			$buttons.removeClass('selected').find('i').removeClass('fa-check-circle').addClass('fa-circle-o');
			$btn.addClass('selected').find('i').removeClass('fa-circle-o').addClass('fa-check-circle');

			this.mode = 'fast';
			this.fromText = '';
			this.toText = '';
			this.changed();

			sendEvent('OrderRequestForm', 'choose', 'fast');
		},

		leftDay: function(key, first, $el){

			this.leftDays.parent().removeClass('active');
			$el.parent().addClass('active');
			var disabled = $el.data('disabled') + '';
			disabled = disabled.split('|');

			var $first = 1;
			this.rightDays.each(function(k, el){
				var $t = $(el);
				var $key = $t.data('key');

				if($.inArray($key + '', disabled) >= 0){
					$t.hide();
					$t.data('first', 0);
				}else{
					$t.show();
					$t.data('first', $first);
					if($first){
						$t.trigger('click');
						$first = 0;
					}
				}
			});

			this.balanceTime();
			this.changed();

		},

		rightDay: function(key, first, $el){
			this.rightDays.parent().removeClass('active');
			$el.parent().addClass('active');
			this.balanceTime();
			this.changed();

		},

		leftTime: function(key, $el){
			this.leftTimes.parent().removeClass('active');
			$el.parent().addClass('active');
			this.balanceTime();
			this.changed();
		},

		rightTime: function(key, $el){
			this.rightTimes.parent().removeClass('active');
			$el.parent().addClass('active');
			this.balanceTime();
			this.changed();
		},

		leftActive: function(){

			var $item = $form.find('.select-day.day.left li.active a');
			if($item && $item.length >0){
				return $item;
			}
			return null;

		},

		rightActive: function(){

			var $item = $form.find('.select-day.day.right li.active a');
			if($item && $item.length >0){
				return $item;
			}
			return null;
		},

		leftTimeActive: function(){

			var $item = $form.find('.select-day.time.left li.active a');
			if($item && $item.length >0){
				return $item;
			}
			return null;

		},

		rightTimeActive: function(){

			var $item = $form.find('.select-day.time.right li.active a');
			if($item && $item.length >0){
				return $item;
			}
			return null;
		},


		balanceTime: function() {

			var $left = this.leftActive();
			var $right = this.rightActive();

			var $leftTime = this.leftTimeActive();

			$(this.rightTimes[0]).show();
			$(this.rightTimes[1]).show();
			$(this.rightTimes[2]).show();

			var disabled = $left && ($left.data('disabled') + '').split('|');
			var maxDisabled = 0;
			if(disabled && disabled.length > 0){
				maxDisabled = Math.max.apply(Math, disabled);
			}

			// справа выбран день, следующий за последним запрещенным в списке левого дня
			// то есть это первый доступный для чистки вещей день после выбранного слева
			if ($left && $right && maxDisabled === $right.data('prev')) {
				if($leftTime) {

				}
			}

			$(this.leftTimes[0]).show();
			$(this.leftTimes[1]).show();
			$(this.leftTimes[2]).show();

			if($left && $left.data('key') == 0){
				$(this.leftTimes[0]).hide().parent().removeClass('active');
				$(this.leftTimes[1]).hide().parent().removeClass('active');
				if(TC_HOUR >= 14){
					$(this.leftTimes[2]).hide().parent().removeClass('active');
				}
			}

			if($left && $left.data('key') == 1){
				if(TC_HOUR >= 18){
					$(this.leftTimes[0]).hide().parent().removeClass('active');
				}
			}

		},

		leftTimes: $form.find('.on-time-left'),
		rightTimes: $form.find('.on-time-right'),

		leftDays: $form.find('.on-day-left'),
		rightDays: $form.find('.on-day-right')

	};

	$form.find('.logic-checkbox').off('change').on('change', function(){
		var ch = $(this);
		$options.notWantDescription = ch.parent().find('input:checked').length > 0;
		$options.changed();
	});

	$form.find('.strict-checkbox').off('change').on('change', function(){
		var ch = $(this);
		$options.strictDelivery = ch.parent().find('input:checked').length > 0;
		$options.changed();
	});

	$options.leftDays.off('click').on('click', function(){
		var $t = $(this);
		var first = $t.data('first');
		var key = $t.data('key');
		$options.leftDay(key, first, $t);
		return false;
	});

	$options.rightDays.off('click').on('click', function(){
		var $t = $(this);
		var first = $t.data('first');
		var key = $t.data('key');
		$options.rightDay(key, first, $t);
		return false;
	});

	$options.leftTimes.off('click').on('click', function(){
		var $t = $(this);
		var key = $t.data('key');
		$options.leftTime(key, $t);
		return false;
	});

	$options.rightTimes.off('click').on('click', function(){
		var $t = $(this);
		var key = $t.data('key');
		$options.rightTime(key, $t);
		return false;
	});

	$form.find('.select-mode.order-standard').off('click').on('click', function(){
		$options.standard($(this));
	});
	$form.find('.select-mode.order-fast').off('click').on('click', function(){
		$options.fast($(this));
	});

	$options.init();


}
