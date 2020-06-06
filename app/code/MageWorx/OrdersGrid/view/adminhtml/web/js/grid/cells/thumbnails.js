/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'MageWorx_OrdersGrid/grid/cells/thumbnails',
            fieldClass: {
                'data-grid-thumbnail-cell': true
            }
        },
        getSrc: function (row, pid) {
            return row['product_thumbnails'][pid]['src']
        },
        getAlt: function (row, pid) {
            return row['product_thumbnails'][pid]['src']
        },
        getThumbData: function (row) {
            if (!row || !row['product_thumbnails']) {
                return [];
            }

            return row['product_thumbnails'];
        }
    });
});
