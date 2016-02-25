var AppEventListeners = {
};

var AppSession = {
	id: '',
	set: function(value){
		this.id = value;
	},
	get: function(){
		return this.id;
	}
};

var AppHelpers = {

	decode_text: function(str){
		return decodeURIComponent(str).replace(/[+]/g, ' ');
	},

	message: function(title){
		var modal = $('#myMessage');
		modal.find('.modal-content h3').html(title.replace('<%phone%>',''));
		modal.modal('show');
		return modal;
	},

	ajaxMe: function(action, data, success, error, method){

		method = method || 'post';
		data.lang = LANG;

		$.ajax(
			API_BASE_URL + action,
			{
				data: data,
				type: method,
				crossDomain: true,
				xhrFields: {
					withCredentials: true
				},

				success: success,
				error: function (q, s, e) {

					if(!error || typeof error != 'function'){
						return;
					}

					if('return' === error()){
						return;
					}

					var errorMessage = q.responseJSON && q.responseJSON.errors && AppHelpers.firstError(q.responseJSON.errors);
					errorMessage = errorMessage || q.responseJSON.message;
					if (!errorMessage) {
						errorMessage = TRANS['serverErrorMessage'];
						console.log(q);
						console.log(s);
						console.log(e);
					}

					AppHelpers.message(errorMessage);

				}
			}
		);
	},

	isPhoneNumber: function(str) {
		return (
			str.replace(/[- ]/g, '').search(/^[0-9]{10,11}$/) >= 0
		);
	},

	firstError: function ( input ) {
		var tmpArr = [], cnt = 0;
		for ( var key in input ){
			if(!input.hasOwnProperty(key)) continue;
			tmpArr[cnt] = input[key];
			cnt++;
		}
		return (tmpArr.length > 0)? tmpArr[0] : null;
	},

	renderSession: function(){

		var link = DASHBOARD_URL;

		var $accountNav = $('nav.account-nav');

		$accountNav.find('li.li-nav-wait,li.li-nav-login,li.li-nav-register').css({'display': 'none'});
		$accountNav.find('li.li-nav-logout,li.li-nav-user').css({display: 'list-item'});
		$accountNav.find('li.li-nav-user a').attr({'href': link});

		var $accountMenu = $('.account-menu');
		$accountMenu.css({'display': 'block'});
		$accountMenu.find('a:has(i.fa-tachometer)').attr({'href': link});

		var $basketBox = $('div.basket-box');
		$basketBox.find('a.basket-icon, div.basket-box a.basket-link').attr({'href': link});
		$basketBox.css({'display': 'block'});

		return link;

	},

	closeSession: function(){

		var $accountNav = $('nav.account-nav');

		$accountNav.find('li.li-nav-wait,li.li-nav-logout,li-nav-user').css({'display': 'none'});
		$accountNav.find('li.li-nav-login,li.li-nav-register').css({display: 'list-item'});
		$accountNav.find('li.li-nav-user a').attr({'href': '#'});
		$accountNav.find('li.li-nav-user a').html('<i class="fa fa-user"></i>');

		var $accountMenu = $('.account-menu');

		$accountMenu.css({'display': 'none'});
		$accountMenu.find('a:has(i.fa-tachometer)').attr({'href': '#'});
		$accountMenu.find('a:has(i.fa-user)').html('<i class="fa fa-user"></i>');

		var $basketBox = $('div.basket-box');

		$basketBox.find('a.basket-icon, div.basket-box a.basket-link').attr({'href': '#'});
		$basketBox.css({'display': 'none'});
		$basketBox.find('span.number').text('');

	}


};

function trans(code){
	return TRANS[code] || code;
}


function sendEvent(category, action, label, value) {

	if (typeof ga != 'function') {
		var ga = function () {

		};
	}
	if(typeof window.yaCounter27721494 !== 'object' || typeof window.yaCounter27721494.reachGoal !== 'function'){
		window.yaCounter27721494 = {reachGoal: function(){}};
	}
	var ya = window.yaCounter27721494.reachGoal;

	window._fbq = window._fbq || [];

	// успешная отправка заявки, трекаем в фб
	if(category == 'OrderRequestForm' && action == 'result' && label == 'success'){
		window._fbq.push(['track', '6026056380635', {'value':'0.00','currency':'RUB'}]);
	}

	if (value) {
		ga('send', 'event', category, action, label, value);
		ya(category + '_' + action + '_' + label);
	}
	else if (label) {
		ga('send', 'event', category, action, label);
		ya(category + '_' + action + '_' + label);
	}
	else {
		ga('send', 'event', category, action);
		ya(category + '_' + action);
	}

	console.log({
		category: category,
		action: action,
		label: label,
		value: value
	});
}

(function() {
	var _fbq = window._fbq || (window._fbq = []);
	if (!_fbq.loaded) {
		var fbds = document.createElement('script');
		fbds.async = true;
		fbds.src = '//connect.facebook.net/en_US/fbds.js';
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(fbds, s);
		_fbq.loaded = true;
	}

<<<<<<< HEAD
	/*var hash = parseUrl();
	if (hash) {
		var blockPos = $('#' + hash).offset().top;
		console.log(blockPos);
	}*/
})();
=======

})();

function scrollToHash() {
	var hash = parseUrl();
	if (hash && $('#' + hash).length) {
		if ($('#' + hash).closest('#price')) {
			$('#price-section').show();
		}
		setTimeout(function() {
			var blockPos = $('#' + hash).offset().top;
			if ($('#fixed-header-section:visible').length) {
				var fHeaderHeight = $('#fixed-header-section').height();
				blockPos = blockPos - fHeaderHeight;
			}
			window.scrollTo(0, blockPos);

		}, 1000);
	}
}
>>>>>>> bdbe29180145219d37259cbbbce228fe77dc3501

function parseUrl() {
	var url = location.href;

	var subUrl = url.split('#');
	if (subUrl.length > 1) {
		return subUrl.pop();
	}

	return null;
}


