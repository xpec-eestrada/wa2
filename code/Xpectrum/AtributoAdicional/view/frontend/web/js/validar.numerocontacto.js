(function(window){
    'use strict';
    var numero = {
        validaNumeroContacto : function (numero) {
            var tmp=numero.trim();
            if(tmp!=""){
                if(tmp.length==9 || tmp.length==8 ){
                    var res = parseInt(tmp.substring(0,1));
                    if(isNaN(res) || (res!=9 && res!=2) ){
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }
    }
    window.numero=numero;
})(window);