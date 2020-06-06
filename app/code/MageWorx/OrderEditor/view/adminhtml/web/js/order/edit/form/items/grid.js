/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define(
    [
        "jquery",
        "jquery/ui",
        "Magento_Catalog/catalog/product/composite/configure",
        'Magento_Sales/order/create/scripts'
    ],
    function ($) {
        'use strict';

        $.widget('mage.mageworxOrderEditorItemsGrid', {
            params: {
                searchButtonId: '#order-items-add-products',
                searchCancelId: '#ordereditor-cancel-add-products',
                searchUpdateId: '#ordereditor-apply-add-products',

                removeQuoteItemButton: '.remove_quote_item',

                searchGridUrl:       '',
                addProductsUrl:      '',
                magentoLoadBlockUrl: '',
                removeQuoteItemUrl:  '',

                blockId: '#order-items_grid',
                formBlockId: '#order-items_grid'
            },

            init: function (params) {
                this._initParams(params);

                this.initSearchAction();
                this.initCancelAction();
                this.initAddAction();
                this.initRemoveNewQuoteItems();
            },

            _initParams: function (params) {
                var self = this;
                params = params || {};
                $.each(params, function (i, e) {
                    self.params[i] = e;
                });
            },

            initSearchAction: function () {
                var self = this;
                $(document).off('click', this.params.searchButtonId);
                $(document).on('click', this.params.searchButtonId, function (e) {
                    e.preventDefault();
                    self.initOrderAdmin();
                    self.searchProducts();
                });
            },

            initCancelAction: function () {
                var self = this;
                $(document).off('click', this.params.searchCancelId);
                $(document).on('click', this.params.searchCancelId, function (e) {
                    e.preventDefault();
                    self.removeProductsGrid();
                });
            },

            initAddAction: function () {
                var self = this;
                $(document).off('click', this.params.searchUpdateId);
                $(document).on('click', this.params.searchUpdateId, function (e) {
                    e.preventDefault();
                    self.productGridAddSelected();
                });
            },

            initRemoveNewQuoteItems: function () {
                var self = this;
                $(document).off('change', this.params.removeQuoteItemButton);
                $(document).on('change', this.params.removeQuoteItemButton, (function (e) {
                    e.preventDefault();
                    self.removeNewQuoteItem(this);
                }));
            },

            removeNewQuoteItem: function (item) {
                var id = $(item).attr('data-item-id');
                var self = this;

                if ($(item).hasClass('remove_quote_item_error')) {
                    self.removeQuoteItemSuccessHandler(id);
                    return;
                }

                if ($(item).val() != 'remove') {
                    return;
                }

                $.ajax({
                    url: this.params.removeQuoteItemUrl,
                    data: {
                        'form_key':FORM_KEY,
                        'id':id
                    },
                    type: 'post',
                    dataType: 'json',
                    context: this,
                    beforeSend: function () {
                        self.showIsLoading();
                    }
                })
                    .done(function (response) {
                        if (response.error || response.status == false) {
                            self.ajaxResponseErrorHandler(response);
                        } else {
                            self.removeQuoteItemSuccessHandler(id);
                        }
                    })
                    .fail(function (error) {
                        self.ajaxResponseErrorHandler(error);
                    });
            },

            removeQuoteItemSuccessHandler: function (id) {
                $(this.params.formBlockId + ' tr[data-item-id="' + id + '"]').remove();
                $(this.params.formBlockId + ' tr[data-parent-id="' + id + '"]').remove();
                this.hideIsLoading();
                this.updateNewTotals();
            },

            initOrderAdmin: function () {
                var config = {};
                var magentoLoadBlockUrl = this.params.magentoLoadBlockUrl;

                var order = new AdminOrder(config);
                order.setLoadBaseUrl(magentoLoadBlockUrl);

                window.order = order;
            },

            searchProducts: function () {
                var self = this;

                $.ajax({
                    url: this.params.searchGridUrl,
                    data: {'form_key':FORM_KEY},
                    type: 'post',
                    dataType: 'json',
                    context: this,
                    beforeSend: function () {
                        self.showIsLoading();
                    }
                })
                    .done(function (response) {
                        if (response.error || response.status == false) {
                            self.ajaxResponseErrorHandler(response);
                        } else {
                            self.searchProductsSuccessHandler(response);
                        }
                    })
                    .fail(function (error) {
                        self.ajaxResponseErrorHandler(error);
                    });
            },

            ajaxResponseErrorHandler: function (response) {
                var message = typeof(response.error) == "string" ? response.error : 'Error! Can not get response.';
                this.showErrorMessage(message);
                console.log(JSON.stringify(response));
                this.hideIsLoading();
            },

            showErrorMessage: function (message) {
                //alert(message);
            },

            searchProductsSuccessHandler: function (response) {
                $('#ordereditor-search-products').remove();
                $(this.params.blockId).append('<div id="ordereditor-search-products">' + response.search_grid + '</div>');

                this.hideIsLoading();
                $('#ordereditor-items-actions-block').hide();
            },

            productGridAddSelected: function () {
                var orderId = this.getCurrentOrderId();
                var listType = 'product_to_add';

                // prepare fields
                var fieldsPrepare = {};
                var itemsFilter = [];
                var products = order.gridProducts.toObject();

                if (Object.getOwnPropertyNames(products).length === 0) {
                    return;
                }

                for (var productId in products) {
                    itemsFilter.push(productId);
                    var paramKey = 'item['+productId+']';
                    for (var productParamKey in products[productId]) {
                        paramKey += '['+productParamKey+']';
                        fieldsPrepare[paramKey] = products[productId][productParamKey];
                    }
                }
                fieldsPrepare.order_id = orderId;
                fieldsPrepare.form_key = FORM_KEY;

                // create fields
                var fields = [];
                for (var name in fieldsPrepare) {
                    fields.push(new Element('input', {type:'hidden', name:name, value:fieldsPrepare[name]}));
                }
                productConfigure.addFields(fields);

                // filter items
                if (itemsFilter) {
                    productConfigure.addItemsFilter(listType, itemsFilter);
                }

                // prepare and do submit
                var self = this;

                productConfigure.addListType(listType, {urlSubmit: self.params.addProductsUrl});
                productConfigure.setOnLoadIFrameCallback(listType, function (response) {
                    self.loadAreaResponseHandler(response);
                }.bind(this));
                productConfigure.submit(listType);
            },

            loadAreaResponseHandler: function (response) {
                if (response) {
                    if (response.status == true) {
                        $('#order-items_grid').find('.order-tables').append(response.result);
                        this.updateNewTotals();
                    } else {
                        var errorMessage = response.error ? response.error : '';
                        this.showErrorMessage(errorMessage);
                    }
                } else {
                    console.log('Can not get response.');
                }

                this.removeProductsGrid();
            },

            updateNewTotals: function () {
                $('#order-items_grid').find('input.qty_input').each(function () {
                    if (!$(this).hasClass('cancelled')) {
                        $(this).change();
                    }
                });
            },

            removeProductsGrid: function () {
                $('#ordereditor-search-products').remove();
                $('#ordereditor-items-actions-block').show();
            },

            showIsLoading: function () {
                $('body').trigger('processStart');
            },

            hideIsLoading: function () {
                $('body').trigger('processStop');
            },

            getCurrentOrderId: function () {
                var VRegExp = new RegExp(/order_id\/([0-9]+)/);
                var VResult = window.location.href.match(VRegExp);
                return VResult[1];
            }
        });

        return $.mage.mageworxOrderEditorItemsGrid;
    }
);