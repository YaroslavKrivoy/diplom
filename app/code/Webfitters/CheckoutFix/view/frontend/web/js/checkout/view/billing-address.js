/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-billing-address',
    'Webfitters_CheckoutFix/js/model/billing-address/form-popup-state',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/action/set-billing-address',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate',
    'Magento_Checkout/js/view/shipping'
],
function (
    $,
    ko,
    _,
    Component,
    customer,
    addressList,
    quote,
    createBillingAddress,
    formPopUpState,
    selectBillingAddress,
    checkoutData,
    checkoutDataResolver,
    customerData,
    setBillingAddressAction,
    modal,
    globalMessageList,
    navi,
    $t,
    shipping
) {
    'use strict';

    var popUp = null;
    var countryData = customerData.get('directory-data');
    var lastSelectedBillingAddress = null,
        newAddressOption = {
            /**
             * Get new address label
             * @returns {String}
             */
            getAddressInline: function () {
                return $t('New Address');
            },
            customerAddressId: null
        },
        countryData = customerData.get('directory-data'),
        addressOptions = addressList().filter(function (address) {
            return address.getType() == 'customer-address'; //eslint-disable-line eqeqeq
        });

    //addressOptions.push(newAddressOption);

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/billing-address',
            billingMethodItemTemplate: 'Webfitters_CheckoutFix/checkout/address/billing-default',
            billingFormTemplate: 'Webfitters_CheckoutFix/checkout/form/billing-address-popup',
            billingFormEditTemplate: 'Webfitters_CheckoutFix/checkout/form/billing-address-edit-popup',
            navigator: navi,
            popUpForm: {
                element: '#opc-new-billing-address',
                options: {
                    buttons: {
                        save: {},
                        cancel: {}
                    }
                }
            }
        },
        onStep: ko.observable(false),
        currentBillingAddress: quote.billingAddress,
        addressOptions: ko.observable(addressOptions),
        customerHasAddresses: addressOptions.length > 1,
        isFormPopUpVisible: formPopUpState.isVisible,
        isFormInline: addressOptions.length === 0,
        isNewAddressAdded: ko.observable(false),
        /**
         * Init component
         */
        initialize: function () {
            this._super();
            var t = this;
            var addresses = this.addressOptions();
            for(var i = 0; i < addresses.length; i++){
                addresses[i] = t.buildAddressObject(addresses[i]);
            }
            
            this.addressOptions(addresses);
            this.isFormPopUpVisible.subscribe(function (value) {
                if (value) {
                    t.getPopUp().openModal();
                }
            });
            this.navigator.currentStep.subscribe(function(){
                if(t.navigator.currentStep() == 'shipping' || quote.isVirtual()){
                    t.onStep(true);
                } else {
                    t.onStep(false);
                }
            });
            t.onStep(this.navigator.currentStep() == 'shipping' || quote.isVirtual());
            checkoutDataResolver.resolveBillingAddress();
            quote.paymentMethod.subscribe(function () {
                checkoutDataResolver.resolveBillingAddress();
            }, this);
            t.updateAddress(false);
            registry.async('checkoutProvider')(function (checkoutProvider) {
                var billingAddressData = checkoutData.getBillingAddressFromData();
                if (billingAddressData) {
                    checkoutProvider.set(
                        'billingAddress',
                        $.extend(true, {}, checkoutProvider.get('billingAddress'), billingAddressData)
                    );
                }
                checkoutProvider.on('billingAddress', function (billingAddrData) {
                    checkoutData.setBillingAddressFromData(billingAddrData);
                });
            });
        },

        addFieldListeners: function(){
            var t = this;
            $(document).on('change', '#billing-address-inline select, #billing-address-inline input', function(event){
                t.updateAddress(false);
            });
            this.source.on('billingAddress.data.validate', function(){
                t.updateAddress();
                //return false;
            });
        },

        /**
         * @return {exports.initObservable}
         */
        initObservable: function () {
            this._super().observe({
                    selectedAddress: null,
                    isAddressDetailsVisible: false/*quote.billingAddress() != null*/,
                    isAddressFormVisible: !customer.isLoggedIn() || addressOptions.length === 1,
                    isAddressSameAsShipping: false,
                    saveInAddressBook: 1
            });
            quote.billingAddress.subscribe(function (newAddress) {
                /*if(shipping.isAddressSameAsBilling()){
                   quote.shippingAddress(newAddress);
                }*/
                /*if (quote.isVirtual()) {
                    this.isAddressSameAsShipping(false);
                } else {
                    this.isAddressSameAsShipping(
                        newAddress != null &&
                        newAddress.getCacheKey() == quote.shippingAddress().getCacheKey() //eslint-disable-line eqeqeq
                    );
                }*/
                if (newAddress != null && newAddress.saveInAddressBook !== undefined) {
                    this.saveInAddressBook(newAddress.saveInAddressBook);
                } else {
                    this.saveInAddressBook(1);
                }
                var addresses = this.addressOptions();
                for(var i = 0; i < addresses.length; i++){
                    if(newAddress.getKey() == addresses[i].getKey()){
                        addresses[i].isSelected(true);
                    } else {
                        addresses[i].isSelected(false);
                    }
                }
                //this.isAddressDetailsVisible(true);
            }, this);

            return this;
        },

        canUseShippingAddress: ko.computed(function () {
            return !quote.isVirtual() && quote.shippingAddress() && quote.shippingAddress().canUseForBilling();
        }),

        /**
         * @param {Object} address
         * @return {*}
         */
        addressOptionsText: function (address) {
            return address.getAddressInline();
        },

        /**
         * @return {Boolean}
         */
        useShippingAddress: function () {
            if (this.isAddressSameAsShipping()) {
                selectBillingAddress(quote.shippingAddress());

                this.updateAddresses();
                //this.isAddressDetailsVisible(true);
            } else {
                lastSelectedBillingAddress = quote.billingAddress();
                quote.billingAddress(null);
                //this.isAddressDetailsVisible(false);
            }
            checkoutData.setSelectedBillingAddress(null);

            return true;
        },

        /**
         * Update address action
         */
        updateAddress: function (validate) {
            if(typeof(validate) === 'undefined' || validate == null){
                validate = true;
            }
            var addressData, newBillingAddress;
            if (this.selectedAddress() && this.selectedAddress() != newAddressOption) { //eslint-disable-line eqeqeq
                selectBillingAddress(this.selectedAddress());
                checkoutData.setSelectedBillingAddress(this.selectedAddress().getKey());
            } else {
                this.source.set('params.invalid', false);
                if(validate){
                    this.source.trigger(this.dataScopePrefix + '.data.validate');

                    if (this.source.get(this.dataScopePrefix + '.custom_attributes')) {
                        this.source.trigger(this.dataScopePrefix + '.custom_attributes.data.validate');
                    }
                }
                if (!this.source.get('params.invalid')) {
                    addressData = this.source.get(this.dataScopePrefix);

                    if (customer.isLoggedIn() && !this.customerHasAddresses) { //eslint-disable-line max-depth
                        this.saveInAddressBook(1);
                    }
                    if(typeof(addressData) !== 'undefined' && addressData != null){
                        addressData['save_in_address_book'] = this.saveInAddressBook() ? 1 : 0;
                    }
                    newBillingAddress = createBillingAddress(addressData);

                    // New address must be selected as a billing address
                    selectBillingAddress(newBillingAddress);
                    checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                    checkoutData.setNewCustomerBillingAddress(addressData);
                }
            }
            if(validate){
                this.updateAddresses();
            }
        },

        /**
         * Edit address action
         */
        editAddress: function () {
            lastSelectedBillingAddress = quote.billingAddress();
            quote.billingAddress(null);
            //this.isAddressDetailsVisible(false);
        },

        /**
         * Cancel address edit action
         */
        cancelAddressEdit: function () {
            this.restoreBillingAddress();

            if (quote.billingAddress()) {
                // restore 'Same As Shipping' checkbox state
                this.isAddressSameAsShipping(
                    quote.billingAddress() != null &&
                        quote.billingAddress().getCacheKey() == quote.shippingAddress().getCacheKey() && //eslint-disable-line
                        !quote.isVirtual()
                );
               // this.isAddressDetailsVisible(true);
            }
        },

        /**
         * Restore billing address
         */
        restoreBillingAddress: function () {
            if (lastSelectedBillingAddress != null) {
                selectBillingAddress(lastSelectedBillingAddress);
            }
        },

        /**
         * @param {Object} address
         */
        onAddressChange: function (address) {
            //this.isAddressFormVisible(address == newAddressOption); //eslint-disable-line eqeqeq
        },

        /**
         * @param {Number} countryId
         * @return {*}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        },

        /**
         * Trigger action to update shipping and billing addresses
         */
        updateAddresses: function () {
            if (window.checkoutConfig.reloadOnBillingAddress ||
                !window.checkoutConfig.displayBillingOnPaymentMethod
            ) {
                setBillingAddressAction(globalMessageList);
            }
        },

        getPopUp: function () {
            var self = this,
                buttons;

            if (!popUp) {
                buttons = this.popUpForm.options.buttons;
                this.popUpForm.options.buttons = [
                    {
                        text: buttons.save.text ? buttons.save.text : $t('Save Address'),
                        class: buttons.save.class ? buttons.save.class : 'action primary action-save-address',
                        click: self.saveNewAddress.bind(self)
                    },
                    {
                        text: buttons.cancel.text ? buttons.cancel.text : $t('Cancel'),
                        class: buttons.cancel.class ? buttons.cancel.class : 'action secondary action-hide-popup',

                        /** @inheritdoc */
                        click: this.onClosePopUp.bind(this)
                    }
                ];

                /** @inheritdoc */
                this.popUpForm.options.closed = function () {
                    self.isFormPopUpVisible(false);
                };

                this.popUpForm.options.modalCloseBtnHandler = this.onClosePopUp.bind(this);
                this.popUpForm.options.keyEventHandlers = {
                    escapeKey: this.onClosePopUp.bind(this)
                };
                
                /** @inheritdoc */
                this.popUpForm.options.opened = function () {
                    // Store temporary address for revert action in case when user click cancel action
                    self.temporaryAddress = $.extend(true, {}, checkoutData.getBillingAddressFromData());
                };
                popUp = modal(this.popUpForm.options, $(this.popUpForm.element));

            }

            return popUp;
        },

        saveNewAddress: function () {
            var addressData,
                newBillingAddress;
            var t = this;
            //console.log(this.source);
            this.source.set('params.invalid', false);
            this.source.trigger(this.dataScopePrefix + '.data.validate');
            
            if (this.source.get(this.dataScopePrefix + '.custom_attributes')) {
                this.source.trigger(this.dataScopePrefix + '.custom_attributes.data.validate');
            }
            
            if (!this.source.get('params.invalid')) {
                addressData = this.source.get(this.dataScopePrefix);
                
                if (customer.isLoggedIn() && !this.customerHasAddresses) { //eslint-disable-line max-depth
                    this.saveInAddressBook(1);
                }
                addressData['save_in_address_book'] = this.saveInAddressBook() ? 1 : 0;
                newBillingAddress = createBillingAddress(addressData); 
                var obAddress = t.buildAddressObject(newBillingAddress);
                obAddress.isEditable(true);
                var addresses = this.addressOptions();
                addresses.push(obAddress);
                this.addressOptions(addresses);
                //this.addressOptions(addressOptions);
                // New address must be selected as a billing address
                selectBillingAddress(newBillingAddress);
                checkoutData.setSelectedBillingAddress(newBillingAddress.getKey());
                checkoutData.setNewCustomerBillingAddress(addressData);
                
                this.getPopUp().closeModal();
                this.isNewAddressAdded(true);
                this.updateAddresses();
            }
            
        },

        buildAddressObject: function(address){
            var t = this;
            var a = address;
            address.isFormPopUpVisible = ko.observable(false);
            address.isSelected = ko.observable(false);
            address.isEditable = ko.observable(false);
            address.isCustomerLoggedIn = ko.observable(t.isCustomerLoggedIn);
            address.customerHasAddresses = ko.observable(t.customerHasAddresses);
            address.popUpForm = ko.observable({
                options: {
                    buttons: {
                        save: {},
                        cancel: {}
                    }
                }
            });
            address.isFormPopUpVisible.subscribe(function(value){
                if (value) {
                    a.getPopUp().openModal();
                }
            });
            address.getPopUp = function(){
                var self = this, buttons;
                if (!self.popUp) {
                    var popupForm = this.popUpForm();
                    buttons = popupForm.options.buttons;
                    popupForm.options.buttons = [{
                        text: buttons.save.text ? buttons.save.text : $t('Save Address'),
                        class: buttons.save.class ? buttons.save.class : 'action primary action-save-address',
                        click: self.saveAddress.bind(self)
                    }, {
                        text: buttons.cancel.text ? buttons.cancel.text : $t('Cancel'),
                        class: buttons.cancel.class ? buttons.cancel.class : 'action secondary action-hide-popup',
                        click: this.onClosePopUp.bind(this)
                    }];
                    popupForm.options.closed = function() {
                        self.isFormPopUpVisible(false);
                    };
                    popupForm.options.modalCloseBtnHandler = this.onClosePopUp.bind(this);
                    popupForm.options.keyEventHandlers = {
                        escapeKey: this.onClosePopUp.bind(this)
                    };
                    popupForm.options.opened = function() {
                        self.temporaryAddress = $.extend(true, {}, checkoutData.getBillingAddressFromData());
                    };
                    self.popUp = modal(popupForm.options, $('#opc-billing-address-edit-' + this.customerAddressId));
                }
                return self.popUp;
            };
            address.onClosePopUp = function(){
                //checkoutData.setBillingAddressFromData($.extend(true, {}, this.temporaryAddress));
                this.getPopUp().closeModal();
            };
            address.saveAddress = function(){
                this.getPopUp().closeModal();
                //this.isFormPopUpVisible(false);
            };
            address.getTemplate = function(){
                return t.billingMethodItemTemplate;
            };
            address.getPopupTemplate = function(){
                return t.billingFormEditTemplate;
            };
            address.getCountryName = function (countryId) {
                return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
            };
            address.getRegion = function(region){
                return t.getRegion(region);
            };
            address.editAddress = function(){
                this.isFormPopUpVisible(true);
                var self = this;
                var streets = ['street[0]', 'street[1]', 'street[2]'];
                $('#opc-billing-address-edit-' + this.customerAddressId).find('input, textarea, select').each(function(){
                     var key = $(this).attr('name');
                    if(key == 'region_id'){
                        key = 'regionId';
                    }
                    if(key == 'country_id'){
                        key = 'countryId';
                    }
                    var street = streets.indexOf(key);
                    if(street !== -1){
                        $(this).val(self.street[street]);
                    } else {
                        $(this).val(self[key]);
                    }
                });
            };
            address.selectAddress = function(){
                selectBillingAddress(this);
                checkoutData.setSelectedBillingAddress(this.getKey());
            };
            return address;
        },

        onClosePopUp: function () {
            checkoutData.setBillingAddressFromData($.extend(true, {}, this.temporaryAddress));
            this.getPopUp().closeModal();
        },

        showFormPopUp: function () {
            this.isFormPopUpVisible(true);
        },

        /**
         * Get code
         * @param {Object} parent
         * @returns {String}
         */
        getCode: function (parent) {
            return _.isFunction(parent.getCode) ? parent.getCode() : 'shared';
        }
    });
});
