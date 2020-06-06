/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'mage/utils/wrapper',
    'Aheadworks_OneStepCheckout/js/action/get-sections-details',
    'Magento_Checkout/js/model/shipping-service',
    'Aheadworks_OneStepCheckout/js/model/payment-methods-service',
    'Aheadworks_OneStepCheckout/js/model/totals-service'
], function (
    $,
    wrapper,
    getSectionsDetailsAction,
    shippingService,
    paymentMethodsService,
    totalsService
) {
    'use strict';

    return function (getPaymentInformationAction) {
        return wrapper.wrap(getPaymentInformationAction, function (originalAction, deferred, messageContainer) {
            deferred = deferred || $.Deferred();

            shippingService.isLoading(true);
            paymentMethodsService.isLoading(true);
            totalsService.isLoading(true);

            return getSectionsDetailsAction(
                ['shippingMethods', 'paymentMethods', 'totals'],
                false,
                messageContainer
            ).done(function () {
                deferred.resolve();
            }).fail(function () {
                deferred.reject();
            }).always(function () {
                shippingService.isLoading(false);
                paymentMethodsService.isLoading(false);
                totalsService.isLoading(false);
            });
        });
    };
});
