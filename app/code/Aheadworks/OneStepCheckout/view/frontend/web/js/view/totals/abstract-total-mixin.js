/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    ['underscore'],
    function (_) {
        'use strict';

        return function (abstractTotal) {

            /**
             * @inheritdoc
             */
            return _.extend(abstractTotal, {
                isFullMode: function () {
                    return !!this.getTotals();
                }
            });
        }
    }
);
