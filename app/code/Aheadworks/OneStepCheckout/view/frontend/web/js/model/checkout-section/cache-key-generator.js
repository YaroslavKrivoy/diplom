/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    ['underscore'],
    function (_) {
        'use strict';

        var stateKeys = {
            shippingAddress: ['countryId'],
            billingAddress: ['countryId'],
            shippingMethod: ['shippingMethod'],
            totals: ['grand_total']
        };

        return {

            /**
             * Generate cache key
             *
             * @param {Object} state
             * @returns {string}
             */
            generateCacheKey: function (state) {
                var cacheKeyData = [];

                _.each(state, function (object, key) {
                    if (stateKeys[key] !== undefined) {
                        _.each(stateKeys[key], function (stateField) {
                            if (_.isObject(object) && object[stateField] !== undefined) {
                                var stateValue = _.isFunction(object[stateField])
                                    ? object[stateField]()
                                    : object[stateField];

                                cacheKeyData.push(stateValue);
                            }
                        });
                    }
                });

                return cacheKeyData.join('-');
            }
        };
    }
);