AppEventListeners.$request = {
	xhr: undefined,
	set: function(ob){
		this.abort();
		this.xhr = ob;
	},
	remove: function(){
		this.xhr = undefined;
	},
	abort: function(){
		if(this.xhr){
			this.xhr.abort();
		}
	}
};
AppEventListeners.registrationPromoField = function () {

	var $this = AppEventListeners;
	var $field = $(this);
	var value = $.trim($field.val());
	var $modal = $field.parents('#myModal');
	var $form = $modal.find('form');
	var $loading = $form.find('.address-select-loading');

	// промо-код должен быть не менее 4-х символов
	if (value.length < 4) {
		$loading.hide()
			.data('found', false)
			.trigger('DH.promo.changed');
		return;
	}

	var $select = $form.find('select.jcf-hidden');
	var $selectDiv = $form.find('div:has(select.jcf-hidden)');
	var optionEmpty = '<option value="null" promo_code_id="null">' + TRANS['notSelected'] + '</option>';

	(function(){

		$loading.html(TRANS['checkPromoCode'])
			.show()
			.data('found', false)
			.trigger('DH.promo.changed');

		$this.$request.set($.ajax({
			url: API_BASE_URL + 'customer/signup/promo',
			data: {promo: value.toUpperCase()},
			success: doSuccess,
			error: doError
		}));

	}());


	function doSuccess(response){

		if (response) {

			if(response.data && response.data.addresses && response.data.addresses.length > 0){

				$selectDiv.show();
				$select.empty();
				$select.append(optionEmpty);
				dropdown(response.data.addresses, $select);
				if(!initCustomForms()){
					var $selDiv = $('.row-holder.step-start.address-select-list');
					$selDiv.show();
					var $sel = $selDiv.find('select');
					dropdown(response.data.addresses, $sel);
					$sel.show();
				}
				$loading.html(TRANS['foundPromoCode'])
					.data('found', true)
					.trigger('DH.promo.changed');
			}else{

				$selectDiv.hide();
				$select.empty();
				$loading.html(TRANS['foundPromoCodeNot'])
					.data('found', false)
					.trigger('DH.promo.changed');

			}

		}

		return response;

	}

	function doError(){

	}

	function dropdown(addresses, $select){

		/**
		 * @namespace address.promo_code_id
		 * @namespace address.address
		 * @namespace address.id
		 */

		var address, i, qnt = addresses.length;

		for (i=0; i < qnt; i++) {
			address = addresses[i];

			$select.append(
				'<option ' + (qnt == 1 ? 'selected' : '') +
				' value="' + address.id + '"' +
				' promo_code_id="' + address.promo_code_id + '"' +
				'>' +
				AppHelpers.decode_text(address.address) +
				'</option>'
			);

		}

	}

};

$(document).ready(function() {

	var $myModa2 = $('#myModa2');
	var signUp = {
		modal: $myModa2,
		form: $myModa2.find('form'),
		button: $myModa2.find('form').find('button.btn-primary'),
		hide: function(){
			this.modal.modal('hide');
		},
		loading: function(){
			this.button.button('loading');
		},
		reset: function(){
			this.button.button('reset');
		}
	};

	var $ResubmitSms = $('#ResubmitSms');
	var sms = {
		modal: $ResubmitSms,
		form: $ResubmitSms.find('form'),
		button: $ResubmitSms.find('form').find('button.btn-primary'),
		hide: function(){
			this.modal.modal('hide');
		},
		loading: function(){
			this.button.button('loading');
		},
		reset: function(){
			this.button.button('reset');
		}
	};

	signUp.form.submit(submitSignUp);
	sms.form.submit(submitSignUp);

	function submitSignUp(){
		var $form = $(this);
		var
			tel = $form.find('input[type=tel]'),
			pwd = $form.find('input.input-password');
		signUpExec(tel, pwd);
		return false;
	}

	function empty(str){
		return !str || str == '' || str.length == 0;
	}

	function signUpExec($phone, $password) {

		/**
		 * @namespace response.Session_id
		 * @var {String} phone
		 */

		var phone = $.trim($phone.val());
		var password = $.trim($password.val());

		if (empty(phone)) {
			$phone.focus();
			return false;
		}

		if (password == '') {
			$password.focus();
			return false;
		}

		if (!AppHelpers.isPhoneNumber(phone)) {
			AppHelpers.message(TRANS['errorFieldPhone']);
			return false;
		}

		var data = {
			phone: phone,
			password: password,
			remember: signUp.form.find('#save_me:checked').val()
		};

		$('#PasswordReset').find('input[name=phone]').val(phone);

		sms.loading();
		signUp.loading();
		AppHelpers.ajaxMe('customer/signup/login', data, doSuccess, doError);

		return false;
	}

	function doError(){
		sms.reset();
		signUp.reset();
	}

	function doSuccess (response) {

		sms.reset();
		signUp.reset();

		AppSession.set(response.data && response.data.key);

		if(!AppSession.get()){
			AppHelpers.message(TRANS['serverErrorTryLater']);
			return;
		}

		signUp.hide();
		sms.hide();

		location.href = AppHelpers.renderSession();

	}

});

/**
 * повторная отправка смс с паролем
 * после регистрации
 */

$(document).ready(function(){

	var $modal = $('#ResubmitSms');
	var $form = $modal.find('form');
	var $link = $modal.find('.button-box a:first');
	var $phone = $form.find('input[type=tel]');

	function linkLoading(){
		$link.data('oldInner', $link.html());
		$link.html($link.data('loading-text'));
		$link.attr('disabled', 'disabled');
	}
	function linkReset(){
		$link.html($link.data('oldInner'));
		$link.removeAttr('disabled');
	}

	$link.click(function(){

		var phone = $.trim($phone.val());

		if (phone == ''){
			$phone.focus();
			return false;
		}

		if(!AppHelpers.isPhoneNumber(phone)){
			AppHelpers.message(TRANS['errorFieldPhone']);
			return false;
		}

		linkLoading();
		AppHelpers.ajaxMe('customer/signup/sms/repeat', {phone: phone}, doSuccess, doError);

		function doSuccess(response){

			linkReset();
			$modal.modal('hide');
			AppHelpers.message(response.message);

			return response;
		}


		function doError(){
			linkReset();
		}

		return false;

	});

});

