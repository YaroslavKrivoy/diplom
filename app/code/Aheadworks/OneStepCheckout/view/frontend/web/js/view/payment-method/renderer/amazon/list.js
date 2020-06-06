/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/lib/view/utils/async',
    'underscore',
    'ko',
    'Aheadworks_OneStepCheckout/js/view/payment-method/list',
    'Aheadworks_OneStepCheckout/js/model/render-postprocessor',
    'Amazon_Payment/js/model/storage'
], function (
    $,
    _,
    ko,
    Component,
    postProcessor,
    amazonStorage
) {
    'use strict';

    return Component.extend({
        paymentMethodItemsSelectors: '[data-role=payment-methods-load] div.payment-method',
        amazonPayment: 'amazon_payment',

        /**
         * @inheritDoc
         */
        onRender: function () {
            var self = this;

            if (amazonStorage.isAmazonAccountLoggedIn()) {
                $.async(this.paymentMethodItemsSelectors, function (methodItem) {
                    self._processPaymentsMethods($(methodItem));
                });
            } else {
                postProcessor.initProcessing();
            }

            this._setupDeclineHandler();
        },

        /**
         * Handle Amazon Payment Service decline codes
         * @private
         */
        _setupDeclineHandler: function () {
            amazonStorage.amazonDeclineCode.subscribe(function (declined) {
                switch (declined) {
                    //hard decline
                    case 4273:
                        amazonStorage.amazonlogOut();
                        this._reloadPaymentMethods();
                        amazonStorage.amazonDeclineCode(false);
                        break;
                    //soft decline
                    case 7638:
                        this._reInitializeAmazonWalletWidget();
                        amazonStorage.amazonDeclineCode(false);
                        break;
                    default:
                        amazonStorage.amazonDeclineCode(false);
                        break;
                }
            }, this);
        },

        /**
         * Re-initialize Amazon wallet widget
         * @private
         */
        _reInitializeAmazonWalletWidget: function () {
            var child = this.getChild('amazon_payment');

            if (child) {
                child.renderPaymentWidget();
            }
        },

        /**
         * Reload payments methods if we received error from Amazon Payment Service
         * @private
         */
        _reloadPaymentMethods: function () {
            $.async(this.paymentMethodItemsSelectors, function (methodItem) {
                var paymentMethod = $(methodItem),
                    methodCode = postProcessor._getPaymentMethodCode(paymentMethod);

                if (methodCode == this.amazonPayment) {
                    paymentMethod.hide();
                } else {
                    paymentMethod.show();
                    postProcessor._hideActionToolbar(paymentMethod);
                }
            });
        },

        /**
         * Process payments methods if we logged in with Amazon
         *
         * @param {jQuery} paymentMethod
         */
        _processPaymentsMethods: function (paymentMethod) {
            var methodCode = postProcessor._getPaymentMethodCode(paymentMethod);

            if (methodCode != this.amazonPayment) {
                paymentMethod.hide();
            } else {
                postProcessor._hideActionToolbar(paymentMethod);
            }
        }
    })
});
