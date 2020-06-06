define([
    'jquery',
    'underscore',
    "mage/template",
    "mage/translate",
    'mage/mage',
    "bssfancybox",
    "domReady!"
], function ($, _,mageTemplate) {
    'use strict';
    $.widget('mage.Multipleaddtocart', {
        options: {
            jsonClassApply:{},
            showCheckbox:'',
            showStick:'',
            positionBT:'',
            urlAddToCart:'',
            priceIncludesTax:''
        },

        _init: function () {
            if (this.options.jsonClassApply !== '') {
                this._RenderForm();
                this._RightButton();
            } else {
                console.log('Multipleaddtocart: No input data received');
            }
        },

        _create: function () {
            var $widget = this;
            var showstick = this.options.showStick;
            var showcheckbox = this.options.showCheckbox;
            // remove redirect-url
            if ($('button.tocart').parent().find('.qty-m-c').length > 0) {
                $('button.tocart').removeAttr('data-mage-init');
            }
            
            if (!showcheckbox) {
                setTimeout(function () {
                    $('input[name="product-select[]"]').each(function () {
                        var form = $('#add-muntiple-product-' + $(this).data('froma'));
                        $widget._RenderOption(form, $(this).val());
                    })
                    $('.qty-m-c').trigger('input');
                },0)
            }

            $widget._EventListener();

            $('.option-er-pu input.bundle.option,.option-er-pu select.bundle.option').trigger('keyup');
        },

        _RenderForm: function (config) {
            var template_form = mageTemplate('#form-multiple-add');
            var template_qty = mageTemplate('#qty-multiple-add');
            var template_checkbox = mageTemplate('#checkbox-multiple-add');
            var template_button = mageTemplate('#button-multiple-add');
            var positionbs = this.options.positionBT;
            var product_id_ad;

            $.each(this.options.jsonClassApply, function (index, el) {
                if ($(el).length) {
                    //add checkbox all
                    $(el).each(function (i) {
                        var form_addmuntiple = template_form({
                            data: {
                                id:'add-muntiple-product-trim'+ i,
                                class:'add-mt-'+ i,
                                name:'add-muntiple-product-trim'+ i,
                            }
                        });

                        var qty_addmuntiple = template_qty({
                            data: {
                                group:'gr-add-mt-'+ i
                            }
                        });
                        
                        var button_addmuntiple = template_button({
                            data: {
                                id:'bt-ad-mt-'+ i,
                                froma:'trim'+ i
                            }
                        });
                        // if( $(this).find('.actions-primary').length ){
                            $(this).css('position','relative');
                            $(this).addClass('gr-add-mt-'+ i);
                             // add form
                            $(this).append(form_addmuntiple);
                            if($('.sidebar.sidebar-main').length==0){
                                $('.sidebar.sidebar-additional').prepend(button_addmuntiple);
                            } else {
                                $('.sidebar.sidebar-main').prepend(button_addmuntiple);
                            }
                            
                            // add button
                            /*switch (positionbs) {
                                case 0:
                                    $(this).prepend(button_addmuntiple);
                                    break;
                                case 1:
                                    $(this).append(button_addmuntiple);
                                    break;
                                case 2:
                                    $(this).prepend(button_addmuntiple);
                                    $(this).append(button_addmuntiple);
                                    break;
                                case 3:
                                    $(this).append(button_addmuntiple).find('.addmanytocart').addClass('left-scroll');
                                    $(this).css('overflow','hidden');
                            }*/
                        // }
                   
                        // add box qty and check box
                        $(this).find('.product-item').each(function () {
                            if ($(this).find('form').length) {
                                if ($(this).find('input[name="product"]').length) {
                                    product_id_ad = $(this).find('input[name="product"]').val();
                                } else {
                                    if ($(this).parents('.product.info').find('.price-box').data('product-id')!='') {
                                        product_id_ad = $(this).parents('.product.info').find('.price-box').data('product-id');
                                    }
                                }
                                if (product_id_ad != '') {
                                    var checkbox_addmuntiple = template_checkbox({
                                        data: {
                                            id:'product_' + product_id_ad,
                                            class:'product-select add-mt-'+ i,
                                            froma:'trim'+ i,
                                            value:product_id_ad
                                        }
                                    });
                                    $(this).find('button.tocart').before(checkbox_addmuntiple);
                                    $(this).find('button.tocart').before(qty_addmuntiple);
                                }
                            } else {
                                var dataPost = jQuery.parseJSON($(this).find('button.tocart').attr('data-post'));
                                if (dataPost && dataPost.data.product) {
                                    product_id_ad = dataPost.data.product;
                                } else {
                                    product_id_ad = $(this).parents('.product-item').find('.price-box').data('product-id');
                                }
                                if (Math.floor(product_id_ad) == product_id_ad && $.isNumeric(product_id_ad)) {
                                    var checkbox_addmuntiple = template_checkbox({
                                        data: {
                                            id:'product_' + product_id_ad,
                                            class:'product-select add-mt-'+ i,
                                            froma:'trim'+ i,
                                            value:product_id_ad
                                        }
                                    });
                                    $(this).find('button.tocart').before(checkbox_addmuntiple);
                                    $(this).find('button.tocart').before(qty_addmuntiple);
                                }
                            }
                        })
                    })
                }
            });
        },

        _RightButton: function () {
            $(".addmanytocart.left-scroll").css({
                    position: 'absolute',
                    top: '0px',
                    zIndex:'999',
                    right: '0px'
            });
            var timeout = null;
            var target = '';
            if ($(".addmanytocart.left-scroll").length) {
                 target = $(".addmanytocart.left-scroll").offset().top;
            }
            $(window).scroll(function () {
                if ($(".addmanytocart.left-scroll").length) {
                    if ($(window).scrollTop() >= target ) {
                        $(".addmanytocart.left-scroll").css({
                                position: 'absolute',
                                top: $(window).scrollTop()  + 15 - target + 'px',
                                right: '0px'
                        });
                    } else {
                        $(".addmanytocart.left-scroll").css({
                                position: 'absolute',
                                top: '0px',
                                zIndex:'999',
                                right: '0px'
                        });
                    }
                }
            });
        },

        _EventListener: function () {

            var $widget = this;

            $(document).on('click', '.add-all-product,.product-select', function () {
                return $widget._OnClick($(this));
            });

            $(document).on('input', '.qty-m-c', function () {
                return $widget._InputChange($(this));
            });

            $('body').on('change','.product-item-actions input,.product-item-actions select , .product-item-actions textarea', function () {
                return $widget._OnChange($(this));
            });

            $('body').on('click','button.tocart,.addmanytocart,.addmanytocart-popup',function (e) {
                if ($('.addmanytocart').length) {
                    e.preventDefault();
                    return $widget._AddToCart($(this));
                }
            })
            // popup
            var decimalSymbol = $('#currency-add').val();
            var priceIncludesTax = $widget.options.priceIncludesTax;

            $('body').on("change paste keyup",'.option-er-pu input.product-custom-option,.option-er-pu select.product-custom-option , .option-er-pu textarea.product-custom-option', function () {
                var productid = $(this).parents('.info-er-pu').find('.price-box').data('product-id');
                var ratetax = $('#rate_' + productid).val();
                return $widget._ReloadPriceCustomOption($(this), productid, decimalSymbol, ratetax, priceIncludesTax);
            });
            // bundel product
            $('body').on("change paste keyup",'.option-er-pu input.bundle.option,.option-er-pu select.bundle.option', function () {
                var productid = $(this).parents('.info-er-pu').find('.price-box').data('product-id');
                var ratetax = $('#rate_' + productid).val();
                return $widget._ReloadPriceBundel($(this),productid, decimalSymbol, ratetax, priceIncludesTax);
            });

            // download
            $('body').on("change paste keyup",'.option-er-pu .downloads input,.option-er-pu .downloads select', function () {
                var productid = $(this).parents('.info-er-pu').find('.price-box').data('product-id');
                var ratetax = $('#rate_' + productid).val();
                return $widget._ReloadPriceDownloads($(this),productid, decimalSymbol, ratetax, priceIncludesTax);
            });
        
        },

        _OnClick: function ($this) {
            var $widget = this;
            var showstick = this.options.showStick;
            var showcheckbox = this.options.showCheckbox;
            if ($this.hasClass('add-all-product')) {
                var select = $this.parents('.button-bs-ad').find('.addmanytocart').attr('id').split('-');
                var form = $('#add-muntiple-product-trim' + select[3]);
                if ($this.is(':checked')) {
                    $('.add-all-product').prop("checked", true);
                    $('.add-mt-' + select[3]).each(function () {
                        if (!$(this).is(':checked')) {
                            $(this).trigger('click');
                        }
                        $widget._RenderOption(form,$this.val());
                    })
                } else {
                    $('.add-all-product').prop("checked", false);
                    $('.add-mt-' + select[3]).each(function () {
                        if ($(this).is(':checked')) {
                            $(this).trigger('click');
                        }
                        $(form).find('.vls_' + $this.val()).remove();
                    })
                }
            } else {
                var total_qty = 0;
                var total_product = 0;
                var product_id = $this.val();
                var form = $('#add-muntiple-product-' + $this.data('froma'));
                if ($this.is(':checked')) {
                   $widget._RenderOption(form, product_id);
                    if (showstick) {
                        if ($this.siblings('.qty-m-c').val() > 0 ) {
                            $this.parents('.product-item').css('position','relative');
                            $this.parents('.product-item').prepend('<div class="ad-mt-stick"></div>');
                        } else {
                            $this.parents('.product-item').css('position','');
                            $this.parents('.product-item').find('.ad-mt-stick').remove();
                        }
                    }
                } else {
                    $(form).find('.vls_' + product_id).remove();
                    $this.parents('.product-item').css('position','');
                    $this.parents('.product-item').find('.ad-mt-stick').remove();
                }
                var _select = $this.siblings('.qty-m-c').data('group').split('-');
                if ($('.gr-add-mt-'+ _select[3]).length) {
                    $('.gr-add-mt-'+ _select[3]).find('.qty-m-c').each(function () {
                        if ($(this).parents('.add-option').length === 0) {
                            if ($(this).val() && $(this).val() > 0) {
                                if (showcheckbox) {
                                    if ($(this).siblings('input[name="product-select[]"]').is(':checked')) {
                                        total_qty += parseInt($(this).val());
                                        total_product += 1;
                                    }
                                } else {
                                    total_qty += parseInt($(this).val());
                                    total_product += 1;
                                }
                            }
                        }
                    })
                    $('.button-bs-ad #bt-ad-mt-'+ _select[3] +' .total_qty span').text(total_qty);
                    $('.button-bs-ad #bt-ad-mt-'+ _select[3] +' .total_products span').text(total_product);
                }
            }
        },

        _OnChange: function ($this) {
            var product_id = $this.parent().find('input[name="product-select[]"]').val();
            var form = $('#add-muntiple-product-' + $this.parent().find('input[name="product-select[]"]').data('froma'));
            var name = product_id + '_' + $this.attr('name');
            if ($this.is("input")) {
                $(form).find(".add-option").find('input[name="'+ name +'"]').val($this.val());
            }
            if ($this.is("select")) {
                $(form).find(".add-option").find('select[name="'+ name +'"]').val($this.val());
            }
            if ($this.is("textarea")) {
                $(form).find(".add-option").find('textarea[name="'+ name +'"]').val($this.val());
            }
        },

        _InputChange: function ($this) {
            var $widget = this;
            var showstick = this.options.showStick;
            var showcheckbox = this.options.showCheckbox;
            var total_qty = 0;
            var total_product = 0;
            if ($this.parents('.'+ $this.data('group')).length) {
                $this.parents('.'+ $this.data('group')).find('.qty-m-c').each(function () {
                    if ($(this).parents('.add-option').length === 0) {
                        if ($(this).val() && $(this).val() > 0) {
                            if (showcheckbox) {
                                if ($(this).siblings('input[name="product-select[]"]').is(':checked')) {
                                    total_qty += parseInt($(this).val());
                                    total_product += 1;
                                    if (showstick) {
                                        $(this).parents('.product-item').css('position','relative');
                                        $(this).parents('.product-item').prepend('<div class="ad-mt-stick"></div>');
                                    }
                                } else {
                                    $(this).parents('.product-item').css('position','');
                                    $(this).parents('.product-item').find('.ad-mt-stick').remove();
                                }
                            } else {
                                total_qty += parseInt($(this).val());
                                total_product += 1;
                                if (showstick) {
                                    $(this).parents('.product-item').css('position','relative');
                                    $(this).parents('.product-item').prepend('<div class="ad-mt-stick"></div>');
                                }
                            }
                        } else {
                            $(this).parents('.product-item').css('position','');
                            $(this).parents('.product-item').find('.ad-mt-stick').remove();
                        }
                    }
                })
                var bt = $this.data('group').split('-');
                $('.button-bs-ad bt-ad-mt-'+ bt[3] +' .total_qty span').text(total_qty);
                $('.button-bs-ad bt-ad-mt-'+ bt[3] +' .total_products span').text(total_product);
            }
        },

        _RenderOption: function (form, product_id) {
            $('#product_' + product_id).parent().find('input').each(function () {
                if ($(this).attr('name') !='uenc' && $(this).attr('name') !='form_key' && $(this).attr('name') !='product') {
                    var name = product_id + '_' + $(this).attr('name');
                    if ($(this).attr('name') == 'product-select[]' && ($(form).find('#product_' + product_id).length == 0 || $(this).parents('.wishlist').length)) {
                            $(this).clone().prependTo($(form).find(".add-option")).addClass('vls_' + product_id).val($(this).val());
                    } else {
                        if ($(form).find('input[name="'+ name +'"]').length == 0) {
                            $(this).clone().prependTo($(form).find(".add-option")).addClass('vls_' + product_id).attr('name', name).val($(this).val());
                        }
                    }
                }
            })
            $('#product_' + product_id).parent().find('textarea').each(function () {
                var name = product_id + '_' + $(this).attr('name');
                if ($(form).find('textarea[name="'+ name +'"]').length == 0) {
                    $(this).clone().prependTo($(form).find(".add-option")).addClass('vls_' + product_id).attr('name' ,name).val($(this).val());
                }
            })
            $('#product_' + product_id).parent().find('select').each(function () {
                var name = product_id + '_' + $(this).attr('name');
                if (!$(form).find('select[name="'+ name +'"]').length == 0) {
                    $(this).clone().prependTo($(form).find(".add-option")).addClass('vls_' + product_id).attr('name', name).val($(this).val());
                }
            })
        },
        // Popup
        _ReloadPriceCustomOption: function ($this, productId, decimalSymbol, tax, priceIncludesTax) {
            var priceplus = 0;
            var allselected = '';
            var itemp = $('.er-pu-'+ productId);
            $(itemp).find('input').each(function () {
                if ($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio') {
                    if ($(this).is(':checked')) {
                        if ($(this).attr('price') > 0) {
                            priceplus += parseFloat($(this).attr('price'));
                        }
                    }
                }

                if ($(this).attr('type') == 'text' || $(this).attr('type') == 'time' ) {
                    if ($(this).val() != '') {
                        if ($(this).attr('price') > 0) {
                            priceplus += parseFloat($(this).attr('price'));
                        }
                    }
                }
                if ($(this).attr('type') == 'file') {
                }
            })

            $(itemp).find('select').each(function () {
                if ($(this).is("select[multiple]")) {
                    if ($(this).val() != '') {
                        $(this).find('option:selected').each(function () {
                            if ($(this).attr('price') > 0) {
                                priceplus += parseFloat($(this).attr('price'));
                            }
                        });
                    }
                } else if ($(this).hasClass('datetime-picker')) {
                    allselected = 1;
                    $(this).parent().find('select').each(function () {
                        if ($(this).val() == '') {
                            allselected = 0;
                        }
                    })
                    if (allselected == 1) {
                        if ($('option:selected', this).attr('price') > 0) {
                            priceplus += parseFloat($('option:selected', this).attr('price'));
                        }
                    }
                } else {
                    if ($(this).val() != '') {
                        if ($('option:selected', this).attr('price') > 0) {
                            priceplus += parseFloat($('option:selected', this).attr('price'));
                        }
                    }
                }
            })

            $(itemp).find('textarea.product-custom-option').each(function () {
                if ($(this).val() != '') {
                    if ($(this).attr('price') > 0) {
                        priceplus += parseFloat($(this).attr('price'));
                    }
                }
            })

            var finalPrice =  $this.parents('.info-er-pu').find('.fixed-price-ad-pu span.finalPrice').text();
            finalPrice = finalPrice.replace(decimalSymbol, '');
            finalPrice = parseFloat(finalPrice);
            var basePrice =  $this.parents('.info-er-pu').find('.fixed-price-ad-pu span.basePrice').text();
            basePrice = basePrice.replace(decimalSymbol, '');
            basePrice = parseFloat(basePrice);
            var oldPrice =  $this.parents('.info-er-pu').find('.fixed-price-ad-pu span.oldPrice').text();
            oldPrice = oldPrice.replace(decimalSymbol, '');
            oldPrice = parseFloat(oldPrice);
            
            if (priceIncludesTax == '1') {
                if (tax && tax > 0) {
                    basePrice = (parseFloat(basePrice + priceplus) - (parseFloat(basePrice + priceplus)*(1- 1/(1+parseFloat(tax))))).toFixed(2);
                    finalPrice = finalPrice + priceplus;
                    oldPrice = oldPrice + priceplus;
                } else {
                    basePrice = basePrice + priceplus;
                    finalPrice = finalPrice + priceplus;
                    oldPrice = oldPrice + priceplus;
                }
            } else {
                if (tax && tax > 0) {
                    finalPrice = (parseFloat(finalPrice + priceplus) + (parseFloat(priceplus)*(parseFloat(tax)))).toFixed(2);
                    basePrice = basePrice + priceplus;
                    oldPrice = oldPrice + priceplus;
                } else {
                    finalPrice = finalPrice + priceplus;
                    basePrice = basePrice + priceplus;
                    oldPrice = oldPrice + priceplus;
                }
            }

            $this.parents('.info-er-pu').find('.price-box .price-container> span[data-price-type="finalPrice"] > .price').text($('#currency-add').val() + parseFloat(finalPrice).toFixed(2));

            $this.parents('.info-er-pu').find('.price-box .price-container> span[data-price-type="basePrice"] > .price').text($('#currency-add').val() + parseFloat(basePrice).toFixed(2));

            $this.parents('.info-er-pu').find('.price-box .price-container> span[data-price-type="oldPrice"] > .price').text($('#currency-add').val() + parseFloat(oldPrice).toFixed(2));
            // return priceplus;
        },

        _ReloadPriceBundel: function ($this, productId, decimalSymbol, tax, priceIncludesTax) {
            var price = 0;
            var price_ect = 0;
            var itemp = $('.er-pu-'+ productId);
            $(itemp).find('input').each(function () {
                if ($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio') {
                    if ($(this).is(':checked')) {
                        if ($(this).attr('price') > 0) {
                            price += parseFloat($(this).attr('price'));
                            if (tax && tax > 0) {
                                price_ect += parseFloat((parseFloat($(this).attr('price')) - (parseFloat($(this).attr('price'))*(1- 1/(1+parseFloat(tax))))).toFixed(2));
                            } else {
                                price_ect += parseFloat((parseFloat($(this).attr('price'))).toFixed(2));                                
                            }
                        }
                    }
                }
            })

            $(itemp).find('select').each(function () {
                if ($(this).is("select[multiple]")) {
                    if ($(this).val() != '') {
                        $(this).find('option:selected').each(function () {
                            if ($(this).attr('price') > 0) {
                                price += parseFloat($(this).attr('price'));
                                if (tax && tax > 0) {
                                    price_ect += parseFloat((parseFloat($(this).attr('price')) - (parseFloat($(this).attr('price'))*(1- 1/(1+parseFloat(tax))))).toFixed(2));
                                } else {
                                    price_ect += parseFloat((parseFloat($(this).attr('price'))).toFixed(2));
                                }
                            }
                        });
                    }
                } else {
                    if ($(this).val() != '') {
                        if ($('option:selected', this).attr('price') > 0) {
                            price += parseFloat($('option:selected', this).attr('price'));
                            if (tax && tax > 0) {
                                price_ect += parseFloat((parseFloat($('option:selected', this).attr('price')) - (parseFloat($('option:selected', this).attr('price'))*(1- 1/(1+parseFloat(tax))))).toFixed(2));
                            } else {
                                price_ect += parseFloat((parseFloat($('option:selected', this).attr('price'))).toFixed(2));
                            }
                        }
                    }
                }
            })

            $this.parents('.info-er-pu').find('.price-box .price-container> span[data-price-type="maxPrice"] > .price').text($('#currency-add').val() + (parseFloat(price)).toFixed(2))

            $this.parents('.info-er-pu').find('.price-box .price-container> span[data-price-type="basePrice"] > .price').text($('#currency-add').val() + (parseFloat(price_ect)).toFixed(2))

            $this.parents('.info-er-pu').find('.price-box .price-container> span[data-price-type="oldPrice"] > .price').text($('#currency-add').val() + (parseFloat(price)).toFixed(2))
        },

        _ReloadPriceDownloads: function ($this, productId, decimalSymbol, tax, priceIncludesTax) {
            var price = 0;
            var price_ect = 0;
            var itemp = $('.er-pu-'+ productId);
            $(itemp).find('input').each(function () {
                if ($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio') {
                    if ($(this).is(':checked')) {
                        if ($(this).parent().find("span[data-price-type='']").length > 0 ) {
                            if ($(this).parent().find("span[data-price-type='']").first().attr('data-price-amount') > 0) {
                                price += parseFloat($(this).parent().find("span[data-price-type='']").first().attr('data-price-amount'));
                                if (tax && tax > 0) {
                                    price_ect += parseFloat((parseFloat($(this).parent().find("span[data-price-type='']").first().attr('data-price-amount')) - (parseFloat($(this).parent().find("span[data-price-type='']").first().attr('data-price-amount'))*(1- 1/(1+parseFloat(tax))))).toFixed(2));
                                } else {
                                    price_ect += parseFloat((parseFloat($(this).parent().find("span[data-price-type='']").first().attr('data-price-amount'))).toFixed(2));
                                }
                            }
                        }
                    }
                }
            })

            $this.parents('.info-er-pu').find('.price-box .price-container> span[data-price-type="finalPrice"] > .price').text($('#currency-add').val() + (parseFloat(price)).toFixed(2))

            $this.parents('.info-er-pu').find('.price-box .price-container> span[data-price-type="basePrice"] > .price').text($('#currency-add').val() + (parseFloat(price_ect)).toFixed(2))

            $this.parents('.info-er-pu').find('.price-box .price-container> span[data-price-type="oldPrice"] > .price').text($('#currency-add').val() + (parseFloat(price)).toFixed(2))
        },

        _AddToCart: function ($this) {
            var $widget = this;
            var form = $this.parents('form').get(0);
            var addUrl = this.options.urlAddToCart;
            var data = '';
            var product_id = '';
            if ($this.hasClass('tocart')) {
                if (form && $(form).attr('id') != 'wishlist-view-form') {
                    var serialize = $(form).serialize();
                    $widget._sendAjax(addUrl, serialize);
                } else {
                    var qty = $this.siblings('.qty-m-c').val();
                    var dataPost = $.parseJSON($this.attr('data-post'));
                    if (dataPost && dataPost.data.product) {
                        product_id = dataPost.data.product;
                    } else if ($this.parents('.product-item').find('input[name="product"]').first().val() !='') {
                        product_id = $this.parents('.product-item').find('input[name="product"]').first().val();
                    } else {
                        product_id = $this.parents('.product-item').find('.price-box').first().data('product-id');
                    }
                    if (Math.floor(product_id) == product_id && $.isNumeric(product_id)) {
                        data +='&product=' + product_id + '&qty=' + qty;
                        $widget._sendAjax(addUrl, data);
                        return false;
                    }
                }
            }
            if ($this.hasClass('addmanytocart')) {
                var form = $('#add-muntiple-product-'+ $this.data('froma'));
                if ($(form).find('.product-select').length == 0) {
                    alert($.mage.__('Please select product !'));
                    return false;
                }
                data = $(form).serialize();
                addUrl = $(form).attr('action');
                $widget._sendAjax(addUrl, data);
                return false;
            }
            if ($this.hasClass('addmanytocart-popup')) {
                var dataForm = $('#product_addmuntile_form_popup');
                dataForm.mage('validation', {});
                var form = $this.parents('form').get(0);
                if ($(dataForm).valid()) {
                    $('.fancybox-opened').css('zIndex', '1');
                    var addUrl = $(form).attr('action');
                    var data = $(form).serialize();
                    $widget._sendAjax(addUrl, data);
                }
                return false;
            }
        },

        _sendAjax: function (addUrl, data) {
            var $widget = this;
            $.fancybox.showLoading();
            $.fancybox.helpers.overlay.open({parent: 'body'});
            $.ajax({
                type: 'post',
                url: addUrl,
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.popup) {
                        $('#bss_ajaxmuntiple_cart_popup').html(data.popup);
                        $('#bss_ajaxmuntiple_cart_popup').trigger('contentUpdated');
                        $.fancybox({
                            href: '#bss_ajaxmuntiple_cart_popup',
                            modal: false,
                            helpers: {
                                overlay: {
                                    locked: false
                                }
                            },
                            afterClose: function () {
                            }
                        });
                    } else {
                        $.fancybox.hideLoading();
                        $('.fancybox-overlay').hide();
                        return false;
                    }
                },
                error: function (xhr, status, error) {
                    console.log(status);
                    console.log(error);
                    $.fancybox.hideLoading();
                    $('.fancybox-overlay').hide();
                    return false;
                    // window.location.href = '';
                }
            });
        }
    });
    return $.mage.Multipleaddtocart;
});
