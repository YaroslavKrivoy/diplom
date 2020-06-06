var config = {
    paths: {
        "webfitters.pdf": "Webfitters_Pdf/js/pdf"
    },
    shim: {
        "webfitters.pdf": {
            "deps": ["jquery"]
        }
    }
};
requirejs(["webfitters.pdf"], function(pdf){
    pdf.init();
});