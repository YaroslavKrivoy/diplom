/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'uiComponent',
        'Aheadworks_OneStepCheckout/js/model/render-postprocessor/float-label/converters-pool',
        'Aheadworks_OneStepCheckout/js/model/render-postprocessor/float-label/converter/braintree-hosted-fields'
    ],
    function (
        Component,
        convertersPool,
        braintreeHostedFieldsConverter
    ) {
        'use strict';

        convertersPool.register('braintree', braintreeHostedFieldsConverter);

        return Component.extend({});
    }
);
