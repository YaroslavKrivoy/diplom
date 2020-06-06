/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Initialization widget to upload html content by Ajax
 *
 * @method ajax(placeholders)
 * @method replacePlaceholder(placeholder, html)
 */
define([
    'jquery',
    'jquery/ui'
], function($) {
    "use strict";

    $.widget('mage.awArpAjax', {
        options: {
            url: '/',
            dataPattern: 'aw-arp-block-rule-id'
        },

        /**
         * Initialize widget
         */
        _create: function () {
            var placeholders = $('[data-' + this.options.dataPattern + ']');

            if (placeholders && placeholders.length) {
                this.ajax(placeholders);
            }
        },

        /**
         * Send AJAX request
         * @param {Object} placeholders
         */
        ajax: function (placeholders) {
            var self = this,
                data = {
                    blocks: []
                };

            placeholders.each(function() {
                data.blocks.push($(this).data(self.options.dataPattern));
            });
            data.blocks = JSON.stringify(data.blocks.sort());
            $.ajax({
                url: this.options.url,
                data: data,
                type: 'GET',
                cache: false,
                dataType: 'json',
                context: this,

                /**
                 * Response handler
                 * @param {Object} response
                 */
                success: function (response) {
                }
            });
        }
    });

    return $.mage.awArpAjax;
});
