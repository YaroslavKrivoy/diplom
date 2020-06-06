define([
	"jquery",
	"webfitters.masonry"
], function($, masonry){
	function Grid(){

	}

	Grid.prototype.init=function(){
		$(document).ready(function(){
			$('.post-list-body').each(function(){
				var iso = new masonry(this, {
   					itemSelector: '.post-list-item',
			    	layoutMode: 'fitRows'
				});
			});
		});
	}

	return new Grid();
});