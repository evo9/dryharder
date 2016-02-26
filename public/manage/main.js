var App = angular.module('Dryharder', ["textAngular"]);

App.controller('ContentBlocksCtrl', ['$rootScope', '$http', function ($scope, $http) {

	$scope.list = [];
	$scope.store = {};
	$scope.languages = {
		'ru': 'ru',
		'en': 'en'
	};
	$scope.edit = {};

	$scope.editLang = function (item, lang) {

		$('.div-lang-editor').show();
		var id = 'content-' + item.id + '-' + lang;
		$('.lang-buttons').find('a').removeClass('editButton');
		var $btn = $('#' + id);
		$btn.addClass('editButton');

		item.lang = item.lang || {};
		item.lang[lang] = item.lang[lang] || '';

		$scope.edit = {
			itemId: item.id,
			btnId: id,
			lang: lang,
			content: item.lang[lang] || '',
			title: 'Изменение текста [' + lang + '] для блока [' + item.code + ']',
			comment: item.comment,
			change: item.code
		}

	};

	$scope.saveLang = function(edit){
		var $btn = $('.btn-save-item-lang,.btn-remove-item-lang');
		$btn.button('loading');
		$http
			.post('/man/content/blocks/' + edit.itemId + '/', edit)
			.success(function(){
				init(function(){
					$btn.button('reset');
				});
			}).error(function (r) {
				$btn.button('reset');
				error(r);
			});
	};

	init();

	function init(callback) {
		$http
			.get('/man/content/blocks')
			.success(function (data) {
				$scope.list = data;
				if(callback){
					callback();
				}
			});
	}


	$scope.createNew = function (store) {
		var $btn = $('.btn-create-new');
		$btn.button('loading');
		$http
			.post('/man/content/blocks/store', {code: store.code})
			.success(function () {
				$scope.newCode = '';
				init(function(){
					$btn.button('reset');
					store.code = '';
				});
			})
			.error(function (r) {
				$btn.button('reset');
				error(r);
			});
	};


	$scope.remove = function(edit){

		if(!confirm('Вы уверены? Блок будет удален')) {
			return false;
		}

		var $btn = $('.btn-save-item-lang,.btn-remove-item-lang');
		$btn.button('loading');
		$http
			.delete('/man/content/blocks/' + edit.itemId)
			.success(function () {
				init(function(){
					$btn.button('reset');
					edit = {};
				});
			})
			.error(function (r) {
				$btn.button('reset');
				error(r);
			});

	};

	function error(r){
		var error = r.error && r.error.message;
		error = error || 'Непонятная ошибка на сервере';
		alert(error);
	}

}]);





App.controller('ServiceTitlesCtrl', ['$rootScope', '$http', function ($scope, $http) {

	$scope.list = [];
	$scope.store = {};
	$scope.languages = {
		'ru': 'ru',
		'en': 'en'
	};
	$scope.edit = {};

	$scope.editLang = function (item, lang) {

		$('.div-lang-editor').show();
		var id = 'content-' + item.id + '-' + lang;
		$('.lang-buttons').find('a').removeClass('editButton');
		var $btn = $('#' + id);
		$btn.addClass('editButton');

		item.lang = item.lang || {};
		item.lang[lang] = item.lang[lang] || '';

		$scope.edit = {
			itemId: item.id,
			btnId: id,
			lang: lang,
			content: item.lang[lang] || '',
			title: 'Изменение текста [' + lang + '] для услуги [' + item.name + ']'
		}

	};

	$scope.saveLang = function(edit){
		var $btn = $('.btn-save-item-lang,.btn-remove-item-lang');
		$btn.button('loading');
		$http
			.post('/man/service/titles/' + edit.itemId + '/', edit)
			.success(function(){
				init(function(){
					$btn.button('reset');
				});
			}).error(function (r) {
				$btn.button('reset');
				error(r);
			});
	};

	init();

	function init(callback) {
		$http
			.get('/man/service/titles')
			.success(function (data) {
				$scope.list = data;
				if(callback){
					callback();
				}
			});
	}

	function error(r){
		var error = r.error && r.error.message;
		error = error || 'Непонятная ошибка на сервере';
		alert(error);
	}

}]);


App.controller('ReporterCtrl', ['$scope', '$http', function ($scope, $http) {

	$scope.list = [];

	init();

	$scope.pretty = function(data){
		var source = $.extend({}, data);
		delete source.message;
		/** @namespace source.ip */
		delete source.ip;
		/** @namespace source.sid */
		delete source.sid;
		delete source.date;
		/** @namespace source.dt */
		delete source.dt;
		delete source.time;
		if(source.result && data.sid == 'himstat.response.log'){
			return angular.toJson(JSON.parse(source.result), true);
		}
		return angular.toJson(source, true);
	};

	function init(callback) {
		$http
			.get('/man/reporter')
			.success(function (data) {
				$scope.list = data;
				if(callback){
					callback();
				}
				setTimeout(function(){
					var $pre = $('#ReporterCtrl').find('pre');
					$pre.off('click').on('click', function(){
						if($(this).hasClass('active')){
							$(this).removeClass('active');
							return;
						}
						$pre.removeClass('active');
						$(this).addClass('active');
					});
				}, 500);
			});
	}

}]);


App.controller('ReporterReviewCtrl', ['$scope', '$http', function ($scope, $http) {

	$scope.list = [];

	init();

	function init(callback) {
		$http
			.get('/man/reporter/reviews')
			.success(function (data) {
				$scope.list = data;
				if(callback){
					callback();
				}
			});
	}

}]);

App.controller('OrderRequestCtrl', ['$scope', '$http', function ($scope, $http) {

	$scope.list = [];

	init();

	function init() {
		$http
			.get('/man/requests')
			.success(function (data) {
				$scope.list = data;
			});
	}

}]);

