require(
    ['jquery', "fancybox", 'jquery/jquery.cookie', 'domReady!'],
    function($, fancybox) {
        $(document).ready(function () {
            // $('body').append('<a href="#xpec-popup-content" style="display:none;" class="fancybox xpec-newsletter">newletter</a>');
            // $('.xpec-newsletter').fancybox({
            //     minWidth	: 400,
            //     minHeight	: 600,
            //     width		: '50%',
            //     height		: '40%',
            //     scrolling       : 'no',
            //     autoSize	: true,
            //     closeClick	: false,
            //     fitToView	: false,
            //     openEffect      : 'elastic',
            //     helpers : {
            //         overlay : {closeClick: false}
            //     }
            // });
            // setTimeout(function() {
            //     if ($.cookie('xpec-nomostrar') != null) {
            //         if ($.cookie('xpec-nomostrar') != null && $.cookie('xpec-nomostrar') != '') {
            //             var nomo = new Date($.cookie('xpec-nomostrar')).getTime();
            //             var date = new Date().getTime();;
            //             var diff = nomo - date;
            //             if (diff<= 0) {
            //                 removeCookie('xpec-nomostrar');
            //             }
            //         } else {
            //         }
            //     } else {
            //     }
            //     removeCookie('xpec-newsletter');
            // }, 100);
            // $('.action.subscribe').click(function() {
            //     cookieNomostrar();
            // });
            // $('input[name="nomostrar"]').click(function() {
            //     if ($(this).prop('checked')) {
            //         cookieNomostrar();
            //     } else {
            //         removeCookie('xpec-nomostrar');
            //     }
            // });
        });
        
        function cookie() {
            var $nombre = 'xpec-newsletter';
            var check_cookie = $.cookie($nombre); // Get Cookie Value
            var date = new Date();
            var minutes = 2;
            date.setTime(date.getTime() + (minutes * 60 * 1000));
            $.cookie($nombre, 'flag', {path: '/', expires: date}); // Expire Cookie
        }
        
        function cookieNomostrar() {
            var $nombre = 'xpec-nomostrar';
            var check_cookie = $.cookie($nombre); // Get Cookie Value
            var date = new Date();
            var minutes = 2;            
            date.setTime(date.getTime() + (15 * 24 * 60 * 60 * 1000));
            $.cookie($nombre, date, {path: '/', expires: date}); // Expire Cookie
        }
        
        function removeCookie($nombre) {
            var check_cookie = $.cookie($nombre); // Get Cookie Value
            var date = new Date();
            var minutes = 1;
            date.setTime(date.getTime() + (minutes * 60 * 1000));
            $.cookie($nombre, '', {path: '/', expires: date}); // Expire Cookie
        }        
    }
);