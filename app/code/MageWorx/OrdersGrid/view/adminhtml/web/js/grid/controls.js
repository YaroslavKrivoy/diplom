define([
    'ko',
    'underscore',
    'mageUtils',
    'uiLayout',
    'Magento_Ui/js/grid/controls/columns',
    'jquery',
    'jquery/ui'
], function (ko, _, utils, layout, uiColumns, $) {
    'use strict';

    return uiColumns.extend({
        defaults: {
            selectedTab: 'unassigned',
            template: 'MageWorx_OrdersGrid/grid/controls',
            _tabs: [],
            _productCols: [],
            imports: {
                addTabs: '${ $.name }:tabsData',
            },
            clientConfig: {
                component: 'Magento_Ui/js/grid/editing/client',
                name: '${ $.name }_client'
            },
            listens: {
                '${ $.provider }:reloaded': 'gridReloaded'
            },
            modules: {
                client: '${ $.clientConfig.name }',
                source: '${ $.provider }'
            },
        },

        initialize: function () {

            this._super();

            layout([this.clientConfig]);

            return this;
        },

        initObservable: function () {
            this._super()
                .track(['selectedTab'])
                .observe({
                    tabs: [],
                    productCols: []
                });

            return this;
        },

        /**
         * Work
         *
         * @param tabs
         */
        addTabs: function (tabs) {
            _.map(tabs, function (value, key) {
                return utils.insert({
                    key: key,
                    value: value,
                    _parent: this,
                    visible: this.isVisibleTab
                }, this._tabs);
            }.bind(this));

            this._tabs = this._tabs.reverse();
            this.tabs(this._tabs);
        },

        hasSelected: function (tabKey) {
            return this.selectedTab == tabKey;
        },

        isVisibleTab: function () {
            return this._parent.getColumns(this.key).length > 0;
        },

        getTabs: function () {
            return this.tabs.filter('visible');
        },

        getColumns: function (tab) {
            return this.elems.filter(function (column) {
                var valid = false;
                if (tab == 'unassigned' && (!column.tab || typeof column.tab === 'undefined')) {
                    valid = true;
                } else if (column.tab && typeof column.tab !== 'undefined' && column.tab == tab) {
                    valid = true;
                }

                return valid;
            });
        },

        countVisible: function () {
            return this.elems.filter('visible').length;
        },

        getHeaderMessage: function () {
            return utils.template(this.templates.headerMsg, {
                visible: this.countVisible(),
                total: this.elems().length
            });
        },

        initElement: function (el) {
            el.track(['label'])
        },

        cancel: function () {
            $('#column-controls-button').trigger('click');
            return this;
        }
    });
});
