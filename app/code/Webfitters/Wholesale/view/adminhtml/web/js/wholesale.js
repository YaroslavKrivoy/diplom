define([
	"jquery",
], function($){
	function Wholesale(){}

	Wholesale.prototype.init=function(){
		$(document).ready(function(){
			function clearTypeModal(){
				var modal = $('.type-modal');
				modal.find('[name="title"]').val('');
				modal.find('[name="content"]').val('');
				modal.find('[name="id"]').val('');
				modal.find('[name="image"]').val('');
				modal.find('.nicer-image').find('img').remove();
				modal.find('[name="hero_image"]').val('');
			}

			var addNewType = function(event){
				event.preventDefault();
				$('.type-modal').get(0).targetRow = null;
				$('.type-modal').fadeIn();
			}

			var editType = function(event){
				event.preventDefault();
				var type = JSON.parse($(event.currentTarget).attr('data-type'));
				var modal = $('.type-modal');
				modal.get(0).targetRow = $(event.currentTarget).closest('.type-row').get(0);
				modal.find('[name="title"]').val(type.title);
				modal.find('[name="content"]').val(type.content);
				modal.find('[name="id"]').val(type.id);
				modal.find('[name="image"]').val('');
				modal.find('[name="hero_image"]').val('');
				var iinput = modal.find('[name="image"]').closest('.nicer-image');
				var hinput = modal.find('[name="hero_image"]').closest('.nicer-image');
				if(iinput.find('img').length == 0){
					iinput.prepend('<img/>');
				}
				if(hinput.find('img').length == 0){
					hinput.prepend('<img/>');
				}
				iinput.find('img').attr('src', type.image);
				hinput.find('img').attr('src', type.hero);
				modal.fadeIn();
			}

			var deleteType = function(event){
				event.preventDefault();
				$.ajax({
					method: 'post',
					url: $(event.currentTarget).attr('data-action'),
					showLoader: true,
					data: {'form_key': window.FORM_KEY, 'id': $(event.currentTarget).attr('data-id')},
					success: function(response) {
						$(event.currentTarget).closest('.type-group').remove();
					}
				});
			}

			var uploadImage = function(event){
				event.preventDefault();
				$(event.currentTarget).closest('.nicer-image').find('input[type="file"]').trigger('click');
			}

			var changeImage = function(event){
				event.preventDefault();
				if($(event.currentTarget).closest('.nicer-image').find('img').length == 0){
					$(event.currentTarget).closest('.nicer-image').prepend('<img />');
				}
				var files = event.currentTarget.files;
				if (FileReader && files && files.length) {
			        var fr = new FileReader();
			        fr.onload = function (e) {
			            $(event.currentTarget).closest('.nicer-image').find('img').attr('src', fr.result);
			        }
			        fr.readAsDataURL(files[0]);
			    }
			}

			var saveType = function(event){
				var modal =  $(event.currentTarget).closest('.modal-front');
				var hero_image = ((modal.find('[name="hero_image"]').get(0).files.length > 0)?modal.find('[name="hero_image"]').get(0).files[0]:null);
				var image = ((modal.find('[name="image"]').get(0).files.length > 0)?modal.find('[name="image"]').get(0).files[0]:null);
				var fd = new FormData();
				fd.append('hero_image', hero_image);
				fd.append('image', image);
				fd.append('title', modal.find('[name="title"]').val());
				fd.append('content', modal.find('[name="content"]').val());
				fd.append('id', modal.find('[name="id"]').val());
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
						$('.save-type').off('click', saveType);
						$('.delete-type').off('click', deleteType);
						$('.edit-type').off('click', editType);
						$('.add-category').off('click', addCategory);
						var row = $(modal.closest('.type-modal').get(0).targetRow);
						if(modal.closest('.type-modal').get(0).targetRow == null){
							row = $($('#type-template').html());
						}
						row.find('.image-holder').attr('src', data.type.image);
						row.find('.title-holder').text(data.type.title);
						row.find('.delete-type').attr('data-id', data.type.id);
						row.find('.edit-type').attr('data-type', JSON.stringify(data.type));
						row.find('.add-category').attr('data-type-id', data.type.id);
						if(modal.closest('.type-modal').get(0).targetRow == null){
							$('.type-holder').append(row);
						}
						$('.add-category').on('click', addCategory);
						$('.save-type').on('click', saveType);
						$('.delete-type').on('click', deleteType);
						$('.edit-type').on('click', editType);
						clearTypeModal();
						modal.closest('.type-modal').fadeOut();
					}
				});
			}

			var addCategory = function(event){
				event.preventDefault();
				$('.add-product').off('click', addProduct);
				$('.edit-category').off('click', editCategory);
				$('.delete-category').off('click', deleteCategory);
				$('.save-category').off('click', saveCategory);
				var template = $($('#category-template').html());
				template.find('[name="type_id"]').val($(event.currentTarget).attr('data-type-id'));
				$(event.currentTarget).closest('.type-group').find('.category-holder').append(template);
				$('.add-product').on('click', addProduct);
				$('.edit-category').on('click', editCategory);
				$('.delete-category').on('click', deleteCategory);
				$('.save-category').on('click', saveCategory);	
			}

			var editCategory = function(event){
				event.preventDefault();
				var row = $(event.currentTarget).closest('.category-row');
				row.find('[name="name"]').css('display', 'inline-block');
				row.find('.category-name').css('display', 'none');
				row.find('.edit-category').css('display', 'none');
				row.find('.save-category').css('display', 'inline-block');
			}

			var deleteCategory = function(event){
				event.preventDefault();
				if($(event.currentTarget).closest('.category-row').find('[name="id"]').val() != ''){
					$.ajax({
						method: 'post',
						url: $(event.currentTarget).attr('data-action'),
						data: {id: $(event.currentTarget).closest('.category-row').find('[name="id"]').val(), 'form_key': window.FORM_KEY},
						success: function(response){
							$(event.currentTarget).closest('.category-group').remove();
						}
					});
				} else {
					$(event.currentTarget).closest('.category-group').remove();
				}
			}

			var saveCategory = function(event){
				event.preventDefault();
				var row = $(event.currentTarget).closest('.category-row');
				$.ajax({
					method: 'post',
					url: $(event.currentTarget).attr('data-action'),
					data: {
						form_key: window.FORM_KEY,
						id: row.find('[name="id"]').val(),
						title: row.find('[name="name"]').val(),
						type_id: row.find('[name="type_id"]').val()
					},
					success: function(response){
						var data = JSON.parse(response);
						row.find('[name="id"]').val(data.category.id);
						row.find('[name="name"]').val(data.category.title);
						row.find('.category-name').text(data.category.title);
						row.find('.add-product').attr('data-category-id', data.category.id);
						row.find('[name="name"]').css('display', 'none');
						row.find('.category-name').css('display', 'inline-block');
						row.find('.edit-category').css('display', 'inline-block');
						row.find('.save-category').css('display', 'none');
					}
				});
			}

			var addProduct = function(event){
				event.preventDefault();
				$('.save-product').off('click', saveProduct);
				$('.edit-product').off('click', editProduct);
				$('.delete-product').off('click', deleteProduct);
				var template = $($('#product-template').html());
				template.find('[name="category_id"]').val($(event.currentTarget).attr('data-category-id'));
				$(event.currentTarget).closest('.category-group').find('.product-holder').append(template);
				$('.save-product').on('click', saveProduct);
				$('.edit-product').on('click', editProduct);
				$('.delete-product').on('click', deleteProduct);
			}

			var saveProduct = function(event){
				event.preventDefault();
				var row = $(event.currentTarget).closest('.product-row');
				$.ajax({
					method: 'post',
					url: $(event.currentTarget).attr('data-action'),
					data: {
						form_key: window.FORM_KEY,
						id: row.find('[name="id"]').val(),
						category_id: row.find('[name="category_id"]').val(),
						description: row.find('[name="name"]').val(),
						item_number: row.find('[name="number"]').val(),
						size: row.find('[name="size"]').val()
					},
					success: function(response){
						var data = JSON.parse(response);
						row.find('[name="id"]').val(data.product.id);
						row.find('[name="name"]').val(data.product.description);
						row.find('[name="number"]').val(data.product.item_number);
						row.find('[name="size"]').val(data.product.size);
						row.find('.product-name').text(data.product.description);
						row.find('.product-number').text(data.product.item_number);
						row.find('.product-size').text(data.product.size);
						row.find('[name="name"]').css('display', 'none');
						row.find('[name="number"]').css('display', 'none');
						row.find('[name="size"]').css('display', 'none');
						row.find('.product-name').css('display', 'inline-block');
						row.find('.product-number').css('display', 'inline-block');
						row.find('.product-size').css('display', 'inline-block');
						row.find('.edit-product').css('display', 'inline-block');
						row.find('.save-product').css('display', 'none');
					}
				});
			}

			var editProduct = function(event){
				event.preventDefault();
				var row = $(event.currentTarget).closest('.product-row');
				row.find('[name="name"]').css('display', 'inline-block');
				row.find('[name="number"]').css('display', 'inline-block');
				row.find('[name="size"]').css('display', 'inline-block');
				row.find('.product-name').css('display', 'none');
				row.find('.product-number').css('display', 'none');
				row.find('.product-size').css('display', 'none');
				row.find('.edit-product').css('display', 'none');
				row.find('.save-product').css('display', 'inline-block');
			}

			var deleteProduct = function(event){
				event.preventDefault();
				if($(event.currentTarget).closest('.product-row').find('[name="id"]').val() != ''){
					$.ajax({
						method: 'post',
						url: $(event.currentTarget).attr('data-action'),
						data: {id: $(event.currentTarget).closest('.product-row').find('[name="id"]').val(), 'form_key': window.FORM_KEY},
						success: function(response){
							$(event.currentTarget).closest('.product-row').remove();
						}
					});
				} else {
					$(event.currentTarget).closest('.product-row').remove();
				}
			}

			$('.modal .modal-close').on('click', function(event){
				event.preventDefault();
				$(event.currentTarget).closest('.modal').fadeOut();
			});
			$('.new-type').on('click', addNewType);
			$('.nicer-image .image-upload').on('click', uploadImage);
			$('.nicer-image input[type="file"]').on('change', changeImage);

			$('.save-type').on('click', saveType);
			$('.delete-type').on('click', deleteType);
			$('.edit-type').on('click', editType);
			$('.add-category').on('click', addCategory);
			
			$('.add-product').on('click', addProduct);
			$('.edit-category').on('click', editCategory);
			$('.delete-category').on('click', deleteCategory);
			$('.save-category').on('click', saveCategory);

			$('.save-product').on('click', saveProduct);
			$('.edit-product').on('click', editProduct);
			$('.delete-product').on('click', deleteProduct);
		});


	}

	return new Wholesale();

});