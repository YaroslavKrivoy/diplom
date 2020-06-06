define([
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Amasty_GiftCard/js/model/resource-url-manager',
        'Magento_Checkout/js/model/error-processor',
        'Amasty_GiftCard/js/model/payment/gift-card-messages',
        'mage/storage',
        'mage/translate',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/full-screen-loader'
    ], function (ko, $, quote, urlManager, errorProcessor, messageContainer,
                 storage, $t, getPaymentInformationAction, totals, fullScreenLoader
    ) {
        'use strict';
        return function (giftCode) {
            var quoteId = quote.getQuoteId(),
                url = urlManager.getGiftCodeUrl(giftCode, quoteId),
                message = $t('Gift Card "%1" was added.').replace('%1', giftCode);

            messageContainer.clear();
            fullScreenLoader.startLoader();

            return storage.put(
                url,
                {},
                false
            ).done(function (newCode) {
                var deferred,
                    applyCodes = $('#gift-code').val().split(",").filter(function(e){return e});

                if (newCode) {
                    deferred = $.Deferred();

                    totals.isLoading(true);
                    getPaymentInformationAction(deferred);
                    applyCodes.push(newCode);
                    $.when(deferred).done(function () {
                        $('#gift-code').val(applyCodes.join(',')).change();
                        fullScreenLoader.stopLoader();
                        totals.isLoading(false);
                    });
                    messageContainer.addSuccessMessage({
                        'message': message
                    });
                    $('#gift-code-fake').val('');
                }
            }).fail(function (response) {
                fullScreenLoader.stopLoader();
                totals.isLoading(false);
                errorProcessor.process(response, messageContainer);
            });
        };
    }
);
