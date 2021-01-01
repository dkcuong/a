<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('staff', 'edit_item'); ?></h3>
			</div>

			<div class="box-body ">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'staff-edit-form')); ?>
					<fieldset>
                        <?=$this->Form->input('id');?>

                        <div class="row">
							<div class="col-sm-4 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('name', array('class' => 'form-control', 'label' => '<font color="red">*</font>'.__('name'))); ?>						
								</div>
                            </div>
							<div class="col-sm-2 col-xs-12">
								<div class="form-group">
								<?= $this->Form->input('code', array('class' => 'form-control', 'label' => '<font color="red">*</font>'.__('code'))); ?>						
								</div>
                            </div>					
						</div>
						
						<div class="row">
							<div class="col-sm-4 col-xs-12">
								<div class="form-group">
									<?php echo $this->Form->input('role', array(
										'class' => 'form-control selectpicker',
										'title' => __('please_select'),
										'data-live-search' => true,
										'multiple' => false,
										'required' => true,
										'id' => 'roles',
										'label' => '<font color="red">*</font>'.__('role'),
										'options' => $role,
									)); ?>
								</div>
                            </div>
                        </div>

						<div class="row">
								<div class="col-sm-2 col-xs-12">
									<div class="form-group">
										<?php
											echo $this->Form->input('country_code', array(
												'class' => 'form-control selectpicker',
												'label' => '<font style="color: red">*</font>' . __d('staff','country_code'),
												'empty' => __('please_select'),
												'required' => true,
												'options' => $country_codes
											));
										?>
									</div>
								</div>
								<div class="col-sm-4 col-xs-12">
									<div class="form-group">
										<?=$this->Form->input('phone', array('class' => 'form-control', 'required' => true, 'label' => '<font style="color: red">*</font>' . __('phone'))); ?>
									</div>
								</div>
						</div>
						<div class="row">
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('username', array('class' => 'form-control', 'label' => '<font color="red">*</font>'.__('username'))); ?>						
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