/**
 * Copyright © 2015 Customer Paradigm. All rights reserved.
 * See COPYING.txt for license details.
 * @version 1.0.2
 * @since 1.0.2
 */
var config = {
    map: {
        "*":{
            'Magento_Checkout/js/action/place-order':'CustomerParadigm_OrderComments/js/action/place-order',
            transparent: "Magento_Payment/transparent"
        }
    },
    config: {
        mixins: {
            'Magento_Payment/transparent':{
                "CustomerParadigm_OrderComments/transparent-mixin": true
            }
        }
    }
};

