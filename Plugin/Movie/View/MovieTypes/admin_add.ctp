<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('movie', 'add_item_type'); ?></h3>
			</div>

			<div class="box-body ">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'buses-add-form')); ?>
					<fieldset>
						
						<div class="form-group">
							<?= $this->Form->input('name', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__('name'))); ?>
						</div>
				
                        <div class="form-group">
                            <?=$this->Form->input('enabled', array('checked' => 'checked', 'label' => __('enabled'))); ?>
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