<div class="form-group images-upload">

<?php 
	if( isset($this->request->data[$images_model]) && !empty($this->request->data[$images_model]) ){
		foreach ($this->request->data[$images_model] as $key => $image) :
			
?>
			<div class="well well-sm">
				<div class="row images-upload-row">
					<div class="col-xs-4">
						<video width="320" height="240" controls>
							<source src="<?= $this->webroot . 'img/' . $image['video_path'];?>">
						</video>
					</div>

                    <div class="col-xs-4">
                        <?php if (isset($image['poster_path']) && !empty($image['poster_path'])) { ?>
                            <img height="240" src="<?= $this->webroot . 'img/' . $image['poster_path'];?>" />
                        <?php } ?>
                    </div>

					<div class="col-xs-2 images-buttons text-right">
						<?php
							print $this->Html->link('<i class="glyphicon glyphicon-remove"></i>', '#', array(
								'class' => 'btn-remove-uploaded-image',
								'data-image-id' => $image['id'],
								'escape' => false
							));
						?>
					</div>
				</div>
			</div>
<?php
			
		endforeach;
	}
?>
	
<?php if (strpos($this->request->params['action'], 'edit') === false): ?>
	<div class="well well-sm">
		<div class="row images-upload-row">
            <div class="col-sm-4 col-xs-12">
			<?php
				$label = '<font color="red">*</font>Video'
                    . '<br> ( Full Screen ): Width : 1920 - Height : 1080 - Ratio : 16-9'
                    . '<br> ( Before Expand ): Frame Width : 1430 - Frame Height : 805 - Ratio : 16-9';
				if ($images_model == 'ProductIngredient') {
					$label = 'Ingredient (360px * 430px) <a href="#notes"><sup class="notes-link">(1)</sup></a>';
				} else if ($images_model == 'ProductImage') {
					$label = 'Product Image in Homepage & Product Details(Desktop & Mobile) (800px * 800px) <a href="#notes"><sup class="notes-link">(1)</sup></a>';
				}
				echo $this->Form->input($images_model .'.0.Movie', array(
					'div' => 'col-xs-12',
					'type' => 'file',
					'accept' => "video/*",
					'label' => $label
				));
			?>
            </div>
            <div class="col-sm-6 col-xs-12">
                    <?php
                    echo $this->element('images_upload_customize', array(
                        'name' => 'MovieTrailer.0.Poster',
                        'label' => "<font color='red'>* </font>" . __d('movie', 'thumbnail'). ' (Width: 1920 px - Height: 760 px - Ratio: 1.2)',
                        'required' => true,
                        'img_review_url' => isset($this->request->data['MovieTrailer']['poster']) ? $this->request->data['Movie']['poster'] : ''
                    ));
                    ?>
            </div>
            <div class="col-sm-2 col-xs-12 images-buttons text-right custom-right">
				<?php
					echo $this->Html->link('<i class="glyphicon glyphicon-remove"></i>', '#', array(
						'class' => 'btn-remove-image',
						'escape' => false
					));
				?>
			</div>

			<div class="form-group-label col-xs-12">
				<span class="image-type-limitation"></span>
			</div>
		</div>
	</div>
<?php endif ?>

	<div class="row images-upload-row">
		<div class="col-xs-12 text-center">
			<?php
				$caption = ($images_model == 'ProductIngredient') ? 'Ingredients' : 'Images';
				$caption = "Video";
				print $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.'Add '.$caption, '#', array(
					'class' => 'btn btn-primary btn-new-image',
					'escape' => false
				));
			?>
		</div>
	</div>
</div><!-- .form-group -->


<script type="text/javascript" charset="utf-8">
	var article_images = { count: 0 };

	$(document).ready(function(){
		article_images.count = $('.images-upload > .well').length;

		$('.btn-remove-image').on('click', function( e ){
			e.preventDefault();

			article_images.count--;

			$(this).closest(".well").remove();
		});

		$('.btn-remove-uploaded-image').on('click', function( e ){
			e.preventDefault();

			var image_id = $(this).data('image-id');

			var remove_hidden_input = '<input type="hidden" name="data[remove_image][]" value="'+image_id+'">';

			article_images.count--;
			
			$(this).parents('.images-upload').append( remove_hidden_input );
			$(this).closest(".well").remove();
		});

		$('.btn-new-image').on('click', function( e ){
			e.preventDefault();

			var url = '<?php echo $add_new_images_url; ?>';

			COMMON.call_ajax({
				type: "POST",
				url: url,
				dataType: 'html',
				cache: false,
				data: {
					count: article_images.count,
					images_model: '<?php echo $images_model; ?>',
					base_model: '<?php echo isset($base_model) ? $base_model : ''; ?>',
				},
				success: function( result ){
					var counter = (article_images.count - 1);

					if( counter < 0 ){
						$('.images-upload > .images-upload-row').before( result );
					} else {
						$('.images-upload > .well').eq( counter ).after( result );
					}

					article_images.count++;

					$('.btn-remove-image').on('click', function( e ){
						e.preventDefault();

						article_images.count--;

						$(this).closest(".well").remove();
					});
				},
				error: function( result ){
					// console.log('error :');
					// console.log( result );
				}
			});
		});
	});
</script>