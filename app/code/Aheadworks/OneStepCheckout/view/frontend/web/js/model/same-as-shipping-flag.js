/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'ko',
        'Aheadworks_OneStepCheckout/js/model/checkout-data'
    ],
    function (ko, oscCheckoutData) {
        'use strict';

        var flag = ko.observable(oscCheckoutData.getSameAsShippingFlag());
        flag.subscribe(function (newValue) {
            oscCheckoutData.setSameAsShippingFlag(newValue)
        });

        return {
            sameAsShipping: flag
        };
    }
);
