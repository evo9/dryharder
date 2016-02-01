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