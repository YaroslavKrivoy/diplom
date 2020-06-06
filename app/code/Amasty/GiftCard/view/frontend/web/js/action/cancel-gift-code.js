define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Amasty_GiftCard/js/model/resource-url-manager',
    'Magento_Checkout/js/model/error-processor',
    'Amasty_GiftCard/js/model/payment/gift-card-messages',
    'mage/storage',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, quote, urlManager, errorProcessor, messageContainer,
             storage, getPaymentInformationAction, totals, $t, fullScreenLoader
) {
    'use strict';

    return function (giftCode) {
        var quoteId = quote.getQuoteId(),
            url = urlManager.getGiftCodeUrl(giftCode, quoteId),
            message = $t('Gift Card %1 was removed.').replace('%1', giftCode);

        messageContainer.clear();
        fullScreenLoader.startLoader();

        return storage.delete(
            url,
            false
        ).done(function (code) {
            var deferred = $.Deferred(),
                applyCodes = $('#gift-code').val().split(",").filter(function(e){return e});

            if (applyCodes.indexOf(code) !== -1) {
                applyCodes.splice(applyCodes.indexOf(code), 1);
            }

            totals.isLoading(true);
            getPaymentInformationAction(deferred);
            $.when(deferred).done(function () {
                $('#gift-code').val(applyCodes.join(',')).change();
                totals.isLoading(false);
                fullScreenLoader.stopLoader();
            });

            messageContainer.addSuccessMessage({
                'message': message
            });
        }).fail(function (response) {
            totals.isLoading(false);
            fullScreenLoader.stopLoader();
            errorProcessor.process(response, messageContainer);
        });
    };
});
