/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default',
    'Magento_Checkout/js/action/set-payment-information',
    'Magento_Checkout/js/model/quote',
    'Aheadworks_OneStepCheckout/js/model/checkout-data',
    'Magento_Ui/js/model/messageList'
], function ($, Component, setPaymentInformationAction, quote, checkoutData, messages) {
    'use strict';

    return Component.extend({

        /**
         * Save payment details
         */
        _savePaymentDetails: function () {
            var deferred = $.Deferred(),
                data = {
                method: quote.paymentMethod().method
            };

            setPaymentInformationAction(
                messages, data
            ).fail(
                function () {
                    messages.clear();
                }
            ).always(
                function () {
                    checkoutData.resetAwCheckoutData();
                    deferred.resolve();
                }
            );

            return deferred;
        }
    });
});
