define(
    [
        'ko',
        'uiComponent',
		'jquery',
		'mage/url',
		'Magento_Checkout/js/model/quote',
		'Magento_Checkout/js/model/full-screen-loader',
		'Magento_Customer/js/model/customer',
		'mage/translate'
    ],
    function (ko, Component , $, getUrl, quote, fullScreenLoader, customer,$t) {
        "use strict";

        return Component.extend({
            defaults: {
               template: 'MageArray_StorePickup/storepickup'
            },
            showStorePickup: function() {
				var value = $("#checkout-with-store:checked").val();
				if(typeof value  !== "undefined") {
					$("#label_method_storepickup_storepickup").parent().css("pointer-events","auto");
					$("#label_method_storepickup_storepickup").parent().css("opacity","1");
					$("#label_method_storepickup_storepickup").parent().siblings(".row").css("pointer-events","none");
					$("#label_method_storepickup_storepickup").parent().siblings(".row").css("opacity","0.5");
					$("#label_method_storepickup_storepickup").parent().trigger("click");
					if (customer.isLoggedIn()) {
						$("#checkout-step-shipping").prev().css({"display": "none"});
						$("#checkout-step-shipping").css({"display": "none"});
					} else {
						$("#co-shipping-form").hide();
					}
				} else {
					$("#label_method_storepickup_storepickup").parent().siblings().hide();
					$("#label_method_storepickup_storepickup").parent().siblings(".row").show();
					$("#label_method_storepickup_storepickup").parent().siblings(".row").css("pointer-events","auto");
					$("#label_method_storepickup_storepickup").parent().siblings(".row").css("opacity","1");
					$("#label_method_storepickup_storepickup").parent().css("pointer-events","none");
					$("#label_method_storepickup_storepickup").parent().css("opacity","0.5");
					
					if(quote.shippingMethod() && quote.shippingMethod().carrier_code == "storepickup") {
						$("#label_method_storepickup_storepickup").parent().siblings(".row").first().trigger("click");
					}
					if (customer.isLoggedIn()) {
						$("#checkout-step-shipping").prev().css({"display": "block"});
						$("#checkout-step-shipping").css({"display": "block"});
					} else {
						$("#co-shipping-form").show();
					}
				}
				var storeList = jQuery.parseJSON(checkoutConfig.storeList);
				var storeCount = 0;
				if(storeList) {
					storeCount = storeList.length;
				}
				var storeDetails = checkoutConfig.storePickup;
				quote.shippingMethod.subscribe(function (value) {
					var storeHtml = "<tr class='storepickup-row'><td colspan=4><div class='storepickup-info'></div></td></tr>";
					var dateHtml = '<div id="storepickup-date" class="storepickup-date-show field required">'
					+ '<label for="pickup_date">'+$t('Pickup Date :')+'</label>'
					+ '<input name="pickup_date" type="text" id="pickup_date" value="" class="input-text required-entry form-control" readonly>'+'<br /><br />'+ '<label class="pickup-time" for="pickup_time">'+$t('Pickup Time :')+'</label>'
					+ '<select name="pickup_time" class="pickup-time" id="pickup_time" value="" class="input-text form-control" ></select>'
					+ '</div>' +'<br />';
					var storepickupMethod = $("#label_method_storepickup_storepickup").parent();
					if($(".storepickup-info").length == 0) {
						storepickupMethod.after(storeHtml);
						$(".storepickup-info").append("<div class='show-store-detail'><label>"+$t('Pickup Store')+"</label></div>");
						$(".show-store-detail").append("<select id='pickup-store' class='store-detail-select'></select>");
						$(".store-detail-select").append("<option class='store-detail-item' value=''>"+$t('Please Select Store')+"</option>");
						$.each(storeList, function (index, el) {
							$(".store-detail-select").append("<option class='store-detail-item' value='"+el.storepickup_id+"'>"+$t(el.store_name)+"</option>");
						});
						$(".storepickup-info").append("<div class='display-storeinfo'></div>");
						$('.display-storeinfo').hide();
						var storeSelected = checkoutConfig.quoteData.pickup_store;

						$('#pickup-store').change(function () {
							$.each(storeList, function (index, el) {
								if(el.storepickup_id == $('#pickup-store').val()){
								fullScreenLoader.startLoader();
								$.ajax({
									type: 'POST',
									url: getUrl.build('storepickup/checkout/store'),
									data: {storeId: $('#pickup-store').val()},
									dataType: 'json',
									success: function(data) {	
										if(data.success == 1) {
											if(data.html) {
												$('.display-storeinfo').html(data.html);
												if(data.disable_date != 1) {
													if($("#storepickup-date").length == 0){
														$("#label_method_storepickup_storepickup").parent().trigger("click");
														$(".storepickup-info").append(dateHtml);
													} else {
														$('#storepickup-date').show();
													}
												}
												checkoutConfig.quoteData.pickup_store = $("#pickup-store").val();
												fullScreenLoader.stopLoader();
												var holidays = data.holidays;
												if(holidays) {
													holidays = holidays.split(',');
												}
												$('.display-storeinfo').show();
												$('.pickup-time').hide();
												$('#pickup_time').empty();
												$("#pickup_date").removeAttr('value');
												var timeslot = data.timeslot;
												var additionalTimeslot = data.additional_timeslot;
												$("#pickup_date").datepicker("destroy");
												$("#pickup_date").datepicker( {
													minDate: -0,
													dateFormat: 'mm/dd/yy',
													beforeShowDay: function(day) {
														var date = (day.getMonth() + 1)+"/"+ day.getDate() +"/"+day.getFullYear();
														if ($.inArray(date, holidays) != -1 ) {
															return [ false ];
														} else {
															return [ ($.inArray(day.getDay(),data.working_days) == -1) ];
														}
													}
												}).on("change", function(e) {
													$("#label_method_storepickup_storepickup").parent().trigger("click");
													var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];	
													var day = new Date($(this).val());
													var dayText = weekday[day.getDay()];
													var date = (day.getMonth() + 1)+"/"+ day.getDate() +"/"+day.getFullYear();
													if(timeslot) {
														$('#pickup_time').empty();
														$('.pickup-time').hide();
														if(dayText in timeslot) {
															var dayTimeSlot = timeslot[dayText];
															if(dayTimeSlot) {
																dayTimeSlot = dayTimeSlot.split(',');
																$('#pickup_time').empty();
																$.each(dayTimeSlot, function( index, value ) {
																	  $('#pickup_time')
																		 .append($("<option></option>")
																					.attr("value",value)
																					.text(value)); 
																});
																$('.pickup-time').show();
															}
														}
														if(additionalTimeslot) {
															if(date in additionalTimeslot) {
																var dateTimeslot = additionalTimeslot[date];
																if(dateTimeslot) {
																	$('#pickup_time').empty();
																	dateTimeslot = dateTimeslot.split(',');
																	$.each(dateTimeslot, function( index, value ) {
																		  $('#pickup_time')
																			 .append($("<option></option>")
																						.attr("value",value)
																						.text(value)); 
																	});
																	$('.pickup-time').show();
																}
															}
														}
													} else {
														$('.pickup-time').hide();
													}
													checkoutConfig.quoteData.pickup_date = $("#pickup_date").val();
													$('#pickup_time').change(function () {
														$("#label_method_storepickup_storepickup").parent().trigger("click");
														if($("#pickup_time").val()) {
															checkoutConfig.quoteData.pickup_time = $("#pickup_time").val();
														}
													});
													var pickupTime = checkoutConfig.quoteData.pickup_time;
													if(pickupTime) {
														jQuery('#pickup_time').val(pickupTime);
														jQuery('#pickup_time').trigger("change");
													}
												});
												var pickupDate = checkoutConfig.quoteData.pickup_date;
												
												
												if(pickupDate) {
													jQuery('#pickup_date').datepicker('setDate', pickupDate);
													jQuery('#pickup_date').trigger("change");
												}
											}
										} else {
											fullScreenLoader.stopLoader();
											$('.display-storeinfo').hide();
											$('#storepickup-date').hide();
										}
									}
								});
								} else {
									$('.display-storeinfo').hide();
									$('#storepickup-date').hide();
								} 
							});
							
						});
						
						
						if(storeCount === 1) {
							var preSelectStore = storeList[0].storepickup_id;
							$("#pickup-store").val(preSelectStore);
							$("#pickup-store").trigger("change");
						}
						if(storeSelected) {
							$("#pickup-store").val(storeSelected);
							$("#pickup-store").trigger("change");
						}
					}
					if (quote.shippingMethod().carrier_code == 'storepickup') {
						$(".storepickup-row").show();
					} else {
						$(".storepickup-row").hide();
					}
				});
			}
        });
    }
);