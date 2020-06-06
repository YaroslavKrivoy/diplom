/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'ko',
    'Amazon_Payment/js/model/storage',
    'Aheadworks_OneStepCheckout/js/view/shipping-address',
    'Magento_Customer/js/model/address-list'
], function ($, ko, amazonStorage, Component, addressList) {
    'use strict';

    return Component.extend({
        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super();

            if (amazonStorage.isAmazonAccountLoggedIn()) {
                this.showForm(false);
                this.showNewAddressFormHeader(false);
            }

            amazonStorage.isAmazonAccountLoggedIn.subscribe(function (value) {
                if (value) {
                    this.showForm(false);
                    this.showNewAddressFormHeader(false);
                } else {
                    this.showForm(addressList().length == 0);
                    this.showNewAddressFormHeader(true);
                }
            }, this);

            return this;
        }
    });
});
