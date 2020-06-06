/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'MageArray_StorePickup/js/view/storepickup',
    'underscore',
    'Magento_Ui/js/form/form',
    'ko',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/action/set-billing-address',
    'Magento_Checkout/js/view/billing-address',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Ui/js/modal/modal',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/checkout-data',
    'uiRegistry',
    'mage/translate',
    'mage/calendar',
    'Magento_Checkout/js/model/shipping-rate-service',
    'Magento_Ui/js/model/messageList'
], function (
    $,
    storepickup,
    _,
    Component,
    ko,
    customer,
    addressList,
    addressConverter,
    quote,
    createShippingAddress,
    selectShippingAddress,
    shippingRatesValidator,
    formPopUpState,
    shippingService,
    selectShippingMethodAction,
    rateRegistry,
    setShippingInformationAction,
    setBillingAddressAction,
    billing,
    stepNavigator,
    modal,
    checkoutDataResolver,
    checkoutData,
    registry,
    $t,
    date,
    shippingRateService,
    errorProcessor
) {
    'use strict';

    var popUp = null;
    var storeList = $.parseJSON(checkoutConfig.storeList);
    console.log(storeList);
    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/shipping',
            shippingFormTemplate: 'Magento_Checkout/shipping-address/form',
            shippingFormPopupTemplate: 'Webfitters_CheckoutFix/checkout/form/shipping-address-popup',
            shippingMethodListTemplate: 'Magento_Checkout/shipping-address/shipping-method-list',
            shippingMethodItemTemplate: 'Magento_Checkout/shipping-address/shipping-method-item'
        },
        visible: ko.observable(!quote.isVirtual()),
        errorValidationMessage: ko.observable(false),
        isCustomerLoggedIn: customer.isLoggedIn,
        isFormPopUpVisible: formPopUpState.isVisible,
        isFormInline: addressList().length === 0,
        isStorePickupInline: (storeList.length === 1),
        isNewAddressAdded: ko.observable(false),
        saveInAddressBook: 1,
        quoteIsVirtual: quote.isVirtual(),
        isAddressSameAsBilling: ko.observable((addressList().length === 0)?true:false),
        isAddressPickupInStore: ko.observable(false),
        lastSelectedShippingAddres: null,
        addSubscription: null,
        stores: ko.observableArray(storeList),
        store: ko.observable((storeList.length === 1)?storeList[0]:null),
        /**
         * @return {exports}
         */
        initialize: function () {
            var self = this,
                hasNewAddress,
                fieldsetName = 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset';

            this._super();

            if (!quote.isVirtual()) {
                stepNavigator.registerStep(
                    'shipping',
                    '',
                    $t('Shipping'),
                    this.visible, _.bind(this.navigate, this),
                    10
                );
            }
            checkoutDataResolver.resolveShippingAddress();
            this.isAddressPickupInStore.subscribe(function(value){
                if(value){
                    selectShippingMethodAction({
                        carrier_code: 'storepickup',
                        method_code: 'storepickup'
                    });
                    checkoutData.setSelectedShippingRate('storepickup_storepickup');
                    checkoutConfig.quoteData.pickup_store = (self.store())?self.store().storepickup_id:null;
                    var pieces = $('#pickup_date').val().split(' ');
                    checkoutConfig.quoteData.pickup_date = pieces[0];
                    checkoutConfig.quoteData.pickup_time = pieces[1];
                } else {
                    selectShippingMethodAction(null);
                    checkoutData.setSelectedShippingRate(null);
                    checkoutConfig.quoteData.pickup_store = null;
                    checkoutConfig.quoteData.pickup_date = null;
                    checkoutConfig.quoteData.pickup_time = null;

                }
            });
            this.store.subscribe(function(value){
               checkoutConfig.quoteData.pickup_store = (value)?value.storepickup_id:null;
               if(value){
                    var hours = value.working_hours.split('-');
                    $('#pickup_date').datetimepicker({
                        hourMin: parseInt(hours[0]),
                        hourMax: parseInt(hours[1]) + 12
                    });
               }
            });
            hasNewAddress = addressList.some(function (address) {
                return address.getType() == 'new-customer-address'; //eslint-disable-line eqeqeq
            });

            this.isNewAddressAdded(hasNewAddress);

            this.isFormPopUpVisible.subscribe(function (value) {
                if (value) {
                    self.getPopUp().openModal();
                }
            });
            if(self.isAddressSameAsBilling()){
                self.addSubscription = quote.billingAddress.subscribe(function(value){
                    selectShippingAddress(value);
                });
            }
            this.isAddressSameAsBilling.subscribe(function(value){
                if (value) {
                    quote.shippingAddress(quote.billingAddress());
                     self.addSubscription = quote.billingAddress.subscribe(function(val){
                        selectShippingAddress(val);
                    });
                } else {
                    checkoutData.setSelectedShippingAddress(null);
                    //quote.shippingAddress(null);
                    self.addSubscription.dispose();
                    self.addSubscription = null;
                    //self.lastSelectedShippingAddress = quote.shippingAddress();
                    //quote.shippingAddress(null);
                }
            });
            quote.shippingMethod.subscribe(function () {
                self.errorValidationMessage(false);
            });
            registry.async('checkoutProvider')(function (checkoutProvider) {
                var shippingAddressData = checkoutData.getShippingAddressFromData();
                if (shippingAddressData) {
                    checkoutProvider.set(
                        'shippingAddress',
                        $.extend(true, {}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                    );
                }
                checkoutProvider.on('shippingAddress', function (shippingAddrsData) {
                    checkoutData.setShippingAddressFromData(shippingAddrsData);
                });
                shippingRatesValidator.initFields(fieldsetName);
            });
            
            return this;
        },

        setPickupDate: function(elem, event){
            var pieces = $('#pickup_date').val().split(' ');
            checkoutConfig.quoteData.pickup_date = pieces[0];
            checkoutConfig.quoteData.pickup_time = pieces[1]+pieces[2];
        },

        setupDatepicker: function(){
            var self = this;
            var min = 0;
            var max = 23;
            if(self.store()){
                var hours = self.store().working_hours.split('-');
                min = parseInt(hours[0]);
                max = parseInt(hours[1]) + 11;
            }
            $("#pickup_date").datetimepicker({
                minDate: 0,
                dateFormat: 'mm/dd/yy',
                stepMinute: 15,
                hourMin: min,
                hourMax: max,
                timeFormat: 'hh:mm tt'/*,
                addSliderAccess: true,
                sliderAccessArgs: {touchonly: true}*/,
                beforeShowDay: function(day) {
                    var holidays = (self.store().holiday)?self.store().holiday.split(','):[];
                    var working_days = (self.store().opening_days)?self.store().opening_days.split(','):[];
                    var date = (day.getMonth() + 1)+"/"+ day.getDate() +"/"+day.getFullYear();
                    var now = new Date();
                    if(now.getDate() == day.getDate() && now.getMonth() == day.getMonth() && now.getFullYear() == day.getFullYear()){
                        return false;
                    } 
                    if ($.inArray(date, holidays) != -1 ) {
                        return [ false ];
                    } else {
                        
                        return [ ($.inArray(day.getDay()+'', working_days) != -1) ];
                    }
                }
            });
        },

        /**
         * Navigator change hash handler.
         *
         * @param {Object} step - navigation step
         */
        navigate: function (step) {
            step && step.isVisible(true);
        },

        /**
         * @return {*}
         */
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
                    self.temporaryAddress = $.extend(true, {}, checkoutData.getShippingAddressFromData());
                };
                popUp = modal(this.popUpForm.options, $(this.popUpForm.element));
            }

            return popUp;
        },

        /**
         * Revert address and close modal.
         */
        onClosePopUp: function () {
            checkoutData.setShippingAddressFromData($.extend(true, {}, this.temporaryAddress));
            this.getPopUp().closeModal();
        },

        /**
         * Show address form popup
         */
        showFormPopUp: function () {
            this.isFormPopUpVisible(true);
        },

        /**
         * Save new shipping address
         */
        saveNewAddress: function () {
            var addressData,
                newShippingAddress;

            this.source.set('params.invalid', false);
            this.triggerShippingDataValidateEvent();

            if (!this.source.get('params.invalid')) {
                addressData = this.source.get('shippingAddress');
                // if user clicked the checkbox, its value is true or false. Need to convert.
                addressData['save_in_address_book'] = this.saveInAddressBook ? 1 : 0;

                // New address must be selected as a shipping address
                newShippingAddress = createShippingAddress(addressData);
                selectShippingAddress(newShippingAddress);
                checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                checkoutData.setNewCustomerShippingAddress($.extend(true, {}, addressData));
                this.getPopUp().closeModal();
                this.isNewAddressAdded(true);
            }
        },

        /**
         * Shipping Method View
         */
        rates: shippingService.getShippingRates(),
        isLoading: shippingService.isLoading,
        isSelected: ko.computed(function () {
            return quote.shippingMethod() ?
                quote.shippingMethod()['carrier_code'] + '_' + quote.shippingMethod()['method_code'] :
                null;
        }),

        /**
         * @param {Object} shippingMethod
         * @return {Boolean}
         */
        selectShippingMethod: function (shippingMethod) {
            selectShippingMethodAction(shippingMethod);
            checkoutData.setSelectedShippingRate(shippingMethod['carrier_code'] + '_' + shippingMethod['method_code']);

            return true;
        },

        /**
         * Set shipping information handler
         */
        setShippingInformation: function () {
            if (this.validateShippingInformation()) {
                console.log('wrong order?');
                setBillingAddressAction().done(function(){
                    setShippingInformationAction().done(function(){
                        stepNavigator.next();
                    });
                });
                /*setShippingInformationAction().done(
                    function(){
                        setBillingAddressAction().done(
                            function () {
                                stepNavigator.next();
                            }
                        );
                    }
                );*/
            }
        },

        /*useBillingAddress: function(){
            if(this.isAddressSameAsBilling()){
                this.isAddressSameAsBilling(false);
            } else {
                this.isAddressSameAsBilling(true);
            }
            if (this.isAddressSameAsBilling()) {
                selectShippingAddress(quote.billingAddress());
                //this.updateAddresses();
            } else {
                lastSelectedShippingAddress = quote.shippingAddress();
                quote.shippingAddress(null);
            }
            return true;
        },*/

        /**
         * @return {Boolean}
         */
        validateShippingInformation: function () {
            var shippingAddress,
                addressData,
                loginFormSelector = 'form[data-role=email-with-possible-login]',
                emailValidationResult = customer.isLoggedIn(),
                field;

            if(this.isAddressSameAsBilling()){
                this.source.set('shippingAddress', this.source.get('billingAddressshared'));
            }

            if(customer.isLoggedIn() && !quote.billingAddress().city){
                errorProcessor.addErrorMessage({message: 'Please choose a billing address.'});
                return false
            }

            if(customer.isLoggedIn() && !quote.shippingAddress().city){
                errorProcessor.addErrorMessage({message: 'Please choose a shipping address.'});
                return false
            }

            if (!quote.shippingMethod()) {
                this.errorValidationMessage($t('Please specify a shipping method.'));

                return false;
            }

            if (!customer.isLoggedIn()) {
                $(loginFormSelector).validation();
                emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
            }

            if (this.isFormInline) {
                this.source.set('params.invalid', false);
                this.triggerShippingDataValidateEvent();

                if (emailValidationResult &&
                    this.source.get('params.invalid') ||
                    !quote.shippingMethod()['method_code'] ||
                    !quote.shippingMethod()['carrier_code']
                ) {
                    this.focusInvalid();

                    return false;
                }

                shippingAddress = quote.shippingAddress();
                addressData = addressConverter.formAddressDataToQuoteAddress(
                    this.source.get('shippingAddress')
                );

                //Copy form data to quote shipping address object
                for (field in addressData) {
                    if (addressData.hasOwnProperty(field) &&  //eslint-disable-line max-depth
                        shippingAddress.hasOwnProperty(field) &&
                        typeof addressData[field] != 'function' &&
                        _.isEqual(shippingAddress[field], addressData[field])
                    ) {
                        shippingAddress[field] = addressData[field];
                    } else if (typeof addressData[field] != 'function' &&
                        !_.isEqual(shippingAddress[field], addressData[field])) {
                        shippingAddress = addressData;
                        break;
                    }
                }

                if (customer.isLoggedIn()) {
                    shippingAddress['save_in_address_book'] = 1;
                }
                selectShippingAddress(shippingAddress);
            }

            if (!emailValidationResult) {
                $(loginFormSelector + ' input[name=username]').focus();

                return false;
            }

            return true;
        },

        /**
         * Trigger Shipping data Validate Event.
         */
        triggerShippingDataValidateEvent: function () {
            this.source.trigger('shippingAddress.data.validate');

            if (this.source.get('shippingAddress.custom_attributes')) {
                this.source.trigger('shippingAddress.custom_attributes.data.validate');
            }
        }
    });
});
