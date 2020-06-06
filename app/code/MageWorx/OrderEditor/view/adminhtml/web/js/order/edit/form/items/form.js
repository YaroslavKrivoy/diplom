/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        "jquery",
        "jquery/ui"
    ],
    function ($) {
        'use strict';

        $.widget('mage.mageworxOrderEditorItemsForm', {

            params: {
                calcCatalogPricesInclTax:     0,
                calcShippingPricesInclTax:    0,
                taxCalculationMethod:         0, /* unit_price, row_total, total */
                taxCalculationBasedOn:        0, /* shipping address, billing address, shipping origin */
                applyTaxAfterDiscount:        0,
                applyDiscountOnPricesInclTax: 0,

                configureQuoteItemsUrl: '',
                configureConfirmUrl: ''
            },

            itemActionDropdown: '.item-action-dropdown',

            CALC_TAX_BEFORE_DISCOUNT_ON_EXCL: '0_0',
            CALC_TAX_BEFORE_DISCOUNT_ON_INCL: '0_1',
            CALC_TAX_AFTER_DISCOUNT_ON_EXCL:  '1_0',
            CALC_TAX_AFTER_DISCOUNT_ON_INCL:  '1_1',

            init: function (params) {
                this.initParams(params);

                this.validateNumber();
                this.onInputChange();
                this.onActionChange();
                this.onClickConfigureButton();
                this.initUnavailableItems();
            },

            initParams: function (params) {
                var self = this;
                params = params || {};
                $.each(params, function (i, e) {
                    self.params[i] = e;
                });
            },

            validateNumber:function () {
                $('input.validate-number').on('keypress', function (e) {
                    if (e.which == 13 || e.which == 8) {
                        return 1;
                    }
                    var letters = '1234567890.,+-';
                    return (letters.indexOf(String.fromCharCode(e.which)) != -1);
                });
            },

            initUnavailableItems:function () {
                var self = this;
                $('#order-items_grid').find('input.qty_input').each(function () {
                    if ($(this).hasClass('cancelled')) {
                        var id = $(this).attr('data-item-id') || null;
                        var row_item = $('#order-items_grid tr[data-item-id="' + id + '"]');
                        row_item.find('input[type=text]').attr('readonly', 'readonly').addClass('cancelled');
                    }
                });
            },

            onInputChange:function () {
                var self = this;
                $(document).on('change', 'input.mw-order-editor-order-item', function () {
                    self.updateInputValues(this);
                });
            },

            updateInputValues:function (item) {
                var id = this.getInputId(item);
                var name = this.getInputName(item);
                var input = this.getInputs(id);

                //this.readProductStockQty(input);

                switch (name) {
                    case "price":
                        this.calculatePriceExclTax(input);
                        this.calculateSubtotal(input);
                        break;
                    case "price_incl_tax":
                        this.calculatePriceInclTax(input);
                        this.calculateSubtotal(input);
                        break;
                    case "fact_qty":
                        this.checkFactQty(input);
                        this.calculateSubtotal(input);
                        break;
                    case "tax_amount":
                        break;
                    case "tax_percent":
                        this.changePrice(input);
                        this.calculateSubtotal(input);
                        break;
                    case "discount_amount":
                        this.checkDiscountAmount(input);
                        break;
                    case "discount_percent":
                        this.checkDiscountPercent(input);
                        this.checkDiscountAmount(input);
                        break;
                }

                this.baseCalculation(input);
                this.calculateRowTotal(input);

                this.updateBundleProduct(input, name, id);

                if (input.parent.val()) {
                    var parentId = input.parent.val();
                    var bundleItems = this.getBundleItems(parentId);
                    this.calculateBundleTotals(bundleItems, parentId);
                }

                this.calculateNewTotals();
            },

            updateBundleProduct: function (input, name, id) {
                var bundleItems = this.getBundleItems(id);
                if (Object.keys(bundleItems).length == 0) {
                    return;
                }

                if (name == "fact_qty") {
                    var bundle = this.getInputs(id);
                    var bundleQty = parseFloat(bundle.fact_qty.val());
                    var self = this;

                    $.each(bundleItems, function (i, input) {
                        var qtyItemInBundle = parseFloat(input.qty_item_in_bundle.val());
                        input.fact_qty.val(bundleQty * qtyItemInBundle).change();
                        //self.updateQtyInBundle(input, bundle);
                    });
                }
            },

            getInputId: function (item) {
                var reg = /item\[(\w+)\]\[(\w+)\]/i;
                var attr_name = reg.exec($(item).attr('name'));
                return attr_name[1];
            },
            getInputName: function (item) {
                var reg = /item\[(\w+)\]\[(\w+)\]/i;
                var attr_name = reg.exec($(item).attr('name'));
                return attr_name[2];
            },
            getInputs: function (id) {
                var fields = [
                'price',
                'price_incl_tax',
                'subtotal',
                'subtotal_incl_tax',
                'tax_amount',
                'weee_tax_applied_row_amount',
                'discount_tax_compensation_amount',
                'tax_percent',
                'discount_amount',
                'discount_percent',
                'row_total',
                'qty_item_in_bundle',
                'fact_qty',
                'parent',
                'product_id'
                ];

                var inputs = {
                    "item_id" : id
                };

                $.each(fields, function (i, name) {
                    inputs[name] = $("input[name='item[" + id + "]["+ name +"]']");
                });
                inputs['remove'] = $("select[name='item[" + id + "][action]']");

                return inputs;
            },

            checkFactQty: function (item) {
                var qty_value = this.getInputValue(item.fact_qty, 1);
                //todo stock check
                if (qty_value <= 0) {
                    qty_value = 1;
                }
                $(item.fact_qty).val(qty_value);
            },

            calculatePriceExclTax: function (input) {
                var price_excl_tax = this.getInputValue(input.price, 0.0);
                var tax_percent = this.getInputValue(input.tax_percent, 0.0);
                var price = price_excl_tax * (1 + tax_percent / 100);
                input.price.val(price_excl_tax.toFixed(2));
                input.price_incl_tax.val(price.toFixed(2));
                input.tax_percent.val(tax_percent.toFixed(2));
            },

            calculatePriceInclTax: function (input) {
                var price_incl_tax = this.getInputValue(input.price_incl_tax, 0.0);
                var tax_percent = this.getInputValue(input.tax_percent, 0.0);
                var price = price_incl_tax / (1 + tax_percent / 100);
                input.price.val(price.toFixed(2));
                input.price_incl_tax.val(price_incl_tax.toFixed(2));
                input.tax_percent.val(tax_percent.toFixed(2));
            },

            changePrice: function (input) {
                if (this.params.calcCatalogPricesInclTax) {
                    this.calculatePriceInclTax(input);
                } else {
                    this.calculatePriceExclTax(input);
                }
            },

            checkDiscountPercent: function (input) {
                var discount_percent = this.getInputValue(input.discount_percent, 0.0);
                if (isNaN(discount_percent) || !discount_percent) {
                    input.discount_amount.val(0.0);
                }
            },

            checkDiscountAmount: function (input) {
                var discount_amount = parseFloat(input.discount_amount.val());
                if (discount_amount > input.subtotal.val()) {
                    input.discount_amount.val(input.subtotal.val());
                }
                var discount_percent = this.getInputValue(input.discount_percent, 0.0);
                if (discount_percent > 100) {
                    discount_percent = 100.0;
                    input.discount_percent.val(100.0);
                }
                if (isNaN(discount_percent) || !discount_percent) {
                    input.discount_percent.val(0.0);
                    input.discount_amount.removeClass('disabled');
                } else {
                    input.discount_amount.addClass('disabled');
                }
                if (isNaN(discount_amount)) {
                    input.discount_amount.val(0.0);
                }
                $(input.discount_percent).val(discount_percent);
            },

            calculateSubtotal: function (input) {
                var subtotal = parseFloat(input.price.val()) * parseFloat(input.fact_qty.val());
                var subtotal_incl_tax = parseFloat(input.price_incl_tax.val()) * parseFloat(input.fact_qty.val());
                input.subtotal.val(subtotal.toFixed(2));
                input.subtotal_incl_tax.val(subtotal_incl_tax.toFixed(2));
            },

            calculateBundleTotals: function (bundle_items, bundle_id) {
                if (!bundle_items[Object.keys(bundle_items)[0]].price.val()) {
                    return false;
                }

                var total_price_tax_incl = 0;
                var total_price_tax_excl = 0;
                var total_subtotal_tax_incl = 0;
                var total_subtotal_tax_excl = 0;
                var total_tax_amount = 0;
                var total_discount_amount = 0;
                var row_total = 0;
                var bundle = this.getInputs(bundle_id);
                var self = this;

                var bundle_qty = parseFloat(bundle.fact_qty.val());
                $.each(bundle_items, function (i, input) {
                    /* item was removed */
                    if (input.remove.value == "remove") {
                        return true;
                    }
                    var qty = parseFloat(input.fact_qty.val()) / bundle_qty;
                    total_price_tax_incl += parseFloat(input.price_incl_tax.val()) * qty;
                    total_price_tax_excl += parseFloat(input.price.val()) * qty;
                    total_subtotal_tax_incl += parseFloat(input.subtotal_incl_tax.val());
                    total_subtotal_tax_excl += parseFloat(input.subtotal.val());
                    total_tax_amount += parseFloat(input.tax_amount.val());
                    total_discount_amount += parseFloat(input.discount_amount.val());
                    row_total += parseFloat(input.row_total.val());
                });

                bundle.price_incl_tax.val(total_price_tax_incl.toFixed(2));
                bundle.price.val(total_price_tax_excl.toFixed(2));
                bundle.subtotal_incl_tax.val(total_subtotal_tax_incl.toFixed(2));
                bundle.subtotal.val(total_subtotal_tax_excl.toFixed(2));
                bundle.tax_amount.val(total_tax_amount.toFixed(2));
                bundle.discount_amount.val(total_discount_amount.toFixed(2));
                bundle.row_total.val(row_total.toFixed(2));

                return true;
            },

            getInputValue: function (item, defaultValue) {
                var val = $(item).val();
                val = parseFloat(val);
                if (isNaN(val)) {
                    return defaultValue;
                }
                return val;
            },

            baseCalculation: function (input) {
                switch (this.params.taxCalculationMethod) {
                    case 'UNIT_BASE_CALCULATION':
                        this.unitBaseCalculation(input);
                        break;
                    case 'ROW_BASE_CALCULATION':
                        this.rowBaseCalculation(input);
                        break;
                    case 'TOTAL_BASE_CALCULATION':
                        this.totalBaseCalculation(input);
                        break;
                }
            },

            unitBaseCalculation: function (input) {
                var price;
                var tax_amount = 0;
                var discount_tax_compensation_amount = 0;
                var unitTax = 0;
                var unitTaxDiscount = 0;
                var qty;
                var discountAmount;
                var discountRate;

                switch (this.getCalculationSequence()) {
                    case this.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
                        tax_amount = this.calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                        this.calculateDiscountAmount(input, input.subtotal.val());
                        break;
                    case this.CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                        tax_amount = this.calcTaxAmount(input.subtotal_incl_tax.val(), input.tax_percent.val(), 1);
                        this.calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                        break;

                    case this.CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
                        this.calculateDiscountAmount(input, input.subtotal.val());

                        qty = parseFloat(input.fact_qty.val());
                        discountAmount = parseFloat(input.discount_amount.val()) / qty;
                        price = parseFloat(input.price_incl_tax.val());

                        if (this.params.calcCatalogPricesInclTax) {
                            unitTax = this.calcTaxAmount(price, input.tax_percent.val(), 1);
                            discountRate = (unitTax / price) * 100;
                            unitTaxDiscount = this.calcTaxAmount(discountAmount, discountRate, 0);  /*1*/
                            discount_tax_compensation_amount = this.calcTaxAmount(discountAmount, input.tax_percent.val(), 1);
                        } else {
                            price = parseFloat(input.price.val());
                            unitTax = this.calcTaxAmount(price, input.tax_percent.val(), 0);
                            unitTaxDiscount = this.calcTaxAmount(discountAmount, input.tax_percent.val(), 0);
                        }

                        unitTax = Math.max(unitTax - unitTaxDiscount, 0);
                        tax_amount = Math.max(qty * unitTax, 0);
                        discount_tax_compensation_amount = Math.max(qty * discount_tax_compensation_amount, 0);
                        break;

                    case this.CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                        this.calculateDiscountAmount(input, input.subtotal_incl_tax.val());

                        qty = parseFloat(input.fact_qty.val());
                        discountAmount = parseFloat(input.discount_amount.val()) / qty;
                        price = parseFloat(input.price_incl_tax.val());

                        if (this.params.calcCatalogPricesInclTax) {
                            unitTax = this.calcTaxAmount(price, input.tax_percent.val(), 1);
                            discountRate = (unitTax / price) * 100;
                            unitTaxDiscount = this.calcTaxAmount(discountAmount, discountRate, 0); /*1*/
                            discount_tax_compensation_amount = this.calcTaxAmount(discountAmount, input.tax_percent.val(), 1);
                        } else {
                            price = parseFloat(input.price.val());
                            unitTax = this.calcTaxAmount(price, input.tax_percent.val(), 0);
                            unitTaxDiscount = this.calcTaxAmount(discountAmount, input.tax_percent.val(), 0);
                        }

                        unitTax = Math.max(unitTax - unitTaxDiscount, 0);
                        tax_amount = Math.max(qty * unitTax, 0);
                        discount_tax_compensation_amount = Math.max(qty * discount_tax_compensation_amount, 0);
                        break;
                }

                input.tax_amount.val(tax_amount.toFixed(2));
                input.discount_tax_compensation_amount.val(discount_tax_compensation_amount.toFixed(2));
            },

            rowBaseCalculation: function (input) {
                var tax_amount = 0;
                var discount_tax_compensation_amount = 0;
                switch (this.getCalculationSequence()) {
                    case this.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
                        tax_amount = this.calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                        this.calculateDiscountAmount(input, input.subtotal.val());
                        break;

                    case this.CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                        tax_amount = this.calcTaxAmount(input.subtotal_incl_tax.val(), input.tax_percent.val(), 1);
                        this.calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                        break;

                    case this.CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
                        this.calculateDiscountAmount(input, input.subtotal.val());
                        if (this.params.calcCatalogPricesInclTax) {
                            discount_tax_compensation_amount = this.calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 1);
                            tax_amount = this.calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                            tax_amount -= discount_tax_compensation_amount;
                        } else {
                            tax_amount = this.calcTaxAmount(input.subtotal.val() - input.discount_amount.val(), input.tax_percent.val(), 0);
                        }
                        break;

                    case this.CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                        this.calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                        if (this.params.calcCatalogPricesInclTax) {
                            discount_tax_compensation_amount = this.calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 1);
                            tax_amount = this.calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                            tax_amount -= discount_tax_compensation_amount;
                        } else {
                            tax_amount = this.calcTaxAmount(input.subtotal.val() - input.discount_amount.val(), input.tax_percent.val(), 0);
                        }
                        break;
                }

                input.tax_amount.val(tax_amount.toFixed(2));
                input.discount_tax_compensation_amount.val(discount_tax_compensation_amount.toFixed(2));
            },

            totalBaseCalculation: function (input) {
                var tax_amount = 0;
                var price = 0;
                var discount_tax_compensation_amount = 0;

                switch (this.getCalculationSequence()) {
                    case this.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
                        tax_amount = this.calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                        this.calculateDiscountAmount(input, input.subtotal.val());
                        break;

                    case this.CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                        tax_amount = this.calcTaxAmount(input.subtotal_incl_tax.val(), input.tax_percent.val(), 1);
                        this.calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                        break;

                    case this.CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
                        this.calculateDiscountAmount(input, input.subtotal.val());
                        discount_tax_compensation_amount = this.calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 0);

                        price = input.subtotal_incl_tax.val() - input.discount_amount.val();
                        if (!this.params.calcCatalogPricesInclTax) {
                            price -= discount_tax_compensation_amount;
                            discount_tax_compensation_amount = 0;
                        }

                        tax_amount = this.calcTaxAmount(price, input.tax_percent.val(), 1);
                        break;

                    case this.CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                        this.calculateDiscountAmount(input, input.subtotal_incl_tax.val());

                        price = input.subtotal.val() - input.discount_amount.val();
                        if (this.params.calcCatalogPricesInclTax) {
                            discount_tax_compensation_amount = this.calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 1);
                            price += discount_tax_compensation_amount;
                        } else {
                            discount_tax_compensation_amount = 0;
                        }

                        tax_amount = this.calcTaxAmount(price, input.tax_percent.val(), 0);
                        break;
                }

                input.tax_amount.val(tax_amount.toFixed(2));
                input.discount_tax_compensation_amount.val(discount_tax_compensation_amount.toFixed(2));
            },

            getBundleItems: function (bundle_id) {
                var children = {};
                var self = this;
                $(".has-parent-" + bundle_id).each(function () {
                    var item_id = $(this).attr('data-item-id');
                    if (item_id != bundle_id) {
                        children[item_id] = self.getInputs(item_id);
                    }
                });
                return children;
            },

            getCalculationSequence: function () {
                if (this.params.applyTaxAfterDiscount) {
                    if (this.params.applyDiscountOnPricesInclTax) {
                        return this.CALC_TAX_AFTER_DISCOUNT_ON_INCL;
                    } else {
                        return this.CALC_TAX_AFTER_DISCOUNT_ON_EXCL;
                    }
                } else {
                    if (this.params.applyDiscountOnPricesInclTax) {
                        return this.CALC_TAX_BEFORE_DISCOUNT_ON_INCL
                    } else {
                        return this.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL;
                    }
                }
            },

            calculateDiscountAmount: function (input, subtotal) {
                var discount_percent = parseFloat(input.discount_percent.val());
                var discount_amount = 0;

                if (discount_percent != 0) {
                    discount_amount = subtotal * discount_percent / 100;
                } else {
                    discount_amount = parseFloat(input.discount_amount.val());
                    discount_percent = 0;
                }

                input.discount_amount.val(discount_amount.toFixed(2));
                input.discount_percent.val(discount_percent.toFixed(2));
            },

            calcTaxAmount: function (price, tax_percent, priceIncludeTax) {
                var tax_rate = parseFloat(tax_percent) / 100;
                price = parseFloat(price);

                if (priceIncludeTax) {
                    return price * (1 - 1 / (1 + tax_rate));
                } else {
                    return price * tax_rate;
                }
            },

            calculateRowTotal: function (input) {
                var subtotal = parseFloat(input.subtotal.val());
                var discount_amount = parseFloat(input.discount_amount.val());
                var tax_amount = parseFloat(input.tax_amount.val());
                var discount_tax_compensation_amount = parseFloat(input.discount_tax_compensation_amount.val());
                var weee_tax_applied_row_amount = parseFloat(input.weee_tax_applied_row_amount.val());

                var row_total = subtotal + tax_amount + discount_tax_compensation_amount + weee_tax_applied_row_amount - discount_amount;

                input.row_total.val(row_total.toFixed(2));
                return row_total;
            },

            calculateNewTotals: function () {
                var subtotal = 0;
                var subtotalInclTax = 0;
                $("input[name*='[subtotal]']").each(function () {
                    if ($(this).closest('tr').hasClass('has-parent') || $(this).closest('tr').hasClass('removed_item')) {
                        return;
                    }
                    subtotal += parseFloat($(this).val());
                });
                $('#ordereditor-new-total-subtotal span').text(subtotal.toFixed(2));

                $("input[name*='[subtotal_incl_tax]']").each(function () {
                    if ($(this).closest('tr').hasClass('has-parent') || $(this).closest('tr').hasClass('removed_item')) {
                        return;
                    }
                    subtotalInclTax += parseFloat($(this).val());
                });
                $('#ordereditor-new-total-subtotal-incl-tax span').text(subtotalInclTax.toFixed(2));

                var shipping = 0;
                shipping += parseFloat($('#ordereditor-shipping-amount').val());
                $('#ordereditor-new-total-shipping-shipping span').text(shipping.toFixed(2));

                var shippingInclTax = 0;
                shippingInclTax += parseFloat($('#ordereditor-shipping-amount').val()) + parseFloat($('#ordereditor-shipping-tax-amount').val());
                $('#ordereditor-new-total-shipping-shipping-incl-tax span').text(shippingInclTax.toFixed(2));

                var tax = 0;
                $("input[name*='[tax_amount]']").each(function () {
                    if ($(this).closest('tr').hasClass('has-parent') || $(this).closest('tr').hasClass('removed_item')) {
                        return;
                    }
                    tax += parseFloat($(this).val());
                });
                tax += parseFloat($('#ordereditor-shipping-tax-amount').val());
                $('#ordereditor-new-total-tax span').text(tax.toFixed(2));


                // Discount applied to shipping should be collected separately
                var discount = 0;
                discount += parseFloat($("#ordereditor-shipping-discount-amount").val());
                $("input[name*='[discount_amount]']").each(function () {
                    if ($(this).closest('tr').hasClass('has-parent') || $(this).closest('tr').hasClass('removed_item')) {
                        return;
                    }
                    discount += parseFloat($(this).val());
                });
                $('#ordereditor-new-total-discount span').text(discount.toFixed(2));
                
                var grandTotal = 0;
                $("input[name*='[row_total]']").each(function () {
                    if ($(this).closest('tr').hasClass('has-parent') || $(this).closest('tr').hasClass('removed_item')) {
                        return;
                    }
                    grandTotal += parseFloat($(this).val());
                });

                // Discount applied to shipping should be collected separately
                grandTotal += parseFloat($('#ordereditor-new-total-shipping-shipping span').text() - $("#ordereditor-shipping-discount-amount").val());
                grandTotal += parseFloat($('#ordereditor-shipping-tax-amount').val());

                if ($('#ordereditor-new-total-giftcardaccount span').length) {
                    grandTotal += parseFloat($('#ordereditor-new-total-giftcardaccount span').text());
                }

                if ($('#ordereditor-new-total-store-credit span').length) {
                    grandTotal -= parseFloat($('#ordereditor-new-total-store-credit span').text());
                }

                $('#ordereditor-new-total-grand_total span').text(grandTotal.toFixed(2));
                $('#ordereditor_new_totals').show();
            },

            onClickConfigureButton: function () {
                var self = this;
                $(document).off('click', '.configure-order-item');
                $(document).on('click', '.configure-order-item', function () {
                    var id = $(this).data('order-item-id');
                    self.showQuoteItemConfiguration(id);
                });
            },

            showQuoteItemConfiguration: function (itemId) {
                productConfigure.blockMsgError.innerHTML = '';
                productConfigure.blockMsg.hide();
                var self = this;

                if (window.ProductConfigure) {
                    productConfigure.addListType('order_items', {
                        urlFetch: self.params.configureQuoteItemsUrl,
                        urlConfirm: self.params.configureConfirmUrl
                    });
                }

                var listType = 'order_items';
                var qtyElement = $('#order-items_grid input[name="item\['+itemId+'\]\[fact_qty\]"]')[0];

                productConfigure.setShowWindowCallback(listType, function (response) {
                    var formCurrentQty = productConfigure.getCurrentFormQtyElement();
                    if (formCurrentQty && qtyElement && !isNaN(qtyElement.value)) {
                        formCurrentQty.value = qtyElement.value;
                    }
                    $('.loading-mask').hide();
                }.bind(this));

                productConfigure.setOnLoadIFrameCallback(listType, function (response) {
                    $('.loading-mask').hide();

                    if (!response.ok) {
                        return;
                    }
                    var itemId = response.item_id;

                    $('.item_name_' + itemId).html(response.name);
                    $('.item_sku_' + itemId).html(response.sku);
                    $('.item_options_' + itemId).html(response.options_html);
                    $('input[name="item[' + itemId + '][price]"]').val(response.price).change();
                    $('input[name="item[' + itemId + '][product_options]"]').val(response.product_options);
                    $('input[name="item[' + itemId + '][sku]"]').val(response.sku);

                    var confirmedCurrentQty = productConfigure.getCurrentFormQtyElement();
                    if (qtyElement && confirmedCurrentQty && !isNaN(confirmedCurrentQty.value)) {
                        if (response.stock) {
                            $.each(response.stock, function (data, val) {
                                $(qtyElement).attr(data, val);
                            });
                        }

                        qtyElement.value = confirmedCurrentQty.value;
                        $(qtyElement).change();
                    }
                }.bind(this));

                productConfigure.showItemConfiguration(listType, itemId);
            },

            onActionChange: function () {
                var self = this;
                $(self.itemActionDropdown).on('change', function () {
                    self.removeItemRow(this);
                    self.calculateNewTotals();
                });
            },

            removeItemRow: function (action) {
                var parent_id = $(action).attr('data-parent-id') || null;
                var id = $(action).attr('data-item-id') || null;
                var remove = true;

                if ($(action).val() == 'remove') {
                    remove = this.disabledRow(id, parent_id);
                } else {
                    remove = this.enabledRow(id, parent_id);
                }

                if (parent_id && remove) {
                    var bundle_items = this.getBundleItems(parent_id);
                    if (!this.isRemoveAllBundleItems(bundle_items, parent_id)) {
                        this.calculateBundleTotals(bundle_items, parent_id);
                    }
                }
            },

            disabledRow: function (row_id, parent_id) {
                var row_item = $('#order-items_grid tr[data-item-id="' + row_id + '"]');
                row_item.addClass('removed_item');
                row_item.find('input[type=text]').attr('disabled', 'disabled');

                /* for bundle product */
                $('.item-action-dropdown[data-parent-id="' + row_id + '"]').each(function () {
                    $(this).val("remove");
                    $(this).hide();
                    $(this).click(this.deactivator);
                });
                $('tr.has-parent-' + row_id).addClass('removed_item');
                $('tr.has-parent-' + row_id + ' input[type=text]').attr('disabled', 'disabled');

                return true;
            },

            enabledRow: function (row_id, parent_id) {
                if (parent_id && $('#action_dropdown_' + parent_id).val() == 'remove') {
                    return false;
                }

                var row_item = $('#order-items_grid tr[data-item-id="' + row_id + '"]');
                row_item.removeClass('removed_item');
                row_item.find('input[type=text]').removeAttr('disabled');

                /* for bundle product */
                $('.item-action-dropdown[data-parent-id="' + row_id + '"]').each(function () {
                    $(this).val("");
                    $(this).show();
                    $(this).unbind('click', this.deactivator);
                });
                $('tr.has-parent-' + row_id).removeClass('removed_item');
                $('tr.has-parent-' + row_id + ' input[type=text]').removeAttr('disabled');

                return true;
            },

            isRemoveAllBundleItems: function (bundle_items, bundle_id) {
                var count_removed_items = 0;
                $.each(bundle_items, function (i, input) {
                    if (input.remove.val() == 'remove') {
                        count_removed_items++;
                    }
                });

                /* checked all bundle items */
                if (count_removed_items == Object.keys(bundle_items).length) {
                    $('.item-action-dropdown[data-parent-id="' + bundle_id + '"]').val("");
                    this.calculateBundleTotals(bundle_items, bundle_id);
                    $('select[name="item[' + bundle_id + '][action]"]').val("remove");
                    this.disabledRow(bundle_id, null);
                    return true;
                }

                return false;
            },

            deactivator: function (event) {
                event.preventDefault();
            }
        });

        return $.mage.mageworxOrderEditorItemsForm;
    }
);
