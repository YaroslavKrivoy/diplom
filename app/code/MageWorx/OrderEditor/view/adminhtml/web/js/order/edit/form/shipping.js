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

        $.widget('mage.mageworxOrderEditorShipping', $.mage.mageworxOrderEditorBase, {
            params: {
                updateUrl: '',
                loadFormUrl: '',

                cancelButtonId: '#shipping-method-cancel',
                submitButtonId: '#shipping-method-submit',
                editLinkId: '#ordereditor-shipping-link',

                blockId: 'shipping_method',
                formId: '#shipping-form',
                blockContainerId: '.admin__page-section-item-content',
                formContainerId: '.order-shipping-method',
                linkContainerId: '.admin__page-section-item-title',

                shippingMethodBlockId: '#order-shipping-method-choose'
            },

            init: function (params) {
                this.params = this._mergeParams(this.params, params);
                this._initParams(params);
                this._initActions();
                this.initInput();
            },

            getLoadFormParams:function () {
                var orderId = this.getCurrentOrderId();
                var blockId = this.params.blockId;
                return {'form_key':FORM_KEY, 'order_id':orderId, 'block_id':blockId};
            },

            validateForm:function () {
                return $(this.params.shippingMethodBlockId).find('input[name="order[shipping_method]"]:checked').length == 1;
            },

            getConfirmUpdateData:function () {
                var self = this;
                var orderId = this.getCurrentOrderId();
                var shippingMethod = $(self.params.shippingMethodBlockId)
                    .find('input[name="order[shipping_method]"]:checked')
                    .val();

                var params = {'form_key':FORM_KEY, 'shipping_method':shippingMethod, 'order_id':orderId};

                $(['price_excl_tax', 'price_incl_tax', 'tax_percent', 'description', 'discount_amount']).each(function (j,i) {
                    params[i] = $(self.params.shippingMethodBlockId)
                        .find('input[name="shipping_method[' + shippingMethod + '][' + i + ']"]')
                        .val();
                });

                return params;
            },

            initInput:function () {
                var self = this;
                var input = this.params.shippingMethodBlockId + ' input[type="text"]';
                var radio = this.params.shippingMethodBlockId + ' input[type="radio"]';

                $(document).off('change', input);
                $(document).on('change', input, function () {
                    self.editShippingMethodInfo($(this));
                    self.calculateNewTotals();
                });

                $(document).off('change', radio);
                $(document).on('change', radio, function () {
                    self.showEditForm($(this));
                    $(this).parent().find('input[type="text"]').change();
                    self.calculateNewTotals();
                });

                $(document).off('keypress', input);
                $(document).on('keypress', input, function (e) {
                    if (e.which == 13 || e.which == 8) {
                        return 1;
                    }
                    var letters = '1234567890.,+-';
                    return (letters.indexOf(String.fromCharCode(e.which)) != -1);
                });
            },

            showEditForm:function (radio) {
                var id = $(radio).val();
                $(this.params.shippingMethodBlockId).find('.edit_price_form').hide();
                $('#edit_price_form_' + id).show();
            },

            editShippingMethodInfo:function (input) {
                var VRegExp = new RegExp(/shipping_method\[(\w+)\]\[(\w+)\]/);
                var VResult = $(input).attr('name').match(VRegExp);
                var id = VResult[1];
                var code = VResult[2];

                var form = $(this.params.shippingMethodBlockId);
                var priceExclTaxInput = form.find('input[name="shipping_method[' + id + '][price_excl_tax]"]');
                var priceInclTaxInput = form.find('input[name="shipping_method[' + id + '][price_incl_tax]"]');
                var taxPercentInput = form.find('input[name="shipping_method[' + id + '][tax_percent]"]');
                var shippingDiscountInput = form.find('input[name="shipping_method[' + id + '][discount_amount]"]');

                var priceExclTax = this.getInputValue(priceExclTaxInput, 0);
                var priceInclTax = this.getInputValue(priceInclTaxInput, 0);
                var taxPercent = this.getInputValue(taxPercentInput, 0);
                var discountAmount = this.getInputValue(shippingDiscountInput, 0);

                if (code == 'price_excl_tax' || code == 'tax_percent' || code == 'discount_amount') {
                    var inclTax = priceExclTax + ((priceExclTax - discountAmount) * taxPercent / 100);
                    priceInclTaxInput.val(inclTax.toFixed(2));
                    priceExclTaxInput.val(priceExclTax.toFixed(2));
                } else if (code == 'price_incl_tax') {
                    var exclTax = priceInclTax / (1 + taxPercent / 100);
                    priceExclTaxInput.val(exclTax.toFixed(2));
                    priceInclTaxInput.val(priceInclTax.toFixed(2));
                }
                taxPercentInput.val(taxPercent.toFixed(2));

                $('#ordereditor-shipping-amount').val(priceExclTaxInput.val());
                $('#ordereditor-shipping-tax-amount').val(priceInclTaxInput.val() - priceExclTaxInput.val());
            },

            getInputValue: function (item, defaultValue) {
                var val = $(item).val();
                val = parseFloat(val);
                if (isNaN(val)) {
                    return defaultValue;
                }
                return val;
            },

            calculateNewTotals: function () {
                var subtotal = 0;
                subtotal += parseFloat($('#ordereditor-subtotal-amount').val());
                $('#ordereditor-new-total-subtotal span').text(subtotal.toFixed(2));

                var shipping = 0;
                shipping += parseFloat($('#ordereditor-shipping-amount').val());
                $('#ordereditor-new-total-shipping-shipping span').text(shipping.toFixed(2));

                var tax = 0;
                tax += parseFloat($('#ordereditor-subtotal-tax-amount').val());
                tax += parseFloat($('#ordereditor-shipping-tax-amount').val());
                tax += parseFloat($('#ordereditor-discount-tax-compensation-amount').val());
                $('#ordereditor-new-total-tax span').text(tax.toFixed(2));

                var discount = 0;
                discount += parseFloat($('#ordereditor-discount-amount').val());
                discount += parseFloat($('#ordereditor-shipping-discount-amount').val());
                discount -= parseFloat($("input[name*='[discount_amount]']:visible").val());
                $('#ordereditor-new-total-discount span').text(discount.toFixed(2));

                var grandTotal = 0;
                grandTotal += parseFloat($('#ordereditor-subtotal-amount').val());
                grandTotal += parseFloat($('#ordereditor-subtotal-tax-amount').val());
                grandTotal += parseFloat($('#ordereditor-shipping-amount').val());
                grandTotal += parseFloat($('#ordereditor-shipping-tax-amount').val());
                grandTotal += parseFloat($('#ordereditor-discount-tax-compensation-amount').val());
                grandTotal += parseFloat($('#ordereditor-new-total-discount span').text());
                if ($('#ordereditor-new-total-giftcardaccount span').length) {
                    grandTotal += parseFloat($('#ordereditor-new-total-giftcardaccount span').text());
                }
                if ($('#ordereditor-new-total-store-credit span').length) {
                    grandTotal -= parseFloat($('#ordereditor-new-total-store-credit span').text());
                }
                $('#ordereditor-new-total-grand_total span').text(grandTotal.toFixed(2));

                $('#ordereditor_new_totals').show();
            }
        });

        return $.mage.mageworxOrderEditorShipping;
    }
);