var AppOrderRequest = AppOrderRequest || {};
$(document).ready(function(){


	var $modal = $('#myModal');
	var $form = $modal.find('.signup-form');
	var $buttonFirst = $form.find('.btn-signup-next');
	var $buttonSend = $form.find('.btn-signup-password');

	// эту функцию вызовет форма отправки заявки,
	// где в data будут емейл, адрес и телефон
	AppOrderRequest.onResponse = function(){
		// и мы сохраним эти данные в форме чтобы
		// автоматически подставить в поля,
		// когда пользователь будет регистрироваться
		var data = AppOrderRequest.data || {};
		$form.data('exists', data);
		if(data.phone){
			$form.find('input[type=tel]').val(data.phone);
		}
		if(data.email){
			$form.find('input[type=email]').val(data.email);
		}
	};
	// эта функция будет вызвана после отправки заявки для новых клиентов
	// надо просто показать окно регистрации
	AppOrderRequest.openNewUserRegisterModal = function (modTitle, addComment){
		$('#myMessage').modal('hide');

		// нужно подменить заголовок и добавить комментарий

		var $title = $modal.find('.modal-content .heading h2');
		$title.html(modTitle);
		var $comment = $('<p>' + addComment + '</p>');
		$title.parent().append($comment);

		// и надо скрыть поля с емейлом и телефоном, т.к. они заполнены

		var phone = $modal.find('input[name=phone]');
		if(phone.val()){
			phone.parents('div.row-holder').hide();
		}

		var email = $modal.find('input[name=email]');
		if(email.val()){
			email.parents('div.row-holder').hide();
		}

		$modal.data('show')();
	};

	$form.submit(function(){

		// в форме уже может присутствовать адрес из внешнего источника
		var existsAddress = $form.data('exists');
		existsAddress = existsAddress && existsAddress.address;

		var
			dataForm = {},
			name = $form.find('input[type=text]:first'),
			phone = $form.find('input[type=tel]'),
			email = $form.find('input[type=email]');

		dataForm.phone = phone.val();
		dataForm.email = email.val();
		dataForm.name = name.val();

		if ($.trim(dataForm.name) == ''){
			name.focus();
			return false;
		}

		if (dataForm.phone.replace(/[- ]/g,'').search(/^[0-9]{10,11}$/) < 0) {
			AppHelpers.message(TRANS['errorFieldPhone']);
			return false;
		}

		if (dataForm.email == ''){
			AppHelpers.message(TRANS['errorFieldEmail']);
			return false;
		}

		var cStep = $form.data('step') || 'next';

		// промокод не был введен, поэтому показываем форму ввода адреса
		if(cStep == 'next' && !existsAddress){
			DH.signupForm.goToAddress();
			return false;
		}

		if (cStep == 'fin') {

			// шаг формы: финальный, заполнение данных об адресе
			dataForm.city = $form.find('input[name=city]').val();
			dataForm.street = $form.find('input[name=street]').val();
			dataForm.house = $form.find('input[name=house]').val();
			dataForm.room = $form.find('input[name=room]').val();
			dataForm.float = $form.find('input[name=float]').val();

			if(dataForm.street == ''){
				AppHelpers.message(TRANS['errorFieldAddress']);
				return false;
			}

			if(dataForm.house == ''){
				AppHelpers.message(TRANS['errorFieldHouse']);
				return false;
			}

			if(dataForm.room == ''){
				AppHelpers.message(TRANS['errorFieldRoom']);
				return false;
			}


		}else {

			// здесь мы пропускаем шаг с заполнением адреса
			// поэтому:
			// или должен быть введен промокод и выбран адрес по промокоду
			// или есть адрес из внешнего источника

			var promo_code = $form.find('input[type=text].promo');
			if ($.trim(promo_code.val()) == '' && !existsAddress) {
				AppHelpers.message(TRANS['errorFieldPromoCode']);
				promo_code.focus();
				return false;
			}

			var promo = getPromoAddressId();
			if (!promo.id && !existsAddress) {
				AppHelpers.message(TRANS['errorFieldAddressId']);
				promo.field.focus();
				return false;
			}

			// есть адрес
			if(existsAddress){
				dataForm.address = existsAddress;
			}

			// есть промокод
			if(promo.id > 0){
				dataForm.address_id = promo.id;
			}

		}

		// идет отправка с кнопки на первом шаге или на шаге с адресом
		var $btn = (cStep == 'next')? $buttonFirst : $buttonSend;

		$btn.data('oldInner', $btn.find('.inner-text').html());
		$btn.find('.inner-text').html($btn.data('loading'));
		$btn.attr('disabled', 'disabled');
		$btn.data('reset', function(){
			$btn.find('.inner-text').html($btn.data('oldInner'));
			$btn.removeAttr('disabled');
		});

		AppHelpers.ajaxMe('customer/signup/register', dataForm, doSuccess, doError);

		function doSuccess(response){
			$btn.data('reset')();

			$('#myModal').data('close')();
			succesful();
			$form.find('.link-signup-sms').show();
			$('input[type=tel],input[name=phone]').val(dataForm.phone);
			$('#ResubmitSms').modal('show');

			return response;
		}

		function doError(){
			$btn.data('reset')();
		}

		function succesful(){
			$form.find('input').val('');
			$form.find('input[name=city]').val(TRANS['Moscow']);
			$form.data('step','password');
			$form.find('select.jcf-hidden option[value!="null"]').remove();
			initCustomForms();
			$form.find('.address-select-list').css({'display': 'none'});
		}

		function getPromoAddressId(){
			var promo = $form.find('select.jcf-hidden');
			var promo2 = $form.find('.row-holder.step-start.address-select-list select');
			var value = promo.val() || promo2.val();
			if(value == '' || value == 'null' || value == null){
				return {
					id: null,
					field: promo
				};
			}
			return {
				id: value,
				field: promo
			}
		}

		return false;

	});

});
$(document).ready(function(){

	var $modal = $('#PasswordReset');
	var $form = $modal.find('.signup-form');
	var $phone = $form.find('input[type=tel]');
	var $modalSignUp = $('#myModa2');
	var $button = $modal.find('button.btn-primary');

	var $resetLink = $('a[data-target=PasswordReset]');
	$resetLink.on('click', function(){
		$modalSignUp.modal('hide');
		$modal.modal('show');
		return false;
	});

	var $cancelLink = $modal.find('.cancel-from-reset');
	$cancelLink.on('click', function(){
		$modal.modal('hide');
		$modalSignUp.modal('show');
		return false;
	});

	$form.submit(function (){

		var phone = $.trim($phone.val());

		if (phone == ''){
			$phone.focus();
			return false;
		}

		if(!AppHelpers.isPhoneNumber(phone)){
			AppHelpers.message(TRANS['errorFieldPhone']);
			return false;
		}

		$button.button('loading');
		AppHelpers.ajaxMe('customer/signup/reset', {phone: phone}, doSuccess, doError);

		function doSuccess(response){
			$button.button('reset');
			$modal.modal('hide');
			$modalSignUp.modal('show');
			AppHelpers.message(response.message);
		}

		function doError(){
			$button.button('reset');
		}

		return false;

	});

});
$(document).ready(function(){

	AppHelpers.ajaxMe('customer', {}, function(response){
		/**
		 * @namespace response.data.order_qnt
		 */

		if(!response.data.key){
			AppHelpers.closeSession();
			return;
		}

		initFeedbackAjaxSend({
			name: response.data.name,
			email: response.data.email,
			phone: response.data.phone
		});

		var $modalReg = $('#myModal');
		if($modalReg && $modalReg.length >0 && typeof $modalReg.data('close') == 'function'){
			$modalReg.data('close')();
		}

		AppSession.set(response.data.key);
		AppHelpers.renderSession();

		$('.account-menu').find('a:has(i.fa-user)').html('<i class="fa fa-user"></i> ' + response.data.name + ' <i class="arrow fa fa-long-arrow-down"></i>');
		$('nav.account-nav').find('li.cl a').html('<i class="fa fa-user"></i> ' + response.data.name);
		$('div.basket-box').find('span.number').text(response.data.order_qnt);

	}, function(){
		AppHelpers.closeSession();
		return 'return';
	}, 'get');

	var $modal = $('#myModa3');
	var $logout = $modal.find('a.btn-primary');

	$logout.click(function() {

		$modal.modal('hide');

		AppHelpers.ajaxMe('customer/signup/logout', {key: AppSession.get()}, doSuccess);
		AppSession.set('');

		function doSuccess() {
			AppHelpers.closeSession();
		}

		return false;

	});

});

