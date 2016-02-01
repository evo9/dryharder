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
