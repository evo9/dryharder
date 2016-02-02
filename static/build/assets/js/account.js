if (typeof console == 'undefined' || (typeof windows != 'undefined' && !windows.console)) {
    var console = {
        log: function () {
        }
    };
}

var AppAccount = AppAccount || {};
AppAccount.payBtn = function () {
    return $('.button-holder.checkout');
};
AppAccount.ajaxError = AppAccount.ajaxError || null;
AppAccount.findResponseError = function (q) {
    return q.responseJSON && q.responseJSON.errors && q.responseJSON.errors[0] || AppAccount.errorMessage;
};
AppAccount.alertAjaxError = function (q) {
    this.alertError(this.findResponseError(q));
};
AppAccount.alertMessage = function (text) {
    $('.alert-success').remove();
    var $alert = $('.alert-template');
    $alert = $($alert.html());
    $alert.addClass('alert-success');
    $alert.find('h4').html(text);
    $('body').append($alert);
    $alert.fadeIn();
    setTimeout(function () {
        $alert.fadeOut()
    }, 10000);
    return $alert;
};
AppAccount.alertError = function (text) {
    $('.alert-danger').remove();
    var $alert = $('.alert-template');
    $alert = $($alert.html());
    $alert.addClass('alert-danger');
    $alert.find('h4').html(text);
    $('body').append($alert);
    $alert.fadeIn();
    setTimeout(function () {
        $alert.fadeOut()
    }, 10000);
    return $alert;
};
AppAccount.confirmError = function (text, textButton, funcConfirm) {
    $('.alert-danger').remove();
    var $alert = $('.alert-template-confirm');
    $alert = $($alert.html());
    $alert.addClass('alert-danger');
    $alert.find('h4').html(text);

    var $confirmBtn = $alert.find('.confirm-alert-button');
    $confirmBtn.html(textButton);
    $confirmBtn.off('click').on('click', funcConfirm);

    $('body').append($alert);
    $alert.fadeIn();
    return $alert;
};
AppAccount.closeAlert = function () {
    $('.alert-danger').remove();
};
var trans = trans || null;
var ua = detect.parse(navigator.userAgent);
var safari = ua.browser.family == 'Safari' || ua.device.family == 'iPhone' || ua.os.family == 'iOS';

