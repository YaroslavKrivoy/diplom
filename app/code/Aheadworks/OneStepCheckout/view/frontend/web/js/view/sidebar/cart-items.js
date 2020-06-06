/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'ko',
        'underscore',
        'uiComponent',
        'Magento_Checkout/js/model/totals',
        'Aheadworks_OneStepCheckout/js/action/get-sections-details',
        'Aheadworks_OneStepCheckout/js/model/gift-message-service'
    ],
    function (ko, _, Component, totals, getSectionsDetailsAction, giftMessageService) {
        'use strict';

        /**
         * Initial sorted items
         */
        var initialItems = (totals.getItems())();

        /**
         * Sort cart items according to initial positions
         *
         * @param {Array} items
         */
        function sortItems(items) {
            var sortedItems = [],
                diff = [];

            _.each(items, function (item) {
                var founded = _.find(initialItems, function (initItem) {
                    return item.item_id == initItem.item_id;
                });

                if (founded === undefined) {
                    diff.push(item);
                }
            });
            _.each(initialItems, function (initialItem) {
                var candidate = _.find(items, function (newItem) {
                    return initialItem.item_id == newItem.item_id;
                });

                if (candidate !== undefined) {
                    sortedItems.push(candidate);
                } else if (diff.length > 0) {
                    candidate = diff.pop();
                    sortedItems.push(candidate);
                }
            });
            if (diff.length > 0) {
                _.each(diff, function (item) {
                    sortedItems.push(item);
                });
            }
            initialItems = sortedItems;

            return sortedItems;
        }

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/sidebar/cart-items',
                items: sortItems(initialItems),
                itemsQty: parseFloat(totals.totals().items_qty),
                isExpanded: window.checkoutConfig.isMiniCartExpanded
            },

            /**
             * @inheritdoc
             */
            initialize: function () {
                var self = this;

                this._super();
                totals.getItems().subscribe(function (newItems) {
                    self.items(sortItems(newItems));
                });
                totals.totals.subscribe(function (newTotals) {
                    self.itemsQty(parseFloat(newTotals.items_qty));
                });
                giftMessageService.isLoading(true);
                getSectionsDetailsAction(['giftMessage']).always(function () {
                    giftMessageService.isLoading(false);
                });
            },

            /**
             * @inheritdoc
             */
            initObservable: function () {
                this._super();
                this.observe(['items','itemsQty']);

                return this;
            }
        });
    }
);
