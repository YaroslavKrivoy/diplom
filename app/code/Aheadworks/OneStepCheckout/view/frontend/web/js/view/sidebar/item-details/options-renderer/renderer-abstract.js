/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'uiComponent'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                options: {},
                itemId: null,
                scopeId: 'productOptions'
            },

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super()
                    .initScopeId();

                return this;
            },

            /**
             * Init scope Id value
             */
            initScopeId: function () {
                if (this.itemId) {
                    this.scopeId = this.scopeId + '.' + this.itemId;
                }
            },

            /**
             * Dispose subscriptions
             */
            disposeSubscriptions: function () {
            }
        });
    }
);