$(function () {
    $.support.cors = true;
    $(document).ajaxError(function (r, q) {
        if (typeof AppAccount.ajaxError == 'function') {
            AppAccount.ajaxError(q);
            AppAccount.ajaxError = null;
        }
    });

    initCustomGallery();
    $('[data-toggle="tooltip"]').tooltip();

    $('.orders-tabset').contentTabs({
        tabLinks: 'a',
        onChange: function (oldTab, newTab) {

            switch (newTab.selector) {

                case '#tab1_2':
                    loadOrdersList($('#tab1_2'));
                    break;

                case '#tab2_2':
                    loadHistoryOrdersList($('#tab2_2'));
                    break;

                case '#tab3_1':
                    $('.subscription-button-holder').show();
                    break;

                case '#tab7_2':
                    loadPersonalAccount();
                    break;

                case '#tab7_3':
                    var $input = $('#share-links').find('input.share-url');
                    $input.focus(function () {
                        $(this).select();
                    });
                    $input.trigger('focus');
                    break;

                case '#tab3_2':
                    sendEvent('OrderRequestForm', 'click', 'tab');
                    $('#new-order-form').fadeIn();
                    initCreateOrderForm(AppAccount);
                    break;

                default:
                    break;

            }

        }
    });


    // ссылка на список текущих заказов
    var $ordersTabLink = $('a[href=#tab1_2]');

    // контейнер для списка заказов
    var $ordersTable = $('.orders-results-table.current-orders');
    var $ordersTableSm = $('.orders-accordion.visible-xs.current-orders');

    // контейнер для списка истории заказов
    var $ordersTableHistory = $('.orders-results-table.history-orders');
    var $ordersTableHistorySm = $('.orders-accordion.visible-xs.history-orders');

    // контейнер детальной информации о заказе
    var $orderDetails = $('.orders-table');
    var $orderDetailsModal = $('#myModal4');

    // слой закрывающий табы на время загрузки их содержимого
    var $overlay = $('.overlay-loading-orders');

    // список заказов
    $ordersTabLink.click();

    // кнопка оплаты
    AppAccount.payBtnLock = false;
    AppAccount.payWait = function () {
        if (AppAccount.payBtnLock) {
            return;
        }
        AppAccount.payBtnLock = true;
        AppAccount.payBtn().data('html', AppAccount.payBtn().find('a').html());
        AppAccount.payBtn().find('a').html(trans('Waiting...'));
        AppAccount.payBtn().find('a').attr('disabled', 'disabled');
    };
    AppAccount.payReset = function () {
        if (!AppAccount.payBtnLock) {
            return;
        }
        AppAccount.payBtnLock = false;
        AppAccount.payBtn().find('a').html(AppAccount.payBtn().data('html'));
        AppAccount.payBtn().data('html', '');
        AppAccount.payBtn().find('a').removeAttr('disabled');
    };

    // загрузка списка текущих заказов
    function loadOrdersList($tab) {
        $orderDetails.html('');
        AppAccount.payBtn().hide();
        $overlay.show();

        $ordersTable.html(trans('Loading order list...'));
        $ordersTableSm.html(trans('Loading order list...'));

        AppAccount.ajaxError = function () {
            var m = trans('Error loading orders list. Please try later');
            $ordersTable.html(m);
            $ordersTableSm.html(m);
        };
        $.get('/account/orders', function (data) {
            /**
             * @namespace data.qnt
             * @namespace data.browser
             * @namespace data.mobile
             */
            $overlay.hide();
            $ordersTable.html(data['browser']);
            $ordersTableSm.html(data['mobile']);

            var $tabDetails = $tab.find('.table-tabset-holder');
            $tabDetails.show();

            if (data.qnt <= 0) {
                $tabDetails.hide();
            }
            else {
                onOrderOpen($tab);
            }

            registerEventsOrderItems($ordersTable, $ordersTableSm);
            return false;
        });

    }

    // загрузка списка истории заказов
    function loadHistoryOrdersList($tab) {
        $orderDetails.html('');
        AppAccount.payBtn().hide();
        $overlay.show();

        $ordersTableHistory.html(trans('Loading order list...'));
        $ordersTableHistorySm.html(trans('Loading order list...'));

        AppAccount.ajaxError = function () {
            var m = trans('Error loading orders list. Please try later');
            $ordersTableHistory.html(m);
            $ordersTableHistorySm.html(m);
        };
        $.get('/account/history', function (data) {

            $overlay.hide();
            $ordersTableHistory.html(data['browser']);
            $ordersTableHistorySm.html(data['mobile']);

            var $tabDetails = $tab.find('.table-tabset-holder');
            $tabDetails.show();

            if (data.qnt <= 0) {
                $tabDetails.hide();
            }
            else {
                onOrderOpen($tab);
            }

            registerEventsOrderItems($ordersTableHistory, $ordersTableHistorySm);
            return false;
        });

    }

    // обработчик клика "информация по заказу"
    function onOrderOpen($tab) {

        // слайдеры информации о заказе под катом (мобильная версия)
        var $openers = $tab.find('.orders-accordion .opener');
        $openers.on('click', function () {

            var $this = $(this);
            var $slide = $this.next();

            $slide.removeClass('slide');
            $('.slide').hide();
            $slide.addClass('slide');
            $slide.slideToggle();

            return false;
        });
        $openers.eq(0).click();

        // подгрузка услуг заказа в правый блок (для полной версии)
        var $links = $tab.find('a.on-order-details');
        $links.on('click', function () {

            var $this = $(this);
            var id = $this.data('order_id');
            var paid = $this.data('paid');
            var sum = $this.data('sum');
            $links.parent().removeClass('active');
            $this.parent().addClass('active');

            $orderDetails.html(trans('Loading order information...'));
            AppAccount.payBtn().hide();

            AppAccount.ajaxError = function () {
                $orderDetails.html(trans('Error loading order data. Please try later'));
            };
            $overlay.show();
            $.get('/account/order/services/' + id, function (data) {
                $orderDetails.html(data);
                if (!paid) {
                    AppAccount.payBtn().data('id', id);
                    AppAccount.payBtn().data('sum', sum);
                    AppAccount.payBtn().show();
                }
                hoverOrderDetails();
                $overlay.hide();

            });

            return false;

        });
        $links.eq(0).click();

        // подгрузка услуг заказа в модальное окно (для мобильной версии)
        var $modalLinks = $('a.on-order-details-modal');
        $modalLinks.on('click', function () {

            var $table = $orderDetailsModal.find('.orders-table');

            var $this = $(this);
            var id = $this.data('order_id');
            var paid = $this.data('paid');
            var sum = $this.data('sum');

            $table.html(trans('Loading order information...'));
            AppAccount.payBtn().hide();
            $orderDetailsModal.modal('show');

            AppAccount.ajaxError = function () {
                $table.html(trans('Error loading order data. Please try later'));
            };
            $.get('/account/order/' + id, function (data) {
                $table.html(data);
                if (!paid) {
                    AppAccount.payBtn().data('id', id).data('sum', sum).show();
                    // передано сразу начинать оплату
                    if ($this.data('onpaystart') == true) {
                        AppAccount.payBtn().click();
                        $this.data('onpaystart', false);
                    }
                }
            });

            return false;

        });


        // псевдо-кнопки оплаты о заказе под катом (мобильная версия)
        var $openersCheckout = $tab.find('.checkout-accordion');
        $openersCheckout.on('click', function () {
            var $modalOpener = $tab.find('.' + $(this).data('paybtn'));
            // сообщаем что нужно стартовать оплату сразу после открытия окна
            $modalOpener.data('onpaystart', true);
            $modalOpener.click();
            return false;
        });


    }

    // отзывы
    function registerEventsOrderItems($list1, $list2) {

        $list1.find('.open-review-modal').on('click', review);
        $list2.find('.open-review-modal').on('click', review);

        function review() {

            var $this = $(this);
            var $modal = $('#reviewSurvey');
            $modal.modal('show');
            var $stars = $modal.find('a.star');
            hoverStars();
            clickStars(null, 0);
            $modal.find('textarea').val('');
            $stars.off('click').on('click', clickStars);
            var $button = $modal.find('.btn-primary');
            $button.off('click').on('click', submit);

            var review_id = $this.data('review');
            if (review_id > 0) {
                $.get('/account/order/review/' + review_id, function (res) {
                    $modal.find('textarea').val(res.data.text);
                    clickStars(null, res.data.stars);
                });
            }

            function submit() {

                var data = {
                    stars: $modal.data('star'),
                    text: $modal.find('textarea').val(),
                    order: $this.data('order'),
                    request: $this.data('request') === '1'
                };

                console.log(data);

                if (!data.stars && !data.text) {
                    return false;
                }

                $button.button('loading');
                AppAccount.ajaxError = function (q) {
                    $button.button('reset');
                    AppAccount.alertAjaxError(q);
                };
                $.post('/account/order/review', data, function (res) {
                    $this.data('review', res.data.id);
                    $this.addClass('review-exists');
                    $button.button('reset');
                    console.log(res);
                    AppAccount.alertMessage(trans('Review sent successful'));
                    $modal.modal('hide');
                });

                return false;

            }

            function clickStars(e, num) {
                if (e && typeof e.preventDefault == 'function') e.preventDefault();
                if (!num || num <= 0) {
                    var $star = $(this);
                    num = $star.data('num');
                    $stars.find('i').removeClass('fa-star').addClass('fa-star-o');
                }
                $modal.data('star', num);
                $stars.each(function (key, el) {
                    el = $(el);
                    if (num >= el.data('num')) {
                        el.find('i').addClass('fa-star').removeClass('fa-star-o');
                        el.data('fix', true);
                        el.css('borderBottom', '3px solid #2dd982');
                    }
                    else {
                        el.data('fix', false);
                        el.css('borderBottom', '0');
                    }
                });
                return false;
            }

            function hoverStars() {

                $stars.hover(
                    function () {
                        var num = $(this).data('num');
                        $stars.each(function (key, el) {
                            el = $(el);
                            if (el.data('fix')) {
                                return;
                            }
                            if (num >= el.data('num')) {
                                el.find('i').addClass('fa-star').removeClass('fa-star-o');
                            }
                            else {
                                el.find('i').removeClass('fa-star').addClass('fa-star-o');
                            }
                        });
                    },
                    function () {
                        $stars.each(function (key, el) {
                            el = $(el);
                            if (el.data('fix')) {
                                return;
                            }
                            el.find('i').removeClass('fa-star').addClass('fa-star-o');
                        });
                    }
                );

            }


        }

    }


    // загрузка данных в формы аккаунта
    function loadPersonalAccount() {

        var $tab = $('#account-forms');
        var $formUser = $tab.find('.registration-form.data-form');
        var $formUserInputs = $formUser.find('input');
        var $formPass = $tab.find('.registration-form.password-form');
        var $allButtons = $tab.find('button');

        $allButtons.button('loading');
        AppAccount.ajaxError = function (q) {
            $allButtons.button('reset');
            this.alertAjaxError(q);
        };

        // получение данных и заполнение форм
        $.get('/account/forms/fields', function (fields) {
            /**
             * @namespace fields.user
             */
            $allButtons.button('reset');

            $formUserInputs.each(function (key, $input) {
                $input = $($input);
                for (var fd in fields.user) {
                    if (!fields.user.hasOwnProperty(fd)) continue;
                    if ($input.attr('name') == fd) {
                        $input.val(fields.user[fd]);
                    }
                }
            });

            listenUserForm($formUser);
            listenPasswordForm($formPass);

        });

    }


    function listenUserForm($formUser) {

        var $btn = $formUser.find('button');
        $formUser.off('submit');
        $formUser.on('submit', function () {

            $btn.button('loading');
            AppAccount.ajaxError = function (q) {
                $btn.button('reset');
                showFormErrors(q, $formUser);
            };

            $.post('/account/forms/user', $formUser.serializeArray(), function () {
                $btn.button('reset');
                AppAccount.alertMessage(trans('Registration data has been changed'));
            });

            return false;
        });

    }


    function listenPasswordForm($formPass) {

        var $btn = $formPass.find('button');
        $formPass.off('submit');
        $formPass.on('submit', function () {

            $btn.button('loading');
            AppAccount.ajaxError = function (q) {
                $btn.button('reset');
                AppAccount.alertAjaxError(q);
            };

            $.post('/account/forms/password', $formPass.serializeArray(), function () {
                $btn.button('reset');
                AppAccount.alertMessage(trans('Password has been changed'));
                $formPass.find('input').val('');
            });

            return false;
        });

    }


    function showFormErrors(q, $form) {
        /** @namespace q.responseJSON.errors */
        var errors = q.responseJSON && q.responseJSON.errors;
        if (errors && typeof errors == 'object') {
            for (var key in errors) {
                if (!errors.hasOwnProperty(key)) return;
                var $input = $form.find('*[name=' + key + ']');
                $input.addClass('error');
                $input.off('keyup').on('keyup', function () {
                    $(this).removeClass('error');
                });
            }
        }
        else {
            AppAccount.alertAjaxError(q);
        }
    }

    // выход
    $('.signout-link').on('click', function () {
        var text = '<small>' + trans('Waiting...') + '</small>';
        $('.waiting-for-logout').replaceWith(text);
        $.ajax(
            API_BASE_URL + 'customer/signup/logout',
            {
                type: 'POST',
                crossDomain: true,
                xhrFields: {
                    withCredentials: true
                },
                success: function () {
                    window.location.reload();
                }
            }
        );
        return false;
    });


    // меню для мобильных устройств
    jQuery('.mobile-navigation').openClose({
        hideOnClickOutside: true,
        activeClass: 'active',
        opener: '.opener',
        slider: '.drop',
        animSpeed: 400,
        effect: 'slide'
    });


    // галерея табов
    function initCustomGallery() {
        jQuery('.orders-tabset').each(function () {
            var holder = jQuery(this);
            var list = holder.find('ul');
            var items = holder.find('li');
            ResponsiveHelper.addRange({
                '..991': {
                    on: function () {
                        initGallery();
                    },
                    off: function () {
                        destroyGallery();
                    }
                }
            });
            function initGallery() {
                list.carouFredSel({
                    width: '100%',
                    prev: '.btn-prev',
                    next: '.btn-next',
                    auto: false,
                    items: {
                        start: 1
                    }
                });
            }

            function destroyGallery() {
                list.trigger("destroy");
                setTimeout(function () {
                    items.removeAttr('style');
                }, 10)
            }
        });
    }

    var $langs = $('a.lang-href');
    $langs.on('click', function () {
        var $this = $(this);
        var lang = $this.data('lang');
        $this.parent().html(trans('just a second...'));
        AppAccount.ajaxError = null;
        $.get('/account/lang/' + lang, function () {
            window.location.reload();
        });
    });

    jQuery('.accordion-account').slideAccordion({
        opener: 'a.opener',
        slider: 'div.slide',
        animSpeed: 300
    });


    $('.show-license-link').click(function () {
        ShowLicense();
        return false;
    });

    function ShowLicense() {
        $.ajax({
            'async': false,
            'url': '/payments_' + LANG + '.html',
            'dataType': 'text',
            'success': function (response) {
                var $modal = $('#myLicense');
                $modal.find('div.row-holder').html(response);
            },
            'error': function (XMLHttpRequest) {
                console.log(XMLHttpRequest);
            },
            'josnpCallback': 'callback',
            'timeout': 30000
        });
        $('#myLicense').modal('show');
    }

    // форма заказа
    $.ajax(
        API_BASE_URL + 'customer/request/form',
        {
            data: {
                account: 1
            },
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },
            success: function (data) {
                $('.create-order-form-content').html(data);
                initCustomForms();
            },
            error: function () {

            }
        }
    );

    // удерживаем чек на месте
    $(window).scroll(function () {
        var $el = $('div.ticket-fix .aside');
        var $table = $('.orders-tab-content');
        var table = $table.offset().top + 60;
        var height = $el.height();
        var winHeight = $(window).height();
        var top = $(window).scrollTop();
        var pos = table - top;
        if (pos + height < top + winHeight) {
            pos = table - top;
        }
        if (pos < 20) {
            pos = 20;
        }
        if (height > table) {
            $table.css('minHeight', height + 'px');
        }
        else {
            $table.css('minHeight', '600px');
        }
        $el.css('top', pos + 'px');
    });

    // flash messages
    AppAccount.ajaxError = null;
    getFlashMessage('add_card');

    $('.bonuses-qnt').hide();
    $.get('/account/bonus', function (data) {
        /** @namespace data.bonus */
        var bonus = data.bonus > 0 ? data.bonus : 0;
        $('.bonuses-qnt').html(bonus).show();
    });

    // подписки
    $.get('/account/subscriptions', function (data) {
        var $content = $('.subscriptions-content');
        $content.html(data);
        $content
            .find('button.btn-primary')
            .off('click')
            .on('click', function () {
                var $btn = $(this);
                $btn.button('loading');

            });
        doPaymentListeners();
    });

});


