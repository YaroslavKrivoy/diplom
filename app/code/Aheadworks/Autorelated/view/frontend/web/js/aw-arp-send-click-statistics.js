/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Initialization widget for redirect
 *
 * @method click()
 */
define([
    'jquery',
    'jquery/ui'
], function($) {
    "use strict";

    $.widget('mage.awArpSendClickStatistics', {
        options: {
            ruleId: null,
            url: '/'
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this.element.on('click', $.proxy(this.click, this));
            this.element.on('mousedown', $.proxy(this.mouseDown, this));
        },

        /**
         * Mouse down event
         *
         * @param {Object} e
         */
        mouseDown: function (e) {
            if (e.which === 2 || e.which === 3) {
                this.click();
            }
        },

        /**
         * Send statistics after click
         */
        click: function () {
            var ruleId = this.options.ruleId, data;

            if (ruleId && navigator.sendBeacon) {
                data = new FormData();
                data.append('awarp_rule', ruleId);
                data.append('ajax', true);

                navigator.sendBeacon(this.options.url, data);
            }
        },
    });

    return $.mage.awArpSendClickStatistics;
});
