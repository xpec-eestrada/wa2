require([
    "jquery",'ValidarRut','ValidarNumeroContacto','domReady!'
], function($){
    $(document).ready(function () {
        $("#telephone").blur(function(){
            if( window.numero.validaNumeroContacto($(this).val()) ){
                $(this).parent('.control').find("#telephone-error").remove();
                $(this).removeClass('mage-error');
            }else{
                $(this).parent('.control').find("#telephone-error").remove();
                $(this).addClass('mage-error');
                $(this).parent('.control').append('<div for="telephone" generated="true" class="mage-error" id="telephone-error">Numero de teléfono invalido.</div>');
            }
        });
        $("form").submit(function( event ) {
            if($(this).find("#telephone").length){
                if(window.numero.validaNumeroContacto($(this).find("#telephone").val()) ){
                    $(this).parent('.control').find("#telephone-error").remove();
                    $(this).removeClass('mage-error');
                }else{
                    event.preventDefault();
                    $(this).find("#telephone").parent('.control').find("#telephone-error").remove();
                    $(this).find("#telephone").removeClass('mage-error');
                    $(this).find("#telephone").addClass('mage-error');
                    $(this).find("#telephone").parent('.control').append('<div for="telephone" generated="true" class="mage-error" style="display:block;" id="telephone-error">Numero de teléfono invalido.</div>');
                    setTimeout(function(){ 
                        $("#telephone-error").css({'display':'block'});    
                        $(this).find("#telephone").focus();
                    }, 100);
                    return false;
                }
            }
        });
    });
    
});

