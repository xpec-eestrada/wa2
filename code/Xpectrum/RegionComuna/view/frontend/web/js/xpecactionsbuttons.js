require([
    "jquery","Validador",'domReady!'
], function($){
    $(document).ready(function () {
        //xpecactionsbuttons
        //.actions-toolbar button.add
        $(document).off("click",".actions-toolbar button.add");
        $(document).on("click",".actions-toolbar button.add",function(){
            //console.log("agregar");
            var baseUrl   = document.location.origin;
            var redirect  = baseUrl+'/customer/address/new/';
            console.log(redirect);
            location.href = redirect;
        });


    });
    
});