// оплата
function doPaymentListeners() {

    AppAccount.errorMessage = trans('Server error. Please try later');

    // кнопка "оплатить" - выбор карты
    var $btn = AppAccount.payBtn();
    var $modal = $('#select-pay-card');

    $btn.off('click').on('click', function () {

        var $this = $(this);
        var order_id = $this.data('id');
        var subscription_id = $this.data('subscriptionid');
        var sum = $this.data('sum');
        var request = subscription_id ?
        {
            id: subscription_id,
            target: 'subscription'
        } : {
            id: order_id,
            target: 'order'
        };

        AppAccount.payWait();
        AppAccount.ajaxError = function (q) {
            AppAccount.payReset();
            AppAccount.alertAjaxError(q);
        };
        $.get('/account/pay/card', function (data) {

            var $html = $modal.find('.card-table');
            $btn.each(function (key, btn) {
                var m = $(btn).parents('.modal');
                m.length > 0 && m.modal('hide');
            });

            // предлагаем выбор - оплатить привязанной или новой
            $html.html(data);
            $modal.modal('show');
            $modal.off('hidden.bs.modal').on('hidden.bs.modal', function () {
                AppAccount.payReset();
            });

            onClickSaveCardCheckbox();

            $modal.find('.select-card-item').on('click', function () {
                var $this = $(this);
                var messageId = 'Payment in progress...';
                if ($this.data('type') == 'card') {
                    messageId = 'Loading payment form...';
                    setTimeout(beginPay.bind(null, request.id, request.target, function () {
                        $modal.modal('hide');
                    }), 200);
                }
                else if ($this.data('type') == 'yandex') {
                    messageId = 'Loading payment form...';

                    $.get('/account/pay/check/' + request.id, function (res) {
                        if (res.data.state == 'error') {
                            submitYamForm(request.id, sum);
                        }
                        else {
                            $modal.modal('hide');
                            AppAccount.alertMessage(res.data.message)
                        }
                    });

                }
                else if ($this.data('type') == 'token') {
                    setTimeout(beginPayToken.bind(null, request.id, request.target, function () {
                        $modal.modal('hide');
                    }), 200);
                }
                $html.html('<p class="title-waiting-paid">' + trans(messageId) + '</p>');
            });

        });

        return false;
    });


    function submitYamForm(orderId, sum) {
        $('#ya-payment-form').remove();
        var $form = $('<form action="' + YAM_URL + '" id="ya-payment-form" target="_top"></form>');
        $form.append('<input name="shopId" value="' + YAM_SID + '" type="hidden"/>');
        $form.append('<input name="scid" value="' + YAM_SCID + '" type="hidden"/>');
        $form.append('<input name="sum" value="' + sum + '" type="hidden">');
        //noinspection JSUnresolvedVariable
        $form.append('<input name="customerNumber" value="' + CID + '" type="hidden"/> ');
        $form.append('<input name="orderNumber" value="' + orderId + '" type="hidden"/>  ');
        $form.append('<input type="submit" value="Оплатить"/>');
        $form.submit();
    }

    // платеж по токену
    function beginPayToken(id, target, callback) {

        AppAccount.payWait();

        AppAccount.ajaxError = function (q) {
            callback();
            AppAccount.payReset();
            AppAccount.alertAjaxError(q);
        };
        $.post('/account/pay/token', {id: id, target: target}, function (data) {
            callback();
            console.log(data);
            AppAccount.payReset();
            AppAccount.alertMessage(trans('Payment successful'));
        });

    }

    // инициализация платежа в CloudPayments
    function beginPay(id, target, callback) {

        AppAccount.payWait();

        cp.CloudPayments = cp.CloudPayments || null;
        var payments = new cp.CloudPayments();
        payments.charge = payments.charge || null;

        AppAccount.ajaxError = function (q) {
            callback();
            AppAccount.payReset();
            if (q.status && q.status == 409) {
                AppAccount.confirmError(q.responseJSON.message, q.responseJSON.data['repeatText'], function () {
                    AppAccount.closeAlert();
                    requestInitPay(true);
                });
            }
            else {
                AppAccount.alertAjaxError(q);
            }
        };

        requestInitPay(false);

        function requestInitPay(reset) {
            reset = reset ? 1 : 0;
            $.ajax(
                '/account/pay/init/' + id + '/' + target + '/' + reset,
                {
                    success: function (data) {

                        callback();
                        AppAccount.payWait();

                        payments.charge(
                            data.data,
                            // успешная оплата
                            function (options) {
                                console.log(options);
                                AppAccount.alertMessage(trans('Payment successful'));
                                AppAccount.payReset();
                            },
                            // ошибка оплаты
                            function (reason, options) {
                                console.log(reason);
                                console.log(options);
                                AppAccount.payReset();
                                AppAccount.alertError(trans('Payment not finished'));
                            }
                        );

                    }
                }
            );
        }


    }


    $('.save-card-remove').off('click').on('click', function () {

        var $btn = $(this);
        $btn.button('loading');

        AppAccount.ajaxError = function (q) {
            $btn.button('reset');
            AppAccount.alertAjaxError(q);
        };

        $.post('/account/forms/card/remove', function () {
            $btn.button('reset');
            $('.info-card-in-account').fadeOut().remove();
            $('.header-card-item').fadeOut().remove();
        });

    });

    function onClickSaveCardCheckbox() {

        var $ch = $('.checkbox-save-card');
        var $lb = $ch.parent().find('label');
        var alertText = $ch.parents('.modal').find('.save-card-info').html();

        var $auto = $('.checkbox-autopay-card');
        var $lbAuto = $auto.parent().find('label');
        var alertTextAuto = $auto.parents('.modal,.personal-card-settings').find('.autopay-info').html();
        var alertTextAutoOff = $auto.parents('.modal,.personal-card-settings').find('.autopay-info-off').html();

        $auto.parent().find('i').tooltip().off('click').on('click', function () {
            AppAccount.alertMessage(alertTextAuto);
            return false;
        });
        $ch.parent().find('i').tooltip().off('click').on('click', function () {
            AppAccount.alertMessage(alertText);
            return false;
        });

        $ch.off('change').on('change', function () {

            var check = $(this).is(':checked') ? 1 : 0;
            load();

            AppAccount.ajaxError = function (q) {
                reset();
                AppAccount.alertAjaxError(q);
            };

            $.post('/account/forms/card/save', {as: check}, function (data) {
                data == '1' ? $ch.prop('checked', true) : $ch.removeAttr('checked', false);
                if (data == 1) {
                    AppAccount.alertMessage(alertText);
                }
                reset();
            });

            if (!check) {
                $auto.removeAttr('checked');
                $auto.attr('disabled', 'disabled');
                $auto.parent().addClass('disabled').removeClass('active');
            }
            else {
                $auto.removeAttr('disabled');
                $auto.parent().addClass('active').removeClass('disabled');
            }

        });

        $auto.off('change').on('change', function () {

            var check = $(this).is(':checked') ? 1 : 0;
            loadAuto();

            AppAccount.ajaxError = function (q) {
                resetAuto();
                AppAccount.alertAjaxError(q);
            };

            $.post('/account/forms/autopay/save', {as: check}, function (data) {
                if (data == 1) {
                    AppAccount.alertMessage(alertTextAuto);
                }
                else {
                    AppAccount.alertMessage(alertTextAutoOff);
                }
                data == '1' ? $auto.prop('checked', true) : $auto.removeAttr('checked', false);
                resetAuto();
            });

        });

        function reset() {
            $ch.removeAttr('disabled');
            $lb.html($lb.data('html'));
        }

        function load() {
            $ch.attr('disabled', 'disabled');
            $lb.data('html', $lb.html());
            $lb.html(trans('saving'));
        }

        function resetAuto() {
            $auto.removeAttr('disabled');
            $lbAuto.html($lbAuto.data('html'));
        }

        function loadAuto() {
            $auto.attr('disabled', 'disabled');
            $lbAuto.data('html', $lbAuto.html());
            $lbAuto.html(trans('saving'));
        }

    }

    onClickSaveCardCheckbox();

}

