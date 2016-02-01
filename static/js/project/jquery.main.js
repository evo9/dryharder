// page init
var DH = {};
var ua = detect.parse(navigator.userAgent);
var safari = ua.browser.family == 'Safari' || ua.device.family == 'iPhone' || ua.os.family == 'iOS';

jQuery(window).load(function(){
	jQuery(function(){

		DH.signupForm = {};

		initTabs();
		initCustomForms();
		initTouchNav();
		initCarousel();
		initCycleCarousel();
		initOpenClose();
		initAccordion();
		initSameHeight();
		initCustomHover();
		jQuery('input, textarea').placeholder();
		jQuery('[data-toggle=tooltip]').tooltip();
		jQuery('.carousel').carousel('pause');
		initFixedHeader();
		initAnchorLinks();
		initFadeIcons();
		initLightbox();
		initCustomOpenClose();
		initCustomGallery();
		initPageScroll();
		initFeedbackAjaxSend();
		initSignupLogic();
		initActionByHash();
	});
	initDatapicker();
});

// init page scroll
function initPageScroll(){
	jQuery('body').pageScroll({
		header: '.header-fixed-container',
		links: '.navigation a, #nav ul li a',
		addToParent: true
	});
}

// initDatapicker
function initDatapicker(){
	jQuery(".calendar-block").each(function(){
		var holder=jQuery(this);
		var input=holder.find("input");
		var button=holder.find(".calendar");
			
		input.datepicker();
		button.on("click", function(e){
			input.focus();
			e.preventDefault();
		})
	})
}

// init language form
function iniLangForm(){
	jQuery('.language-form').each(function(){
		var form = jQuery(this);
		form.find('select').on('change', function(){
			document.location.href = jQuery(this).val();
		});
	});
}

// init custom gallery
function initCustomGallery(){
	jQuery('.orders-tabset').each(function(){
		var holder = jQuery(this);
		var list = holder.find('ul');
		var items = holder.find('li');
		ResponsiveHelper.addRange({
			'..991': {
				on: function() {
					initGallery();
				},
				off: function() {
					destroyGallery();
				}
			}
		});
		function initGallery(){
			list.carouFredSel({
				width: '100%',
				prev: '.btn-prev',
				next: '.btn-next',
				auto: false
			});
		}
		function destroyGallery(){
			list.trigger("destroy");
			setTimeout(function(){
				items.removeAttr('style');
			},10)
		}
	});
}

// initCustomOpenClose
function initCustomOpenClose(){
	var activeClass = 'active';
	var page = jQuery('html, body');
	var animSpeed = 500;
	var columnsHolder = jQuery('.add-price-style');
	jQuery('.add-price-section').each(function(){
		var holder = jQuery(this).hide();
		var frame = holder.children();
		var holderId = holder.attr('id');
		var closer = holder.find('.link-close');
		var openers = jQuery('a[href="#'+holderId+'"]');
		var header = jQuery('.header-fixed-container');
		var tabs = holder.find('.price-tabset li a');
		var animationActive = false;
		var headerHeight = 0;
		function showHolder(){
			animationActive = true;
			headerHeight = 0;
			if(header.css('position') == 'fixed'){
				headerHeight = header.outerHeight();
			} else {
				headerHeight = 0;
			}
			holder.slideDown(animSpeed, function(){
				holder.addClass(activeClass);
				scrollPage(holder.offset().top - headerHeight);
				animationActive = false;
			});
		}
		function hideHolder(){
			animationActive = true;
			holder.css({
				overflow:'hidden',
				width:frame.width()
			});
			frame.css({
				position:'relative',
				width:frame.width()
			}).stop().animate({
				left:-frame.width()
			},{
				duration:animSpeed,
				complete:function(){
					holder.slideUp(animSpeed, function(){
						holder.removeClass(activeClass).css({
							display:'none',
							overflow:'',
							width:''
						});
						frame.removeAttr('style');
						scrollPage(columnsHolder.offset().top - headerHeight);
						animationActive = false;
					});
				}
			});
		}
		closer.on('click', function(e){
			e.preventDefault();
			if(!animationActive){
				hideHolder();
			}
		});
		openers.on('click', function(e){
			e.preventDefault();
			var opener = jQuery(this);
			tabs.filter('[href="#'+opener.data('tab')+'"]').trigger('click');
			if(!animationActive && !holder.hasClass(activeClass)){
				showHolder();
			}else if(holder.hasClass(activeClass)){
				hideHolder();
			}
		});
		holder.on('close', function(){
			holder.removeClass(activeClass).css({
				display:'none',
				overflow:'',
				width:''
			});
			frame.removeAttr('style');
		});
	});
}

