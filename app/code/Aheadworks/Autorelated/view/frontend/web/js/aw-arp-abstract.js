/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Abstract widget for initialization of ARP block
 */
define([
    'jquery',
    'jquery/ui'
], function($) {
    "use strict";

    $.widget('mage.awArpAbstract', {
        options: {
            itemsSelector: '[data-aw-arp-block="items"]'
        },

        /**
         * Widget creation
         */
        _create: function() {
            this.init();
        },

        /**
         * Initialize widget
         */
        init: function()
        {
            this.recalculateParams();
            this.updateWidgetPosition();
            $(window).on('resize', $.proxy(this.onWindowResizeEventListener, this));
        },

        /**
         * Recalculate needed parameters of the widget
         */
        recalculateParams: function() {},

        /**
         * Update container position if needed
         */
        updateWidgetPosition: function()
        {
            var prevElem = this.element.prev();
            if (prevElem && prevElem.attr('class') === 'cart-discount') {
                prevElem.css('margin-bottom','70px');
            }
        },

        onWindowResizeEventListener: function()
        {
            this.recalculateParams();
        }
    });

    return $.mage.awArpAbstract;
});
