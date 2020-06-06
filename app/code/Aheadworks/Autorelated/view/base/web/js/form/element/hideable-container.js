/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            visible: true
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe('visible');

            return this;
        },

        /**
         * Show element.
         *
         * @returns {Object} Chainable.
         */
        show: function () {
            this.visible(true);

            return this;
        },

        /**
         * Hide element.
         *
         * @returns {Object} Chainable.
         */
        hide: function () {
            this.visible(false);

            return this;
        },

        /**
         * Checks if element has message setting
         *
         * @returns {Boolean}
         */
        hasMessage: function () {
            return this.message && this.message.template;
        },
    });
});