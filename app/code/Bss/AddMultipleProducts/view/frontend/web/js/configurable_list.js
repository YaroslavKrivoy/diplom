if (typeof Bss_Product == 'undefined') {
    var Bss_Product = {};
}

require(['prototype','jquery'], function (prototype) {
    Bss_Product.Config = Class.create();
    Bss_Product.Config.prototype = {
        initialize: function (config) {
            this.config     = config;
            this.taxConfig  = this.config.taxConfig;
            var settingsClassToSelect = '.super-attribute-select-'+this.config.productId;
            this.settings   = $$(settingsClassToSelect);
            this.state      = new Hash();
            this.priceTemplate = new Template(this.config.template);
            this.prices     = config.prices;

            // Set default values from config
            if (config.defaultValues) {
                this.values = config.defaultValues;
            }

            // Overwrite defaults by url
            var separatorIndex = window.location.href.indexOf('#');
            if (separatorIndex != -1) {
                var paramsStr = window.location.href.substr(separatorIndex+1);
                var urlValues = paramsStr.toQueryParams();
                if (!this.values) {
                    this.values = {};
                }
                for (var i in urlValues) {
                    this.values[i] = urlValues[i];
                }
            }

            // Overwrite defaults by inputs values if needed
            if (config.inputsInitialized) {
                this.values = {};
                this.settings.each(function (element) {
                    if (element.value) {
                        var attributeId = element.id.replace(/[a-z]*/, '');
                        this.values[attributeId] = element.value;
                    }
                }.bind(this));
            }

            // Put events to check select reloads
            this.settings.each(function (element) {
                Event.observe(element, 'change', this.configure.bind(this));
            }.bind(this));

            // fill state
            this.settings.each(function (element) {
                var attributeId = element.id.replace(/[a-z]*/, '');
                if (attributeId && this.config.attributes[attributeId]) {
                    element.config = this.config.attributes[attributeId];
                    element.attributeId = attributeId;
                    this.state[attributeId] = false;
                }
            }.bind(this));

            // Init settings dropdown
            var childSettings = [];
            for (var i=this.settings.length-1; i>=0; i--) {
                var prevSetting = this.settings[i-1] ? this.settings[i-1] : false;
                var nextSetting = this.settings[i+1] ? this.settings[i+1] : false;
                if (i == 0) {
                    this.fillSelect(this.settings[i]);
                } else {
                    this.settings[i].disabled = true;
                }
                $(this.settings[i]).childSettings = childSettings.clone();
                $(this.settings[i]).prevSetting   = prevSetting;
                $(this.settings[i]).nextSetting   = nextSetting;
                childSettings.push(this.settings[i]);
            }

            // Set values to inputs
            this.configureForValues();
            document.observe("dom:loaded", this.configureForValues.bind(this));
        },

        configureForValues: function () {
            if (this.values) {
                this.settings.each(function (element) {
                    var attributeId = element.attributeId;
                    element.value = (typeof(this.values[attributeId]) == 'undefined')? '' : this.values[attributeId];
                    this.configureElement(element);
                }.bind(this));
            }
        },

        configure: function (event) {
            var element = Event.element(event);
            this.configureElement(element);
        },

        configureElement : function (element) {
            this.reloadOptionLabels(element);
            if (element.value) {
                this.state[element.config.id] = element.value;
                if (element.nextSetting) {
                    element.nextSetting.disabled = false;
                    this.fillSelect(element.nextSetting);
                    this.resetChildren(element.nextSetting);
                }
            } else {
                this.resetChildren(element);
            }
            this.reloadPrice();
        },

        reloadOptionLabels: function (element) {
            var selectedPrice;
            if (element.options[element.selectedIndex].config && !this.config.stablePrices) {
                selectedPrice = parseFloat(element.options[element.selectedIndex].config.price);
            } else {
                selectedPrice = 0;
            }
            for (var i=0; i<element.options.length; i++) {
                if (element.options[i].config) {
                    element.options[i].text = this.getOptionLabel(element.options[i].config, element.options[i].config.price-selectedPrice);
                }
            }
        },

        resetChildren : function (element) {
            if (element.childSettings) {
                for (var i=0; i<element.childSettings.length; i++) {
                    element.childSettings[i].selectedIndex = 0;
                    element.childSettings[i].disabled = true;
                    if (element.config) {
                        this.state[element.config.id] = false;
                    }
                }
            }
        },

        fillSelect: function (element) {
            var attributeId = element.id.replace(/[a-z]*/, '');
            var options = this.getAttributeOptions(attributeId);
            this.clearSelect(element);
            element.options[0] = new Option('', '');
            element.options[0].innerHTML = this.config.chooseText;

            var prevConfig = false;
            if (element.prevSetting) {
                prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
            }

            if (options) {
                var index = 1;
                for (var i=0; i<options.length; i++) {
                    var allowedProducts = [];
                    if (prevConfig) {
                        for (var j=0; j<options[i].products.length; j++) {
                            if (prevConfig.config.allowedProducts
                                && prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1) {
                                allowedProducts.push(options[i].products[j]);
                            }
                        }
                    } else {
                        allowedProducts = options[i].products.clone();
                    }

                    if (allowedProducts.size()>0) {
                        options[i].allowedProducts = allowedProducts;
                        element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                        if (typeof options[i].price != 'undefined') {
                            element.options[index].setAttribute('price', options[i].price);
                        }
                        element.options[index].config = options[i];
                        index++;
                    }
                }
            }
        },

        getOptionLabel: function (option, price) {
            var str = option.label;
            return str;
        },
        clearSelect: function (element) {
            for (var i=element.options.length-1; i>=0; i--) {
                element.remove(i);
            }
        },
     
        getAttributeOptions: function (attributeId) {
            if (this.config.attributes[attributeId]) {
                return this.config.attributes[attributeId].options;
            }
        },
     
        reloadPrice: function () {
            var price    = 0;
            var oldPrice = 0;
            for (var i=this.settings.length-1; i>=0; i--) {
                var selected = this.settings[i].options[this.settings[i].selectedIndex];
                if (selected.config) {
                    basePrice  = parseFloat(this.config.optionPrices[selected.config.allowedProducts[0]].basePrice.amount);
                    finalPrice = parseFloat(this.config.optionPrices[selected.config.allowedProducts[0]].finalPrice.amount);
                    oldPrice = parseFloat(this.config.optionPrices[selected.config.allowedProducts[0]].oldPrice.amount);
                    break;
                }
            }
            jQuery.noConflict();
            var $j = jQuery;
            var productId = this.config.productId;
            $j('.info-er-pu .price-box').each(function () {
                if ($j(this).data('product-id') == productId) {
                    if (!$j('.er-pu-' + productId).find('input.product-custom-option,select.product-custom-option,textarea.product-custom-option').length) {
                        $j(this).find('.price-container> span[data-price-type="finalPrice"] > .price').text($j('#currency-add').val() + finalPrice.toFixed(2))
                        $j(this).find('.price-container> span[data-price-type="basePrice"] > .price').text($j('#currency-add').val() + basePrice.toFixed(2))
                        $j(this).find('.price-container> span[data-price-type="oldPrice"] > .price').text($j('#currency-add').val() + oldPrice.toFixed(2))
                        $j(this).parent().find('.fixed-price-ad-pu span.finalPrice').text($j('#currency-add').val() + finalPrice)
                        $j(this).parent().find('.fixed-price-ad-pu span.basePrice').text($j('#currency-add').val() + basePrice)
                        $j(this).parent().find('.fixed-price-ad-pu span.oldPrice').text($j('#currency-add').val() + oldPrice)
                    } else {
                        $j(this).parent().find('.fixed-price-ad-pu span.finalPrice').text($j('#currency-add').val() + finalPrice)
                        $j(this).parent().find('.fixed-price-ad-pu span.basePrice').text($j('#currency-add').val() + basePrice)
                        $j(this).parent().find('.fixed-price-ad-pu span.oldPrice').text($j('#currency-add').val() + oldPrice)
                        $j('.er-pu-' + productId).find('input.product-custom-option,select.product-custom-option,textarea.product-custom-option').change();
                    }
                    return false;
                }
            })
        }
    }
})