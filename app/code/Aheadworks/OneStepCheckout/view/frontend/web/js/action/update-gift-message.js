/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Aheadworks_OneStepCheckout/js/action/get-sections-details',
    'Aheadworks_OneStepCheckout/js/model/gift-message-service',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/full-screen-loader'
], function (
    storage,
    urlBuilder,
    customer,
    getSectionsDetailsAction,
    giftMessageService,
    messageList,
    errorProcessor,
    quote,
    fullScreenLoader
) {
    'use strict';

    return function (itemId, giftMessage) {
        var serviceUrl;

        if (customer.isLoggedIn()) {
            serviceUrl = urlBuilder.createUrl('/carts/mine/gift-message', {});
            if (itemId !== 'order') {
                serviceUrl = urlBuilder.createUrl('/carts/mine/gift-message/:itemId', {itemId: itemId});
            }
        } else {
            serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/gift-message', {cartId: quote.getQuoteId()});
            if (itemId !== 'order') {
                serviceUrl = urlBuilder.createUrl(
                    '/guest-carts/:cartId/gift-message/:itemId',
                    {cartId: quote.getQuoteId(), itemId: itemId}
                );
            }
        }
        messageList.clear();
        fullScreenLoader.startLoader();

        return storage.post(
            serviceUrl,
            JSON.stringify({
                'gift_message': giftMessage
            })
        ).done(function () {
            giftMessageService.isLoading(true);
            getSectionsDetailsAction(['giftMessage']).always(function () {
                giftMessageService.isLoading(false);
            });
        }).fail(function (response) {
            errorProcessor.process(response);
        }).always(function () {
            fullScreenLoader.stopLoader();
        });
    };
});
