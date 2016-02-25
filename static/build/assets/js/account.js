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
    //var $orderDetails = $('.orders-table');
    var $orderDetails = $('#order_pay_button');
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
    var $btn = AppAccount.payBtn(),
        $orderDetails = $('#order_pay_button'),
        data = null,
        sum = 0;

    $orderDetails.on('click', '.button-holder.checkout', function (e) {
        e.preventDefault();
        $('#prepayment, .modal-backdrop').remove();

        var orderId = $(this).data('id'),
            subscriptionId = $(this).data('subscriptionid');
        sum = $(this).data('sum');
        data = subscriptionId ?
        {
            id: subscriptionId,
            target: 'subscription'
        } : {
            id: orderId,
            target: 'order'
        };

        $.get('/account/prepayment', function (html) {
            if (html) {
                $('body').append(html);
                $('#prepayment').modal('show');
                cardSelector();
                payStart();
            }
        });
    });

    function cardSelector() {
        var modal = $('#prepayment');
        var selected = modal.find('.selected'),
            list = modal.find('ul');
        if (list.find('li').length > 1) {
            selected.click(function () {
                $(this).hide();
                list.slideDown(200);
            });
        }
        list.find('li').click(function () {
            var card = $(this).text(),
                payment = $(this).data('payment');
            list.slideUp(200, function () {
                selected.find('span').text(card);
                selected.find('input').val(payment);
                selected.show();
            });
        });
    }

    function payStart() {
        var modal = $('#prepayment');
        var submitBtn = modal.find('.btn-dh-green');
        submitBtn.click(function () {
            var loadText = $(this).data('load'),
                defaultText = $(this).text();
            if (loadText != defaultText) {
                submitBtn.text(loadText);
                var modal = $('#prepayment');

                if (modal.find('#yandex_input:checked').length > 0) {
                    $.get('/account/pay/check/' + data.id, function (res) {
                        if (res.data.state == 'error') {
                            payByYandex();
                        }
                        else {
                            modal.modal('hide');
                        }
                    });
                }
                else {
                    var payment = modal.find('.selected input').val();
                    if (payment > 0) {
                        payByToken();
                    }
                    else {
                        modal.modal('hide');
                        payNewCard(true);
                    }
                }
            }
        });
    }

    // оплата через Яндекс
    function payByYandex() {
        $('#ya-payment-form').remove();
        var $form = $('<form action="' + YAM_URL + '" id="ya-payment-form" target="_top"></form>');
        $('body').append($form);
        $form.append('<input name="shopId" value="' + YAM_SID + '" type="hidden"/>');
        $form.append('<input name="scid" value="' + YAM_SCID + '" type="hidden"/>');
        $form.append('<input name="sum" value="' + sum + '" type="hidden">');
        //noinspection JSUnresolvedVariable
        $form.append('<input name="customerNumber" value="' + CID + '" type="hidden"/> ');
        $form.append('<input name="orderNumber" value="' + data.id + '" type="hidden"/>  ');
        $form.append('<input type="submit" value="Оплатить"/>');
        $form.submit();
    }

    // новая карта
    function payNewCard(reset) {
        if (data) {
            reset = reset ? 1 : 0;
            $.get(
                '/account/pay/init/' + data.id + '/' + data.target + '/' + reset,
                function (json) {
                    var payments = new cp.CloudPayments();
                    payments.charge(
                        json.data,
                        // успешная оплата
                        function (options) {
                            getFlashMessage('pay_new_card_success');
                        },
                        // ошибка оплаты
                        function (reason, options) {
                        }
                    );
                }
            );
        }
    }

    // оплата по токену
    function payByToken() {
        if (data) {
            data['payment_id'] = $('#prepayment .selected > input[name="payment"]').val();
            $.post('/account/pay/token', data, function (json) {
                if (json.errors) {
                    AppAccount.alertError(json.message);
                    $('.modal').modal('hide');
                }
                else {
                    getFlashMessage('pay_success');
                    $orderDetails.html('');
                }
            });
        }
    }
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
    $('.modal').modal('hide');
    setTimeout(function () {
        $('#flashMessage, #prepayment, .modal-backdrop').remove()
        if (type != '') {
            $.get('/account/flash/message/' + type, function (html) {
                if (html) {
                    $('body').append(html);
                    $('#flashMessage').modal('show');
                }
            });
        }
    }, 200);
}

function addCard(reload) {
    $.get('/account/new_card', function (json) {
        $('#flashMessage').modal('hide');
        var payments = new cp.CloudPayments();
        payments.charge(
            json.data,
            // успешная оплата
            function (options) {
                if (reload) {
                    refund();
                }
                else {
                    getFlashMessage('add_card_success');
                }
            },
            // ошибка оплаты
            function (reason, options) {
            }
        );
    });
}

function refund() {
    $.post('/account/pay/refund', {newCard: 1}, function (json) {
        cardList();
    });
}

function cardList() {
    $.get('/account/customers_cards', function (html) {
        $('ul#account_card_list').html(html);
    });
}

function autopay() {
    var modal = $('#flashMessage');
    if (modal.find('input:checked').length > 0) {
        $.post('/account/autopay', {autopay: 1}, function (json) {
            getFlashMessage(json.message);

        });
    }
    else {
        modal.modal('hide');
    }
}

function payFinish() {
    var modal = $('#flashMessage'),
        data = {};
    modal.find('input[type="checkbox"]:checked').each(function () {
        data[$(this).attr('name')] = $(this).val();
    });
    $.post('/account/pay_finish', data, function (json) {
        getFlashMessage(json.message);
    });
}

function deleteCard(self) {
    if ($('#account_card_list span.select_card > .label.checked').length) {
        var payments = [],
            text = self.text(),
            loadText = self.data('loading-text');
        self.text(loadText);
        $('#account_card_list span.select_card > .label.checked').each(function () {
            payments.push($(this).data('payment'));
        });
        $.post('/account/delete_card', {payments: payments}, function () {
            self.text(text);
            cardList();
        });
    }
}

(function () {
    $('#payment_info > a.opener').click(function () {
        cardList();
    });
    $('#account_card_list').on('click', 'span.select_card > .label', function () {
        var self = $(this),
            isCheck = $(this).hasClass('checked');
        if (!isCheck) {
            self.addClass('checked');
        }
        else {
            self.removeClass('checked');
        }
    });
    $('#account_card_list').on('click', 'span.card_autopay .label', function () {
        var data = {},
            self = $(this),
            isCheck = $(this).hasClass('checked')
        $('#account_card_list span.card_autopay > .label').removeClass('checked');
        if (!isCheck) {
            data['autopay'] = 1;
            data['payment_id'] = self.data('payment');
            self.addClass('checked');
        }
        $.post('/account/autopay', data, function (json) {
            $('span.card-info.header-card-item').html(json.currentCard);
        });
    });
})();
