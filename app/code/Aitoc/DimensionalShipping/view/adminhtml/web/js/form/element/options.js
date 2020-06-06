define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, select, modal) {
    'use strict';

    return select.extend({

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            var field1 = uiRegistry.get('index = select_box');
            var field2 = uiRegistry.get('index = pack_separately');
            if (value == 1) {
                field1.show();
                field2.disabled(true);
                field2.value(0);
            } else {
                field1.hide();
                field2.disabled(false);
            }

            return this._super();
        },
    });
});