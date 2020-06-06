/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'underscore',
    'uiRegistry',
    'mageUtils',
    'uiComponent',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
    'mage/url'
], function (_, registry, utils, uiComponent, confirm, alert, $t, url) {
    'use strict';

    /**
     * Adds additional mass-actions
     */
    return uiComponent.extend({
        defaults: {
            template: 'MageWorx_OrdersGrid/grid/select-massactions',
            stickyTmpl: 'ui/grid/sticky/actions',
            selectProvider: 'ns = ${ $.ns }, index = ids',
            actions: [],
            noItemsMsg: $t('You haven\'t selected any items!'),
            modules: {
                selections: '${ $.selectProvider }'
            }
        },

        /**
         * Initializes observable properties.
         *
         * @returns {exports}
         */
        initObservable: function () {
            this._super()
                .observe('actions')
                .observe('selectedValue');

            this.actions().unshift({
                'component': 'uiComponent',
                'label': $t('Please Select'),
                'title': $t('Please Select'),
                'type': ''
            });

            return this;
        },

        /**
         * Applies specified action.
         *
         * @param actionIndex
         * @returns {exports}
         */
        applyAction: function (actionIndex) {
            if (typeof actionIndex === 'undefined' || !actionIndex) {
                return this;
            }

            try {
                var data = this.getSelections(),
                    action,
                    callback;

                if (!data.total) {
                    alert({
                        content: this.noItemsMsg
                    });
                    this.selectedValue('');

                    return this;
                }

                action = this.getAction(actionIndex);
                if (!action.type) {
                    return this;
                }
                callback = this._getCallback(action, data);

                action.confirm ?
                    this._confirm(action, callback) :
                    callback();
            } catch (e) {
                console.log(e);
            }

            return this;
        },

        apply: function (model, args) {
            return true;
        },

        /**
         * Retrieves selections data from the selections provider.
         *
         * @returns {*|Array|{excluded, selected, total, excludeMode, params}|Object|Undefined}
         */
        getSelections: function () {
            return this.selections() && this.selections().getSelections();
        },

        /**
         * Retrieves action object associated with a specified index.
         *
         * @param actionIndex
         * @returns {*}
         */
        getAction: function (actionIndex) {
            var foundAction;
            _.map(this.actions(), function (action) {
                if (foundAction) {
                    return;
                }
                if (typeof action.actions != 'undefined') {
                    _.map(action.actions, function (subAction) {
                        if (foundAction) {
                            return;
                        }
                        if (subAction.type == actionIndex) {
                            foundAction = subAction;
                        }
                    });
                    if (foundAction) {
                        return foundAction;
                    }
                }
                if (action.type == actionIndex) {
                    foundAction = action;
                }
            });

            return foundAction;
        },

        /**
         * Adds new action. If action with a specified identifier
         * already exists, than the original one will be overwritten.
         *
         * @param action
         * @returns {exports}
         */
        addAction: function (action) {
            var actions = this.actions(),
                index = _.findIndex(actions, {
                    type: action.type
                });

            ~index ?
                actions[index] = action :
                actions.push(action);

            this.actions(actions);

            return this;
        },

        /**
         * Creates action callback based on its' data. If action doesn't spicify
         * a callback function than the default one will be used.
         *
         * @param action
         * @param selections
         * @returns {Function}
         * @private
         */
        _getCallback: function (action, selections) {
            var callback = action.callback,
                args = [action, selections];

            if (utils.isObject(callback)) {
                args.unshift(callback.target);

                callback = registry.async(callback.provider);
            } else if (typeof callback != 'function') {
                callback = this.defaultCallback.bind(this);
            }

            return function () {
                callback.apply(null, args);
            };
        },

        /**
         * Default action callback. Sends selections data
         * via POST request.
         *
         * @param action
         * @param data
         */
        defaultCallback: function (action, data) {
            var itemsType = data.excludeMode ? 'excluded' : 'selected',
                selections = {};

            selections[itemsType] = data[itemsType];

            if (!selections[itemsType].length) {
                selections[itemsType] = false;
            }

            _.extend(selections, data.params || {});

            utils.submit({
                url: action.url,
                data: selections
            });
        },

        /**
         * Shows actions' confirmation window.
         *
         * @param action
         * @param callback
         * @private
         */
        _confirm: function (action, callback) {
            var confirmData = action.confirm;

            confirm({
                title: confirmData.title,
                content: confirmData.message,
                actions: {
                    confirm: callback
                },
                buttons: [{
                    text: $t('Close'),
                    class: 'action-secondary action-dismiss',
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: $t('OK'),
                    class: 'action-primary action-accept',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }]
            });
        }
    });
});
