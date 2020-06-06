define([
    'jquery',
    'ko',
    'uiComponent',
    'Amasty_GiftCard/js/action/set-gift-code',
    'Amasty_GiftCard/js/action/cancel-gift-code',
    'Amasty_GiftCard/js/action/check-gift-code',
    'Magento_Checkout/js/model/totals'
], function ($, ko, Component, setGiftCodeAction, cancelGiftCodeAction, checkGiftCodeAction, total) {
    'use strict';

    var giftCardCode = ko.observable(null);

    if (total.getSegment("amasty_giftcard")) {
        var codes = total.getSegment("amasty_giftcard")['title'].replace(' ', '');
        giftCardCode(codes);
    }

    if (!giftCardCode()){
        giftCardCode('');
    }

    return Component.extend({
        defaults: {
            template: 'Amasty_GiftCard/payment/gift-card'
        },
        giftCardCode: giftCardCode,

        /**
         * Coupon code remove
         */
        removeSelected : function (obj) {
            cancelGiftCodeAction(obj);
        },

        /**
         * Coupon code application procedure
         */
        apply: function () {
            var codeFake = $('input[name="gift_code_fake"]'),
                newGiftCode = null,
                formForValidate;

            if (codeFake.length === 2) {
                newGiftCode = codeFake.last().val();
                formForValidate = $('.gift-card-form').last();
            } else {
                newGiftCode = codeFake.first().val();
                formForValidate = $('.gift-card-form').first();
            }

            if (this.validate(formForValidate)) {
                setGiftCodeAction(newGiftCode);
            }
        },

        /**
         * Check using coupon
         */
        check: function () {
            if (this.validate()) {
                checkGiftCodeAction();
            }
        },

        /**
         * Cancel using coupon
         */
        cancel: function () {
            if (this.validate()) {
                cancelGiftCodeAction(giftCardCode());
            }
        },

        /**
         * Coupon form validation
         *
         * @returns {Boolean}
         */
        validate: function (form) {
            var formForValidate = form === undefined ? $('#gift-card-form') : form;

            return formForValidate.validation() && formForValidate.validation('isValid');
        },

        isGiftCardEnable: function () {
            return window.checkoutConfig.giftCard.is_enable;
        },
    });
});
