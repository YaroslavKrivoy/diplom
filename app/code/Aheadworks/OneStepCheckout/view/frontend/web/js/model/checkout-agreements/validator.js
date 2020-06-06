/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define(
    [
        'jquery',
        'mage/validation'
    ],
    function ($) {
        'use strict';

        var checkoutConfig = window.checkoutConfig,
            agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {};

        return {
            agreementsForm: '[data-role=checkout-agreements-form]',
            agreementsInput: '[data-role=checkout-agreements-input]',

            /**
             * Validate checkout agreements
             *
             * @returns {boolean}
             */
            validate: function() {
                var input = $(this.agreementsForm + ' ' + this.agreementsInput),
                    validator,
                    failureFound;

                if (agreementsConfig.isEnabled && input.length > 0) {
                    validator = $(this.agreementsForm).validate({
                        errorClass: 'mage-error',
                        errorElement: 'div',
                        meta: 'validate',
                        ignore: '[type=hidden]',
                        errorPlacement: function (error, element) {
                            var errorPlacement = element;

                            if (element.is(':checkbox') || element.is(':radio')) {
                                errorPlacement = element.siblings('label').last();
                            }
                            errorPlacement.after(error);
                        }
                    });

                    failureFound = false;
                    input.each(function(){
                        if (!validator.element(this)) {
                            failureFound = true;
                        }
                    });

                    return !failureFound;
                }

                return true;
            }
        }
    }
);
