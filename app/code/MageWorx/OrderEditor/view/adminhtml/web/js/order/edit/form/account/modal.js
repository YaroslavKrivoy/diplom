/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal',
        'jquery/ui'
    ],
    function ($) {
        "use strict";

        /**
         *  Creating modal widget to make available to select a customer form a grid
         */
        $.widget('MageWorx.modalForm', {
            options: {
                modalFormId: '#select-a-customer-modal',
                modalButtonId: '#select_customer',
                isGridLoaded: false,
                modalOption: {
                    type: 'slide',
                    responsive: true,
                    title: $.mage.__('Please select a customer.'),
                    buttons: [
                        {
                            text: $.mage.__('Continue'),
                            class: '',
                            click: function () {
                                this.closeModal();
                            }
                        }
                    ]
                }
            },

            _create: function () {
                this._on($(this.options.modalFormId), {
                    'loadGrid': this.loadGrid
                });
                this._bind();
            },

            /**
             * Bind event listeners to the trigger to open modal window and load the grid with a customers
             *
             * @private
             */
            _bind: function () {
                var self = this,
                    modalFormId = this.options.modalFormId;

                $(document).on('click', this.options.modalButtonId, function (e) {
                    e.preventDefault(e);
                    if (!self.options.isGridLoaded) {
                        $(modalFormId).trigger('loadGrid', {"url": self.options.renderGridUrl});
                    } else {
                        $(modalFormId).trigger('openModal');
                    }
                });

                window.selectedCustomer = function (tr) {
                    var $tr = $(tr),
                        customerId = $tr.find('.col-entity_id').text().trim(),
                        customerEmail = $tr.find('.col-chooser_email').text().trim(),
                        customerFirstName = $tr.find('.col-chooser_firstname').text().trim(),
                        customerLastName = $tr.find('.col-chooser_lastname').text().trim(),
                        customerGroup = $tr.find('.col-chooser_group_id').text().trim(),
                        customerGroupId = $('[name="chooser_group_id"]')
                            .find('option:contains(' + customerGroup + ')')
                            .val();

                    $('[name="order[account][group_id]"]').val(Number(customerGroupId));
                    $('[name="order[account][email]"]').val(customerEmail);
                    $('[name="order[account][customer_firstname]"]').val(customerFirstName);
                    $('[name="order[account][customer_lastname]"]').val(customerLastName);
                    $('[name="order[account][customer_id]"]').val(customerId);

                    $(modalFormId).trigger('closeModal');
                }
            },

            /**
             * Load grid from the backend if not loaded yet.
             * Does nothing when grid was already loaded.
             * @see this.options.isGridLoaded
             */
            loadGrid: function () {
                if (this.options.isGridLoaded) {
                    return;
                } else {
                    this.options.isGridLoaded = true;
                }

                var $modalContent = '',
                    url = arguments[1]['url'] ? arguments[1]['url'] : this.options.renderGridUrl,
                    modalFormId = this.options.modalFormId,
                    modalOption = this.options.modalOption;

                $.ajax(url, {
                    data: {
                        'form_key': FORM_KEY
                    },
                    beforeSend: function (xhr) {
                        $('body').trigger('processStart');
                    }
                }).done(function (data) {
                    $modalContent = data;
                }).fail(function (xhr, textStatus, errorThrown) {
                    $modalContent = '<h3>' + textStatus + '</h3>';
                }).always(function (data) {
                    $('body').trigger('processStop');
                    $(modalFormId).html($modalContent);
                    $(modalFormId).modal(modalOption);
                    $(modalFormId).trigger('openModal');
                });

                return this.element;
            }
        });

        return $.MageWorx.modalForm;
    }
);
