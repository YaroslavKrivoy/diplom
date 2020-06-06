/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
], function ($, _) {
    'use strict';

    return function (checkoutExpress) {

        return checkoutExpress.extend({

            /**
             * @inheritdoc
             */
            initListeners: function() {
                $('.aw-onestep-main input, .aw-onestep-main select, .aw-onestep-main textarea').on('change', function () {
                    if ($('#co-payment-form #paypal_express').is(':checked')) {
                        this.validate();
                    }
                }.bind(this));

                this._super()
            }
        });
    }
});
