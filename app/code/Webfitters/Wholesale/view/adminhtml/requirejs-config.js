var config = {
    paths: {
        "webfitters.wholesale": "Webfitters_Wholesale/js/wholesale"
    },
    shim: {
        "webfitters.wholesale": {
            "deps": ["jquery"]
        }
    }
};
requirejs(["webfitters.wholesale"], function(wholesale){
    wholesale.init();
});