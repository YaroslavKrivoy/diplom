/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/amazon/payment/pay'
        },

        /**
         * @inheritdoc
         */
        placeOrderAmazon: function (data, event) {
            var self = this;
                self._getMethodRenderComponent().placeOrder();
        }
    });
});
