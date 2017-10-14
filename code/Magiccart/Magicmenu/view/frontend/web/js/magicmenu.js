require(["jquery"], function($){
    (function($) {
        "use strict";
        $.fn.meanmenu = function(options) {
            var defaults = {
                meanMenuTarget: jQuery(this),
                meanMenuContainer: 'body',
                meanMenuClose: "X",
                meanMenuCloseSize: "18px",
                meanMenuOpen: "<span /><span /><span />",
                meanRevealPosition: "right",
                meanRevealPositionDistance: "0",
                meanRevealColour: "",
                meanRevealHoverColour: "",
                meanScreenWidth: "480",
                meanNavPush: "",
                meanShowChildren: true,
                meanExpandableChildren: true,
                meanExpand: "+",
                meanContract: "-",
                meanRemoveAttrs: false,
                onePage: false,
                removeElements: "",
                meanMenuExpandTop: true,
                expandActive: true,
                meanMenuResponsive: true,
            };
            var options = $.extend(defaults, options);
            var currentWidth = window.innerWidth || document.documentElement.clientWidth;
            return this.each(function() {
                var meanMenu = options.meanMenuTarget;
                var meanContainer = options.meanMenuContainer;
                var meanReveal = options.meanReveal;
                var meanMenuClose = options.meanMenuClose;
                var meanMenuCloseSize = options.meanMenuCloseSize;
                var meanMenuOpen = options.meanMenuOpen;
                var meanRevealPosition = options.meanRevealPosition;
                var meanRevealPositionDistance = options.meanRevealPositionDistance;
                var meanRevealColour = options.meanRevealColour;
                var meanRevealHoverColour = options.meanRevealHoverColour;
                var meanScreenWidth = options.meanScreenWidth;
                var meanNavPush = options.meanNavPush;
                var meanRevealClass = ".meanmenu-reveal";
                var meanShowChildren = options.meanShowChildren;
                var meanExpandableChildren = options.meanExpandableChildren;
                var meanExpand = options.meanExpand;
                var meanContract = options.meanContract;
                var meanRemoveAttrs = options.meanRemoveAttrs;
                var onePage = options.onePage;
                var removeElements = options.removeElements;
                var meanMenuExpandTop = options.meanMenuExpandTop;
                var meanMenuResponsive = options.meanMenuResponsive;
                var expandActive = options.expandActive;
                if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i)) || (navigator.userAgent.match(/Android/i)) || (navigator.userAgent.match(/Blackberry/i)) || (navigator.userAgent.match(/Windows Phone/i))) {
                    var isMobile = true;
                }
                if ((navigator.userAgent.match(/MSIE 8/i)) || (navigator.userAgent.match(/MSIE 7/i))) {
                    jQuery('html').css("overflow-y", "scroll");
                }

                function meanCentered() {
                    if (meanRevealPosition == "center") {
                        var newWidth = window.innerWidth || document.documentElement.clientWidth;
                        var meanCenter = ((newWidth / 2) - 22) + "px";
                        meanRevealPos = "left:" + meanCenter + ";right:auto;";
                        if (!isMobile) {
                            jQuery('.meanmenu-reveal').css("left", meanCenter);
                        } else {
                            jQuery('.meanmenu-reveal').animate({
                                left: meanCenter
                            });
                        }
                    }
                }
                var menuOn = false;
                var meanMenuExist = false;
                if (meanRevealPosition == "right") {
                    meanRevealPos = "right:" + meanRevealPositionDistance + ";left:auto;";
                }
                if (meanRevealPosition == "left") {
                    var meanRevealPos = "left:" + meanRevealPositionDistance + ";right:auto;";
                }
                meanCentered();
                var meanStyles = "background:" + meanRevealColour + ";color:" + meanRevealColour + ";" + meanRevealPos;
                var $navreveal = "";

                function meanInner() {
                    if(meanMenuExpandTop){
                        if (jQuery($navreveal).is(".meanmenu-reveal.meanclose")) {
                            $navreveal.html(meanMenuClose);
                        } else {
                            $navreveal.html(meanMenuOpen);
                        }
                    }
                }

                function meanOriginal() {
                    jQuery(meanContainer).removeClass("mean-container").find('.mean-bar,.mean-push').hide();
                    jQuery(meanMenu).show();
                    menuOn = false;
                    jQuery(removeElements).removeClass('mean-remove');
                }

                function showMeanMenu() {
                    jQuery(removeElements).addClass('mean-remove');
                    jQuery(meanContainer).addClass("mean-container");
                    if(meanMenuExist){
                        jQuery(meanContainer).find('.mean-bar,.mean-push').show().find('.mean-nav ul:first').show();
                        return;
                    }
                    if(meanMenuExpandTop) jQuery(meanContainer).append('<div class="mean-bar"><nav class="mean-nav"></nav></div>');
                    else jQuery(meanContainer).append('<div class="mean-bar"><a href="#nav" class="meanmenu-reveal" style="' + meanStyles + '">Show Navigation</a><nav class="mean-nav"></nav></div>');
                    var meanMenuContents = jQuery(meanMenu).html();
                    jQuery(meanContainer).find('.mean-nav').html(meanMenuContents);
                    if (meanRemoveAttrs) {
                        jQuery(meanContainer).find('nav.mean-nav ul, nav.mean-nav ul *').each(function() {
                            jQuery(this).removeAttr("class");
                            jQuery(this).removeAttr("id");
                        });
                    }
                    jQuery(meanMenu).before('<div class="mean-push" />');
                    jQuery(meanContainer).find('.mean-push').css("margin-top", meanNavPush);
                    jQuery(meanMenu).hide();
                    jQuery(meanContainer).find(".meanmenu-reveal").show();
                    jQuery(meanRevealClass).html(meanMenuOpen);
                    $navreveal = jQuery(meanRevealClass);
                    if(meanMenuExpandTop){
                        jQuery(meanContainer).find('.mean-nav ul ul').hide();
                    }else {
                        jQuery(meanContainer).find('.mean-nav ul').hide();
                    }
                    
                    if (meanShowChildren) {
                        if (meanExpandableChildren) {
                            jQuery(meanContainer).find('.mean-nav ul ul').each(function() {
                                if (jQuery(this).children().length) {
                                    jQuery(this, 'li:first').parent().append('<a class="mean-expand" href="#" style="font-size: ' + meanMenuCloseSize + '">' + meanExpand + '</a>');
                                }
                            });
                            if(expandActive){
                                var listActive = jQuery(meanContainer).find('.mean-nav li.active');
                                listActive.find('>ul').show();
                                listActive.find('> .mean-expand').addClass('mean-clicked').html(meanContract);
                            }
                            jQuery(meanContainer).find('.mean-expand').on("click", function(e) {
                                e.preventDefault();
                                jQuery(this).parent().siblings().children('a.mean-expand').text(meanExpand);
                                jQuery(this).parent().siblings().children('a.mean-expand').removeClass('mean-clicked');
                                jQuery(this).parent().siblings().children('ul').slideUp(300, function() {});
                                if (jQuery(this).hasClass("mean-clicked")) {
                                    jQuery(this).text(meanExpand);
                                    jQuery(this).prev('ul').slideUp(300, function() {});
                                } else {
                                    jQuery(this).text(meanContract);
                                    jQuery(this).prev('ul').slideDown(300, function() {});
                                }
                                jQuery(this).toggleClass("mean-clicked");
                            });
                        } else {
                            jQuery(meanContainer).find('.mean-nav ul ul').show();
                        }
                    } else {
                        jQuery(meanContainer).find('.mean-nav ul ul').hide();
                    }
                    jQuery(meanContainer).find('.mean-nav ul li').last().addClass('mean-last');
                    $navreveal.removeClass("meanclose");
                    jQuery($navreveal).click(function(e) {
                        e.preventDefault();
                        if (menuOn == false) {
                            $navreveal.css("text-align", "center");
                            $navreveal.css("text-indent", "0");
                            $navreveal.css("font-size", meanMenuCloseSize);
                            jQuery(meanContainer).find('.mean-nav ul:first').slideDown();
                            menuOn = true;
                        } else {
                            jQuery(meanContainer).find('.mean-nav ul:first').slideUp();
                            menuOn = false;
                        }
                        $navreveal.toggleClass("meanclose");
                        meanInner();
                        jQuery(removeElements).addClass('mean-remove');
                    });
                    if (onePage) {
                        jQuery(meanContainer).find('.mean-nav ul > li > a:first-child').on("click", function() {
                            jQuery(meanContainer).find('.mean-nav ul:first').slideUp();
                            menuOn = false;
                            jQuery($navreveal).toggleClass("meanclose").html(meanMenuOpen);
                        });
                    }
                    meanMenuExist = true;
                }
                if(meanMenuResponsive){
                    if (!isMobile) {
                        jQuery(window).resize(function() {
                            currentWidth = window.innerWidth || document.documentElement.clientWidth;
                            if (currentWidth > meanScreenWidth) {
                                meanOriginal();
                            }else {
                                showMeanMenu();
                                meanCentered();                 
                            }
                        });
                    }
                    window.onorientationchange = function() {
                        meanCentered();
                        currentWidth = window.innerWidth || document.documentElement.clientWidth;
                        if (currentWidth >= meanScreenWidth) {
                            meanOriginal();
                        }
                        if (currentWidth <= meanScreenWidth) {
                            showMeanMenu();
                        }
                    }
                }
                if ( currentWidth <= meanScreenWidth || !meanMenuResponsive ) { 
                    if (meanMenuExist == false){
                        showMeanMenu();
                    } else { 
                        meanOriginal();
                    }
                }
            });
        };
    })(jQuery);

    /**
     * Magiccart 
     * @category    Magiccart 
     * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
     * @license     http://www.magiccart.net/license-agreement.html
     * @Author: DOng NGuyen<nguyen@dvn.com>
     * @@Create Date: 2014-04-25 13:16:48
     * @@Modify Date: 2016-05-31 15:19:52
     * @@Function:
     */
     
    jQuery(document).ready(function($) {

        // For accordion
        (function($) {
            var $content = $('.accordion-container');
            $('.meanmenu-accordion').meanmenu({
                meanMenuContainer: ".accordion-container",
                // meanScreenWidth: "2000",
                removeElements:true,
                // meanMenuExpandTop: false,
                meanMenuResponsive: false,
            });
            var $accordion = $content.find('.nav-accordion');
            // // $accordion.magicaccordion({
            // //             accordion:true,
            // //             speed: 400,
            // //             closedSign: 'collapse',
            // //             openedSign: 'expand',
            // //             easing: 'easeInOutQuad'
            // //         });
            var catplus = $accordion.find('>.level0:hidden');
            if(!catplus.length) $content.find('.all-cat').hide();
            else $content.find('.all-cat').click(function(event) {$(this).children().toggle(); catplus.slideToggle('slow');});
        })(jQuery); 
        // End For accordion

        (function(selector){
            var $content = $(selector);
            // for Mobile
            $('.navigation-mobile').meanmenu({
                meanMenuContainer: ".magicmenu",
                meanScreenWidth: "768",
                meanMenuResponsive: true,
                expandActive: false,
                removeElements:true,
            });
            var $accordion = $content.find('.nav-mobile');
            // $accordion.magicaccordion({
            //             accordion:true,
            //             speed: 400,
            //             closedSign: 'collapse',
            //             openedSign: 'expand',
            //             easing: 'easeInOutQuad'
            //         });
            var catplus = $accordion.find('>.level0:hidden');
            if(!catplus.length) $content.find('.all-cat').hide();
            else $content.find('.all-cat').click(function(event) {$(this).children().toggle(); catplus.slideToggle('slow');});
            // End for Mobile
            var $navDesktop = $('.nav-desktop', $content);
            /* Fix active Cache */
            // var body = $('body');
            // if(!body.hasClass('catalog-category-view')){
            //  if(body.hasClass('catalog-product-view')){
            //      var urlTop = body.find('.breadcrumbs ul').children().eq(1).find('a');
            //      if(urlTop.length){
            //          link = urlTop.attr('href');
            //          var topUrl = $('li.level0 a.level-top', $content);
            //          var catUrl = $('li.level0.cat a.level-top', $content);
            //          var activeUrl = $('li.level0.active a.level-top', $content); // default active
            //          catUrl.each(function() {
            //              var $this = $(this);
            //              if($this.attr('href').indexOf(link) > -1){
            //                  activeUrl = $this;                      
            //                  var activeObj = activeUrl.parent();
            //                  activeObj.addClass('active');   
            //                  $('li.level0.home', $content).removeClass('active');                            
            //              }
            //          });
            //      }
            //  } else {
            //      var currentUrl = document.URL;
            //      var extUrl = $('li.level0.ext a.level-top', $content);
            //      var activeUrl = $('li.level0.home a.level-top', $content); // default active
            //      if(activeUrl.length){
            //          extUrl.each(function() {
            //              var $this = $(this);
            //              if(currentUrl.indexOf($this.attr('href')) > -1 && $this.attr('href').length > activeUrl.attr('href').length) activeUrl = $this;
            //          });
            //      }
            //      var activeObj = activeUrl.parent();
            //      if(activeObj.length) $('li.level0.home', $content).removeClass('active');
            //      activeObj.addClass('active');           
            //  }       
            // } else {
            //  $('li.level0.home', $content).removeClass('active');
            // }
            /* End fix active Cache */

            // Sticker Menu
            if($navDesktop.hasClass('sticker')){            
                $(window).scroll(function () {
                 if ($(this).scrollTop() > 500) {
                  $('.header-sticker').addClass('header-container-fixed');
                 } 
                 else{
                  $('.header-sticker').removeClass('header-container-fixed');
                 }
                 return false;
                });
            }
            // End Sticker Menu

            var $window  = $(window).width();
            setReponsive($window);
            $(window).resize(function(){
                var $window = $(window).width();
                setReponsive($window);
            })
            var $navtop = $content.find('li.level0.hasChild, li.level0.home').not('.dropdown');
            var fullWidth = $navDesktop.data('fullwidth');
            var maxW    = fullWidth ? $('body').outerWidth() : $('.container').outerWidth();
            $navtop.each(function(index, val) {
                var $item     = $(this);
                if(fullWidth) $item.find('.level-top-mega').addClass('parent-full-width').wrap( '<div class="full-width"></div>' );
                var options  = $item.data('options');
                var $cat_mega = $('.cat-mega', $item);
                var $children = $('.children', $cat_mega);
                var columns   = $children.length;
                var wchil     = $children.outerWidth();
                if(options){
                    var col     = parseInt(options.cat_col);
                    if(!isNaN(col)) columns = col;
                    var cat         = parseFloat(options.cat_proportion);
                    var left        = parseFloat(options.left_proportion);
                    var right       = parseFloat(options.right_proportion);
                    if(isNaN(left)) left = 0; if(isNaN(right)) right = 0;
                    var custom      = left + right;
                    var proportion = cat + left + right;
                    var cat_width   = Math.floor(100*cat/proportion);
                    var temp        = 100/columns;
                    var col_width   = (temp+Math.floor(temp))/2; // approximately down
                    var left_width  = 100*left/proportion;
                    var right_width = 100*right/proportion;
                    var $block_left = $('.mega-block-left', $item);
                        $block_left.width(left_width + '%');
                    var $block_right = $('.mega-block-right', $item);
                        $block_right.width(right_width + '%');
                        $cat_mega.width(cat_width + '%');
                    var wcolumns  = wchil*columns;
                        if(custom){
                            var wTopMega = wcolumns + (left_width*wcolumns)/cat_width + (right_width*wcolumns)/cat_width
                            if(wTopMega > maxW) wTopMega = maxW;
                            $('.content-mega-horizontal',$item).width(wTopMega);
                        } else {
                            if(wcolumns > maxW) wcolumns = Math.floor(maxW / wchil)*wchil;
                            $('.content-mega-horizontal',$item).width(wcolumns);    
                        } 
                        $children.each(function(idx) {
                            if(idx % columns ==0 && idx != 0) $(this).css("clear", "both"); 
                        });
                } else {
                    var wcolumns    = wchil*columns;
                    if(wcolumns > maxW) wcolumns = Math.floor(maxW / wchil)*wchil;
                    $('.content-mega-horizontal', $item).width(wcolumns);   
                }

            });

            function setReponsive($window){
                if (767 <= $window){
                    jQuery('.nav-mobile').hide();
                    var $navtop = $content.find('li.level0.hasChild, li.level0.home').not('.dropdown');
                    $navtop.hover(function(){
                        var $item           = $(this);
                        var wrapper         = $('.container');
                        var postionWrapper  = wrapper.offset();
                        var wWrapper        = wrapper.width();      /*include padding border*/
                        var wMega           = $('.level-top-mega', $item).outerWidth(); /*include padding + margin + border*/
                        var postionMega     = $item.offset();
                        var xLeft           = wWrapper - wMega - (wWrapper - wrapper.width())/2;
                        var xLeft2          = postionMega.left - postionWrapper.left;
                        if(xLeft > xLeft2) xLeft = xLeft2;
                        if(xLeft < 0) xLeft = xLeft/2;
                        var topMega = $item.find('.level-top-mega');
                        if(topMega.length){
                            topMega.css('left',xLeft);
                            $item.addClass('over');
                        }
                    },function(){
                       $(this).removeClass('over');
                    })
                }
            }

        })('.magicmenu');
        
        // Vertical Menu
        (function(selector){
            var $content = $(selector);
            var $window  = $(window).width();
            setReponsive($window);
            $(window).resize(function(){
                var $window = $(window).width();
                setReponsive($window);
            })
            $content.find('.v-title').click(function() {$(this).parent().find('.nav-desktop').slideToggle(400);});
            var catplus = $content.find('.level0:hidden');
            if(!catplus.length) $content.find('.all-cat').hide();
            else $content.find('.all-cat').click(function(event) {$(this).children().toggle(); catplus.slideToggle('slow');});
            var $navtop = $content.find('li.level0').not('.dropdown');
            var maxW    = $('.container').outerWidth();
            $navtop.each(function(index, val) {
                var options  = $(this).data('options');
                var $cat_mega = $('.cat-mega', $(this));
                var $children = $('.children', $cat_mega);
                var columns   = $children.length;
                var wchil     = $children.outerWidth();
                if(options){
                    var columns     = parseInt(options.cat_col);
                    var cat         = parseFloat(options.cat_proportion);
                    var left        = parseFloat(options.left_proportion);
                    var right       = parseFloat(options.right_proportion);
                    if(isNaN(left)) left = 0; if(isNaN(right)) right = 0;
                    var custom      = left + right;
                    var proportion = cat + left + right;
                    var cat_width   = Math.floor(100*cat/proportion);
                    var temp        = 100/columns;
                    var col_width   = (temp+Math.floor(temp))/2; // approximately down
                    var left_width  = 100*left/proportion;
                    var right_width = 100*right/proportion;
                    var $block_left = $('.mega-block-left', $(this));
                        $block_left.width(left_width + '%');
                    var $block_right = $('.mega-block-right', $(this));
                        $block_right.width(right_width + '%');
                        $cat_mega.width(cat_width + '%');
                    var wcolumns  = wchil*columns;
                        if(custom){
                            var wTopMega = wcolumns + (left_width*wcolumns)/cat_width + (right_width*wcolumns)/cat_width
                            if(wTopMega > maxW) wTopMega = maxW;
                            $('.content-mega-horizontal',$(this)).width(wTopMega);
                        } else {
                            if(wcolumns > maxW) wcolumns = Math.floor(maxW / wchil)*wchil;
                            $('.content-mega-horizontal',$(this)).width(wcolumns);  
                        } 
                        $children.each(function(idx) {
                            if(idx % columns ==0 && idx != 0)   $(this).css("clear", "both");
                        });
                } else {
                    var wcolumns    = wchil*columns;
                    if(wcolumns > maxW) wcolumns = Math.floor(maxW / wchil)*wchil;
                    $('.content-mega-horizontal',$(this)).width(wcolumns);  
                }

            });

            function setReponsive($window){
                if (767 <= $window){
                    var $navtop = $content.find('li.level0.hasChild, li.level0.home').not('.dropdown');
                    var $container = $('.container');
                    var wContainer = $container.outerWidth();
                    $navtop.hover(function(){
                        var options = $(this).data('options');
                        var children        = $('.children', this);
                        var colSet          = children.length;
                        if(options){
                            var columns     = parseInt(options.cat_col);
                            if(columns) colSet = columns;
                        }
                        var postionWrapper  = $container.offset();
                        var wWrapper        = $container.outerWidth();      /*include padding border*/
                        var wVmenu          = $content.outerWidth(true);
                        var postionMega     = $(this).position();
                        var margin_top      = 0; // set in config
                        var wChild          = children.outerWidth();
                        var outerChildren   = wChild - children.width();
                        var wMageMax        = $container.width()- wVmenu;
                        var wCatMega        = colSet*wChild;
                        if(wCatMega > wMageMax) wCatMega = Math.floor(wMageMax / wChild)*wChild;
                        var rBlock          = $('.mega-block-right', this);
                        var wRblock         = rBlock.width();
                        var megaHorizontal  = wCatMega + wRblock;
                        if(megaHorizontal < wMageMax) $('.content-mega-horizontal', this).width(megaHorizontal);
                        $('.cat-mega', this).width(wCatMega);
                        $('.level-top-mega', this).css('top',postionMega.top);
                        // $('.level-top-mega', this).css('margin-left',wVmenu-2); // - margin

                    },function(){

                    })
                }
            }

        })('.vmagicmenu');

    });

});

