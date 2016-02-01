(function(){
	var modal = $('#modal-tablet');
	var $startRow = $('.start-button-row');
	var $bigButton = $('.start-button');
	var $phoneRow = $('.phone-row');
	var $confirmButton = $('.confirm-button');
	var $backButton = $('.back-button');
	var $errorRow = $('.error-row');
	var $successRow = $('.success-row');

	var phone = $('.input-phone');
	var title = $('.input-title');

	$('.close').on('click', function(){
		modal.modal('hide');
	});

	$('.modal.fade').on('hidden.bs.modal', function(e){
		$backButton.click();
	});

	$bigButton.click(function(){
		$phoneRow.show();
		$errorRow.hide();
		$successRow.hide();
		modal.modal('show');
		return false;
	});

	$backButton.click(function(){
		$phoneRow.hide();
		$errorRow.hide();
		$successRow.hide();
		modal.modal('hide');
		return false;
	});


	$confirmButton.click(function(){

		$confirmButton.button('loading');
		$backButton.hide();

		function reset(clear){
			$confirmButton.button('reset');
			$backButton.show();
			if(!clear){
				phone.val('');
				title.val('');
				phone.css('border', '4px solid black');
				title.css('border', '4px solid black');
			}
		}

		if(!AppHelpers.isPhoneNumber(phone.val())){
			phone.css('border', '4px solid red');
			reset(1);
			return;
		}else{
			phone.css('border', '4px solid green');
		}

		if(title.val().length <= 0){
			title.css('border', '4px solid red');
			reset(1);
			return;
		}else{
			title.css('border', '4px solid green');
		}


		AppHelpers.ajaxMe('customer/order/message', {
			phone: phone.val(),
			title: title.val()
		}, function (res) {
			AppHelpers.messageSuccess(res.message);
			reset();
		}, function(){
			reset();
		});

	});

	AppHelpers.message = function(text){
		$errorRow.find('h3').html(text);
		$errorRow.show();
		$phoneRow.hide();
	};

	AppHelpers.messageSuccess = function(text){
		$successRow.find('h3').html(text);
		$successRow.show();
		$phoneRow.hide();
	};

})();