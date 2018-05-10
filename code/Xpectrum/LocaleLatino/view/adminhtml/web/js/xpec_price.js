require([
    'jquery', 'jquery/ui', 'domReady!'
], function($) {
    $(document).ready(function() {
        $("body").mouseover(function() {
            if ($("input[name='product[price]']").length) {
                var valor = $("input[name='product[price]']").val().replace(/(.+?\,).*/, "$1").replace(/\D/g, "");
                if (!isNaN(valor)) {
                    $("input[name='product[price]']").val(valor);
                    $("input[name='product[price]']").trigger('change');
                }
            }
            if ($("input[name='product[special_price]']").length) {
                var valor = $("input[name='product[special_price]']").val().replace(/(.+?\,).*/, "$1").replace(/\D/g, "");
                if (!isNaN(valor)) {
                    $("input[name='product[special_price]']").val(valor);
                    $("input[name='product[special_price]']").trigger('change');
                }
            }
            if ($("input[name='product[cost]']").length) {
                var valor = $("input[name='product[cost]']").val().replace(/(.+?\,).*/, "$1").replace(/\D/g, "");
                if (!isNaN(valor)) {
                    $("input[name='product[cost]']").val(valor);
                    $("input[name='product[cost]']").trigger('change');
                }
            }
        });
        $(document).scroll(function() {
            if ($("input[name='product[price]']").length) {
                var valor = $("input[name='product[price]']").val().replace(/(.+?\,).*/, "$1").replace(/\D/g, "");
                if (!isNaN(valor)) {
                    $("input[name='product[price]']").val(valor);
                    $("input[name='product[price]']").trigger('change');
                }
            }
            if ($("input[name='product[special_price]']").length) {
                var valor = $("input[name='product[special_price]']").val().replace(/(.+?\,).*/, "$1").replace(/\D/g, "");
                if (!isNaN(valor)) {
                    $("input[name='product[special_price]']").val(valor);
                    $("input[name='product[special_price]']").trigger('change');
                }
            }
            if ($("input[name='product[cost]']").length) {
                var valor = $("input[name='product[cost]']").val().replace(/(.+?\,).*/, "$1").replace(/\D/g, "");
                if (!isNaN(valor)) {
                    $("input[name='product[cost]']").val(valor);
                    $("input[name='product[cost]']").trigger('change');
                }
            }
        });
    });
});