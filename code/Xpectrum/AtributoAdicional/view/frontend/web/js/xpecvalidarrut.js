require([
    "jquery","Validador",'domReady!'
], function($){
    $(document).ready(function () {
        $("#rut").blur(function(){
            var rut=$(this).val();
            if(rut.length){
                if( window.Fn.validaRut(rut) ){
                    $("#rut-error").remove();
                    $("#rut").removeClass('mage-error');
                }else{
                    $("#rut-error").remove();
                    $("#rut").addClass('mage-error');
                    $(this).parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                }
            }
        });
        $(".form-create-account").submit(function( event ) {
            var rut=$("#rut").val();
            if(!window.Fn.validaRut(rut)){
                event.preventDefault();
                $("#rut-error").remove();
                $("#rut").parent('.control').append('<div for="rut" generated="true" class="mage-error" id="rut-error">Rut Invalido.</div>');
                $("#rut").addClass('mage-error');
                $("#rut").focus();
                return false;
            }        
        });
    });
    
});

