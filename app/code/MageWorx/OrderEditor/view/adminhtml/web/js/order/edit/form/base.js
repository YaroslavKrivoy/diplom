/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'jquery',
        'mage/translate',
        'mage/backend/notification',
        'jquery/validate',
        'jquery/ui'
    ],
    function ($, $t, notification) {
        'use strict';

        $.widget('mage.mageworxOrderEditorBase', {
            params: {
                updateUrl: '',
                loadFormUrl: '',

                cancelButtonId: '',
                submitButtonId: '',
                editLinkId: '',

                blockId: '',
                formId: '',
                linkContainerId: '',
                formContainerId: '',
                blockContainerId: ''
            },

            editLinkTemplate: '<div id="%block_id%" class="actions block-edit-link"><a href="#">' + $t("Edit") + '</a></div>',

            _initParams: function (params) {
                var self = this;
                params = params || {};
                $.each(params, function (i, e) {
                    self.params[i] = e;
                });
            },

            _mergeParams: function (params1, params2) {
                $.each(params2, function (i, e) {
                    params1[i] = e;
                });
                return params1;
            },

            _initActions: function () {
                this._initEditLink();
                this._initloadEditForm();
                this._initCancel();
                this._initUpdate();
            },

            _initEditLink: function () {
                var linkTemplate = this.editLinkTemplate,
                    editLink = linkTemplate.replace('%block_id%', this.params.editLinkId.substring(1));
                $(editLink).appendTo($(this.params.formContainerId).children(this.params.linkContainerId));
            },

            cancel: function () {
                $(".ordereditor-fieldset").each(function () {
                    $(this).remove();
                });

                $(".ordereditor-hidden-fieldset").each(function () {
                    $(this).removeClass('ordereditor-hidden-fieldset').show();
                });
            },

            _initloadEditForm: function () {
                var self = this;
                $(document).off('click touchstart', this.params.editLinkId);
                $(document).on('click touchstart', this.params.editLinkId, function () {

                    self.cancel();
                    var data = self.getLoadFormParams(),
                        el = $(this);

                    $.ajax({
                        url: self.params.loadFormUrl,
                        data: data,
                        type: 'post',
                        dataType: 'json',
                        beforeSend: function () {
                            self.showPreLoader();
                        },
                        success: function (response) {
                            if (!response.error && response.status == true) {
                                var fieldsetParent = $(el).parent().parent();
                                fieldsetParent.children(self.params.blockContainerId)
                                    .addClass('ordereditor-hidden-fieldset')
                                    .hide();
                                fieldsetParent.children(self.params.blockContainerId)
                                    .after(
                                        "<div class='" +
                                        self.params.blockContainerId.substring(1) +
                                        " ordereditor-fieldset'></div>"
                                    );
                                fieldsetParent.children(".ordereditor-fieldset").html(response.result);
                                self.hidePreLoader();
                            }

                        },
                        error: function (error) {
                            self.hidePreLoader();
                        }
                    });
                    return false;
                });
            },

            _initCancel: function () {
                var self = this;
                $(document).off('click touchstart', this.params.cancelButtonId);
                $(document).on('click touchstart', this.params.cancelButtonId, (function (e) {
                    e.preventDefault();
                    self.cancel();
                }));
            },

            _initUpdate: function () {
                var self = this;
                $(document).off('click touchstart', this.params.submitButtonId);
                $(document).on('click touchstart', this.params.submitButtonId, (function (e) {
                    e.preventDefault();

                    if (self.validateForm()) {
                        self.confirmUpdate();
                    }
                }));
            },

            confirmUpdate: function (additionalData) {
                var self = this,
                    data = this.getConfirmUpdateData();

                additionalData = additionalData || {};
                $.each(additionalData, function (i, e) {
                    data[i] = e;
                });

                $.ajax({
                    url: self.params.updateUrl,
                    data: data,
                    type: 'post',
                    dataType: 'json',
                    context: this,
                    beforeSend: function () {
                        self.showPreLoader();
                    }
                }).done(function (response) {
                    if (response.error || response.status == false) {
                        self.confirmUpdateErrorHandler(response);
                    } else {
                        self.confirmUpdateSuccessHandler(response);
                    }
                }).fail(function (error) {
                    self.confirmUpdateErrorHandler(error);
                });
            },

            confirmUpdateErrorHandler: function (response) {
                console.log(JSON.stringify(response));
                this.throwError(
                    {
                        "error": true,
                        "message": $.mage.__('Something went wrong. Please, reload the page and try again.')
                    }
                );
                this.hidePreLoader();
            },

            confirmUpdateSuccessHandler: function (response) {
                if (response.result == 'reload') {
                    location.reload();
                } else {
                    alert($t('Something goes wrong'));
                }
            },

            /**
             * Throw error for the customer using the notifications plugin
             * @see lib/web/mage/backend/notification.js
             * @param e {{error: boolean, message: string}}
             */
            throwError: function (e) {
                // Log error in the console
                console.log(e);
                // Show error message for the customer
                $('body').notification('clear')
                    .notification('add', {
                        error: e.error,
                        message: e.message,

                        /**
                         * @param {*} message
                         */
                        insertMethod: function (message) {
                            $('.page-main-actions:first').html(message);
                        }
                    });
            },

            getFormData: function (form) {
                var unindexed_array = $(form).serializeArray(),
                    indexed_array = {};

                $.map(unindexed_array, function (n) {
                    indexed_array[n['name']] = n['value'];
                });

                return indexed_array;
            },

            getCurrentOrderId: function () {
                var VRegExp = new RegExp(/order_id\/([0-9]+)/),
                    VResult = window.location.href.match(VRegExp);

                return VResult[1];
            },

            // CAN be rewritten by blocks
            showPreLoader: function () {
                $('body').trigger('processStart');
            },

            hidePreLoader: function () {
                $('body').trigger('processStop');
            },

            validateForm: function () {
                var validator = $(this.params.formId + ' form').validate();
                return validator.form();
            },

            beforeLoadFromSuccessHandler: function () {
            },
            afterLoadFromSuccessHandler: function () {
            },

            // MUST be rewritten by blocks
            getLoadFormParams: function () {
                return {};
            },
            getConfirmUpdateData: function () {
                return {};
            }
        });

        return $.mage.mageworxOrderEditorBase;
    }
);
