/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'underscore',
    'uiComponent',
    'uiRegistry',
    'mageUtils',
    'Aheadworks_OneStepCheckout/js/model/gift-message-service',
    'Aheadworks_OneStepCheckout/js/action/update-gift-message'
], function (_, Component, registry, utils, giftMessageService, giftMessageAction) {
    'use strict';

    return Component.extend({
        defaults: {
            imports: {
                isActive:'${ $.provider }:giftMessage.config.${ $.level === "order" ? "order" : $.itemId}.enabled'
            },
            listens: {
                '${ $.provider }:data.giftMessageUpdated': 'updateState'
            }
        },
        isLoading: giftMessageService.isLoading,
        cachedMessage: {},

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super()
                .updateState();

            return this;
        },

        /**
         * {@inheritdoc}
         */
        initObservable: function () {
            this._super()
                .observe({
                    isNew: false,
                    isEditable: true,
                    isActive: false
                });

            return this;
        },

        /**
         * Check if module is active
         *
         * @returns {Boolean}
         */
        isActiveModule: function () {
            return this.isActive();
        },

        /**
         * Delete options
         */
        deleteOptions: function () {
            var data = this._getGiftMessageData();

            data = _.mapObject(data, function() {
                return null;
            });

            giftMessageAction(this._getActionItemId(), data);
        },

        /**
         * Submit options
         */
        submitOptions: function () {
            var data = this._getGiftMessageData();

            giftMessageAction(this._getActionItemId(), data);
        },

        /**
         * Edit options action
         */
        editOptions: function () {
            this.isEditable(true);
            this.cachedMessage = utils.copy(this._getGiftMessageData());
        },

        /**
         * Cancel action
         */
        cancel: function () {
            this.isEditable(false);
            this._setGiftMessageData(this.cachedMessage);
        },

        /**
         * Update state after update gift message data
         */
        updateState: function () {
            var data = this._getGiftMessageData();

            if (_.isObject(data) && data['gift_message_id']) {
                this.isNew(false);
            } else {
                this.isNew(true);
            }
            this.isEditable(this.isNew());
        },

        /**
         * Retrieve gift message config data
         *
         * @returns {Mixed}
         * @private
         */
        getConfigData: function (param) {
            var scope = 'giftMessage.config' + (this.level === 'order' ? 'order' : this.itemId) + '.' + param;

            return this.source ? this.source.get(scope) : null;
        },

        /**
         * Retrieve item id for action
         *
         * @returns {Number|String}
         * @private
         */
        _getActionItemId: function () {
            return this.level === 'order' ? 'order' : this.itemId;
        },

        /**
         * Retrieve gift message data
         *
         * @returns {Object|null}
         * @private
         */
        _getGiftMessageData: function () {
            var scope = this.dataScope;

            return this.source.get(scope);
        },

        /**
         * Set gift message data
         *
         * @returns {Object|null}
         * @private
         */
        _setGiftMessageData: function (data) {
            var scope = this.dataScope;

            return this.source.set(scope, data);
        }
    });
});
