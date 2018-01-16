(function(window){
    'use strict';
    var rut = {
        validaRut : function (rutCompleto) {
            if (!/^[0-9]+[-|‐]{1}[0-9kK]{1}$/.test( rutCompleto ))
                return false;
            var tmp 	= rutCompleto.split('-');
            var digv	= tmp[1]; 
            var rutn 	= tmp[0];
            if(rutn.length<7 || rutn.length>8){return false}
            if ( digv == 'K' ) digv = 'k' ;
            return (rut.dv(rutn) == digv );
        },
        dv : function(T){
            var M=0,S=1;
            for(;T;T=Math.floor(T/10))
                S=(S+T%10*(9-M++%6))%11;
            return S?S-1:'k';
        }
    }
    window.rut=rut;
})(window);