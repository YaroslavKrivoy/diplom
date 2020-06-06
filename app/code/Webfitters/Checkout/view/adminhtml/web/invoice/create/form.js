define([
    'jquery',
], function ($) {
    'use strict';

    var payment = {
        switchMethod: function(method){
            $('#order-billing_method_form').find('input[type="radio"]').each(function(){
                if($(this).val() != method){
                    $(this).closest('.admin__field-option').next().css('display', 'none');
                    $(this).closest('.admin__field-option').next().find('fieldset').css('display', 'none');
                } else {
                    $(this).closest('.admin__field-option').next().css('display', 'block');
                    $(this).closest('.admin__field-option').next().find('fieldset').css('display', 'block');
                    $('#edit_form').trigger('changePaymentMethod.'+$(this).val(), method);
                }
            });
            if(method){
                $('#order-billing_method_form').find('input', 'select', 'textarea').each(function(key, elem){
                    if(elem.type != 'radio') elem.disabled = true;
                });
                $('#payment_form_'+method).find('input', 'select', 'textarea').each(function(key, elem){
                    if(elem.type != 'radio') elem.disabled = false;
                });
            }
        }
    };
    window.order = {
        addExcludedPaymentMethod: function(code) {
            
        }
    };
    window.payment = payment;
});
