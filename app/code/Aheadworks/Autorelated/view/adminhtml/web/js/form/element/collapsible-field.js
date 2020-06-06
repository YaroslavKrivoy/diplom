/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            modules: {
                storeViewOptions: '${ $.parentName }.title_store_values'
            }
        },

        /**
         * Toggle visibility of dynamic rows with store view values
         */
        toggleVisibility: function() {
            var isVisible = this.storeViewOptions().visible();

            this.storeViewOptions().visible(!isVisible);
        }
    });
});
