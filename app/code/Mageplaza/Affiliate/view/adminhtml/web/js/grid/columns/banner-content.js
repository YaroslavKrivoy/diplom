/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Mageplaza_Affiliate/grid/cells/content'
        },
        getLabel: function (record) {
            return record['content_html'];
        }
    });
});
