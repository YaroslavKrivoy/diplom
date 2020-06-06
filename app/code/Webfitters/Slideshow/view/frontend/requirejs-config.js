/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    paths: {
    	"webfitters.bxslider": "Webfitters_Slideshow/js/bxslider/jquery.bxslider.min",
        "webfitters.slideshow": "Webfitters_Slideshow/js/slideshow"
    },
    shim: {
    	"webfitters.bxslider": {
    		"deps": ["jquery"]
    	},
        "webfitters.slideshow": {
            "deps": ["jquery", "webfitters.bxslider"]
        }
    }
};
requirejs(["webfitters.slideshow"], function(slideshow){
    slideshow.init();
});