/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [        
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function (Component, url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Transbank_Webpay/payment/webpay',
                redirectAfterPlaceOrder: false
            },            
            
            afterPlaceOrder: function () {
                window.location.replace(url.build('webpay/payment/redirect?_secure=false'));
            },
            
            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            }           
        });
    }
);