function initCustomForms() {
    if (!safari) {
        jcf.setOptions('Select', {
            wrapNative: false,
            wrapNativeOnMobile: false
        });
        jcf.replaceAll();
    }
}


function sendEvent(category, action, label, value) {

    if (typeof ga != 'function') {
        var ga = function () {

        };
    }
    if (typeof window.yaCounter27721494 !== 'object' || typeof window.yaCounter27721494.reachGoal !== 'function') {
        window.yaCounter27721494 = {
            reachGoal: function () {
            }
        };
    }
    var ya = window.yaCounter27721494.reachGoal;

    category = category + '_acc';

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


function hoverOrderDetails() {
    var $modal = $('#order-detail-modal');
    var $links = $('.detail-order-hover');
    var opened = false;
    $('.detail-order-hover i,.download-clothes-description i').tooltip();

    $links.off('click');

    $links.on('click', function () {

        var $link = $(this);

        if ($link.data('clicked') == true) {
            $modal.modal('hide');
            opened = false;
            return;
        }
        if (opened) {
            $modal.modal('hide');
        }

        $modal.find('.detail-order-place').html($link.parent().next().html());
        $modal.modal('show');
        opened = true;

        return false;

    });

}

function getFlashMessage(type) {
    $('#flashMessage').remove()
    $.get('/account/flash/message/' + type, function (html) {
        if (html) {
            $('body').append(html);
            $('#flashMessage').modal('show');
        }
    });
}

function addCard() {
    $.get('/account/add_card', function(json) {
        $('#flashMessage').modal('hide');
        var payments = new cp.CloudPayments();
        payments.charge(
            json.data,
            // успешная оплата
            function (options) {
                $('#flashMessage').rempove();
                $.get('/account/flash/message/add_card_success', function(html) {
                    $('body').append('html');
                    $('#flashMessage').modal('show');
                });
            },
            // ошибка оплаты
            function (reason, options) {
            }
        );
    });
}
