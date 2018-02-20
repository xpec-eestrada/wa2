require([
    "jquery",'jquery/ui','domReady!'
], function($){
    $( document ).ready(function() {
        $("body").mouseover(function(){
            if( $("input[name='product[price]']").length ){
                var valor=parseInt($("input[name='product[price]']").val().replace(".",""));
                if(!isNaN(valor)){
                    $("input[name='product[price]']").val(valor);
                    $("input[name='product[price]']").trigger('change');
                }
            }
        });
        $(document).scroll(function(){
            if( $("input[name='product[price]']").length ){
                var valor=parseInt($("input[name='product[price]']").val().replace(".",""));
                if(!isNaN(valor)){
                    $("input[name='product[price]']").val(valor);
                    $("input[name='product[price]']").trigger('change');
                }
            }
        });
    });
});