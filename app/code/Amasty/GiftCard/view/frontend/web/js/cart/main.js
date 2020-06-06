define([
    "jquery",
    "jquery/ui",
    'mage/mage'
], function ($) {

    $.widget('mage.amGiftcardCart', {
        options: {},

        _create: function () {
            var self = this,
                dataForm = $('[data-amgiftcard-js="amgiftcard-form"]');

            this.checkCardUrl = this.options.checkCardUrl;
            if ($('[data-amgiftcard-js="amgiftcard-check-status"]').length) {
                $('[data-amgiftcard-js="amgiftcard-check-status"]').on('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    $.ajax({
                        url: this.checkCardUrl,
                        data: {"amgiftcard": dataForm.serialize()},
                        type: 'post',
                        showLoader: true,
                        success: function(response) {
                            $('[data-amgiftcard-js="amgiftcard-info"]').html(response);
                        }
                    });
                }.bind(this));
            }

            $('[data-amgiftcard-js="amgiftcard-remove"]').on('click', function () {
                 self.removeGiftCard($(this));
            });

            dataForm.mage('validation', {});
        },

        removeGiftCard: function (removeLink) {
            var form = $('[data-amgiftcard-js="amgiftcard-form-post"]'),
                data = removeLink.data('href');

            form.attr('action', data);
            form.submit();
            form.trigger("submit");
        }

    });
});