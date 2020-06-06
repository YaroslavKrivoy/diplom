/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'mage/storage',
        'Magento_Checkout/js/model/url-builder'
    ],
    function (storage, urlBuilder) {
        'use strict';

        return function (deferred, email) {
            var payload = {email: email},
                serviceUrl = urlBuilder.createUrl('/awOsc/customers/isSubscribedByEmail', {});

            return storage.post(
                serviceUrl,
                JSON.stringify(payload),
                false
            ).done(
                function (isSubscribed) {
                    if (isSubscribed) {
                        deferred.resolve();
                    } else {
                        deferred.reject();
                    }
                }
            ).fail(
                function () {
                    deferred.reject();
                }
            );
        };
    }
);
