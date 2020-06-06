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

        $.widget('mage.mageworxOrderEditorAddress', $.mage.mageworxOrderEditorBase, {

            initAddress: function (params) {
                this._initParams(params);
                this._initAddressIdInput();
                this._initActions();
            },

            onClickAction: function (actionClass, action) {
                var self = this;

                $(document).off('click touchstart', actionClass);
                $(document).on('click touchstart', actionClass, (function (e) {
                    e.preventDefault();
                    eval("self." + action);
                }));
            },

            getAddressIdFromUrl: function (url) {
                if (url === undefined) {
                    return [];
                }
                var VRegExp = new RegExp(/address_id\/([0-9]+)/);
                var VResult = url.match(VRegExp);
                return VResult[1];
            },

            validateForm: function () {
                var validator = $(this.params.formId).validate();
                return validator.form();
            },

            _initAddressIdInput: function () {
                var self = this;
                var href = $(this.params.formContainerId).find('.admin__page-section-item-title .actions a').attr('href');
                var id = this.getAddressIdFromUrl(href);

                $(this.params.formContainerId).find('.actions').each(function () {
                    var addressIdInput = '<input type="hidden" id="' + self.params.blockId + '_id" value="' + id + '" />';
                    $(addressIdInput).appendTo($(self.params.formContainerId));
                    $(this).remove();
                });
            }
        });

        return $.mage.mageworxOrderEditorAddress;
    }
);