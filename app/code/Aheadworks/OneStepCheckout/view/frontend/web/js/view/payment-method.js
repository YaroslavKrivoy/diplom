/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'uiComponent'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/payment-method'
            },

            /**
             * Initializes observable properties of instance
             *
             * @returns {PaymentMethod} Chainable
             */
            initObservable: function () {
                this._super()
                    .observe({
                        errorValidationMessage: ''
                    });

                return this;
            },

            /**
             * Get form key
             *
             * @returns {string}
             */
            getFormKey: function () {
                return window.checkoutConfig.formKey;
            }
        });
    }
);
