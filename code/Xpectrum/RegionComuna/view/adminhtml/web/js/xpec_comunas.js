require([
    "jquery",'domReady!'
], function($){
    'use strict';
    $(document).ready(function($,ko) {
        var baseUrl = document.location.origin;
        var initcmb = false;
        $(document).off('change','div[data-index="region_id"] select');
        $(document).on('change','div[data-index="region_id"] select',function(){
            var fieldregion = $(this);
            var namefieldcomuna = $(this).attr('name').replace("region_id","xpec_comuna");
            var fielcomuna=$('select[name="'+namefieldcomuna+'"]');
            var id_region   = $(this).val();
            if(id_region!=''){
                $(fielcomuna).attr('disabled','disabled');
                $(this).attr('disabled','disabled');
                $(fielcomuna).attr('disabled','disabled');
                $.ajax({
                    url: baseUrl+'/regioncomunas/index/ajaxcomuna/',
                    data: {
                        id_region:id_region
                    },
                    type: "POST",
                    dataType: 'json'
                }).done(function (data) {
                    var options='';
                    if(data.result.length){
                        $.each(data.result,function(index,item){
                            options=options+"<option value='"+item.value+"'>"+item.label+"</option>";
                        });
                    }
                    $(fielcomuna).html(options);
                    $(fieldregion).removeAttr("disabled");
                    $(fielcomuna).removeAttr("disabled");
                });
            }
        });
        $(document).off('change','div[data-index="xpec_comuna"] select');
        $(document).on('change','div[data-index="xpec_comuna"] select',function(){
            if(initcmb){
                var namefieldciudad = $(this).attr('name').replace("xpec_comuna","city");
                var namefieldcodigopostal = $(this).attr('name').replace("xpec_comuna","postcode");
                var fieldcodigopostal=$('input[name="'+namefieldciudad+'"]');
                var fieldciudad=$('input[name="'+namefieldciudad+'"]');
                if($(this).find("option:selected").text()!=''){
                    $(fieldciudad).val($(this).find("option:selected").text());
                }
                $(fieldcodigopostal).val(0);
            }
        });
        $('body').mouseover(function(){
            if( $('div[data-index="xpec_comuna"] select').length ){
                if(!initcmb){
                    var fieldcomuna = $('div[data-index="xpec_comuna"] select');
                    $(fieldcomuna).attr('disabled','disabled');
                    $.each($(fieldcomuna),function(index,element){
                        if( $(element).val() != '' ){                  
                            $.ajax({
                                url: baseUrl+'/regioncomunas/index/ajaxcomunatocomuna/',
                                data: {
                                    id_comuna:$(element).val()
                                },
                                type: "POST",
                                dataType: 'json'
                            }).done(function (data) {
                                var options='';
                                if(data.result.length){
                                    $.each(data.result,function(index,item){
                                        if(item.value==$(element).val()){
                                            options=options+"<option selected='selected' value='"+item.value+"'>"+item.label+"</option>";
                                        }else{
                                            options=options+"<option value='"+item.value+"'>"+item.label+"</option>";
                                        }
                                    });
                                }
                                $(element).html(options);
                            });
                        }
                        $(element).removeAttr('disabled');
                    });
                    initcmb=true;
                }
            }
        });
    });
});