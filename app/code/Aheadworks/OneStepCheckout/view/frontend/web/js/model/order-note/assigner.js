/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Aheadworks_OneStepCheckout/js/model/order-note/order-note'
], function (orderNote) {
    'use strict';

    return function (paymentData) {
        if (window.checkoutConfig.isOrderNoteEnabled && orderNote()) {
            if (paymentData['extension_attributes'] === undefined) {
                paymentData['extension_attributes'] = {};
            }
            paymentData['extension_attributes']['order_note'] = orderNote();
        }
    };
});
