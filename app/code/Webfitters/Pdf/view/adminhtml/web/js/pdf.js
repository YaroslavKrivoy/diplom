define([
	"jquery"
], function($){
	function Pdf(){}

	Pdf.prototype.init=function(){
		var t=this;
		$(document).ready(function(){
			var delFunc = function(event){
				event.preventDefault();
				$.ajax({
					url: $(event.currentTarget).attr('data-action'),
					method: 'post',
					data: {id: $(event.currentTarget).closest('.pdf-holder').attr('data-id'), form_key: window.FORM_KEY},
					success: function(response){
						$(event.currentTarget).closest('.pdf-holder').remove();
					}
				})
			};
			var saveFunc = function(event){
				event.preventDefault();
				$.ajax({
					url: $(event.currentTarget).attr('data-action'),
					method: 'post',
					data: {
						id: $(event.currentTarget).closest('.pdf-holder').attr('data-id'), 
						form_key: window.FORM_KEY,
						link: $(event.currentTarget).closest('.pdf-holder').find('.link-editor').val()
					},
					success: function(response){
						console.log(response);
						$(event.currentTarget).closest('.pdf-holder').find('.link-holder').css('display', 'inline-block');
						$(event.currentTarget).closest('.pdf-holder').find('.link-holder').text($(event.currentTarget).closest('.pdf-holder').find('.link-editor').val());
						$(event.currentTarget).css('display', 'none');
						$(event.currentTarget).closest('.pdf-holder').find('.link-editor').css('display', 'none');
						$(event.currentTarget).closest('.pdf-holder').find('.edit-pdf').css('display', 'inline-block');
					}
				})
			};
			var editFunc = function(event){
				event.preventDefault();
				$(event.currentTarget).closest('.pdf-holder').find('.link-holder').css('display', 'none');
				$(event.currentTarget).css('display', 'none');
				$(event.currentTarget).closest('.pdf-holder').find('.link-editor').css('display', 'inline-block');
				$(event.currentTarget).closest('.pdf-holder').find('.save-pdf').css('display', 'inline-block');
			};
			$('.pdf-upload').on('click', function(event){
				event.preventDefault();
				var input = document.createElement('input');
				input.type = 'file';
				input.accept = 'application/pdf';
				input.dispatchEvent(new MouseEvent('click'));
				$(input).on('change', function(e){
					if(e.currentTarget.files.length > 0){
						var fd = new FormData();
						fd.append('pdf', e.currentTarget.files[0]);
						fd.append('form_key', window.FORM_KEY);
						$.ajax({
							url: $(event.currentTarget).attr('data-action'),
							method: 'post',
							showLoader: true,
							data: fd,
							cache: false,
							contentType: false,
							processData: false,
							success: function(response){
								var data = JSON.parse(response);
								var pdf = data.pdf;
								$('.delete-pdf').off('click', delFunc);
								$('.edit-pdf').off('click', editFunc);
								$('.save-pdf').off('click', saveFunc);
								$('.pdf-grid').prepend(
									'<div class="row pdf-holder" data-id="'+pdf.id+'">' +
										'<div class="col-md-2">' +
											'<img src="'+pdf.image+'" />' +
										'</div>' +
										'<div class="col-md-7">' +
											pdf.base+'pdf/download/<span class="link-holder">'+pdf.link+'</span>' +
											'<input type="text" class="link-editor" style="display: none; width: 300px;" value="'+pdf.link+'" />' +
										'</div>' +
										'<div class="col-md-1">' +
											pdf.created_at +
										'</div>' +
										'<div class="col-md-2">' +
											'<button class="action-primary pull-right edit-pdf"><i class="fa fa-fw fa-pencil"></i></button>' +
											'<button class="action-primary save-pdf pull-right" data-action="'+pdf.edit_url+'" style="display: none;"><i class="fa fa-fw fa-check"></i></button>' +
											'<button class="action-warning delete-pdf pull-right" data-action="'+pdf.delete_url+'"><i class="fa fa-fw fa-trash-o"></i></button>' +
										'</div>' +
									'</div>');
								$('.delete-pdf').on('click', delFunc);
								$('.edit-pdf').on('click', editFunc);
								$('.save-pdf').on('click', saveFunc);
							}
						});
					}
				});
			});
			$('.delete-pdf').on('click', delFunc);
			$('.edit-pdf').on('click', editFunc);
			$('.save-pdf').on('click', saveFunc);
		});
	}

	return new Pdf();

})