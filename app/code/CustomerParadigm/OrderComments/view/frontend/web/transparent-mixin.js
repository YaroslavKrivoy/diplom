/**
 * @copyright 2018 Customer Paradigm. See COPYING.txt for license details.
 * @version 1.0.2
 * @since 1.0.2
 */
define(
    [
        'jquery',
        'underscore',
        'mage/utils/wrapper',
        'mage/url',
        'Magento_Ui/js/modal/alert',
        'jquery/ui'
    ],
    /**
     *
     * @param $
     * @param _
     * @param wrapper
     * @param url
     * @param quote
     * @param alert
     * @returns {Function}
     */
    function ($, _, wrapper, url, alert) {
        'use strict';
        /**
         * Prepare the override method for _orderSave on transparent.js
         * @type {{_orderSave: _orderSave}}
         */
        var transparentHook = {
            _orderSave: function (data) {
                var param = {
                    'quoteid': $('#quote-id').val(),
                    'comment': $('#comment-code').val()
                };
                $.ajax({
                    showLoader: false,
                    url: url.build('ordercomments'),
                    data: param,
                    type: "POST",
                    dataType: 'json'
                }).fail(function (request, status, error) {
                    alert({
                        title: 'Error',
                        content: error,
                        actions: {
                            always: function (){}
                        }
                    });
                });
                // Call the original _orderSave method
                this._super(data);
            }
        };

        /** Magento_Payment/transparent */
        return function (target) {
            // 'Extend' the new method onto the widgets
            $.widget('mage.transparent', target, transparentHook);
            return $.mage.transparent;
        };
    }
);