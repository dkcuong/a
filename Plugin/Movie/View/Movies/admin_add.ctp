<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('movie', 'add_item'); ?></h3>
			</div>

			<div class="box-body ">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'movies-add-form')); ?>
					<fieldset>

						<?php echo $this->element('language_input', array(
								'languages_model' => $languages_model,
								'languages_list' => $languages_list,
								'language_input_fields' => $language_input_fields,
								'languages_edit_data' => isset($this->request->data[$languages_model]) ? $this->request->data[$languages_model] : false,
						)); ?>
						
						<div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('code', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__('code'))); ?>
								</div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('slug', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>' . __d('movie', 'slug'))); ?>	
								</div>
                            </div>
                        </div>

						<div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('film_master_id', array('class' => 'form-control', 'type'=> 'text', 'required' => true, 'label' => '<font color="red">*</font>'.__('film_master_id'))); ?>
								</div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <?= $this->Form->input('writer', array('class' => 'form-control', 'label' => __d('movie', 'writer'))); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?php // $this->Form->input('director', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__d('movie', 'director'))); ?>
								</div>
                            </div>
                        </div>

                        <div class="row">					
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <?= $this->Form->input('rating', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>' . __d('movie', 'rating'))); ?>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <?php echo $this->Form->input('stars_id', array(
                                        'class' => 'form-control selectpicker',
                                        'title' => __('please_select'),
                                        'data-live-search' => true,
                                        'multiple' => true,
                                        'required' => true,
                                        'id' => 'movie_type_ids',
                                        'label' => '<font color="red">*</font>'.__d('movie', 'star'),
                                        'options' => $stars,
                                    )); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <?= $this->Form->input('duration', array('class' => 'form-control', 'label' => __d('movie', 'duration'), 'type' => 'number')); ?>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <?php // $this->Form->input('language', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__d('movie', 'language'))); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">	
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?php // $this->Form->input('genre', array('class' => 'form-control', 'label' => __d('movie', 'genre'))); ?>
								</div>
                            </div>
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?php //$this->Form->input('subtitle', array('class' => 'form-control', 'label' => __d('movie', 'subtitle'))); ?>
								</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <?php // $this->Form->input('lang_info', array('class' => 'form-control', 'label' => __d('movie', 'lang_info'))); ?>
                                </div>
                            </div>
                        </div>

						<?php
							echo $this->element('movie_type_detail_input',array(
								'add_new_movie_type_url' => $add_new_movie_type_url,
								'detail_model' => $detail_model,
								'base_model' => $model,
                            ));
						?>

                        <div class="row">	
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">
			                        <?php 
			                            echo $this->element('images_upload_customize', array(
			                                'name' => 'Movie.poster',
			                                'label' => "<font color='red'>* </font>" . __d('movie', 'poster') . ' (Width: 1080 px - Height: 1520 px - Ratio: 0.7)',
			                                'required' => true,
			                                'img_review_url' => isset($this->request->data['Movie']['poster']) ? $this->request->data['Movie']['poster'] : ''
			                            ));
			                        ?>

								</div>
                            </div>				
                        </div>

                        <div class="row">	
							<div class="col-sm-12 col-xs-12">
								<div class="form-group">
									<?php
										echo $this->element('video_upload',array(
											'add_new_images_url' => $add_movie_trailer_url,
											'images_model' => $movie_model
										));
									?>
								</div>
							</div>
						</div>

                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
		                        <div class="form-group">
		                            <?=$this->Form->input('is_feature', array('class' => '', 'label' => __d('movie', 'is_feature'))); ?>
		                        </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
		                        <div class="form-group">
		                            <?=$this->Form->input('enabled', array('class' => '', 'checked' => 'checked', 'required' => true, 'label' => __('enabled'))); ?>
		                        </div>
                            </div>
                        </div>

						<?= $this->Form->submit(__('submit'), array('class' => 'btn btn-large btn-primary pull-right', 'id' => 'btn-submit-data')); ?>
					</fieldset>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
        $(document).ready(function(){
            $('form').submit(function() {
                var j = $('.btn-remove-image').size();
                if ( j == 0) {
                    alert('Movie Trailer Is Required');
                    event.preventDefault();
                }
            });
        });
	});
</script>