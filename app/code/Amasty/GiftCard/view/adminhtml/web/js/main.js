define([
    "mage/url",
    "jquery",
    "mage/translate",
    "jquery/ui"
], function (urlBuilder, $, $translate) {

    $.widget('mage.amGiftcard', {
        options: {
            maxSizeUserImage: 1572864,
            extentionsUserImage: [
                'image/png',
                'image/jpeg',
                'image/gif'
            ],
            incorrectSizeMessage: $translate("The image couldn't be uploaded because it exceeds 1,5 Mb, the maximum allowed size for uploads."),
            incorrectTypeMessage: $translate("The image couldn't be uploaded, only files with the following extensions are allowed: jpg, gif, png"),
            previewUrl: 'amgiftcard/preview/index'
        },
        
        _create: function () {
            var giftcardAmount = $('[data-amgiftcard-js="amgiftcard-amount"]'),
                giftcardCustomAmount = $('[data-amgiftcard-js="amgiftcard-amount-custom"]'),
                giftcardType = $('[data-amgiftcard-js="amgiftcard-type"]'),
                giftcardImageInput = $('[data-amgiftcard-js="amgiftcard-image-input"]'),
                giftcardPreview = $('[data-amgiftcard-js="amgiftcard-preview"]');

            this.productId = this.options.productId;
            this.feeType = this.options.feeType;
            this.feeValue = this.options.feeValue;

            if (giftcardAmount) {
                giftcardAmount.on('change', this.onGiftChanged.bind(this));
            }

            if (giftcardCustomAmount) {
                giftcardCustomAmount.on('change', this.onGiftChanged.bind(this));
            }

            if (giftcardType) {
                giftcardType.on('change', this.visibilityFields.bind(this));
            }

            if ($('#am_giftcard_amount')) {
                $('#am_giftcard_amount').on('change', this.onGiftChanged.bind(this));
                $('#am_giftcard_amount').val('custom');
                $('#am_giftcard_amount').css('display', 'none');
                this.onGiftChangeNoEvent();
                //$('#am_giftgard_amount').get(0).dispatchEvent(new ChangeEvent());
            }

             if($('#am_giftcard_date_delivery_timezone')){
                $('#am_giftcard_date_delivery_timezone').val('America/Chicago');
                $('#am_giftcard_date_delivery_timezone').closest('.field').css('display', 'none');
            }

            giftcardPreview.on('click', this.previewGiftcard.bind(this));

            if ($('#product-addtocart-button').length) {
                $('#product-addtocart-button').on('click', function (event) {
                    giftcardImageInput.show();
                    if (!$('#product_addtocart_form').validate().form()) {
                        giftcardImageInput.hide();
                        event.preventDefault();
                        event.stopPropagation();
                    } else {
                        giftcardImageInput.hide();
                    }
                });
            }

            if ($('[data-amgiftcard-js="amgiftcard-amount-item"]').length <= 1) {
                var value = $('[data-amgiftcard-js="amgiftcard-amount-item"]:selected').data('value'),
                    changes = {
                        "giftcard": {
                            "finalPrice": {
                                "amount": value
                            }
                        }
                    };

                $('#product-price-' + this.productId).trigger('updatePrice', changes);
            }

            $('#product_addtocart_form').attr('enctype', 'multipart/form-data');
            this.clickImage();
            this.getUserImage();
        },

        onGiftChangeNoEvent: function(event){
            var value = $('#am_giftcard_amount option:selected').data('value'),
                valueForToggle = $('#am_giftcard_amount').val();

            if(valueForToggle == "custom" || valueForToggle == "") {
                $('#amgiftcard_amount_custom_block').show();
                value = valueForToggle;
                return;
            } else if(event.target.id == 'am_giftcard_amount') {
                $('#amgiftcard_amount_custom_block').hide();
            } else if(value === undefined
                    && typeof valueForToggle === "string"
            ){
                value = valueForToggle;
            }

            var feeValue = parseFloat(this.feeValue);
            if (isNaN(feeValue)) {
                feeValue = 0;
            }
            value = parseFloat(value);

            if (this.feeType == 1) {    // PRICE_TYPE_PERCENT
                value += value * feeValue / 100;
            } else if (this.feeType == 2) {     // PRICE_TYPE_FIXED
                value += feeValue;
            }

            var changes = {
                "giftcard": {
                    "finalPrice": {
                        "amount": value
                    }
                }
            };

            $('#product-price-' + this.productId).trigger('updatePrice', changes);
        },

        onGiftChanged: function (event) {
            var value = $('[data-amgiftcard-js="amgiftcard-amount"] option:selected').data('value'),
                valueForToggle = event.target.value;

            if (valueForToggle == "custom" || valueForToggle == "") {
                $('[data-amgiftcard-js="amgiftcard-custom-amount-wrapper"]').show();
                value = valueForToggle;
                return;
            } else if (event.target.id == 'am_giftcard_amount') {
                $('[data-amgiftcard-js="amgiftcard-custom-amount-wrapper"]').hide();
            } else if (value === undefined && typeof valueForToggle === "string") {
                value = valueForToggle;
            }

            var feeValue = parseFloat(this.feeValue);
            if (isNaN(feeValue)) {
                feeValue = 0;
            }
            value = parseFloat(value);

            if (this.feeType == 1) {    // PRICE_TYPE_PERCENT
                value += value * feeValue / 100;
            } else if (this.feeType == 2) {     // PRICE_TYPE_FIXED
                value += feeValue;
            }

            var changes = {
                "giftcard": {
                    "finalPrice": {
                        "amount": value
                    }
                }
            };

            $('#product-price-' + this.productId).trigger('updatePrice', changes);
        },

        visibilityFields: function () {
            var value = event.target.value,
                giftcardRecipientData = $('[data-amgiftcard-js="amgiftcard-recipient-data"]'),
                giftcardDeliveryInfo = $('[data-amgiftcard-js="amgiftcard-delivery-info"]');

            $.each(giftcardRecipientData, function (key, val) {
                $(val).show();
            });
            if (value == 2) {
                $.each(giftcardRecipientData, function (key, val) {
                    $(val).hide();
                });
            }
            if (value == 2 || value == 3) { /*TYPE_PRINTED = 2; TYPE_COMBINED = 3;*/
                $.each(giftcardDeliveryInfo, function (key, val) {
                    $(val).show();
                });
            } else {
                $.each(giftcardDeliveryInfo, function (key, val) {
                    $(val).hide();
                });
            }
        },

        clickImage: function () {
            var self = this,
                giftCardImages = $('[data-amgiftcard-js="amgiftcard-image"]');

            $('[data-gallery-role="gallery-placeholder"]').on('fotorama:load', function () {
                if (giftCardImages.length == 1) {
                    self.changeMainImage(giftCardImages);
                }
            });

            $('[data-amgiftcard-js="amgiftcard-images"]').on('click', '[data-amgiftcard-js="amgiftcard-image"]', function () {
                self.changeMainImage($(this));
            });

        },

        changeMainImage: function (newImage) {
            var fullImage = $('.fotorama__img'),
                userImageContainer = $('.amgiftcard-image-container'),
                imageInputField = $('[data-amgiftcard-js="amgiftcard-image-input"]'),
                giftCardImages = $('[data-amgiftcard-js="amgiftcard-image"]');

            $.each(giftCardImages, function (key, image) {
                $(image).removeClass('-selected');
            });

            userImageContainer.removeClass('-selected');
            if (newImage.parents('.amgiftcard-image-container').length) {
                newImage = newImage.parents('.amgiftcard-image-container');
            }
            newImage.addClass('-selected');
            imageInputField.val(newImage.attr('data-id'));

            if (fullImage && newImage.attr('src')) {
                fullImage.attr('src', newImage.attr('src'));
            } else {
                fullImage.attr('src', newImage.find('[data-amgiftcard-js="amgiftcard-userimage"]').attr('src'));
            }

            this.togglePreviewButton();
        },

        getUserImage: function () {
            $('[data-amgiftcard-js="amgiftcard-userimage-input"]').change(this.addUserImage.bind(this));
        },

        addUserImage: function (event) {
            var self = this,
                input = event.target,
                userImage = $('[data-amgiftcard-js="amgiftcard-userimage"]'),
                userImageContainer = $('.amgiftcard-image-container'),
                imageInputField = $('[data-amgiftcard-js="amgiftcard-image-input"]'),
                errorMessage = $('[data-amgiftcard-js="amgiftcard-userimage-error"]'),
                removeButton = $('[data-amgiftcard-js="amgiftcard-close"]'),
                errorMessageText;

            if (input.files && input.files[0] && input.files[0].size) {
                errorMessageText = self.validateUserImage(input.files[0]);

                if (!errorMessageText) {
                    var reader = new FileReader();

                    imageInputField.removeClass('required-entry');
                    errorMessage.slideUp();
                    reader.onload = function (e) {
                        userImage.attr('src', e.target.result);
                        userImageContainer
                            .addClass('amgiftcard-image')
                            .find('.amgiftcard-image-wrapper')
                            .attr('data-amgiftcard-js', 'amgiftcard-image');
                        self.changeMainImage(userImageContainer);
                        userImageContainer.removeClass('-hidden');
                        removeButton.show()
                    };

                    removeButton.on('click', function (event) {
                        event.preventDefault();
                        input.value = '';
                        userImageContainer.addClass('-hidden').removeClass('-selected');
                        userImage.prop('src', '');
                        removeButton.hide();
                        imageInputField.addClass('required-entry');
                        self.togglePreviewButton();
                        $('.fotorama__img').attr('src', $('[data-amgiftcard-js="amgiftcard-image"]').first().attr('src'));
                    });
                    reader.readAsDataURL(input.files[0]);
                } else {
                    errorMessage.text(errorMessageText).slideDown();
                }
            }
        },

        validateUserImage: function (userImage) {
            var self = this;

            if (userImage.size > self.options.maxSizeUserImage) {
                return self.options.incorrectSizeMessage;
            }

            if ($.inArray(userImage.type, self.options.extentionsUserImage) === -1) {
                return self.options.incorrectTypeMessage;
            }

            return false;
        },

        previewGiftcard: function (event) {
            var form = $(event.target.form),
                old_url = form.prop('action'),
                newUrl = urlBuilder.build(this.options.previewUrl);

            event.preventDefault();
            form.prop('action' , newUrl)
                .prop('target' , '_blank');
            event.target.form.submit();
            form.prop('action', old_url)
                .prop('target' ,'');
        },

        togglePreviewButton: function () {
            var previewButton = $('[data-amgiftcard-js="amgiftcard-preview"]'),
                selectedGiftcardImages = $('[data-amgiftcard-js="amgiftcard-images"] .-selected');

            previewButton.prop('disabled', !selectedGiftcardImages.length)
        }
    });

    return $.mage.amGiftcard;
});