// initAjaxSend
function initFeedbackAjaxSend(contacts){
	contacts = contacts || {};

	var modal = jQuery('#feedbackForm');
	var modalResult = jQuery('#feedbackFormResult');
	var form = modal.find('form');
	var button = modal.find('*[type=submit]');
	var buttonLabel = modal.find('.submit-label');
	var fields = 'input,textarea';

	var links = jQuery('a[data-target="feedbackForm"]');

	links.off('click').on('click', function(){
		modal.find('.heading').hide();
		form.find(fields).removeClass('error').val('');
		form.find('*[name=name]').val(contacts.name || '');
		form.find('*[name=phone]').val(contacts.phone || '');
		form.find('*[name=email]').val(contacts.email || '');
		modal.find('.modal-content').show();
		reset(false);
		modal.modal({});
		return false;
	});
	function messageShow(){
		modal.modal('hide');
		modalResult.modal({});
	}
	function loading(){
		form.find(fields).removeClass('error');
		button.attr('disabled', 'disabled');
		buttonLabel.oldHtml = buttonLabel.html();
		buttonLabel.html(trans('sending') + '...');
	}
	function reset(final){
		if(!final){
			button.removeAttr('disabled');
		}
		buttonLabel.html(buttonLabel.oldHtml);
	}
	function ajaxSend(e){
		if (e && typeof e.preventDefault == 'function') e.preventDefault();
		loading();
		jQuery.ajax({
			url: API_BASE_URL + form.attr('action'),
			type:'POST',
			dataType:'html',
			data:'ajax=1&' + form.serialize(),
			success: function() {
				reset(true);
				messageShow();
			},
			error: function(response) {
				reset(false);
				var json = response && response.responseText && JSON.parse(response.responseText),
					errors = json && json.errors;
				if(showErrors(errors)){
					return;
				}
				alert(trans('feedbackSendErrorMessage'));
			}
		});
	}

	function showErrors(errors){
		if(!errors || typeof errors !== 'object'){
			return false;
		}
		var field;
		for(var key in errors){
			if(!errors.hasOwnProperty(key)) return;
			field = form.find('*[name=' + key + ']');
			field.addClass('error');
			field.data('errorText', errors[key]);
		}
		return true;
	}

	form.off('submit').on('submit', ajaxSend);



	var $formLite = $('form.lite-feedback');
	$formLite.off('submit').on('submit', function(){
		var f = $(this);
		var $btn = f.find('button');
		if(!f.find('textarea').val() || f.find('textarea').val().length < 10){
			alert(trans('Text length is too small'));
			return false;
		}
		$btn.button('loading');
		jQuery.ajax({
			url: API_BASE_URL + f.attr('action'),
			type:'POST',
			dataType:'html',
			data:'ajax=1&' + f.serialize(),
			success: function() {
				$btn.button('reset');
				messageShow();
			},
			error: function() {
				$btn.button('reset');
				alert(trans('feedbackSendErrorMessage'));
			}
		});
		return false;
	});


}


function initSignupLogic(){

	var $modal = $('#myModal');
	var $form = $modal.find('form');

	// поле с промокодом
	var $promo = $form.find('input.input-signup-promo');
	var $promoAddress = $form.find('select.jcf-hidden');
	var $promoAddress2 = $form.find('.row-holder.step-start.address-select-list select');

	// чекбокс возврата на поля с промокодом
	var $addressBack = $form.find('.step-promo-back').find('input, a');
	var $stepBack = $modal.find('.step-back');

	// два варианта одной кнопки, "далее" или "выслать пароль"
	var $next = $('.btn-signup-next');
	var $password = $('.btn-signup-password');

	// индикатор поиска промокода
	var $loading = $form.find('.address-select-loading');
	$loading.on('DH.promo.changed', onInputPromoCode);
	function existsPromoCode(){
		var value = $promo.val();
		var valueAddress = $promoAddress.val() || $promoAddress2.val();
		return(
		value &&
		valueAddress &&
		value != '' &&
		valueAddress != '' &&
		valueAddress != null &&
		valueAddress != 'null' &&
		$loading.data('found')
		);
	}

	// следим за тем, введен промокод или нет
	function onInputPromoCode(){
		var step = $form.data('step');
		// ничего не делаем если это не стартовое положение формы
		if(step && (step != 'next' && step != 'password')){
			return;
		}

		// для регистрации по адресу не должно быть промокода и адреса
		if(!existsPromoCode()){
			$next.show();
			$password.hide();
			// промокода нет, состояние формы "дальше", на заполнение адреса
			$form.data('step', 'next');
		}else{
			$next.hide();
			$password.show();
			// промокод есть, можем отправлять пароль (регистрировать без заполнения адреса)
			$promo.removeClass('flash-border');
			$form.data('step', 'password');
		}
	}

	// скрываем стартовые поля и показываем поля адреса
	DH.signupForm.goToAddress = function(){
		$modal.find('.step-start').hide();
		$modal.find('.step-address').show();
		$form.data('step', 'fin');
		$next.hide();
		$password.show();
		$promo.removeClass('flash-border');
	};

	// клиент считает что у него есть промокод и возвращается к полям с промокодом
	$addressBack.off('click');
	$addressBack.on('click', function(){
		stepBack();
		$promo.addClass('flash-border');
		$promo.focus();
		return false;
	});

	// шаг назад
	$stepBack.off('click');
	$stepBack.on('click', stepBack);

	function stepBack(){

		$modal.find('.step-start').show();
		$modal.find('.step-address').hide();

		// т.к. он не заполнил промокод, состояние формы "дальше"
		$form.data('step', 'next');

		if(!existsPromoCode()){
			// это выпадушка адресов, скроем если нет промокода
			$modal.find('.step-start.address-select-list').hide();
		}

		// скроем подсказку про поиск промокода
		$form.find('.address-select-loading').hide();

		$next.show();
		$password.hide();

		return false;

	}


}
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

