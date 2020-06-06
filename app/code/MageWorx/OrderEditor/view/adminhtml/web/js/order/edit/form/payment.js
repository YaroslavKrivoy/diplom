/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'jquery',
        'MageWorx_OrderEditor/js/order/edit/form/base',
        'jquery/ui'
    ],
    function ($) {
        'use strict';

        $.widget('mage.mageworxOrderEditorPayment', $.mage.mageworxOrderEditorBase, {
            params: {
                updateUrl: '',
                loadFormUrl: '',

                cancelButtonId: '#payment-method-cancel',
                submitButtonId: '#payment-method-submit',
                editLinkId: '#ordereditor-payment-link',

                blockId: 'payment_method',
                formId: '#payment-form',
                blockContainerId: '.admin__page-section-item-content',
                formContainerId: '.order-payment-method',
                linkContainerId: '.admin__page-section-item-title',
                magentoLoadBlockUrl: '',

                paymentMethodBlockId: '#order-payment-method-choose'
            },

            init: function (params) {
                this.params = this._mergeParams(this.params, params);
                this._initParams(params);
                this._initActions();
            },

            getLoadFormParams: function () {
                var orderId = this.getCurrentOrderId();
                var blockId = this.params.blockId;
                return {'form_key': FORM_KEY, 'order_id': orderId, 'block_id': blockId};
            },

            validateForm: function () {
                return $(this.params.paymentMethodBlockId).find('input[name="payment[method]"]:checked').length == 1;
            },

            getConfirmUpdateData: function () {
                var self = this;
                var orderId = this.getCurrentOrderId();
                var paymentMethod = $(self.params.paymentMethodBlockId)
                    .find('input[name="payment[method]"]:checked')
                    .val();

                var params = {'form_key': FORM_KEY, 'payment_method': paymentMethod, 'order_id': orderId};

                $(['payment_title', 'payment_comment']).each(function (j, i) {
                    params[i] = $(self.params.paymentMethodBlockId)
                        .find('input[name="payment[' + paymentMethod + '][' + i + ']"]')
                        .val();
                });

                $(['pmclain_authorizenetcim_cc_type',
                    'pmclain_authorizenetcim_cc_number',
                    'pmclain_authorizenetcim_expiration',
                    'pmclain_authorizenetcim_expiration_yr'
                ]).each(function (j, i) {
                    params[i] = $('#' + i + '').val();
                });

                return params;
            },

            initInput: function () {
                var self = this;
                var input = this.params.paymentMethodBlockId + ' input[type="text"]';
                var radio = this.params.paymentMethodBlockId + ' input[type="radio"]';

            },
        });

        return $.mage.mageworxOrderEditorPayment;
    }
);