require([
    "jquery","Validador",'domReady!'
], function($){
    $(document).ready(function () {
        $("input[name='customer[rut]']").blur(function(){
            var rut=$(this).val();
            var objrut = $(this);
            if(rut.length){
                if( window.Fn.validaRut(rut) ){
                    $(this).parent('.control').find("#rut-error").remove();
                    $(this).removeClass('mage-error');
                }else{
                    $(this).parent('.control').find("#rut-error").remove();
                    $(this).addClass('mage-error');
                    $(this).parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                }
            }
        });
        $("form").submit(function( event ) {
            if( $(this).find("input[name='customer[rut]']").length )  {
                var objrut = $(this).find("#rut");
                var rut    = $(this).find("#rut").val();
                if(!window.Fn.validaRut(rut)){
                    event.preventDefault();
                    $(objrut).parent('.control').find("#rut-error").remove();
                    $(objrut).addClass('mage-error');
                    $(objrut).parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                    $(objrut).focus();
                    return false;
                }
            }
        });
    });
    
});

