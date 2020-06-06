/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'jquery',
        'underscore',
        'ko',
        'Aheadworks_OneStepCheckout/js/view/address-abstract',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Aheadworks_OneStepCheckout/js/model/checkout-data',
        'Magento_Customer/js/model/address-list',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Aheadworks_OneStepCheckout/js/model/shipping-address/new-address-form-state',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'uiRegistry',
        'Aheadworks_OneStepCheckout/js/model/shipping-rate-service'
    ],
    function (
        $,
        _,
        ko,
        Component,
        checkoutDataResolver,
        checkoutData,
        addressList,
        customer,
        quote,
        createShippingAddressAction,
        selectShippingAddressAction,
        newAddressFormState,
        shippingRatesValidator,
        uiRegistry
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/shipping-address',
                scopeId: 'shippingAddress',
                newAddressForm: '[data-role=new-shipping-address-form]',
                isNewAddressAdded: false,
                showNewAddressFormHeader: true
            },
            isCustomerLoggedIn: customer.isLoggedIn,

            /**
             * @inheritdoc
             */
            initialize: function () {
                var hasNewAddress;

                this._super();

                hasNewAddress = addressList.some(function (address) {
                    return address.getType() == 'new-customer-address';
                });
                this.isNewAddressAdded(hasNewAddress);
                newAddressFormState.isShown.subscribe(function (isShown) {
                    if (isShown) {
                        this._openNewShippingAddressForm();
                    } else {
                        this._closeNewShippingAddressForm();
                    }
                }, this);

                return this;
            },

            /**
             * @inheritdoc
             */
            _getCheckoutAddressFormData: function () {
                return checkoutData.getShippingAddressFromData();
            },

            /**
             * @inheritdoc
             */
            _setCheckoutAddressFormData: function (addressData) {
                this._super();
                checkoutData.setShippingAddressFromData(addressData);
            },

            /**
             * @inheritdoc
             */
            _resolveAddress: function () {
                checkoutDataResolver.resolveShippingAddress();
            },

            /**
             * @inheritdoc
             */
            _afterSetInitialAddressFormData: function () {
                uiRegistry.async(this.name + '.shipping-address-fieldset')(function (fieldSet) {
                    if (fieldSet.elems().length > 0) {
                        _.each(fieldSet.elems(), function (fieldRow) {
                            shippingRatesValidator.initFields(fieldRow.name);
                        });
                    }
                    fieldSet.elems.subscribe(function (FieldSetElems) {
                        _.each(FieldSetElems, function (fieldRow) {
                            shippingRatesValidator.initFields(fieldRow.name);
                        });
                    });
                });
            },

            /**
             * On save new address button click event handler
             */
            onSaveNewAddressClick: function () {
                var addressData,
                    newShippingAddress;

                this.source.set('params.invalid', false);
                this.source.trigger('shippingAddress.data.validate');

                if (!this.source.get('params.invalid')) {
                    addressData = this.source.get('shippingAddress');
                    addressData['save_in_address_book'] = this.saveInAddressBook() ? 1 : 0;

                    newShippingAddress = createShippingAddressAction(addressData);
                    selectShippingAddressAction(newShippingAddress);
                    checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                    checkoutData.setNewCustomerShippingAddress(addressData);
                    this._closeNewShippingAddressForm();
                    this.isNewAddressAdded(true);
                }
            },

            /**
             * On cancel button click event event handler
             */
            onCancelClick: function () {
                this._closeNewShippingAddressForm();
            },

            /**
             * Open new address form
             */
            _openNewShippingAddressForm: function () {
                $(this.newAddressForm).collapsible('activate');
                newAddressFormState.isShown(true);
            },

            /**
             * Close new address form
             */
            _closeNewShippingAddressForm: function () {
                $(this.newAddressForm).collapsible('deactivate');
                newAddressFormState.isShown(false);
            },

            /**
             * @inheritdoc
             */
            initObservable: function () {
                var showNewAddressHeader;

                this._super();
                this.observe(['isNewAddressAdded', 'showNewAddressFormHeader']);

                this.isShown = ko.computed(function () {
                    return !quote.isQuoteVirtual();
                });
                this.showForm(addressList().length == 0);
                showNewAddressHeader = !this.isNewAddressAdded() || newAddressFormState.isShown();
                this.showNewAddressFormHeader(showNewAddressHeader);

                return this;
            }
        });
    }
);
