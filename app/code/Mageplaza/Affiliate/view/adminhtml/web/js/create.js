/**
 * Copyright © 2016 Mageplaza. All rights reserved.
 * See https://www.mageplaza.com/LICENSE.txt for license details.
 */
define([
    'jquery',
    'prototype'
], function(jQuery) {

    window.AffiliateCreate = new Class.create();

    AffiliateCreate.prototype = {
        initialize: function(data){
            this.dataContainer = jQuery(data.dataContainer);
            this.customerContainer = jQuery(data.customerContainer);
            this.hideButton = jQuery(data.hideButton);
            this.showButton = jQuery(data.showButton);

            this.loadBaseUrl = this.dataContainer.data('load-base-url');
            this.customerId = false;
        },

        selectCustomer: function(grid, event){
            var element = Event.findElement(event, 'tr');
            if (element.title){
                this.setCustomerId(element.title);
            }
        },
        setCustomerId : function(id){
            this.customerId = id;
            this.loadArea('data');
        },
        loadArea : function(area){
            var deferred = new jQuery.Deferred();
            var url = this.loadBaseUrl;
            if (area) {
                url += 'block/' + area;
            }
            new Ajax.Request(url, {
                parameters:{form_key: FORM_KEY, customer_id: this.customerId},
                onSuccess: function(transport) {
                    var response = transport.responseText;
                    this.dataContainer.html(response);

                    this.updateContent();

                    deferred.resolve();
                }.bind(this)
            });

            return deferred.promise();
        },
        updateContent: function(){
            this.dataContainer.show();
            this.customerContainer.hide();

            this.hideButton.hide();

            this.showButton.show();
        }
    };
});
