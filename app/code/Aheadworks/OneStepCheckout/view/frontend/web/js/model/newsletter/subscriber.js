/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'ko',
        'Magento_Customer/js/model/customer'
    ],
    function (ko, customer) {
        'use strict';

        var newsletterSubscribeConfig = window.checkoutConfig.newsletterSubscribe,
            subscribe = ko.observable(newsletterSubscribeConfig.isChecked),
            isSubscribed = ko.observable(newsletterSubscribeConfig.isSubscribed),
            subscribedStateVerified = ko.observable(customer.isLoggedIn());

        return {
            subscribe: subscribe,
            subscriberEmail: '',
            isSubscribed: isSubscribed,
            subscribedStatusVerified: subscribedStateVerified,
            isAvailableForSubscribe: ko.computed(function () {
                return !isSubscribed() && subscribedStateVerified();
            })
        };
    }
);
