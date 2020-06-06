define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar',
	'Magento_Customer/js/customer-data'
], function ($, Component, quote, stepNavigator, sidebarModel, customerData) {
    'use strict';
	var countryData = customerData.get('directory-data');
	return function (Component) {
		return Component.extend({
			defaults: {
				template: 'MageArray_StorePickup/shipping-information'
			},

			isStoreSelected: function () {
				if(quote.shippingMethod() && quote.shippingMethod().method_code == "storepickup") {
					return 1;
				}
				return 0;
			},
			
			getDetail: function() {
				if(quote.shippingMethod() && quote.shippingMethod().method_code == "storepickup") {
					var selectedStore = checkoutConfig.quoteData.pickup_store;
					var storeDetail = window.checkoutConfig.storePickup;
					var storeAddress = {countryId: storeDetail[selectedStore].country};
					storeAddress.regionId = storeDetail[selectedStore].region_id;
					storeAddress.regionCode = storeDetail[selectedStore].region_code;
					storeAddress.region = storeDetail[selectedStore].state;
					storeAddress.telephone = storeDetail[selectedStore].phone_number;
					storeAddress.postcode = storeDetail[selectedStore].zipcode;
					storeAddress.street = storeDetail[selectedStore].address;
					storeAddress.city = storeDetail[selectedStore].city;
					storeAddress.firstname = storeDetail[selectedStore].firstname;
					storeAddress.lastname = storeDetail[selectedStore].lastname;
					return storeAddress;
				}
				
				return false;
			},
			
			getCountryName: function(countryId) {
				return (countryData()[countryId] != undefined) ? countryData()[countryId].name : "";
			}
		});
	}
});
		