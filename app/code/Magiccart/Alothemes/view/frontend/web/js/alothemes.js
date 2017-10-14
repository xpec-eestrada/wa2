/**
 * Magiccart 
 * @category 	Magiccart 
 * @copyright 	Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license 	http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2014-06-30 14:27:05
 * @@Modify Date: 2016-06-02 11:05:07
 * @@Function:
 */

function magicproduct(el, iClass) {
	require([
        'jquery',
        'slick',
        ], function($){
            (function($) {
				var options = el.data();
				if(iClass === undefined){
					el.children().addClass('alo-item');
					iClass = '.alo-item';
				}
				var selector = el.selector;
				var classes = selector + ' '+ iClass;
				var padding = options.padding;
				var style = padding ? classes + '{float: left; padding-left: '+padding+'px; padding-right:'+padding+'px} ' + selector + '{margin-left: -'+padding+'px; margin-right: -'+padding+'px}' : '';
				if(options.slidesToShow){
					if(el.hasClass('slick-initialized')) el.slick("refresh");
					else el.slick(options);
				} else {
					var responsive 	= options.responsive;
					if(!responsive) return;
					var length = Object.keys(responsive).length;
					/*
					jQuery.each( responsive, function( key, value ) { // data-responsive="[{"1":"1"},{"361":"1"},{"480":"2"},{"640":"3"},{"768":"3"},{"992":"4"},{"1200":"4"}]"
						var col = 0;
						var maxWith = 3600;
						var minWith = 0;
						jQuery.each( value , function(size, num) { minWith = size; col = num; });
						if(key+1<length){
							jQuery.each( responsive[key+1], function( size, num) { maxWith = size-1; });
							// padding = options.padding*(maxWith/1200); // padding responsive
						}
						style += ' @media (min-width: '+minWith+'px) and (max-width: '+maxWith+'px) {'+classes+'{padding-left: '+padding+'px; padding-right:'+padding+'px; width: '+(Math.floor((10/col) * 100000000000) / 10000000000)+'%} '+classes+':nth-child('+col+'n+1){clear: left;}}';
					});
					*/
					$.each( responsive, function( key, value ) { // data-responsive="[{"1":"1"},{"361":"1"},{"480":"2"},{"640":"3"},{"768":"3"},{"992":"4"},{"1200":"4"}]"
						var col = 0;
						var maxWith = 0;
						var minWith = 0;
						$.each( value , function(size, num) { minWith = parseInt(size) + 1; col = num;});
						if(key+2<length){
							$.each( responsive[key+1], function( size, num) { maxWith = size; col = num;});
							// padding = options.padding*(maxWith/1200); // padding responsive
							style += ' @media (min-width: '+minWith+'px) and (max-width: '+maxWith+'px)';
						} else { 
							if(key+2 == length) return; // don't use key = length - 1;
							$.each( responsive[key], function( size, num) { maxWith = size; col = num;});
							style += ' @media (min-width: '+maxWith+'px)';
						}
						style += ' {'+selector + '{margin-left: -'+padding+'px; margin-right: -'+padding+'px}'+classes+'{padding-left: '+padding+'px; padding-right:'+padding+'px; width: '+(Math.floor((10/col) * 100000000000) / 10000000000)+'%} '+classes+':nth-child('+col+'n+1){clear: left;}}';
					});

					// $.each( responsive, function( key, value ) { // data-responsive="[{"col":"1","min":1,"max":360},{"col":"2","min":361,"max":479},{"col":"3","min":480,"max":639},{"col":"3","min":640,"max":767},{"col":"4","min":768,"max":991},{"col":"4","min":992,"max":1199},{"col":"4","min":1200,"max":3600}]"
					// 	style += ' @media (min-width: '+value.min+'px) and (max-width: '+value.max+'px) {'+classes+'{padding: 0 '+padding+'px; width: '+(Math.floor((10/value.col) * 100000000000) / 10000000000)+'%} '+classes+':nth-child('+value.col+'n+1){clear: left;}}';
					// });
				}

				$('head').append('<style type="text/css">'+style+'</style>'); // return '<style type="text/css">'+style+'</style>';
            
            })(jQuery); 
    });
}

