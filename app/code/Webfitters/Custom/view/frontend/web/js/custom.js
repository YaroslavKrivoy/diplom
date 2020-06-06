define([
	"jquery",
	"webfitters.bootstrap",
	"webfitters.bxslider"
], function($, bs, bxslider){
	function Custom(){

	}

	Custom.prototype.init=function(){
		var t=this;
		$(document).ready(function(){
			$(document).on('click', '.account-dropdown', function(event){
				event.preventDefault();
				$(event.currentTarget).parent().find('.menu-holder').slideToggle();
			});
			$('.megamenu-link').on('click', function(event){
				event.preventDefault();
				$(event.currentTarget).toggleClass('open');
				if($(document).width()<1040){
					$('.nav-sections-item-content ul:first-child li:nth-child(2) .megamenu').slideToggle();
				}else{
					$('.page-header').children('.minicart-wrapper').next().slideToggle();
				}
			});
			$(document).on('click', function(event){
				$('.block.block-search').find('.open').removeClass('open');
			});
			$(document).on('click', '.block.block-search', function(event){
				event.preventDefault();
				event.stopPropagation();
				return false;
			});
			$(document).on('click', '.field.search .label', function(event){
				// if($(event.currentTarget).hasClass('open')){
					$(event.currentTarget).closest('form').get(0).submit();
				// } else {
				// 	event.preventDefault();
				// 	$(event.currentTarget).addClass('open');
				// 	$(event.currentTarget).closest('.form').find('.control').addClass('open');
				// }
			});
			$('.cms-faqs #maincontent h4').on('click', function(event){
				event.preventDefault();
				$(event.currentTarget).toggleClass('open');
				$(event.currentTarget).next('.faq-wrap').slideToggle();
			});
			$('.products .product-item-info').on('mouseenter', function(event){
				event.preventDefault();
				$(event.currentTarget).find('.actions-primary').slideDown();
			});
			$('.products .product-item-info').on('mouseleave', function(event){
				event.preventDefault();
				$(event.currentTarget).find('.actions-primary').slideUp();
			});
			$('.nav-toggle').on('click', function(event){
				event.preventDefault();
				$('.header.content .nav-sections').slideToggle();
			});
			$('.tocart').on('click', function(event){
				fbq('track', 'AddToCart');
			});
			$('.checkout').on('click', function(event){
				fbq('track', 'InitiateCheckout');
			});
			$(document).on('click', '.payment-method-title', function(event){
				fbq('track', 'AddPaymentInfo');
			});
			$(window).on('resize', function(){
				t.resize();
			});
			window.onload=function(){
				if(t.getCookie('announcement_shown') != '1'){
					setTimeout(function(){
						$('.announcement-panel').css('right', '0');
						$('.panel-close').on('click', function(event){
							$(event.currentTarget).closest('.announcement-panel').css('right', '-500px');
						});
						t.setCookie('announcement_shown', '1', 0.5);
					}, 3000);
				}
			}
			t.resize();

			$('.badge-slider').bxSlider({
				minSlides: 1,
				maxSlides: 5,
				moveSlides: 1,
				shrinkItems: true,
				slideWidth: 260,
				adaptiveHeight: true,
				pager: false,
				auto: true,
				autoStart: true,
				pause: 8000,
				controls: false,
			});

			// Adding megamenu to the mobile navigation as well
			var megamenu = $('.megamenu').html();
			$('.nav-sections-item-content ul:first-child li:nth-child(2)').append('<div class="megamenu"></div>');
			$('.nav-sections-item-content .megamenu' ).html(megamenu);
		});
	}

	Custom.prototype.setCookie = function(name, value, days){
		var expires = "";
	    if (days) {
	        var date = new Date();
	        date.setTime(date.getTime() + (days*24*60*60*1000));
	        expires = "; expires=" + date.toUTCString();
	    }
	    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
	}

	Custom.prototype.getCookie = function(name){
		var nameEQ = name + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0;i < ca.length;i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1,c.length);
	        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	    }
	    return null;
	}

	Custom.prototype.resize=function(){
		// Want to explicitely set height so that mobile menu doesn't adjust
		// margin top of items when menu is open
		siteHeaderHeight = 146;
		if($(document).width()<1040){
			siteHeaderHeight = 80;
		}
		if($('.hero-image').length>0){
			$('.hero-image').css('margin-top', siteHeaderHeight+'px');
		} else {
			$('#maincontent').css('margin-top', siteHeaderHeight+'px');
		}

		if($(document).width()<1040){
			//go mobile
			if($('.header.content').find('.nav-sections').find('.block.block-search').length == 0){
				$('.header.content').find('.nav-sections').prepend($('.page-header').find('.block.block-search').remove());
			}
			$('.header.content').find('.nav-sections').find('.mobile-bottom').before($('.page-header').find('.account-holder').remove());
		} else {
			//go desktop
			//$('.header.content').find('.logo').after($('.page-header').find('.block.block-search').remove());
			$('.page-header').find('.panel.header').find('.after-links').prepend($('.page-header').find('.account-holder').remove());
		}
		if($(document).width() >= 1040){
			$('.header.content').append($('.nav-sections').find('.block.block-search').remove());
		}
	}

	return new Custom();

})
