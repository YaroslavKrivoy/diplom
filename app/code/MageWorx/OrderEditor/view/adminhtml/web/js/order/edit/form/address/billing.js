/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'jquery',
        'MageWorx_OrderEditor/js/order/edit/form/address',
        'jquery/ui'
    ],
    function ($, address) {
        'use strict';

        $.widget('mage.mageworxOrderEditorAddressBilling', $.mage.mageworxOrderEditorAddress, {
            params: {
                updateUrl: '',
                loadFormUrl: '',

                cancelButtonId: '#address-billing-cancel',
                submitButtonId: '#address-billing-submit',
                editLinkId: '#ordereditor-billing-address-link',

                blockId: 'billing_address',
                blockContainerId: '.admin__page-section-item-content',
                formContainerId: '.order-billing-address',
                linkContainerId: '.admin__page-section-item-title',
                formId: '#billing_address_edit_form'
            },

            init: function (params) {
                this.params = this._mergeParams(this.params, params);
                this.initAddress(this.params);
            },

            getLoadFormParams: function () {
                var id = $(this.params.formContainerId).find('#' + this.params.blockId + '_id').val();
                var orderId = this.getCurrentOrderId();
                var blockId = this.params.blockId;

                return {'form_key':FORM_KEY, 'address_id':id, 'order_id':orderId, 'block_id':blockId, 'address_type':'billing'};
            },

            getConfirmUpdateData: function () {
                var data = this.getLoadFormParams();
                var formData = this.getFormData(this.params.formId);
                return this._mergeParams(data, formData);
            }
        });

        return $.mage.mageworxOrderEditorAddressBilling;
    }
);