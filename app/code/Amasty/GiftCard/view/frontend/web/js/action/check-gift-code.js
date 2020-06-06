define([
    'jquery',
    'Amasty_GiftCard/js/model/resource-url-manager',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, urlManager, fullScreenLoader) {
    'use strict';

    return function () {
        var url = urlManager.getCheckGiftCodeUrl(),
            giftForm = $(".gift-card-form");

        giftForm = giftForm.length === 2 ? giftForm.last() : giftForm.first();
        fullScreenLoader.startLoader();

        $.ajax({
            url: url,
            data: {amgiftcard: giftForm.serialize()},
            type: 'post',
            success: function(response) {
                var info = $('.amgiftcard_info');
                info = info.length === 2 ? info.last() : info.first();
                info.html(response);
                fullScreenLoader.stopLoader();
            },
            fail: function () {
                fullScreenLoader.stopLoader();
            }
        });
    };
});
