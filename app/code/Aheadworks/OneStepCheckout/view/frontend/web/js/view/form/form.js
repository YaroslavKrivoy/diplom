/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'jquery',
        'Magento_Ui/js/form/form'
    ],
    function (
        $,
        Component
    ) {
        'use strict';

        return Component.extend({

            /**
             * Scroll to invalid block on form
             *
             * @returns {Object}
             */
            scrollInvalid: function () {
                if (this.invalidBlockSelector && this.source.get('params.invalid')) {
                    $('html, body').animate({ scrollTop: $(this.invalidBlockSelector).offset().top });
                }

                return this;
            },
        });
    }
);