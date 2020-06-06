/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/ebizmarts/sagepaysuite/abstract-method'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/ebizmarts/sagepaysuite/form-method'
        },

        /**
         * Prepare payment
         */
        preparePayment: function () {
            var self = this;

            this._savePaymentDetails().done(function () {
                self._beforeAction().done(function () {
                    self._getMethodRenderComponent().preparePayment();
                });
            });
        }
    });
});
