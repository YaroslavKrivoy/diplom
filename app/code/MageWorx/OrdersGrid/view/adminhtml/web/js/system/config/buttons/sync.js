/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'mage/translate'
], function (jQuery, _, __) {

    function Button(config)
    {
        this.submitUrl = config.submitUrl;

        this.processAction = function () {
            var wrapper = jQuery('#sync_span'),
                params = {},
                base = this,
                result;

            new Ajax.Request(this.submitUrl, {
                parameters: params,
                loaderArea: false,
                asynchronous: true,
                onCreate: function () {
                    wrapper.find('.sync_success').hide();
                    wrapper.find('.sync_error').hide();
                    wrapper.find('.sync_processing').show();
                    jQuery('#sync_message_span').text('');
                },
                onSuccess: function (response) {
                    var responseJSON = response.responseJSON,
                        resultText = '';
                    result = responseJSON;
                    wrapper.find('.sync_processing').hide();
                    if (typeof responseJSON.success == 'undefined' || !responseJSON.success) {
                        resultText = __('Error Message: ') + responseJSON.error_message;
                        wrapper.find('.sync_success').hide();
                        wrapper.find('.sync_error').show();
                    } else {
                        resultText = __('Success');
                        if (typeof responseJSON.message != 'undefined') {
                            resultText = responseJSON.message;
                        }
                        wrapper.find('.sync_error').hide();
                        wrapper.find('.sync_success').show();
                    }
                    jQuery('#sync_message_span').text(resultText);
                }
            });
        }
    }

    return function (config, node) {
        var button = new Button(config);
        node.addEventListener('click', function (e) {
            button.processAction();
        });
    };
});
