<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('place', 'edit_cinema'); ?></h3>
			</div>

			<div class="box-body ">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'companies-edit-form')); ?>
					<fieldset>
                        <?=$this->Form->input('id');?>

						<div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('code', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__('code'))); ?>
								</div>
                            </div>
                        </div>

						<div class="row">
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('address', array('class' => 'form-control', 'label' => '<font color="red">*</font>'.__('address'))); ?>						
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('location', array('class' => 'form-control', 'label' => '<font color="red">*</font>'.__d('place', 'location'))); ?>						
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('description', array('class' => 'form-control', 'label' => '<font color="red">*</font>'.__('description'))); ?>
								</div>
							</div>
						</div>

                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('phone', array('class' => 'form-control', 'label' => __('phone'))); ?>
								</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('email', array('class' => 'form-control', 'label' => __('email'))); ?>
								</div>
                            </div>
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