// page init
var DH = {};
var ua = detect.parse(navigator.userAgent);
var safari = ua.browser.family == 'Safari' || ua.device.family == 'iPhone' || ua.os.family == 'iOS';

jQuery(window).load(function(){
	jQuery(function(){

		DH.signupForm = {};

		initTabs();
		initCustomForms();
		initTouchNav();
		initCarousel();
		initCycleCarousel();
		initOpenClose();
		initAccordion();
		initSameHeight();
		initCustomHover();
		jQuery('input, textarea').placeholder();
		jQuery('[data-toggle=tooltip]').tooltip();
		jQuery('.carousel').carousel('pause');
		initFixedHeader();
		initAnchorLinks();
		initFadeIcons();
		initLightbox();
		initCustomOpenClose();
		initCustomGallery();
		initPageScroll();
		initFeedbackAjaxSend();
		initSignupLogic();
		initActionByHash();
	});
	initDatapicker();
});

// init page scroll
function initPageScroll(){
	jQuery('body').pageScroll({
		header: '.header-fixed-container',
		links: '.navigation a, #nav ul li a',
		addToParent: true
	});
}

// initDatapicker
function initDatapicker(){
	jQuery(".calendar-block").each(function(){
		var holder=jQuery(this);
		var input=holder.find("input");
		var button=holder.find(".calendar");
			
		input.datepicker();
		button.on("click", function(e){
			input.focus();
			e.preventDefault();
		})
	})
}

// init language form
function iniLangForm(){
	jQuery('.language-form').each(function(){
		var form = jQuery(this);
		form.find('select').on('change', function(){
			document.location.href = jQuery(this).val();
		});
	});
}

// init custom gallery
function initCustomGallery(){
	jQuery('.orders-tabset').each(function(){
		var holder = jQuery(this);
		var list = holder.find('ul');
		var items = holder.find('li');
		ResponsiveHelper.addRange({
			'..991': {
				on: function() {
					initGallery();
				},
				off: function() {
					destroyGallery();
				}
			}
		});
		function initGallery(){
			list.carouFredSel({
				width: '100%',
				prev: '.btn-prev',
				next: '.btn-next',
				auto: false
			});
		}
		function destroyGallery(){
			list.trigger("destroy");
			setTimeout(function(){
				items.removeAttr('style');
			},10)
		}
	});
}

// initCustomOpenClose
function initCustomOpenClose(){
	var activeClass = 'active';
	var page = jQuery('html, body');
	var animSpeed = 500;
	var columnsHolder = jQuery('.add-price-style');
	jQuery('.add-price-section').each(function(){
		var holder = jQuery(this)/*.hide()*/;
		var frame = holder.children();
		var holderId = holder.attr('id');
		var closer = holder.find('.link-close');
		var openers = jQuery('a[href="#'+holderId+'"]');
		var header = jQuery('.header-fixed-container');
		var tabs = holder.find('.price-tabset li a');
		var animationActive = false;
		var headerHeight = 0;
		function showHolder(){
			animationActive = true;
			headerHeight = 0;
			if(header.css('position') == 'fixed'){
				headerHeight = header.outerHeight();
			} else {
				headerHeight = 0;
			}
			holder.slideDown(animSpeed, function(){
				holder.addClass(activeClass);
				scrollPage(holder.offset().top - headerHeight);
				animationActive = false;
			});
		}
		function hideHolder(){
			animationActive = true;
			holder.css({
				overflow:'hidden',
				width:frame.width()
			});
			frame.css({
				position:'relative',
				width:frame.width()
			}).stop().animate({
				left:-frame.width()
			},{
				duration:animSpeed,
				complete:function(){
					holder.slideUp(animSpeed, function(){
						holder.removeClass(activeClass).css({
							display:'none',
							overflow:'',
							width:''
						});
						frame.removeAttr('style');
						scrollPage(columnsHolder.offset().top - headerHeight);
						animationActive = false;
					});
				}
			});
		}
		closer.on('click', function(e){
			e.preventDefault();
			if(!animationActive){
				hideHolder();
			}
		});
		openers.on('click', function(e){
			e.preventDefault();
			var opener = jQuery(this);
			tabs.filter('[href="#'+opener.data('tab')+'"]').trigger('click');
			if(!animationActive && !holder.hasClass(activeClass)){
				showHolder();
			}else if(holder.hasClass(activeClass)){
				hideHolder();
			}
		});
		holder.on('close', function(){
			holder.removeClass(activeClass).css({
				display:'none',
				overflow:'',
				width:''
			});
			frame.removeAttr('style');
		});
	});
}

// fancybox modal popup init
function initLightbox() {
	jQuery('a.lightbox, a[rel*="lightbox"]').each(function(){
		var link = jQuery(this);
		var id = link.attr('href');
		var $modal = $(id);
		link.fancybox({
			padding: 0,
			margin: 10,
			cyclic: false,
			autoScale: true,
			overlayShow: true,
			overlayOpacity: 0.7,
			overlayColor: '#f8f8f8',
			titlePosition: 'inside',
			onComplete: function(box) {
				if(link.attr('href').indexOf('#') === 0) {
					var $close = jQuery('#fancybox-content').find('a.close');
					$close.unbind('click.fb').bind('click.fb', function(e){
						jQuery.fancybox.close();
						e.preventDefault();
					});
					$modal.data('close', function(){
						$close.trigger('click');
					});
				}
			}
		});
		$modal.data('show', function(){
			link.trigger('click');
		});
	});
}