// fancybox modal popup init
function initLightbox() {
	jQuery('a.lightbox, a[rel*="lightbox"]').each(function(){
		var link = jQuery(this);
		var id = link.attr('href');
		var $modal = $(id);
		link.fancybox({
			padding: 0,
			margin: 10,
			cyclic: false,
			autoScale: true,
			overlayShow: true,
			overlayOpacity: 0.7,
			overlayColor: '#f8f8f8',
			titlePosition: 'inside',
			onComplete: function(box) {
				if(link.attr('href').indexOf('#') === 0) {
					var $close = jQuery('#fancybox-content').find('a.close');
					$close.unbind('click.fb').bind('click.fb', function(e){
						jQuery.fancybox.close();
						e.preventDefault();
					});
					$modal.data('close', function(){
						$close.trigger('click');
					});
				}
			}
		});
		$modal.data('show', function(){
			link.trigger('click');
		});
	});
}

// initAjaxSend
function initAjaxSend(){
	jQuery('.contacts-form').each(function(){
		var form = jQuery(this);
		var message = jQuery();
		function messageShow(){
			jQuery.fancybox({
				href:form.data('message'),
				padding: 0,
				margin: 0,
				autoScale: true,
				overlayShow: true,
				overlayOpacity: 0.65,
				overlayColor: '#000000'
			});
		}
		function ajaxSend(e){
			e.preventDefault();
			jQuery.ajax({
				url: API_BASE_URL + form.attr('action'),
				type:'POST',
				dataType:'html',
				data:'ajax=1&' + form.serialize(),
				success: function(response) {
					if (form.hasClass('sent')){
						messageShow();
					}
				},
				error: function() {
					// error events
				}
			});
		}
		form.on('submit', ajaxSend);
	});
}

// initAnchorLinks
function initAnchorLinks(){
	jQuery('.btn-top').each(function(){
		var link = jQuery(this);
		var href = link.attr('href');
		var skip = 0;
		if(href.indexOf('#') > 0){
			return;
		}
		var section = jQuery(href);
		link.on('click', function(e){
			e.preventDefault();
			scrollPage(section.offset().top);
		});
	});
}
function scrollPage(target){
	var page = jQuery('html, body');
	if(/Windows Phone/.test(navigator.userAgent)){
		page.scrollTop(target);
	} else {
		page.stop().animate({
			scrollTop:target
		},1000);
	}
}

// init fade icons
function initFadeIcons(){
	var win = jQuery(window);
	var animSpeed = 250;
	var oldIe = jQuery.support.opacity === false;
	if(oldIe){
		return;
	}
	jQuery('.icons-list').each(function(){
		var list = jQuery(this);
		var items = list.find('li').css({opacity:0});
		var listOffset = list.offset().top;
		var listHeight = list.outerHeight();
		var windowHeight = window.innerHeight;
		var index = 0;
		function animateIcons(){
			items.eq(index).fadeTo(animSpeed, 1, function(){
				index++;
				if(items.eq(index).length){
					animateIcons();
				}
			});
		}
		function refreshOffset(){
			listOffset = list.offset().top;
			listHeight = list.outerHeight();
			windowHeight = window.innerHeight;
		}
		function checkScroll(){
			var startAnimate = win.scrollTop() + windowHeight >= listOffset && win.scrollTop() + windowHeight > listOffset + listHeight;
			if(startAnimate){
				animateIcons();
				win.off('scroll', checkScroll);
				win.off('resize orientationchange load', refreshOffset);
			}
		}
		win.on('scroll', checkScroll);
		win.on('resize orientationchange load', refreshOffset);
	});
}

// init fixed header on scroll
function initFixedHeader(){
	var win = jQuery(window);
	var page = jQuery('body');
	var wrapper = jQuery('#wrapper');
	var fixedClass = 'fixed-header';
	jQuery('.header-fixed-container').each(function(){
		var header = jQuery(this);
		var fixedPosition = header.offset().top;
		var headerHeight = header.outerHeight(true);
		function refresh(){
			wrapper.css({paddingTop:''});
			page.removeClass(fixedClass);
			fixedPosition = header.offset().top;
			headerHeight = header.outerHeight(true);
			checkScroll();
		}
		function checkScroll(){
			if(win.scrollTop() >= fixedPosition){
				page.addClass(fixedClass);
				wrapper.css({paddingTop:headerHeight});
			} else {
				page.removeClass(fixedClass);
				wrapper.css({paddingTop:''});
			}
		}
		win.on('resize orientationchange load', refresh);
		win.on('scroll', checkScroll);
		refresh();
	});
}