App.controller('InviteCtrl', ['$scope', '$http', function ($scope, $http) {

	$scope.list = [];

	init();

	function init(callback) {
		$http
			.get('/man/reporter/invite/stat')
			.success(function (data) {
				$scope.list = data;
				if(callback){
					callback();
				}
			});
	}

}]);


App.controller('AutoPayCtrl', ['$scope', '$http', '$rootScope', function ($scope, $http, $rootScope) {

	$scope.list = [];
	$scope.checked = [];
	$scope.orderInfo = [];
	$scope.payProcess = false;
	load();

	$rootScope.$on('dh.order.pay', function(event, data) {
		$scope.orderInfo.countOrders --;
		$scope.orderInfo.totalOrderAmount -= data.amount;
	});

	$rootScope.$on('dh.check.customers.orders', function(event, data) {
		angular.forEach($scope.list, function(item, key) {
			if (item.agbisId == data.customerId) {
				$scope.list[key].isGoodOrder = data.isGoodOrder;
			}
		});
	});

	$scope.orders = function(item){
		$rootScope.$broadcast('dh.load.orders', {id: item.agbisId});
	};

	function load() {
		$http
			.get('/man/autopays')
			.success(function (data) {
				$scope.list = data.list;
				$scope.orderInfo = data.orderInfo
			});
	}

	$scope.check = function(id) {
		if ($scope.inArray(id, $scope.checked)) {
			angular.forEach($scope.checked, function(value, key) {
				if ($scope.checked[key] == id) {
					$scope.checked.splice(key, 1);
				}
			});
		}
		else {
			$scope.checked.push(id);
		}
	};

	$scope.checkAll = function() {
		if ($scope.checked.length > 0) {
			$scope.checked = [];
		}
		else {
			angular.forEach($scope.list, function(item, key) {
				if (item.isGoodOrder)
				$scope.checked.push(item.agbisId);
			});
		}
	};

	$scope.payAll = function() {
		if ($scope.checked.length > 0) {
			$scope.payProcess = true;
			$http
				.post(
					'/man/autopays/autopay_all',
					{ customers: $scope.checked }
				)
				.success(function(data) {
					if (data.errors.length > 0) {
						$rootScope.$broadcast('dh.autopay.error', { errors: data.errors })
					}

					$scope.payProcess = false;
					$scope.ckecked = [];
					if (data.result.length > 0) {
						angular.forEach(data.result, function(v, k) {
							angular.forEach($scope.list, function(item, key) {
								if (item.agbisId == v.customerId){
									$scope.list[key].isGoodOrder = v.isGoodOrder;
								}
							});
						});
					}
				});
			;
		}
	}


	$scope.inArray = function(el, arr) {
		for (var i = 0; i < arr.length; i ++) {
			if (arr[i] == el) {
				return true;
			}
		}
		return false;
	};

}]);

App.controller('AutoPayOrdersCtrl', ['$scope', '$http', '$rootScope', function ($scope, $http, $rootScope) {

	$scope.list = [];
	$scope.cid = 0;

	var $modal = $('#AutoPayOrdersCtrl');

	$rootScope.$on('dh.load.orders', function(e, data){
		$scope.list = [];
		$scope.cid = data.id;
		load($scope.cid);
		$modal.modal('show');
	});

	$scope.autopay = function($event, order){
		$($event.currentTarget).button('loading');
		$http
			.get('/man/autopays/start/' + order.id + '/' + $scope.cid)
			.success(function (data) {
				if (data.amount) {
					$rootScope.$broadcast('dh.order.pay', { amount: data.amount });
				}
				$($event.currentTarget).remove();
				load($scope.cid);
				checkCustomersOrders($scope.cid)
			})
			.error(function (data) {
				load($scope.cid);
				alert(data.message);
			});
	};

	function load(id) {
		$scope.list = [];
		$http
			.get('/man/autopays/orders/' + id)
			.success(function (data) {
				$scope.list = data;
			});
	}

	function checkCustomersOrders(customerId) {
		$http
			.get('/man/autopays/check_customers_orders/' + customerId)
			.success(function(data) {
				$rootScope.$broadcast('dh.check.customers.orders', {
					customerId: customerId,
					isGoodOrder: data.isGoodOrder
				});
			})
	}

}]);

App.controller('AutoPayErrorsCtrl', ['$scope', '$http', '$rootScope', function ($scope, $http, $rootScope) {
	$scope.items = [];
	$scope.modal = $('#AutoPayErrorsCtrl');

	$rootScope.$on('dh.autopay.error', function(e, data) {
		$scope.items = data.errors;
		$scope.modal.modal('show');
	});
}]);


App.filter('tel', function () {
	return function (tel) {
		if (!tel) {
			return '';
		}

		var value = tel.toString().trim().replace(/^\+/, '');

		if (value.match(/[^0-9]/)) {
			return tel;
		}

		var country, city, number;

		switch (value.length) {
			case 10: // +1PPP####### -> C (PPP) ###-####
				country = 1;
				city = value.slice(0, 3);
				number = value.slice(3);
				break;

			case 11: // +CPPP####### -> CCC (PP) ###-####
				country = value[0];
				city = value.slice(1, 4);
				number = value.slice(4);
				break;

			case 12: // +CCCPP####### -> CCC (PP) ###-####
				country = value.slice(0, 3);
				city = value.slice(3, 5);
				number = value.slice(5);
				break;

			default:
				return tel;
		}

		if (country == 1) {
			country = "";
		}

		number = number.slice(0, 3) + '-' + number.slice(3);

		return (country + " (" + city + ") " + number).trim();
	};
});