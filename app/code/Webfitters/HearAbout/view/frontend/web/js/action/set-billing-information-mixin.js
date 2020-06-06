define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    return function (setBillingInformationAction) {
        return wrapper.wrap(setBillingInformationAction, function (originalAction) {
            var billingAddress = quote;
            if (billingAddress['extension_attributes'] === undefined) {
                billingAddress['extension_attributes'] = {};
            }
            billingAddress['extension_attributes']['hear_about_id'] = $('select[name="hear_about"]').val();
            console.log($('select[name="hear_about"]').val());
            return originalAction();
        });
    };
});