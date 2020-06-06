var config = {
    "map": {
        "*": {
            "Magento_Checkout/js/model/shipping-save-processor/default" : "MageArray_StorePickup/js/model/shipping-save-processor/default"
        }
    },
	config: {
    	mixins: {
            'Magento_Checkout/js/view/shipping': {
                'MageArray_StorePickup/js/view/plugin/shipping': true
            },
			'Magento_Checkout/js/view/billing-address': {
                'MageArray_StorePickup/js/view/plugin/billing-address': true
            },
			'Magento_Checkout/js/view/shipping-information': {
                'MageArray_StorePickup/js/view/plugin/shipping-information': true
            }
        }
    }
};