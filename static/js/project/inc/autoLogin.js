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
