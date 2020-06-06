/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
], function ($, wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
            var elem = $(".osc-datepicker");
            var parent = $(".shipping-method-delivery");
            if(quote.shippingMethod().carrier_code === 'storepickup'){
                if (shippingAddress['extension_attributes'] === undefined) {
                    shippingAddress['extension_attributes'] = {};
                }

                shippingAddress['extension_attributes']['pickup_date'] = elem.val();
                shippingAddress['extension_attributes']['pickup_time'] = $('.shipping-method-delivery .control .select option:selected').text();

                $(elem.parent().children()[0]).children().text('Store Pickup Date');
                if(parent.is(':hidden')){
                    parent.show();
                }
            }
            else{
                $(elem.parent().children()[0]).children().text('Delivery Date');
                if(parent.is(':visible')){
                    parent.hide();
                }
            }
            // pass execution to original action ('Magento_Checkout/js/action/set-shipping-information')
            return originalAction();
        });
    };
});