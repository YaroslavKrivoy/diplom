define([
	"jquery",
	"webfitters.bxslider"
], function($){
	function Featured(){

	}

	Featured.prototype.init=function(){
		var t=this;
		$(document).ready(function(){
			var slider = $('.featured-slider .products').bxSlider({
				minSlides: 1,
				maxSlides: 4,
				moveSlides: 1,
				shrinkItems: true,
				slideWidth: 330,
				adaptiveHeight: true,
				pager: false,
				auto: true,
				autoStart: true,
				pause: 8000,
				controls: false,

			});
			$('.featured-slider').find('.next').on('click', function(event){
				event.preventDefault();
				slider.goToNextSlide();
			});
			$('.featured-slider').find('.prev').on('click', function(event){
				event.preventDefault();
				slider.goToPrevSlide();
			});
			/*$('.featured-slider').find('.bx-viewport').find('a').on('click', function(event){
				event.preventDefault();
				window.location.href = $(event.currentTarget).attr('href');
			});*/
		});
	}

	return new Featured();

})
