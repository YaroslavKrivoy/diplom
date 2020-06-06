/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Customer/js/model/customer',
    'mage/validation'
], function ($, customer) {
    'use strict';

    return {

        /**
         * Validate checkout agreements
         *
         * @returns {Boolean}
         */
        validate: function () {
            var emailValidationResult = customer.isLoggedIn(),
                loginFormSelector = 'form[data-role=email-with-possible-login]',
                customerEmailSelector = '#customer-email',
                $loginForm;

            if (!customer.isLoggedIn()) {
                $loginForm = $(loginFormSelector);
                $loginForm.validation();
                emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());

                if (!emailValidationResult) {
                    $loginForm.find(customerEmailSelector).focus()
                }
            }

            return emailValidationResult;
        }
    };
});
