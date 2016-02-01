
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