require(['jquery'],
	function($){
	/* Timer */
	(function ($) {
		"use strict";
		$.fn.timer = function (options) {
			var defaults = {
				classes  	 : '.countdown',
				layout	 	 : '<span class="day">%%D%%</span><span class class="colon">:</span><span class="hour">%%H%%</span><span class="colon">:</span><span class="min">%%M%%</span><span class="colon">:</span><span class="sec">%%S%%</span>',
				//layoutcaption: '<div class="timer-box"><span class="day">%%D%%</span><span class="title">Days</span></div><div class="timer-box"><span class="hour">%%H%%</span><span class="title">Hrs</span></div><div class="timer-box"><span class="min">%%M%%</span><span class="title">Mins</span></div><div class="timer-box"><span class="sec">%%S%%</span><span class="title">Secs</span></div>',
				leadingZero	 : true,
				countStepper : -1, // s: -1 // min: -60 // hour: -3600
				timeout	 	 : '<span class="timeout">Time out!</span>',
			};

			var settings = $.extend(defaults, options);
			var layout			 = settings.layout;
			var leadingZero 	 = settings.leadingZero;
			var countStepper 	 = settings.countStepper;
			var setTimeOutPeriod = (Math.abs(countStepper)-1)*1000 + 990;
			var timeout 		 = settings.timeout;

			var methods = {
				init : function() {
					return this.each(function() {
						var $countdown 	= $(settings.classes, $(this));
						if( $countdown.length )methods.timerLoad($countdown);
					});
				},
				
				timerLoad: function(el){
					var gsecs = el.data('timer');
					if(gsecs > 0 ){
						methods.CountBack(el, gsecs);
					}
				},

				calcage: function (secs, num1, num2) {
					var s = ((Math.floor(secs/num1)%num2)).toString();
					if (leadingZero && s.length < 2) s = "0" + s;
					return "<b>" + s + "</b>";
				},

				CountBack: function (el, secs) {
					if (secs < 0) {
						el.html(timeout);
						return;
					}
					var timerStr = layout.replace(/%%D%%/g, methods.calcage(secs,86400,100000));
					timerStr = timerStr.replace(/%%H%%/g, methods.calcage(secs,3600,24));
					timerStr = timerStr.replace(/%%M%%/g, methods.calcage(secs,60,60));
					timerStr = timerStr.replace(/%%S%%/g, methods.calcage(secs,1,60));
					el.html(timerStr);
					setTimeout(function(){ methods.CountBack(el, (secs+countStepper))}, setTimeOutPeriod);
				},

			};

			if (methods[options]) { // $("#element").pluginName('methodName', 'arg1', 'arg2');
				return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
			} else if (typeof options === 'object' || !options) { // $("#element").pluginName({ option: 1, option:2 });
				return methods.init.apply(this);
			} else {
				$.error('Method "' + method + '" does not exist in timer plugin!');
			}
		}

		if (typeof alo_timer_layout != 'undefined'){
			$('.alo-count-down').not('.exception').timer({
				classes	: '.countdown',
				layout	: alo_timer_layout, 
				timeout : alo_timer_timeout
			});
		}
	})(jQuery);
	/* End Timer */
});