// initialize custom form elements
function initCustomForms() {
	if (safari) {
		return false;
	}
	jcf.setOptions('Select', {
		wrapNative: false,
		wrapNativeOnMobile: false
	});
	jcf.replaceAll();
	return true;
}

// scroll gallery init
function initCarousel() {
	jQuery('.price-carousel').scrollGallery({
		mask: 'div.mask',
		slider: 'div.slideset',
		slides: 'div.slide',
		btnPrev: 'a.btn-prev',
		btnNext: 'a.btn-next',
		pagerLinks: '.pagination li',
		stretchSlideToMask: true,
		maskAutoSize: true,
		autoRotation: false,
		switchTime: 3000,
		animSpeed: 500,
		step: 1
	});
}

// cycle scroll gallery init
function initCycleCarousel() {
	jQuery('div.cycle-gallery').scrollAbsoluteGallery({
		mask: 'div.mask',
		slider: 'div.slideset',
		slides: 'div.slide',
		btnPrev: 'a.btn-prev',
		btnNext: 'a.btn-next',
		pagerLinks: '.switcher li',
		stretchSlideToMask: true,
		pauseOnHover: true,
		maskAutoSize: true,
		autoRotation: false,
		switchTime: 3000,
		animSpeed: 500
	});
}

// content tabs init
function initTabs() {
	var win = jQuery(window);
	jQuery('.price-tabset').contentTabs({
		tabLinks: 'a'
	});
	jQuery('.orders-tabset').contentTabs({
		autoHeight: true,
		tabLinks: 'a.tab',
		onChange: function(oldTab, newTab){
			jQuery('.add-price-section').trigger('close');
		}
	});
	jQuery('.orders-results-table').contentTabs({
		autoHeight: true,
		addToParent: true,
		tabLinks: 'a.tab-link',
		onChange: function(oldTab, newTab){
			win.trigger('customresize');
		}
	});
}

// open-close init
function initOpenClose() {
	jQuery('.mobile-text-openbox').each(function(){
		var holder = jQuery(this);
		var slide = holder.closest('.slide');
		var mask = holder.closest('.mask');
		ResponsiveHelper.addRange({
			'..767': {
				on: function() {
					holder.openClose({
						activeClass: 'active',
						opener: '.opener-text',
						slider: '.slide-text',
						animSpeed: 100,
						effect: 'none',
						hideOnClickOutside: true,
						animEnd: function(){
							mask.stop().animate({height:slide.outerHeight()},400);

						}
					});
				},
				off: function() {
					holder.data('OpenClose').destroy();
				}
			}
		});
	});
	jQuery('.mobile-navigation').openClose({
		hideOnClickOutside: true,
		activeClass: 'active',
		opener: '.opener',
		slider: '.drop',
		animSpeed: 400,
		effect: 'slide'
	});
	jQuery('.registration-form').openClose({
		hideOnClickOutside: true,
		activeClass: 'active',
		opener: '.opener',
		slider: '.slide-box',
		animSpeed: 400,
		effect: 'slide'
	});
}

// accordion menu init
function initAccordion() {
	jQuery('.accordion-account').slideAccordion({
		opener: 'a.opener',
		slider: 'div.slide',
		animSpeed: 300
	});
	jQuery('.orders-accordion').slideAccordion({
		opener: 'a.opener',
		slider: 'div.slide',
		animSpeed: 300
	});
}

// align blocks height
function initSameHeight() {
	jQuery('.price-carousel').sameHeight({
		elements: '.info-box>.frame',
		flexible: true,
		multiLine: true
	});

	jQuery('.twocolumns').sameHeight({
		elements: '.content, .aside',
		flexible: true,
		multiLine: true,
		biggestHeight: true
	});

	jQuery('.price-box-holder').sameHeight({
		elements: '.info-box>.frame',
		flexible: true,
		multiLine: true
	});
}

// handle dropdowns on mobile devices
function initTouchNav() {
	jQuery('.account-menu ul').each(function(){
		new TouchNav({
			navBlock: this,
			menuDrop: '.drop'
		});
	});
}

// add classes on hover/touch
function initCustomHover() {
	jQuery('#nav ul li a').touchHover();
	jQuery('.navigation ul a').touchHover();
	jQuery('.orders-results-table .row-holder').touchHover();
}

function initActionByHash(){
	var hash = window.location.hash;
	switch(hash){
		case '#invite':
			var $modal = $('#myModal');
			var $title = $modal.find('.modal-content .heading h2');
			var $p = ('<p>' + TRANS['inviteRegisterComment'] + '</p>');
			$title.parent().append($p);
			$modal.data('show')();
			break;
		case '#order':
			var $btn = $($('button.leave-a-request')[0]);
			$btn.trigger('click');
			break;
	}
}