define([
    'jquery',
	'mage/url',
    'Magento_Checkout/js/model/quote',
	'Magento_Checkout/js/model/full-screen-loader',
    'mage/translate'
], function (jQuery, getUrl, quote, fullScreenLoader, $t) {
	var storeList = window.storeList;
	//console.log(storeList);
	 quote.shippingMethod.subscribe(function (value) {
		 var storeHtml = "<tr class='newcarrier-row'><td colspan=4><div class='newcarrier-info'></div></td></tr>";
		
		 var newcarrierMethod = jQuery("#label_method_newcarrier_newcarrier").parent();
		 if(jQuery(".newcarrier-info").length == 0){
			 newcarrierMethod.after(storeHtml);
			 jQuery(".newcarrier-info").append("<div class='show-store-detail'><label>"+$t('Seleccione Tienda')+"</label></div>");
			 jQuery(".show-store-detail").append("<select id='pickup-store' class='store-detail-select'></select>");
			 /* jQuery(".store-detail-select").append("<option class='store-detail-item' value=''>"+$t('Please Select Store')+"</option>"); */
			 jQuery.each(storeList, function (index, el) {
				 jQuery(".store-detail-select").append("<option class='store-detail-item' value='"+el.pick_store+"'>"+$t(el.pick_store)+"</option>");
			 });
		 }
		 
		 if (quote.shippingMethod().carrier_code == 'newcarrier') {
             jQuery(".newcarrier-row").show();
			 
		 } else {
			 jQuery(".newcarrier-row").hide();
		 }
			 
	 });
	 
});