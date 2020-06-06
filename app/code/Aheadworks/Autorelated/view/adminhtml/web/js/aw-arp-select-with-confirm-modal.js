/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'prototype'
], function ($, confirm, $t) {
    'use strict';

    $.widget('awarp.selectWithConfirmModal', {
        options: {
            selectId: '',
            selectInitialValue: '',
            disablingConfirmTitle: '',
            disablingConfirmMessage: '',
            disablingConfirmOkButtonText: $t('OK'),
            disablingConfirmCancelButtonText: $t('Cancel'),
            enabledValue: '1',
            disabledValue: '0'
        },

        /**
         * @private
         */
        _create: function () {
            this.saveInitialValue();
            this.bindHandlers();
        },

        /**
         * Save initial value of the select element
         */
        saveInitialValue: function () {
            this.selectInitialValue = this.getSelect().val();
        },

        /**
         * Retrieve select, connected to the current widget
         *
         * @returns {*|jQuery|HTMLElement}
         */
        getSelect: function() {
            return ($('#' + this.options.selectId));
        },

        /**
         * Adding necessary event handlers
         */
        bindHandlers: function() {
            var widget = this;
            this.getSelect().change(function() {
                if (widget.isNeedToShowDisablingConfirm(this.value)) {
                    widget.showDisablingConfirm();
                }
            });
        },

        /**
         * Check if need to show disabling confirmation modal
         *
         * @param selectCurrentValue
         * @returns {boolean}
         */
        isNeedToShowDisablingConfirm: function(selectCurrentValue) {
            return (
                (selectCurrentValue === this.options.disabledValue)
                && (selectCurrentValue !== this.selectInitialValue)
            );
        },

        /**
         * Show disabling confirmation modal
         */
        showDisablingConfirm: function() {
            confirm({
                title: this.options.disablingConfirmTitle,
                content: this.options.disablingConfirmMessage,
                actions: {
                    confirm: function(){},
                    cancel: function(){
                        this.cancelDisabling();
                    }.bind(this),
                    always: function(){}
                },
                buttons: [{
                    text: this.options.disablingConfirmCancelButtonText,
                    class: 'action-secondary action-dismiss',
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: this.options.disablingConfirmOkButtonText,
                    class: 'action-primary action-accept',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }]
            });

        },

        /**
         * Processing disabling cancelling
         */
        cancelDisabling: function() {
            this.rollbackSelectValue();
            this.fireEventForElement(this.options.selectId, 'change');
        },

        /**
         * Reset select value to the initial one
         */
        rollbackSelectValue: function() {
            this.getSelect().val(this.options.enabledValue);
        },

        /**
         * Fires event with exact name for the element with specified id
         *
         * @param elementId
         * @param eventName
         * @returns {boolean}
         */
        fireEventForElement: function(elementId, eventName) {
            var element = document.getElementById(elementId);
            if (document.createEventObject) {
                var eventObjectForIE = document.createEventObject();
                return element.fireEvent('on' + event, eventObjectForIE);
            } else {
                var eventObject = document.createEvent("HTMLEvents");
                eventObject.initEvent(eventName, true, true);
                return !element.dispatchEvent(eventObject);
            }
        }

    });

    return $.awarp.selectWithConfirmModal;
});