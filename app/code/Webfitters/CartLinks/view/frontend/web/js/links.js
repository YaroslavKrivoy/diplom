define(["jquery"], function($){
	function CartLinks(){

	}

	CartLinks.prototype.init = function(){
		$(document).ready(function(){
			$('.modal-close').on('click', function(event){
				event.preventDefault();
				$(event.currentTarget).closest('.add-success').fadeOut();
			});
		});
	}

	return new CartLinks();

});