define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address',
    'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
    'Magento_Checkout/js/model/shipping-rate-registry'
], function ($, wrapper, quote, validator, defaultProcessor, customerAddressProcessor, rateRegistry) {
    'use strict';

    return function (setShippingInformationAction) {
        $(document).on('change',"[name='country']",function(){
            validator.validateFields();
        });

        $(document).on('change',"[name='region_id']",function(){
            validator.validateFields();
        });
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            return originalAction();
        });
    };

});