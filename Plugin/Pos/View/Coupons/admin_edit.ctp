<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('coupon', 'edit_item'); ?></h3>
			</div>

			<div class="box-body">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'buses-edit-form')); ?>
					<fieldset>
                        <?=$this->Form->input('id');?>
						
                        <div class="form-group">
                            <?php echo $this->Form->input('type_id', array(
                                'class' => 'form-control selectpicker',
                                'title' => __('please_select'),
                                'data-live-search' => true,
                                'multiple' => false,
                                'required' => true,
                                'disabled' => true,
                                'id' => 'type',
                                'label' => '<font color="red">*</font>'.__d('coupon', 'type'),
                                'options' => $types,
                                'selected' => $this->request->data[$model]['type'],
                            )); ?>
                        </div>

						<div class="form-group">
							<?= $this->Form->input('description', array(
								'class' => 'form-control', 
								'required' => false, 
                                'disabled' => true,
								'label' => __('code'),
							)); ?>
						</div>

                        <?php echo $this->element('language_input', array(
                            'languages_model' => $languages_model,
                            'languages_list' => $languages_list,
                            'language_input_fields' => $language_input_fields,
                            'languages_edit_data' => isset($this->request->data[$languages_model]) ? $this->request->data[$languages_model] : false,
                        )); ?>

						<!--<div class="form-group">
							<?/*=
                            $this->Form->input('expiry_date', array(
								'class' => 'form-control',
								'required' => false,
                                'disabled' => true,
								//'type' => 'datetime',
								'label' => __d('coupon', 'expiry_date'),
                                'value' => isset($this->request->data[$model]['expiry_date']) ? $this->request->data[$model]['expiry_date'] : null,
							));
                            */?>
						</div>
-->
						<div class="form-group">
							<?= $this->Form->input('expiry_range', array(
								'class' => 'form-control', 
								'required' => true, 
                                'disabled' => false,
								'type' => 'number',
								'label' => '<font color="red">*</font>'.__d('coupon', 'expiry_range'),
							)); ?>
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