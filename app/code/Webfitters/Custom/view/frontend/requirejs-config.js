/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    paths: {
    	"webfitters.bootstrap": "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min",
        "webfitters.custom": "Webfitters_Custom/js/custom",
        "webfitters.bxslider": "Webfitters_Custom/js/bxslider/jquery.bxslider.min"
    },
    shim: {
    	"webfitters.bootstrap": {
    		"deps": ["jquery"]
    	},
        "webfitters.bxslider": {
            "deps": ["jquery"]
        },
        "webfitters.custom": {
            "deps": ["jquery", "webfitters.bootstrap", "webfitters.bxslider"]
        }
    }
};
requirejs(["webfitters.custom"], function(custom){
    custom.init();
});