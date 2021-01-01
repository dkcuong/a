<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('setting', 'add_item'); ?></h3>
			</div>

			<div class="box-body ">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'buses-add-form')); ?>
					<fieldset>
						
						<div class="form-group">
							<?= $this->Form->input('slug', array('class' => 'form-control', 'required' => true , 'label' => __('slug'))); ?>
						</div>

						<div class="form-group">
							<?= $this->Form->input('value', array('class' => 'form-control', 'required' => true, 'label' => __d('setting', 'value'))); ?>
						</div>

						<div class="form-group">
							<?= $this->Form->input('description', array('class' => 'form-control', 'required' => true, 'label' => __('description'))); ?>
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