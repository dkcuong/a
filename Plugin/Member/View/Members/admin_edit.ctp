<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('member', 'edit_item'); ?></h3>
			</div>

			<div class="box-body">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'buses-edit-form')); ?>
					<fieldset>
                        <?=$this->Form->input('id');?>
						<div class="row">
							<div class="col-sm-2 col-xs-12">
								<div class="form-group">
									<?php
										echo $this->Form->input('title', array(
											'class' => 'form-control selectpicker',
											'label' => '<font style="color: red">*</font>' . __('title'),
											'empty' => __('please_select'),
											'required' => true,
											'options' => $title
										));
									?>								
								</div>
							</div>
							<div class="col-sm-4 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('name', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__('name'))); ?>
								</div>
							</div>
							<div class="col-sm-4 col-xs-12">
								<div class="form-group">
                            		<?=$this->Form->input('email', array('class' => 'form-control', 'label' => '<font style="color: red">*</font>' . __('email'))); ?>
                        		</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-4 col-xs-12">
								<div class="form-group">
									<?php
										echo $this->Form->input('country_code', array(
											'class' => 'form-control selectpicker',
											'label' => '<font style="color: red">*</font>' . __d('member','country_code'),
											'empty' => __('please_select'),
											'required' => true,
											'options' => $country_codes
										));
									?>
								</div>
							</div>
							<div class="col-sm-4 col-xs-12">
								<div class="form-group">
									<?=$this->Form->input('phone', array('class' => 'form-control', 'label' => '<font style="color: red">*</font>' . __('phone'))); ?>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-2 col-xs-12">
								<div class="form-group">
									<?php
										echo $this->Form->input('birth_month', array(
											'class' => 'form-control selectpicker',
											'label' => __d('member','month_of_birth'),
											'empty' => __('please_select'),
											'required' => false,
											'options' => $dobMonths
										));
									?>
								</div>
							</div>
							<div class="col-sm-4 col-xs-12">
								<div class="form-group">
									<?php
										echo $this->Form->input('age_group_id', array(
											'class' => 'form-control selectpicker',
											'label' => '<font style="color: red">*</font>' . __('age_group'),
											'empty' => __('please_select'),
											'required' => true,
											'options' => $age_groups
										));
									?>												
								</div>
							</div>	
						</div>
						<div class="row">
							<div class="col-sm-4 col-xs-12">
								<div class="form-group">
									<?php
										echo $this->Form->input('district_id', array(
											'class' => 'form-control selectpicker',
											'label' => '<font style="color: red">*</font>' . __('district'),
											'empty' => __('please_select'),
											'required' => true,
											'options' => $districts
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