// initAjaxSend
function initAjaxSend(){
	jQuery('.contacts-form').each(function(){
		var form = jQuery(this);
		var message = jQuery();
		function messageShow(){
			jQuery.fancybox({
				href:form.data('message'),
				padding: 0,
				margin: 0,
				autoScale: true,
				overlayShow: true,
				overlayOpacity: 0.65,
				overlayColor: '#000000'
			});
		}
		function ajaxSend(e){
			e.preventDefault();
			jQuery.ajax({
				url: API_BASE_URL + form.attr('action'),
				type:'POST',
				dataType:'html',
				data:'ajax=1&' + form.serialize(),
				success: function(response) {
					if (form.hasClass('sent')){
						messageShow();
					}
				},
				error: function() {
					// error events
				}
			});
		}
		form.on('submit', ajaxSend);
	});
}

// initAnchorLinks
function initAnchorLinks(){
	jQuery('.btn-top').each(function(){
		var link = jQuery(this);
		var href = link.attr('href');
		var skip = 0;
		if(href.indexOf('#') > 0){
			return;
		}
		var section = jQuery(href);
		link.on('click', function(e){
			e.preventDefault();
			scrollPage(section.offset().top);
		});
	});
}
function scrollPage(target){
	var page = jQuery('html, body');
	if(/Windows Phone/.test(navigator.userAgent)){
		page.scrollTop(target);
	} else {
		page.stop().animate({
			scrollTop:target
		},1000);
	}
}

// init fade icons
function initFadeIcons(){
	var win = jQuery(window);
	var animSpeed = 250;
	var oldIe = jQuery.support.opacity === false;
	if(oldIe){
		return;
	}
	jQuery('.icons-list').each(function(){
		var list = jQuery(this);
		var items = list.find('li').css({opacity:0});
		var listOffset = list.offset().top;
		var listHeight = list.outerHeight();
		var windowHeight = window.innerHeight;
		var index = 0;
		function animateIcons(){
			items.eq(index).fadeTo(animSpeed, 1, function(){
				index++;
				if(items.eq(index).length){
					animateIcons();
				}
			});
		}
		function refreshOffset(){
			listOffset = list.offset().top;
			listHeight = list.outerHeight();
			windowHeight = window.innerHeight;
		}
		function checkScroll(){
			var startAnimate = win.scrollTop() + windowHeight >= listOffset && win.scrollTop() + windowHeight > listOffset + listHeight;
			if(startAnimate){
				animateIcons();
				win.off('scroll', checkScroll);
				win.off('resize orientationchange load', refreshOffset);
			}
		}
		win.on('scroll', checkScroll);
		win.on('resize orientationchange load', refreshOffset);
	});
}

// init fixed header on scroll
function initFixedHeader(){
	var win = jQuery(window);
	var page = jQuery('body');
	var wrapper = jQuery('#wrapper');
	var fixedClass = 'fixed-header';
	jQuery('.header-fixed-container').each(function(){
		var header = jQuery(this);
		var fixedPosition = header.offset().top;
		var headerHeight = header.outerHeight(true);
		function refresh(){
			wrapper.css({paddingTop:''});
			page.removeClass(fixedClass);
			fixedPosition = header.offset().top;
			headerHeight = header.outerHeight(true);
			checkScroll();
		}
		function checkScroll(){
			if(win.scrollTop() >= fixedPosition){
				page.addClass(fixedClass);
				wrapper.css({paddingTop:headerHeight});
			} else {
				page.removeClass(fixedClass);
				wrapper.css({paddingTop:''});
			}
		}
		win.on('resize orientationchange load', refresh);
		win.on('scroll', checkScroll);
		refresh();
	});
}

// initialize custom form elements
function initCustomForms() {
	if (safari) {
		return false;
	}
	jcf.setOptions('Select', {
		wrapNative: false,
		wrapNativeOnMobile: false
	});
	jcf.replaceAll();
	return true;
}

// scroll gallery init
function initCarousel() {
	jQuery('.price-carousel').scrollGallery({
		mask: 'div.mask',
		slider: 'div.slideset',
		slides: 'div.slide',
		btnPrev: 'a.btn-prev',
		btnNext: 'a.btn-next',
		pagerLinks: '.pagination li',
		stretchSlideToMask: true,
		maskAutoSize: true,
		autoRotation: false,
		switchTime: 3000,
		animSpeed: 500,
		step: 1
	});
}

// cycle scroll gallery init
function initCycleCarousel() {
	jQuery('div.cycle-gallery').scrollAbsoluteGallery({
		mask: 'div.mask',
		slider: 'div.slideset',
		slides: 'div.slide',
		btnPrev: 'a.btn-prev',
		btnNext: 'a.btn-next',
		pagerLinks: '.switcher li',
		stretchSlideToMask: true,
		pauseOnHover: true,
		maskAutoSize: true,
		autoRotation: false,
		switchTime: 3000,
		animSpeed: 500
	});
}

// content tabs init
function initTabs() {
	var win = jQuery(window);
	jQuery('.price-tabset').contentTabs({
		tabLinks: 'a'
	});
	jQuery('.orders-tabset').contentTabs({
		autoHeight: true,
		tabLinks: 'a.tab',
		onChange: function(oldTab, newTab){
			jQuery('.add-price-section').trigger('close');
		}
	});
	jQuery('.orders-results-table').contentTabs({
		autoHeight: true,
		addToParent: true,
		tabLinks: 'a.tab-link',
		onChange: function(oldTab, newTab){
			win.trigger('customresize');
		}
	});
}

// open-close init
function initOpenClose() {
	jQuery('.mobile-text-openbox').each(function(){
		var holder = jQuery(this);
		var slide = holder.closest('.slide');
		var mask = holder.closest('.mask');
		ResponsiveHelper.addRange({
			'..767': {
				on: function() {
					holder.openClose({
						activeClass: 'active',
						opener: '.opener-text',
						slider: '.slide-text',
						animSpeed: 100,
						effect: 'none',
						hideOnClickOutside: true,
						animEnd: function(){
							mask.stop().animate({height:slide.outerHeight()},400);

						}
					});
				},
				off: function() {
					holder.data('OpenClose').destroy();
				}
			}
		});
	});
	jQuery('.mobile-navigation').openClose({
		hideOnClickOutside: true,
		activeClass: 'active',
		opener: '.opener',
		slider: '.drop',
		animSpeed: 400,
		effect: 'slide'
	});
	jQuery('.registration-form').openClose({
		hideOnClickOutside: true,
		activeClass: 'active',
		opener: '.opener',
		slider: '.slide-box',
		animSpeed: 400,
		effect: 'slide'
	});
}

