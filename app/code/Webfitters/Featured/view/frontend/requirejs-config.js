var config = {
    paths: {
    	"webfitters.bxslider": "Webfitters_Featured/js/bxslider/jquery.bxslider",
        "webfitters.featured": "Webfitters_Featured/js/featured"
    },
    shim: {
    	"webfitters.bxslider": {
    		"deps": ["jquery"]
    	},
        "webfitters.featured": {
            "deps": ["jquery", "webfitters.bxslider"]
        }
    }
};
requirejs(["webfitters.featured"], function(featured){
    featured.init();
});