define([
	'ko',
    'underscore',
    'Magento_Ui/js/form/form',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/create-billing-address',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/action/set-billing-address',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function ( ko, _, Component, customer, addressList, quote, createBillingAddress, selectBillingAddress, checkoutData, checkoutDataResolver, customerData, setBillingAddressAction, globalMessageList, $t) {
    'use strict';

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

    addressOptions.push(newAddressOption);
	
    return function (Component) {
        return Component.extend({
			initObservable: function () {
				this._super()
					.observe({
						selectedAddress: null,
						isAddressDetailsVisible: quote.billingAddress() != null,
						isAddressFormVisible: !customer.isLoggedIn() || addressOptions.length === 1,
						isAddressSameAsShipping: false,
						saveInAddressBook: 1
					});

				quote.billingAddress.subscribe(function (newAddress) {
					if (quote.isVirtual()) {
						this.isAddressSameAsShipping(false);
					} else {
						this.isAddressSameAsShipping(
							newAddress != null &&
							newAddress.getCacheKey() == quote.shippingAddress().getCacheKey() //eslint-disable-line eqeqeq
						);
					}

					if (newAddress != null && newAddress.saveInAddressBook !== undefined) {
						this.saveInAddressBook(newAddress.saveInAddressBook);
					} else {
						this.saveInAddressBook(1);
					}
					
					 if( typeof quote.shippingMethod() != 'undefined' && quote.shippingMethod() != null ) {
						if( quote.shippingMethod().carrier_code == 'storepickup') { 
							this.isAddressSameAsShipping(false);
							this.isAddressDetailsVisible(false);
							jQuery(".action-cancel").hide();
						} else {
							jQuery(".action-cancel").show();
						}
					} else { 
						this.isAddressDetailsVisible(true);
					}
				  //  this.isAddressDetailsVisible(true);
				}, this);

				return this;
			},
			/**
			 * Update address action
			 */
			updateAddress: function () {
				this._super();
				if( typeof quote.shippingMethod() != 'undefined' && quote.shippingMethod() != null ) {
					if( quote.shippingMethod().carrier_code == 'storepickup') { 
						this.isAddressDetailsVisible(true);
					}
				 }
			},
			canUseShippingAddress: ko.computed(function () {
				quote.paymentMethod(null);
				if( typeof quote.shippingMethod() != 'undefined' && quote.shippingMethod() != null) {
					if( quote.shippingMethod().carrier_code == 'storepickup') {
						return false;
					}
				}
				return !quote.isVirtual() && quote.shippingAddress() && quote.shippingAddress().canUseForBilling();
			}),
		});
    }
});	