require([
	'jquery',
	'slick',
	], function($){
		(function($) {

			jQuery(document).ready(function($) {

				// var specialOffer = $('#header-offer');
				// specialOffer.find('.header-offer-close').click(function() {
				// 	specialOffer.slideUp('slow');
				// });

				jQuery("*[class^='home-slider']").each(function() { // home-slider
					magicproduct($(this));
				});

				// Realated + Upsell + Crosssell
				/*
				var headCss = '';
				var related = $('body.catalog-product-view .products-related .product-items');
				if(related.length) headCss += magicproduct(related);
				var upsell = $('body.catalog-product-view .products-upsell .product-items');
				if(upsell.length)  headCss += magicproduct(upsell);
				var crosssell = $('body.checkout-cart-index .products-crosssell .product-items');
				if(crosssell.length) headCss += magicproduct(crosssell);
				$('head').append(headCss);
				*/
				var related = $('body.catalog-product-view .products-related .product-items');
				if(related.length) magicproduct(related);
				var upsell = $('body.catalog-product-view .products-upsell .product-items');
				if(upsell.length) magicproduct(upsell);
				var crosssell = $('body.checkout-cart-index .products-crosssell .product-items');
				if(crosssell.length) magicproduct(crosssell);

				// End Realated + Upsell + Crosssell
			/* Back to Top */

			(function(selector){
				var $backtotop = $(selector);
				$backtotop.hide();
				var height =  $(document).height();
				$(window).scroll(function () {
					var ajaxPopup = $('#toPopup');
					if(ajaxPopup.length) {
						var ajaxPosition = ajaxPopup.offset();
						ajaxPopup.css({
							top : ajaxPosition.top,
							position: 'absolute',
						});
					}
					if ($(this).scrollTop() > height/10) {
						$backtotop.fadeIn();
					} else {
						$backtotop.fadeOut();
					}
				});
				$backtotop.click(function () {
					$('body,html').animate({
						scrollTop: 0
					}, 800);
					return false;
				});

			})('#backtotop');

			// add Js
			var $toggleTab  = $('.toggle-tab');
			$toggleTab.click(function(){
				$(this).parent().toggleClass('toggle-visible').find('.toggle-content').slideToggle(300).toggleClass('visible');
			});
			
			var $toggleTabSearch  = $('.toggle-tab-search');
			$toggleTabSearch.click(function(){
				$(this).parent().toggleClass('toggle-visible').find('.toggle-content').toggleClass('visible');
			});
			var $closeSearch = $('.header-search .btn-close');
			$closeSearch.click(function() {
				$(this).closest('.search-switcher').removeClass('toggle-visible').find('.dropdown-switcher').removeClass('visible');
			});

			$('.main').on("click", '.alo_qty_dec', function(){
			    var input = $(this).parent().find('input');
		        var value  = parseInt(input.val());
		        if(value) input.val(value-1);
			});
		    $('.main').on("click", '.alo_qty_inc', function(){
		        var input = $(this).parent().find('input');
		        var value  = parseInt(input.val());
		        input.val(value+1);
		    });

			/* elevator click*/ 
			// (function(selector){
			// 	var $megashop = $(selector);
			// 	var length = $megashop.length;
			// 	$megashop.each(function(index, el) {
			// 		var elevator = $(this).find('.floor-elevator');
			// 		elevator.attr('id', 'elevator-' +index);
			// 		var bntUp 	= elevator.find('.btn-elevator.up');
			// 		var bntDown = elevator.find('.btn-elevator.down');
			// 		bntUp.attr('href', '#elevator-' + (index-1));
			// 		bntDown.attr('href', '#elevator-' +(index+1));
			// 		if(!index) bntUp.addClass('disabled');
			// 		if(index == length-1) bntDown.addClass('disabled');
			// 		elevator.find('.btn-elevator').click(function(e) {
			// 			 e.preventDefault();
			// 		    var target = this.hash;
			// 		    if($(document).find(target).length <=0){
			// 		        return false;
			// 		    }
			// 		    var $target = $(target);
			// 		    $('html, body').stop().animate({
			// 		        'scrollTop': $target.offset().top-50
			// 		    }, 500);
			// 		    return false;
			// 		});
			// 	});

			// })('.megashop');
			
		});

	})(jQuery);	
});

