/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'mage/utils/wrapper',
    'Aheadworks_OneStepCheckout/js/model/newsletter/assigner',
    'Aheadworks_OneStepCheckout/js/model/order-note/assigner',
    'Aheadworks_OneStepCheckout/js/model/delivery-date/assigner',
    'Magento_Checkout/js/model/full-screen-loader'
], function (
    $,
    wrapper,
    newsletterAssigner,
    orderNoteAssigner,
    deliveryDateAssigner,
    fullScreenLoader
) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            newsletterAssigner(paymentData);
            orderNoteAssigner(paymentData);
            deliveryDateAssigner(paymentData);

            return originalAction(paymentData, messageContainer).fail(
                function () {
                    fullScreenLoader.stopLoader();
                }
            );
        });
    };
});
