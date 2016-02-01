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
