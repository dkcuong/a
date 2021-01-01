<style>
	.error-message 
	{
		color: red;
	}
</style>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0">
		<div class="box box-primary">
			<div class="box-header">
			    <h3 class="box-title"><?php echo __d('movie', 'edit_item'); ?></h3>
			</div>
			<div class="box-body">
			    <?php echo $this->Form->create('Movie', array('role' => 'form', 'type' => 'file')); ?>
                    <fieldset>
                        <?php echo $this->Form->input('id', array('class' => 'form-control')); ?>

						<?php echo $this->element('language_input', array(
								'languages_model' => $languages_model,
								'languages_list' => $languages_list,
								'language_input_fields' => $language_input_fields,
								'languages_edit_data' => isset($this->request->data[$languages_model]) ? $this->request->data[$languages_model] : false,
						)); ?>
						
                        <div class="form-group">
                            <?php echo $this->Form->input('code', array(
                                'class' => 'form-control',
                                'required' => 'required',
                                'label' => '<font color="red">*</font>'  . __('code')
                            )); ?>
                        </div><!-- .form-group -->

                        <div class="form-group">
                            <?php echo $this->Form->input('slug', array(
                                'class' => 'form-control',
                                'required' => true,
                                'label' => '<font color="red">*</font>' . __d('movie', 'slug'),
                            )); ?>
                        </div><!-- .form-group -->

						<div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('film_master_id', array('class' => 'form-control', 'type'=> 'text', 'required' => true, 'label' => '<font color="red">*</font>'.__('film_master_id'))); ?>
								</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php /*echo $this->Form->input('director', array(
                                'class' => 'form-control',
                                'required' => 'required',
                                'label' => '<font color="red">*</font>'  . __d('movie', 'director')
                            )); */?>
                        </div><!-- .form-group -->

                        <div class="form-group">
                            <?php echo $this->Form->input('writer', array(
                                'class' => 'form-control',
                                'label' => __d('movie', 'writer')
                            )); ?>
                        </div><!-- .form-group -->

                        <div class="form-group">
                            <?php echo $this->Form->input('stars_id', array(
                                'class' => 'form-control selectpicker',
                                'title' => __('please_select'),
                                'data-live-search' => true,
                                'multiple' => true,
                                'required' => true,
                                'id' => 'stars_ids',
                                'label' => '<font color="red">*</font>'.__d('movie', 'star'),
                                'options' => $stars,
                                'selected' => $current_stars
                            )); ?>
                        </div>

                        <div class="form-group">
                            <?php
                           /* echo $this->Form->input('language', array(
                                'class' => 'form-control',
                                'required' => 'required',
                                'label' => '<font color="red">*</font>'  . __d('movie', 'language')
                            )); */
                            ?>
                        </div><!-- .form-group -->

                        <div class="form-group">
                            <?php echo $this->Form->input('rating', array(
                                'class' => 'form-control',
                                'required' => true,
                                'label' => '<font color="red">*</font>' . __d('movie', 'rating')
                            )); ?>
                        </div><!-- .form-group -->

                        <div class="form-group">
                            <?php echo $this->Form->input('duration', array(
                                'class' => 'form-control',
                                'label' => __d('movie', 'duration'),
                                'type'  => 'number'
                            )); ?>
                        </div><!-- .form-group -->

                        <div class="form-group">
                            <?php /*echo $this->Form->input('genre', array(
                                'class' => 'form-control',
                                'label' => __d('movie', 'genre')
                            )); */?>
                        </div>

                        <div class="form-group">
                           <!-- --><?php /*echo $this->Form->input('subtitle', array(
                                'class' => 'form-control',
                                'label' => __d('movie', 'subtitle')
                            )); */?>
                        </div>

                        <div class="form-group">
                           <!-- --><?php /*echo $this->Form->input('lang_info', array(
                                'class' => 'form-control',
                                'label' => __d('movie', 'lang_info')
                            )); */?>
                        </div>

						<?php
							echo $this->element('movie_type_detail_input',array(
								'add_new_movie_type_url' => $add_new_movie_type_url,
								'detail_model' => $detail_model,
                                'base_model' => $model,
                                'ticket_sold' => $ticket_sold,
                            ));
						?>

                        <div class="form-group">
                            <?php 
                                echo $this->element('images_upload_customize', array(
                                    'name' => 'Movie.poster',
                                    'label' => "<font color='red'>*</font>" . __d('movie', 'poster') . ' (Width: 1080 px - Height: 1520 px - Ratio: 0.7)',
                                    'img_review_url' => isset($this->request->data['Movie']['poster']) ? $this->request->data['Movie']['poster'] : ''
                                ));
                            ?>
                        </div><!-- .form-group -->

                        <div class="form-group">
                            <?php  echo $this->element('video_upload',array(
											'name' => 'Movie.video',
											'add_new_images_url' => $add_movie_trailer_url,
											'images_model' => $movie_model
                                )); ?>
                        </div><!-- .form-group -->

                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
		                            <?=$this->Form->input('is_feature', array('class' => '', 'label' => __('is_feature'))); ?>
		                        </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
		                            <?=$this->Form->input('enabled', array('class' => '', 'required' => true, 'label' => __('enabled'))); ?>
		                        </div>
                            </div>
                        </div>

                        <div class="pull-right">
                            <?php echo $this->Form->submit(__('submit'), array(
                                'id' => 'checkBtn',
                                'class' => 'btn btn-large btn-primary')); ?>
                        </div>
                    </fieldset>
                <?php echo $this->Form->end(); ?>
			</div>
		</div><!-- /.form -->
	</div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->

<script type="text/javascript">
	$(document).ready(function(){
        $('form').submit(function() {
            var i = $('.btn-remove-uploaded-image').size();
            var j = $('.btn-remove-image').size();
            var total = i + j;
            if ( total == 0) {
                alert('Movie Trailer Is Required');
                event.preventDefault();
            }
        });
	});
</script>