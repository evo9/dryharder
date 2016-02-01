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