// accordion menu init
function initAccordion() {
	jQuery('.accordion-account').slideAccordion({
		opener: 'a.opener',
		slider: 'div.slide',
		animSpeed: 300
	});
	jQuery('.orders-accordion').slideAccordion({
		opener: 'a.opener',
		slider: 'div.slide',
		animSpeed: 300
	});
}

// align blocks height
function initSameHeight() {
	jQuery('.price-carousel').sameHeight({
		elements: '.info-box>.frame',
		flexible: true,
		multiLine: true
	});

	jQuery('.twocolumns').sameHeight({
		elements: '.content, .aside',
		flexible: true,
		multiLine: true,
		biggestHeight: true
	});

	jQuery('.price-box-holder').sameHeight({
		elements: '.info-box>.frame',
		flexible: true,
		multiLine: true
	});
}

// handle dropdowns on mobile devices
function initTouchNav() {
	jQuery('.account-menu ul').each(function(){
		new TouchNav({
			navBlock: this,
			menuDrop: '.drop'
		});
	});
}

// add classes on hover/touch
function initCustomHover() {
	jQuery('#nav ul li a').touchHover();
	jQuery('.navigation ul a').touchHover();
	jQuery('.orders-results-table .row-holder').touchHover();
}

function initActionByHash(){
	var hash = window.location.hash;
	switch(hash){
		case '#invite':
			var $modal = $('#myModal');
			var $title = $modal.find('.modal-content .heading h2');
			var $p = ('<p>' + TRANS['inviteRegisterComment'] + '</p>');
			$title.parent().append($p);
			$modal.data('show')();
			break;
		case '#order':
			var $btn = $($('button.leave-a-request')[0]);
			$btn.trigger('click');
			break;
	}
}
//возвращыет хеш сумму по алгоритму sha1
function SHA1(msg) {

	function rotate_left(n,s) {
		var t4 = ( n<<s ) | (n>>>(32-s));
		return t4;
	};

	function lsb_hex(val) {
		var str="";
		var i;
		var vh;
		var vl;

		for( i=0; i<=6; i+=2 ) {
			vh = (val>>>(i*4+4))&0x0f;
			vl = (val>>>(i*4))&0x0f;
			str += vh.toString(16) + vl.toString(16);
		}
		return str;
	};

	function cvt_hex(val) {
		var str="";
		var i;
		var v;

		for( i=7; i>=0; i-- ) {
			v = (val>>>(i*4))&0x0f;
			str += v.toString(16);
		}
		return str;
	};


	function Utf8Encode(string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	};

	var blockstart;
	var i, j;
	var W = new Array(80);
	var H0 = 0x67452301;
	var H1 = 0xEFCDAB89;
	var H2 = 0x98BADCFE;
	var H3 = 0x10325476;
	var H4 = 0xC3D2E1F0;
	var A, B, C, D, E;
	var temp;

	msg = Utf8Encode(msg);

	var msg_len = msg.length;

	var word_array = new Array();
	for( i=0; i<msg_len-3; i+=4 ) {
		j = msg.charCodeAt(i)<<24 | msg.charCodeAt(i+1)<<16 |
		msg.charCodeAt(i+2)<<8 | msg.charCodeAt(i+3);
		word_array.push( j );
	}

	switch( msg_len % 4 ) {
		case 0:
			i = 0x080000000;
			break;
		case 1:
			i = msg.charCodeAt(msg_len-1)<<24 | 0x0800000;
			break;

		case 2:
			i = msg.charCodeAt(msg_len-2)<<24 | msg.charCodeAt(msg_len-1)<<16 | 0x08000;
			break;

		case 3:
			i = msg.charCodeAt(msg_len-3)<<24 | msg.charCodeAt(msg_len-2)<<16 | msg.charCodeAt(msg_len-1)<<8	| 0x80;
			break;
	}

	word_array.push( i );

	while( (word_array.length % 16) != 14 ) word_array.push( 0 );

	word_array.push( msg_len>>>29 );
	word_array.push( (msg_len<<3)&0x0ffffffff );


	for ( blockstart=0; blockstart<word_array.length; blockstart+=16 ) {

		for( i=0; i<16; i++ ) W[i] = word_array[blockstart+i];
		for( i=16; i<=79; i++ ) W[i] = rotate_left(W[i-3] ^ W[i-8] ^ W[i-14] ^ W[i-16], 1);

		A = H0;
		B = H1;
		C = H2;
		D = H3;
		E = H4;

		for( i= 0; i<=19; i++ ) {
			temp = (rotate_left(A,5) + ((B&C) | (~B&D)) + E + W[i] + 0x5A827999) & 0x0ffffffff;
			E = D;
			D = C;
			C = rotate_left(B,30);
			B = A;
			A = temp;
		}

		for( i=20; i<=39; i++ ) {
			temp = (rotate_left(A,5) + (B ^ C ^ D) + E + W[i] + 0x6ED9EBA1) & 0x0ffffffff;
			E = D;
			D = C;
			C = rotate_left(B,30);
			B = A;
			A = temp;
		}

		for( i=40; i<=59; i++ ) {
			temp = (rotate_left(A,5) + ((B&C) | (B&D) | (C&D)) + E + W[i] + 0x8F1BBCDC) & 0x0ffffffff;
			E = D;
			D = C;
			C = rotate_left(B,30);
			B = A;
			A = temp;
		}

		for( i=60; i<=79; i++ ) {
			temp = (rotate_left(A,5) + (B ^ C ^ D) + E + W[i] + 0xCA62C1D6) & 0x0ffffffff;
			E = D;
			D = C;
			C = rotate_left(B,30);
			B = A;
			A = temp;
		}

		H0 = (H0 + A) & 0x0ffffffff;
		H1 = (H1 + B) & 0x0ffffffff;
		H2 = (H2 + C) & 0x0ffffffff;
		H3 = (H3 + D) & 0x0ffffffff;
		H4 = (H4 + E) & 0x0ffffffff;

	}

	var temp = cvt_hex(H0) + cvt_hex(H1) + cvt_hex(H2) + cvt_hex(H3) + cvt_hex(H4);

	return temp.toLowerCase();
}

//возвращает правильный запрос
function get_new_request(){
	var HTTP = {};
	var request;

	HTTP._factories = [
		function() { return new XMLHttpRequest(); },
		function() { return new ActiveXObject("Msxml2.XMLHTTP"); },
		function() { return new ActiveXObject("Microsoft.XMLHTTP");}
	];

	HTTP._factory = null;

	HTTP.newRequest = function() {
		if (HTTP._factory !=  null) return HTTP._factory();
		for(var i = 0; i < HTTP._factories.length; i++) {
			try {
				var  factory = HTTP._factories[i];
				var request = factory();
				if  (request != null) {
					HTTP._factory = factory;
					return request;
				}
			}
			catch(e) {
				continue;
			}
		}
		HTTP._factory = function() {
			throw new Error("Объект XMLHttpRequest не  поддерживается");
		}
		HTTP._factory();
	}
	return  HTTP.newRequest();
}

