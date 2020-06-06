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
            bodyTmpl: 'MageWorx_OrdersGrid/grid/cells/comaSeparated'
        },

        getSrc: function (row) {
            if (!row || !row[this.index]) {
                return [];
            }

            return row[this.index].split(',');
        },

        getText: function (text) {
            return text;
        }
    });
});
