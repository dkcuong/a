<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('member_notification', 'edit_item'); ?></h3>
			</div>

			<div class="box-body">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'member_notification-edit-form')); ?>
					<fieldset>
                        <?=$this->Form->input('id');?>

						<div class="form-group">
							<?= $this->Form->input('pushed', array('class' => 'form-control', 'label' => __('Pushed'))); ?>
						</div>

						<div class="form-group">
							<?= $this->Form->input('read_at', array('class' => 'form-control', 'label' =>__('Read At'))); ?>
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