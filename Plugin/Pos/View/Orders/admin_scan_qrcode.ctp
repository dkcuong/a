<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"> Scan QR Code</h3>
			</div>

			<div class="box-body ">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'ticket_type-add-form')); ?>
					<fieldset>

						<div class="form-group">
							<?= $this->Form->input('qrcode', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__('qrcode'))); ?>
						</div>

						<?= $this->Form->submit(__('submit'), array('class' => 'btn btn-large btn-primary pull-right', 'id' => 'btn-submit-data')); ?>
					</fieldset>
				<?= $this->Form->end(); ?>

				<?php
					if (isset($data_qrcode['id']) && !empty($data_qrcode['id'])) {
						pr($data_qrcode);
					}
				?>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
	});
</script>