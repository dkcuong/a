<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('movie', 'add_star'); ?></h3>
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
									<?= $this->Form->input('first_name', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__d('movie', 'code_first_name'))); ?>
								</div>
                            </div>
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('surname', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__d('movie', 'code_surname'))); ?>
								</div>
                            </div>
                        </div>

                        <div class="row">	
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">
			                        <?php 
			                            echo $this->element('images_upload_customize', array(
			                                'name' => 'Star.photo',
			                                'label' => __d('movie', 'photo').'  (Width: 1080 px - Height: 1520 px - Ratio: 0.7)',
			                                'required' => false,
			                            ));
			                        ?>
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
	});
</script>