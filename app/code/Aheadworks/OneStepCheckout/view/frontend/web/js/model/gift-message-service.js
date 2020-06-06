/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'ko',
    'underscore',
    'uiRegistry'
], function (ko, _, registry) {
    'use strict';

    var isLoading = ko.observable(false);

    return {
        isLoading: isLoading,

        /**
         * Set gift message to date provider
         *
         * @param {Object} giftMessageData
         */
        setGiftMessage: function (giftMessageData) {
            var checkoutProvider = registry.get('checkoutProvider'),
                giftMessageItems = giftMessageData['item_messages'] || [],
                giftMessageOrder = giftMessageData['order_message'] || {};

            _.each(giftMessageItems, function (item) {
                checkoutProvider.set('giftMessage.' + item['config']['item_id'], item['message']);
                checkoutProvider.set('giftMessage.config.' + item['config']['item_id'], item['config']);
            });
            checkoutProvider.set('giftMessage.order', giftMessageOrder['message']);
            checkoutProvider.set('giftMessage.config.order', giftMessageOrder['config']);
            checkoutProvider.trigger('data.giftMessageUpdated');
        }
    };
});
