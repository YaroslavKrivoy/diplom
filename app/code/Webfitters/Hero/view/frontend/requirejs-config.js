var config = {
    paths: {
    	"webfitters.parallax": "Webfitters_Hero/js/parallax.min",
        "webfitters.hero": "Webfitters_Hero/js/hero"
    },
    shim: {
    	"webfitters.parallax": {
    		"deps": ["jquery"]
    	},
        "webfitters.hero": {
            "deps": ["jquery", "webfitters.parallax"]
        }
    }
};
requirejs(["webfitters.hero"], function(hero){
    hero.init();
});