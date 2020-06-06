/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'underscore',
    'Magento_Ui/js/grid/listing'
], function (_, Listing) {
    'use strict';

    return Listing.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/ui/grid/listing',
            imports: {
                totals: '${ $.provider }:data.totals'
            },
            dndConfig: {
                component: 'Aheadworks_OneStepCheckout/js/ui/grid/dnd'
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .track({
                    totals: []
                });

            return this;
        }
    });
});
