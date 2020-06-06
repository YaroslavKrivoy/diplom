/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'Aheadworks_OneStepCheckout/js/view/actions-toolbar/renderer/default',
    'Magento_Ui/js/lib/view/utils/async'
], function ($, Component) {
    'use strict';

    var amazonPayBtnSelector = '#PayWithAmazon img',
        amazonBtnContainerSelector = '.payment-method .amazon-button-container';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/actions-toolbar/renderer/amazon/payment/login'
        },

        /**
         * @inheritdoc
         */
        initMethodsRenderComponent: function () {
            this._super();

            if (this.methodRendererComponent) {
                $.async(amazonBtnContainerSelector, (function() {
                    $(amazonBtnContainerSelector).hide();
                }));
            }

            return this;
        },

        /**
         * Activate amazon payment
         */
        continueToAmazon: function () {
            this._beforeAction().done(function () {
                $(amazonPayBtnSelector).trigger('click');
            });
        }
    });
});
