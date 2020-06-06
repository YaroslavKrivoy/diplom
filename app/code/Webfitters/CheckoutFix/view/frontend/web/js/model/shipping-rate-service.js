/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address',
    'Magento_Checkout/js/model/shipping-rate-processor/customer-address'
], function (quote, shippingService, defaultProcessor, customerAddressProcessor) {
    'use strict';

    var processors = [];

    processors.default =  defaultProcessor;
    processors['customer-address'] = customerAddressProcessor;

    var request = null;
    shippingService.isLoading.subscribe(function(){
        if(!shippingService.isLoading() && request != null){
            var type = request.getType();
            
            if (processors[type]) {
                processors[type].getRates(request);
            } else {
                processors.default.getRates(request);
            }
            request = null;
        }
    });
    quote.shippingAddress.subscribe(function () {
        if(shippingService.isLoading()){
            request = quote.shippingAddress();
        } else {
            var type = quote.shippingAddress().getType();
            
            if (processors[type]) {
                processors[type].getRates(quote.shippingAddress());
            } else {
                processors.default.getRates(quote.shippingAddress());
            }
        }
    });

    return {
        /**
         * @param {String} type
         * @param {*} processor
         */
        registerProcessor: function (type, processor) {
            processors[type] = processor;
        }
    };
});