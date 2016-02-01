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