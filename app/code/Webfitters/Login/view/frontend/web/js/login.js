define([
	"jquery"
], function($){
	function Login(){

	}

	Login.prototype.init=function(){
		$(document).ready(function(){
			$('.authorization-link').on('click', function(event){
				event.preventDefault();
				$('.login-form-holder').fadeIn();
			});
			$('.login-form-holder-close').on('click', function(event){
				event.preventDefault();
				$(event.currentTarget).closest('.login-form-holder').fadeOut();
			});
		});
	}

	return new Login();

})