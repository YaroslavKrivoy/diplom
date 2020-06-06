/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Initialization custom select component
 *
 * @method onUpdate()
 * @method isNeedToShowWvtavFunctionalityAlert()
 * @method isWvtavConditionTypeValueSelected()
 * @method showWvtavFunctionalityAlert()
 * @method unselectWvtavConditionTypeValue()
 * @method disableAndAndRestoreToDefaultValue()
 * @method saveCurrentValueBeforeRestoringToDefault()
 * @method restoreToDefault()
 * @method enableAndRestoreToCurrentValueIfNeeded()
 * @method isNeedToRestoreToCurrentValue()
 * @method restoreToCurrentValue()
 */
define([
    'jquery',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/alert'
], function ($, select, alert) {
    'use strict';

    return select.extend({
        defaults: {
            currentValue: null,
            isWvtavFunctionalityEnabled: false,
            wvtavConditionTypeValue: null,
            wvtavFunctionalityAlertContent: '',
        },

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            if (this.isNeedToShowWvtavFunctionalityAlert()) {
                this.showWvtavFunctionalityAlert();
            }
            this._super();
        },

        /**
         * Check if need to show alert, related to WVTAV functionality
         *
         * @returns {boolean|*}
         */
        isNeedToShowWvtavFunctionalityAlert: function () {
            return ((this.isWvtavFunctionalityEnabled === false)
                && (this.isWvtavConditionTypeValueSelected())
            );
        },

        /**
         * Check if WVTAV-related value selected
         *
         * @returns {boolean}
         */
        isWvtavConditionTypeValueSelected: function () {
            return (String(this.value()) === String(this.wvtavConditionTypeValue));
        },

        /**
         * Show alert, related to WVTAV functionality
         */
        showWvtavFunctionalityAlert: function () {
            alert({
                content: this.wvtavFunctionalityAlertContent,
                actions: {
                    always: function(){
                        this.unselectWvtavConditionTypeValue();
                    }.bind(this),
                },
            });
        },

        /**
         * Unselect WVTAV-related value
         */
        unselectWvtavConditionTypeValue: function () {
            if (this.isWvtavConditionTypeValueSelected()) {
                this.restoreToDefault();
            }
        },

        /**
         * Disable select and switch value to the default one
         *
         * @returns {Object} Chainable.
         */
        disableAndAndRestoreToDefaultValue: function () {
            this.saveCurrentValueBeforeRestoringToDefault();
            this.restoreToDefault();
            this.disable();
            return this;
        },

        /**
         * Save current value before restoring select to default one
         */
        saveCurrentValueBeforeRestoringToDefault: function () {
            this.currentValue = this.value();
        },

        /**
         * Restore select to default value without focusing on the control
         */
        restoreToDefault: function () {
            this.value(this.default);
        },

        /**
         * Enable select and restore to value before hiding
         *
         * @returns {Object} Chainable.
         */
        enableAndRestoreToCurrentValueIfNeeded: function () {
            if (this.isNeedToRestoreToCurrentValue()) {
                this.restoreToCurrentValue();
            }
            this.enable();
            return this;
        },

        /**
         * Check if need to restore select to the value before hiding
         */
        isNeedToRestoreToCurrentValue: function () {
            return (this.currentValue !== null);
        },

        /**
         * Restore select to the value before hiding and
         * clear that field to prevent unnecessary further restoring
         */
        restoreToCurrentValue: function () {
            this.value(this.currentValue);
            this.currentValue = null;
        }
    });
});
