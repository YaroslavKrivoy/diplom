/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'ko',
        'underscore',
        'uiComponent',
        'Aheadworks_OneStepCheckout/js/model/editable-item-options',
        'Magento_Checkout/js/model/totals',
        'uiLayout',
        'mageUtils'
    ],
    function (
        ko,
        _,
        Component,
        editableItemOptions,
        totals,
        layout,
        utils
    ) {
        'use strict';

        var defaultRendererTemplate = {
            parent: '${ $.$data.parentName }',
            name: '${ $.$data.name }',
            displayArea: '${ $.$data.displayArea }',
            component: 'Aheadworks_OneStepCheckout/js/view/sidebar/item-details/options-renderer/default'
        };

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/sidebar/item-details',
                rendererList: []
            },
            rendererComponents: {},
            giftMessageRendererComponents: {},

            /**
             * @inheritdoc
             */
            initialize: function () {
                var self = this;

                this._super();

                totals.getItems().subscribe(function (newItems) {
                    var toRemove = [],
                        rendered = _.keys(self.rendererComponents);

                    _.each(rendered, function (renderedId) {
                        var founded = _.find(newItems, function (item) {
                            return renderedId == item.item_id;
                        });

                        if (!founded) {
                            toRemove.push(renderedId);
                        }
                    });
                    _.each(toRemove, function (removeId) {
                        self._removeOptionsRendererComponent(removeId);
                        self._removeGiftMessageRendererComponent(removeId);
                    });
                });

                return this;
            },

            /**
             * Escape string
             *
             * @param {string} string
             * @returns {string}
             */
            escape: function (string) {
                return String(string)
                    .replace(/&(?!\w+;)/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;');
            },

            /**
             * Create options renderer component
             *
             * @param {string} type
             * @param {number} itemId
             * @param {Object} options
             */
            _createOptionsRendererComponent: function (type, itemId, options) {
                var rendererTemplate,
                    templateData,
                    rendererComponent;

                if (this.rendererComponents[itemId] === undefined) {
                    rendererTemplate = type != 'default' && this.rendererList[type] !== undefined
                        ? utils.extend({}, defaultRendererTemplate, this.rendererList[type])
                        : defaultRendererTemplate;
                    templateData = {
                        parentName: this.name,
                        name: 'options_' + itemId,
                        displayArea: 'options_' + itemId
                    };
                    rendererComponent = utils.template(rendererTemplate, templateData);

                    utils.extend(rendererComponent, {itemId: itemId, options: options});
                    layout([rendererComponent]);
                    this.rendererComponents[itemId] = rendererComponent;
                }
            },

            /**
             * Create gift message renderer component
             *
             * @param {number} itemId
             */
            _createGiftMessageRendererComponent: function (itemId) {
                var giftMessageConfig = {
                        parent: this.name,
                        name: 'gift_message_' + itemId,
                        displayArea: 'gift_message_' + itemId,
                        dataScope: 'giftMessage.' + itemId,
                        itemId: itemId
                    },
                    rendererComponent;

                if (this.giftMessageRendererComponents[itemId] === undefined) {
                    rendererComponent = utils.extend({}, this.giftMessageRendererConfig, giftMessageConfig);

                    layout([rendererComponent]);
                    this.giftMessageRendererComponents[itemId] = rendererComponent;
                }
            },

            /**
             * Remove gift message renderer component
             *
             * @param {Number} itemId
             */
            _removeGiftMessageRendererComponent: function (itemId) {
                var rendererItems = this.getRegion('gift_message_' + itemId);

                _.find(rendererItems(), function (renderer) {
                    renderer.destroy();
                });
                delete this.giftMessageRendererComponents[itemId];
            },

            /**
             * Remove options renderer component
             *
             * @param {Number} itemId
             */
            _removeOptionsRendererComponent: function (itemId) {
                var rendererItems = this.getRegion('options_' + itemId);

                _.find(rendererItems(), function (renderer) {
                    renderer.disposeSubscriptions();
                    renderer.destroy();
                });
                delete this.rendererComponents[itemId];
            },

            /**
             * Get options renderer for item
             *
             * @param {Object} item
             * @returns {ObservableArray}
             */
            getOptionsRenderer: function (item) {
                var itemId = item.item_id,
                    optionsData = editableItemOptions.getConfigOptionsDataByItemId(itemId);

                if (optionsData) {
                    this._createOptionsRendererComponent(optionsData.product_type, itemId, optionsData.options);
                } else {
                    this._createOptionsRendererComponent('default', itemId, JSON.parse(item.options));
                }

                return this.getRegion('options_' + itemId);
            },

            /**
             * Get gift message renderer for item
             *
             * @param {Object} item
             * @returns {ObservableArray}
             */
            getGiftMessageRenderer: function (item) {
                var itemId = item.item_id;

                this._createGiftMessageRendererComponent(itemId);
                return this.getRegion('gift_message_' + itemId);
            }
        });
    }
);
