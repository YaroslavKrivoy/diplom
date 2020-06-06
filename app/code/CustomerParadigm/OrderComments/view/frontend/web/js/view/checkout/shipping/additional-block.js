/**
 * Copyright © 2015 Customer Paradigm. All rights reserved.
 * See COPYING.txt for license details.
 * @version 1.0.0
 * @since 1.0.0
 */
define(
    [
        'jquery',
        'ko',
        'uiComponent',
    ],
    function ($, ko, Component) {
        'use strict';
        var show_hide_custom_blockConfig = window.checkoutConfig.show_hide_custom_block,
            quote_id = window.checkoutConfig.quote_id;
        return Component.extend({
            defaults: {
                template: 'CustomerParadigm_OrderComments/checkout/shipping/additional-block'
            },
            canVisibleBlock: show_hide_custom_blockConfig,
            quoteId: quote_id
        });
    }
);

