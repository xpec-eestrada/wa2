require([
    "jquery",'ValidarRut','ValidarNumeroContacto','domReady!'
], function($){
    $(document).ready(function () {
        $("#rut").blur(function(){
            var rut=$(this).val();
            var objrut = $(this);
            if(rut.length){
                if( window.rut.validaRut(rut) ){
                    $(this).parent('.control').find("#rut-error").remove();
                    $(this).removeClass('mage-error');
                }else{
                    $(this).parent('.control').find("#rut-error").remove();
                    $(this).addClass('mage-error');
                    $(this).parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                }
            }
        });
        $("#numero_contacto").blur(function(){
            if( window.numero.validaNumeroContacto($(this).val()) ){
                $(this).parent('.control').find("#numerocontacto-error").remove();
                $(this).removeClass('mage-error');
            }else{
                $(this).parent('.control').find("#numerocontacto-error").remove();
                $(this).addClass('mage-error');
                $(this).parent('.control').append('<div for="numerocontacto" generated="true" class="mage-error" id="numerocontacto-error">Numero de contacto invalido.</div>');
            }
        });

        $("form").submit(function( event ) {
            if( $(this).find("#rut").length )  {
                var objrut = $(this).find("#rut");
                var rut    = $(this).find("#rut").val();
                if(!window.rut.validaRut(rut)){
                    event.preventDefault();
                    $(objrut).parent('.control').find("#rut-error").remove();
                    $(objrut).addClass('mage-error');
                    $(objrut).parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                    $(objrut).focus();
                    return false;
                }
            }
            if($(this).find("#numero_contacto").length){
                if( window.numero.validaNumeroContacto($(this).find("#numero_contacto").val()) ){
                    $(this).find("#numero_contacto").parent('.control').find("#numerocontacto-error").remove();
                    $(this).find("#numero_contacto").removeClass('mage-error');
                }else{
                    $(this).find("#numero_contacto").parent('.control').find("#numerocontacto-error").remove();
                    $(this).find("#numero_contacto").addClass('mage-error');
                    $(this).find("#numero_contacto").parent('.control').append('<div for="numerocontacto" generated="true" class="mage-error" id="numerocontacto-error">Numero de contacto invalido.</div>');
                    $(this).find("#numero_contacto").focus();
                    event.preventDefault();
                    return false;
                }
            }
        });
    });
    
});

