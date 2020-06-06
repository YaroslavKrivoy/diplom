/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Initialization widget for grid block
 *
 * @method recalculateColumns()
 */
define([
    'jquery',
    'Aheadworks_Autorelated/js/aw-arp-abstract',
    'jquery/ui'
], function($) {
    "use strict";

    $.widget('mage.awArpGrid', $.mage.awArpAbstract, {
        options: {
            columns: 4,
            items: 4,
            rows: 1,
            itemSelector: '[data-aw-arp-block="item"]'
        },

        /**
         * Recalculate the number of columns depending on the width of the screen
         */
        recalculateParams: function()
        {
            var minWidth = 200,
                itemsContainer = this.element.find(this.options.itemsSelector).outerWidth(),
                columns = Math.floor(itemsContainer / minWidth);

            if (columns > this.options.columns) {
                columns = this.options.columns;
            } else if (columns < 1) {
                columns = 1;
            }

            var items = columns * this.options.rows;
            this.element.attr('data-aw-arp-columns', columns);
            this.element.attr('data-aw-arp-items', items);
            $(this.options.itemSelector+':nth-child(n - '+(items)+')', this.element).show();
            $(this.options.itemSelector+':nth-child(n + '+(items+1)+')', this.element).hide();
            this.element.css('visibility', 'visible');
            this.element.css('opacity', '1');
        }
    });

    return $.mage.awArpGrid;
});
