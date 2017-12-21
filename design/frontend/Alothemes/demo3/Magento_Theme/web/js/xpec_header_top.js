require([
    "jquery",'domReady!'
], function($){
    'use strict';
    $(document).ready(function($,ko) {
        window.supper=true;
        window.accon=true;
        $('.user-account').click(function(){
            if(window.accon){
                if($('.header-usp').css('display')!='none' && $('.header-usp').css('display')=='block'){
                    window.supper=false;
                    $('.customer-support a').trigger('click');
                }
            }
            window.supper=true;
        });
        $('.customer-support a').click(function(){
            if(window.supper){
                if($('.header-top').css('display')!='none' && $('.header-top').css('display')=='block'){
                    window.accon=false;
                    $('.user-account').trigger('click');
                }
            }
            window.accon=true;
        });
        $('.magicmenu .level0').hover(function(){
            if(!$(this).find('.xpec_menu_level1').length){
                $(this).find('.level-top-mega').remove();
                $(this).removeClass('hasChild');
                $(this).removeClass('parent');
            }
        });
    });
    
});