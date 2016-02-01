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
