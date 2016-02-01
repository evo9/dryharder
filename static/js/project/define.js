var AppEventListeners = {
};

var AppSession = {
	id: '',
	set: function(value){
		this.id = value;
	},
	get: function(){
		return this.id;
	}
};

var AppHelpers = {

	decode_text: function(str){
		return decodeURIComponent(str).replace(/[+]/g, ' ');
	},

	message: function(title){
		var modal = $('#myMessage');
		modal.find('.modal-content h3').html(title.replace('<%phone%>',''));
		modal.modal('show');
		return modal;
	},

	ajaxMe: function(action, data, success, error, method){

		method = method || 'post';
		data.lang = LANG;

		$.ajax(
			API_BASE_URL + action,
			{
				data: data,
				type: method,
				crossDomain: true,
				xhrFields: {
					withCredentials: true
				},

				success: success,
				error: function (q, s, e) {

					if(!error || typeof error != 'function'){
						return;
					}

					if('return' === error()){
						return;
					}

					var errorMessage = q.responseJSON && q.responseJSON.errors && AppHelpers.firstError(q.responseJSON.errors);
					errorMessage = errorMessage || q.responseJSON.message;
					if (!errorMessage) {
						errorMessage = TRANS['serverErrorMessage'];
						console.log(q);
						console.log(s);
						console.log(e);
					}

					AppHelpers.message(errorMessage);

				}
			}
		);
	},

	isPhoneNumber: function(str) {
		return (
			str.replace(/[- ]/g, '').search(/^[0-9]{10,11}$/) >= 0
		);
	},

	firstError: function ( input ) {
		var tmpArr = [], cnt = 0;
		for ( var key in input ){
			if(!input.hasOwnProperty(key)) continue;
			tmpArr[cnt] = input[key];
			cnt++;
		}
		return (tmpArr.length > 0)? tmpArr[0] : null;
	},

	renderSession: function(){

		var link = DASHBOARD_URL;

		var $accountNav = $('nav.account-nav');

		$accountNav.find('li.li-nav-wait,li.li-nav-login,li.li-nav-register').css({'display': 'none'});
		$accountNav.find('li.li-nav-logout,li.li-nav-user').css({display: 'list-item'});
		$accountNav.find('li.li-nav-user a').attr({'href': link});

		var $accountMenu = $('.account-menu');
		$accountMenu.css({'display': 'block'});
		$accountMenu.find('a:has(i.fa-tachometer)').attr({'href': link});

		var $basketBox = $('div.basket-box');
		$basketBox.find('a.basket-icon, div.basket-box a.basket-link').attr({'href': link});
		$basketBox.css({'display': 'block'});

		return link;

	},

	closeSession: function(){

		var $accountNav = $('nav.account-nav');

		$accountNav.find('li.li-nav-wait,li.li-nav-logout,li-nav-user').css({'display': 'none'});
		$accountNav.find('li.li-nav-login,li.li-nav-register').css({display: 'list-item'});
		$accountNav.find('li.li-nav-user a').attr({'href': '#'});
		$accountNav.find('li.li-nav-user a').html('<i class="fa fa-user"></i>');

		var $accountMenu = $('.account-menu');

		$accountMenu.css({'display': 'none'});
		$accountMenu.find('a:has(i.fa-tachometer)').attr({'href': '#'});
		$accountMenu.find('a:has(i.fa-user)').html('<i class="fa fa-user"></i>');

		var $basketBox = $('div.basket-box');

		$basketBox.find('a.basket-icon, div.basket-box a.basket-link').attr({'href': '#'});
		$basketBox.css({'display': 'none'});
		$basketBox.find('span.number').text('');

	}


};

function trans(code){
	return TRANS[code] || code;
}


function sendEvent(category, action, label, value) {

	if (typeof ga != 'function') {
		var ga = function () {

		};
	}
	if(typeof window.yaCounter27721494 !== 'object' || typeof window.yaCounter27721494.reachGoal !== 'function'){
		window.yaCounter27721494 = {reachGoal: function(){}};
	}
	var ya = window.yaCounter27721494.reachGoal;

	window._fbq = window._fbq || [];

	// успешная отправка заявки, трекаем в фб
	if(category == 'OrderRequestForm' && action == 'result' && label == 'success'){
		window._fbq.push(['track', '6026056380635', {'value':'0.00','currency':'RUB'}]);
	}

	if (value) {
		ga('send', 'event', category, action, label, value);
		ya(category + '_' + action + '_' + label);
	}
	else if (label) {
		ga('send', 'event', category, action, label);
		ya(category + '_' + action + '_' + label);
	}
	else {
		ga('send', 'event', category, action);
		ya(category + '_' + action);
	}

	console.log({
		category: category,
		action: action,
		label: label,
		value: value
	});
}

(function() {
	var _fbq = window._fbq || (window._fbq = []);
	if (!_fbq.loaded) {
		var fbds = document.createElement('script');
		fbds.async = true;
		fbds.src = '//connect.facebook.net/en_US/fbds.js';
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(fbds, s);
		_fbq.loaded = true;
	}
})();

