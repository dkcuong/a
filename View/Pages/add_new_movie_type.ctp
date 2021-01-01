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
							'id' => 'publish_date_'.$count, 
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
							'id' => 'start_date_'.$count, 
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
						)); ?>
					</div>
				</div>

			</div>		
		</div>

		<div class="col-xs-1 images-buttons text-right custom-button-top-right">
			<?php
				/*
				echo $this->Html->link('<i class="glyphicon glyphicon-remove"></i>', '#', array(
					'class' => 'btn-remove-movie-type',
					'escape' => false
				));
				*/
			?>
		</div>

		<div class="form-group-label col-xs-12">
			<span class="image-type-limitation"></span>
		</div>
	</div>
</div>