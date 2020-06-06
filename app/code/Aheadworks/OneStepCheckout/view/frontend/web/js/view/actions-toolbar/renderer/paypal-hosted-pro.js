/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/paypal-hosted-pro'
        },

        /**
         * Redirect to PayPal
         */
        continueToHostedPro: function () {
            var self = this;

            this._beforeAction().done(function () {
                self._getMethodRenderComponent().placePendingPaymentOrder();
            });
        },

        isIframeInAction: function () {
            return this._getMethodRenderComponent().isInAction();
        }
    });
});