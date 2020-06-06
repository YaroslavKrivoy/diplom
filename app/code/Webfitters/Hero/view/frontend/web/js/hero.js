define([
	"jquery",
	"webfitters.parallax"
], function($, parallax){
	function Hero(){}

	Hero.prototype.init=function(){
		$(document).ready(function(){
			$('.parallax-hero').parallax();
		});
	}

	return new Hero();

})