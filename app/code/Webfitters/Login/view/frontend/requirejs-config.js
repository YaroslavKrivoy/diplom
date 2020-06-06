var config = {
    paths: {
        "webfitters.login": "Webfitters_Login/js/login"
    },
    shim: {
        "webfitters.login": {
            "deps": ["jquery"]
        }
    }
};
requirejs(["webfitters.login"], function(login){
    login.init();
});