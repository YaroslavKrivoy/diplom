define([
	'Magento_Customer/js/model/customer',
	'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/model/quote',
	'Magento_Checkout/js/action/select-shipping-address',
	'mage/translate',
    'jquery'
], function (customer, addressConverter, quote, selectShippingAddress, $t, $) {
    'use strict';

    return function (Component) {
        return Component.extend({
			defaults: {
				template: 'MageArray_StorePickup/shipping',
				shippingMethodListTemplate: 'MageArray_StorePickup/shipping-method-list'
			},

			triggerShippingDataValidateEvent: function () {
				if( quote.shippingMethod().carrier_code != 'storepickup') {
					return this._super();
				}
			},
			
			storeEnable: function () {
				var enable = checkoutConfig.enableStore;
				var storeProductWise = checkoutConfig.storeProductWise;
				if(enable !== "0") {
					if(storeProductWise === "1") {
						var productAvailable = jQuery.grep( checkoutConfig.quoteItemData, function( n, i ) {
							return n.product.available_at_store == 1;
						});
						if(productAvailable.length === 0) {
							return false;
						}
					}
					return true;
				}
				return false;
			},
			
			validateShippingInformation: function () {
				var shippingAddress,
					addressData,
					loginFormSelector = 'form[data-role=email-with-possible-login]',
					emailValidationResult = customer.isLoggedIn(),
					field;

				if (!quote.shippingMethod()) {
					this.errorValidationMessage($t('Please specify a shipping method.'));

					return false;
				}
				
				if( quote.shippingMethod().carrier_code == 'storepickup') {
					if($('#pickup-store').length == 0) {
						jQuery("#label_method_storepickup_storepickup").parent().trigger("click");
					}
					if ($('#pickup-store').val() == '') {
						this.errorValidationMessage('Please select pickup store.'); 
						return false; 
					}
					if($('[name="pickup_date"]').is(":visible")){
						if($('[name="pickup_date"]').val() == '') {
							this.errorValidationMessage('Please select pickup date.'); 
							return false;
						}
					}
				}

				return this._super();
			},
			
			shippingMethodPostRender: function (element) {
				if (quote.shippingMethod() && quote.shippingMethod().carrier_code == 'storepickup') {
					jQuery("#label_method_storepickup_storepickup").parent().trigger("click");
					if(jQuery("#checkout-with-store:checked").length === 0) {
						jQuery("#checkout-with-store").trigger("click");
					}
					jQuery("#checkout-with-store").val(1).trigger("change");
				} else {
					jQuery("#checkout-with-store").trigger("change");
				}
				
			}
        });
    }
});