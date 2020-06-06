/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'jquery',
        'underscore',
        'uiRegistry',
        'Aheadworks_OneStepCheckout/js/view/place-order/aggregate-validator'
    ],
    function (
        $,
        _,
        registry,
        aggregateValidator
    ) {
        'use strict';

        return {

            /**
             * Perform overall checkout data validation
             *
             * @returns {Boolean}
             */
            validate: function () {
                return aggregateValidator.groupValidateMethods(true);
            }
        };
    }
);
