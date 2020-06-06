/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    paths: {
    	"webfitters.masonry": "Webfitters_MasonryBlog/js/masonry",
        "webfitters.grid": "Webfitters_MasonryBlog/js/grid"
    },
    shim: {
    	"webfitters.masonry": {
    		"deps": ["jquery"]
    	},
        "webfitters.grid": {
            "deps": ["jquery", "webfitters.masonry"]
        }
    }
};
requirejs(["webfitters.grid"], function(grid){
    grid.init();
});