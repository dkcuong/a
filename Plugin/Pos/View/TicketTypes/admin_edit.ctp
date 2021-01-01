<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('ticket_type', 'edit_item'); ?></h3>
			</div>

			<div class="box-body">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'buses-edit-form')); ?>
					<fieldset>
                        <?=$this->Form->input('id');?>
						
						<?php echo $this->element('language_input', array(
								'languages_model' => $languages_model,
								'languages_list' => $languages_list,
								'language_input_fields' => $language_input_fields,
								'languages_edit_data' => isset($this->request->data[$languages_model]) ? $this->request->data[$languages_model] : false,
						)); ?>

						<div class="form-group">
							<?= $this->Form->input('code', array('class' => 'form-control', 'label' => '<font color="red">*</font>'.__('code'))); ?>
						</div>

						<div class="form-group">
                            <?=$this->Form->input('enabled', array('label' => __('enabled'))); ?>
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