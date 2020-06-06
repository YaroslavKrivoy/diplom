/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/shipping-service',
        'Aheadworks_OneStepCheckout/js/action/get-sections-details'
    ],
    function (
        quote,
        urlBuilder,
        storage,
        errorProcessor,
        fullScreenLoader,
        customer,
        methodConverter,
        paymentService,
        shippingService,
        getSectionsDetailsAction
    ) {
        'use strict';

        return function (item) {
            var serviceUrl,
                payload = {
                    item: item
                };

            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/awOsc/guest-carts/:cartId/cart-items', {
                    cartId: quote.getQuoteId()
                });
            } else {
                serviceUrl = urlBuilder.createUrl('/awOsc/carts/mine/cart-items', {});
            }
            fullScreenLoader.startLoader();

            return storage.post(
                serviceUrl,
                JSON.stringify(payload)
            ).done(
                function (response) {
                    var cartDetails = response.cart_details,
                        paymentDetails = response.payment_details;

                    quote.setTotals(paymentDetails.totals);
                    quote.setQuoteData(cartDetails);
                    paymentService.setPaymentMethods(methodConverter(paymentDetails.payment_methods));

                    shippingService.isLoading(true);
                    getSectionsDetailsAction(['shippingMethods', 'totals']).always(function () {
                        shippingService.isLoading(false);
                    });
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            ).always(function () {
                fullScreenLoader.stopLoader();
            });
        };
    }
);
