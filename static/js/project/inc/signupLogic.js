
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