//декодирует текс алгоритмом base64
var Base64 = {
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
	//метод для кодировки в base64 на javascript
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0
		input = Base64._utf8_encode(input);
		while (i < input.length) {
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
			if( isNaN(chr2) ) {
				enc3 = enc4 = 64;
			}else if( isNaN(chr3) ){
				enc4 = 64;
			}
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
		}
		return output;
	},

	//метод для раскодировки из base64
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
		while (i < input.length) {
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
			output = output + String.fromCharCode(chr1);
			if( enc3 != 64 ){
				output = output + String.fromCharCode(chr2);
			}
			if( enc4 != 64 ) {
				output = output + String.fromCharCode(chr3);
			}
		}
		output = Base64._utf8_decode(output);
		return output;
	},
	// метод для кодировки в utf8
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";
		for (var n = 0; n < string.length; n++) {
			var c = string.charCodeAt(n);
			if( c < 128 ){
				utftext += String.fromCharCode(c);
			}else if( (c > 127) && (c < 2048) ){
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
		}
		return utftext;

	},

	//метод для раскодировки из urf8
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
		while( i < utftext.length ){
			c = utftext.charCodeAt(i);
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}else if( (c > 191) && (c < 224) ) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
		}
		return string;
	}
}

//преобразование из хмл в текст
function XMLFromString(sXML) {
	if (window.ActiveXObject) {
		var oXML = new ActiveXObject("Microsoft.XMLDOM");
		oXML.loadXML(sXML);
		return oXML;
	} else {
		return (new DOMParser()).parseFromString(sXML, "text/xml");
	}
}

//преобразование текста в хмл
function XMLToString(oXML) {
	if (window.ActiveXObject) {
		return oXML.xml;
	} else {
		return (new XMLSerializer()).serializeToString(oXML);
	}
}

//кодирует в утф8
function encode_utf8( s )
{
	return unescape( encodeURIComponent( s ) );
}

//декодирует в утф8
function decode_utf8( s )
{
	return decodeURIComponent( escape( s ) );
}

$(document).ready(function() {
	$.support.cors = true;
	var $fancyBox = $('#fancybox-loading');

	$fancyBox.click = function(){};
	$fancyBox.css({'cursor': 'default'});
	$('#fancybox-overlay').css({'cursor': 'default'});

	var $myModal = $('#myModal');
	var $myLicense = $('#myLicense');

	$('.show-license-link').click(function(){
		ShowLicense();
		return false;
	});

	$myLicense.find('.btn-primary').click(function(){
		$('#AgreeLicense').prop( "checked", true );
		$myModal.find('.btn-primary').removeAttr('disabled');
		$('#myLicense').modal('hide');
	});

	$('#AgreeLicense').click(function(){
		if ($("#AgreeLicense").prop("checked"))
			$myModal.find('.btn-primary').removeAttr('disabled');
		else
			$myModal.find('.btn-primary').attr('disabled', 'disabled');
	});

	$myModal
		.find('.signup-form input[type=text].promo')
		.on('keyup paste change', AppEventListeners.registrationPromoField);

	function ShowLicense(){
		$.ajax({
			'async': false,
			'url': '/payments_' + LANG + '.html',
			'dataType': 'text',
			'success': function(response){
				var $modal = $('#myLicense');
				$modal.find('div.row-holder').html(response);
			},
			'error': function(XMLHttpRequest){
				console.log(XMLHttpRequest);
			},
			'josnpCallback': 'callback',
			'timeout': 30000
		});
		$('#myLicense').modal('show');
	}

	// событие закрытия модального окна (всех)
	$('.modal.fade').on('hidden.bs.modal', function(e){

		// если после закрытия окна, еще остаются открытые
		var $modalOpen = $('.modal.fade.in');
		if($modalOpen.length > 0){
			// повесим на body класс открытого окна
			// что снимет overflow с body и вернет окну прокрутку
			$('body').addClass('modal-open');
		}

		// если это окно - сообщение после регистрации
		// в него специально записан флаг c указанием открыть окно логина
		var $curModal = $(e.currentTarget);
		if($curModal && $curModal.data('do') && $curModal.data('do') == 'openRegister' && AppOrderRequest && typeof AppOrderRequest.openNewUserRegisterModal == 'function' ){
			AppOrderRequest.openNewUserRegisterModal();
		}

	});

	var $langs = $('a.lang-href');
	$.each($langs, function(i, value){
		if(LANG == $(value).data('lang')){
			$(value).addClass('active');
		}
	});
	$langs.on('click', function(){
		var lang = $(this).data('lang');
		var postfix = TEST? '_test.html': '.html';
		if(LANG != lang){
			var path = lang == 'ru'? 'index': lang;
			path = '/' + path + postfix;
			if(!TEST && lang == 'ru'){
				path = '/';
			}
			window.document.location = path;
		}
	});


	$.get('/templates/prices/standard.' + LANG + '.html', function(data){
		$('.price-table.price-standard').html(data);
	});
	$.get('/templates/prices/business.' + LANG + '.html', function(data){
		$('.price-table.price-business').html(data);
	});
	$.get('/templates/prices/care.' + LANG + '.html', function(data){
		$('.price-table.price-care').html(data);
	});


	var $request = {
		a: $('button.leave-a-request'),
		m: $('#orderForm')
	};
	$request.a.on('click', function(){

		sendEvent('OrderRequestForm', 'click', $(this).data('loc'));

		$request.a.button('loading');
		function reset(){
			$request.a.button('reset');
		}
		AppHelpers.ajaxMe('customer/request/form', {account: 0}, function (data) {
			reset();
			$request.m.find('.form-request-content').html(data);
			$request.m.modal('show');
			initCreateOrderForm();
			initCustomForms();
		}, function(){
			reset();
		}, 'GET');
		return false;
	});

	$('.toggle-type-password').on('click', function(){

		var $this = $(this);
		$this.find('i').toggleClass('fa-eye-slash').toggleClass('fa-eye');
		var $input = $this.parent().find('input');
		if($input.attr('type') == 'password'){
			$input.attr('type', 'text');
		}else{
			$input.attr('type', 'password');
		}

		return false;
	});

});
