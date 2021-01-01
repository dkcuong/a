<div class="form-group movie-type-upload">

<?php 
	$count = 0;

	if( isset($this->request->data[$detail_model]) && !empty($this->request->data[$detail_model]) ){
		//its edit
		$count = -1;
		foreach ($this->request->data[$detail_model] as $key => $value) :	
			$count++;
			if(isset($value['id']) && $value['id']){		
?>
			<div class="well well-sm">
				<div class="row images-upload-row">
					
					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<?php echo $this->Form->input($detail_model.'.'.$count.'.movie_type_id', array(
								'class' => 'form-control movie-type-id',
								'empty' => __('please_select'),
								'data-live-search' => true,
								'multiple' => false,
								'required' => true,
								'id' => 'movie_type_ids',
								'label' => '<font color="red">*</font>'.__d('movie', 'movie_type'),
								'options' => $movie_types,
								'value' => $value['id'],
							)); ?>
						</div>
					</div>

					<div class="col-sm-4 col-xs-12">
						<div class="form-group">
							<?= $this->Form->input($detail_model.'.'.$count.'.film_id', array('class' => 'form-control', 'type'=> 'text', 'label' => __('film_id'))); ?>
						</div>
					</div>

					<div class="col-sm-12 col-xs-12">
						<div class="form-group row">
							<div class="col-sm-4 col-xs-12">
								<div class="form-group">
								<?php echo $this->element('datetime_picker',array(
										'format' => 'DD/MM/YYYY',
										'field_name' => $detail_model.'.'.$count.'.publish_date', 
										'label' => __('publish_date'),
										'required' => true,
										'readonly' => $ticket_sold,
										'id' => 'publish_date_'.$count, 
										'value' => $value['MoviesMovieType']['publish_date'],
									)); ?>
								</div>
							</div>


							<div class="col-sm-4 col-xs-12">
								<div class="form-group">
									<?php echo $this->element('datetime_picker',array(
										'format' => 'DD/MM/YYYY',
										'field_name' => $detail_model.'.'.$count.'.start_date', 
										'label' => __('start_date'),
										'required' => true,
										'readonly' => $ticket_sold,
										'id' => 'start_date_'.$count, 
										'value' => $value['MoviesMovieType']['start_date'],
									)); ?>
								</div>
							</div>


							<div class="col-sm-4 col-xs-12">
								<div class="form-group">
									<?php echo $this->element('datetime_picker',array(
										'format' => 'DD/MM/YYYY',
										'field_name' => $detail_model.'.'.$count.'.end_date', 
										'label' => __('end_date'),
										'required' => true,
										'id' => 'end_date_'.$count, 
										'value' => $value['MoviesMovieType']['end_date'],
									)); ?>
								</div>
							</div>

						</div>		
					</div>

					<div class="col-xs-1 images-buttons text-right custom-button-top-right">
						<?php
							/*
							print $this->Html->link('<i class="glyphicon glyphicon-remove"></i>', '#', array(
								'class' => 'btn-remove-existing-movie-type',
								'data-image-id' => $value['id'],
								'escape' => false
							));
							*/
						?>
					</div>
				</div>
			</div>
<?php
			}
		endforeach;
	}
?>

	<div class="row images-upload-row">
		<div class="col-xs-12 text-center">
			<label><font color="red">*</font></label>
			<?php
				print $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('add_movie_type'), '#', array(
					'class' => 'btn btn-primary btn-new-movie-type',
					'escape' => false
				));
			?>
		</div>
	</div>
</div><!-- .form-group -->


<script type="text/javascript" charset="utf-8">
	var article_movie_type = { count: <?= $count ?> };

	$(document).ready(function(){
		article_movie_type.count = $('.movie-type-upload > .well').length;

		$('.btn-remove-movie-type').on('click', function( e ){
			e.preventDefault();

			article_movie_type.count--;

			$(this).closest(".well").remove();
		});

		$('.btn-remove-existing-movie-type').on('click', function( e ){
			e.preventDefault();

			var station_id = $(this).data('image-id');

			var remove_hidden_input = '<input type="hidden" name="data[remove_station][]" value="'+station_id+'">';

			article_movie_type.count--;
			
			$(this).parents('.movie-type-upload').append( remove_hidden_input );
			$(this).closest(".well").remove();
		});

		$('.btn-new-movie-type').on('click', function( e ){
			e.preventDefault();

			var url = '<?php echo $add_new_movie_type_url; ?>';

			COMMON.call_ajax({
				type: "POST",
				url: url,
				dataType: 'html',
				cache: false,
				data: {
					count: article_movie_type.count,
					detail_model: '<?php echo $detail_model; ?>',
				},
				success: function( result ){
					console.log('article_images.count : ' + article_movie_type.count);
					var counter = (article_movie_type.count - 1);

					console.log('counter : ' + counter);

					if( counter < 0 ){
						console.log('counter below 0');
						$('.movie-type-upload > .images-upload-row').before( result );
					} else {
						console.log('counter above 0');
						$('.movie-type-upload > .well').eq( counter ).after( result );
					}

					article_movie_type.count++;

					$('.btn-remove-movie-type').on('click', function( e ){
						e.preventDefault();

						article_movie_type.count--;

						$(this).closest(".well").remove();
					});
				},
				error: function( result ){
					// console.log('error :');
					// console.log( result );
				}
			});



		});

		$('.box-body').on('change', '.movie-type-id', function(e){		
			var movie_type_array = [];	
			console.log('im here');

			var valid = true;
			$( ".movie-type-id" ).each(function() {
				var movie_type = $(this).val();
				console.log('movie_type : ' + movie_type);
				if ((movie_type > 0) && movie_type_array.includes(movie_type)) {
					valid = false;
					alert('unable to use movie type more than 1');
				} else {
					movie_type_array.push(movie_type);
				}
			});
			
			if (!valid) {
				$(this).val('');
			}
				
		});

	});

</script>