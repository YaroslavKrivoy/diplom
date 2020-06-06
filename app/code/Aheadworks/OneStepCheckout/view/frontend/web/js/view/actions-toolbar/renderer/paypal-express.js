/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/paypal-express'
        },

        /**
         * Check if context checkout is enabled
         */
        isContextCheckout: function () {
            var config = window.checkoutConfig.payment.paypalExpress;

            return config && config.isContextCheckout;
        },

        /**
         * Redirect to PayPal
         */
        continueToPayPal: function () {
            var self = this;

            this._beforeAction().done(function () {
                self._getMethodRenderComponent().continueToPayPal();
            });
        }
    });
});
