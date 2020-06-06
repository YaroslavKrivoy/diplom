define([
	"jquery",
	"webfitters.bxslider"
], function($, bxslider){
	function Slideshow(){

	}

	Slideshow.prototype.init=function(){
		$(document).ready(function(){
			$('.bxslider').each(function(){
				var slider = $(this).bxSlider({mode: 'fade', pager: false, auto: true, autoStart: true, pause: 8000, slideSelector: '.slide', controls: false});
				$(this).closest('.slider-wrap').find('.next').on('click', function(event){
					event.preventDefault();
					slider.goToNextSlide();
				});
				$(this).closest('.slider-wrap').find('.prev').on('click', function(event){
					event.preventDefault();
					slider.goToPrevSlide();
				});
			});
		});
	}

	return new Slideshow();

})