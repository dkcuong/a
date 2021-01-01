<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('payment', 'edit_method'); ?></h3>
			</div>

			<div class="box-body">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'payment_method-edit-form')); ?>
					<fieldset>
                        <?=$this->Form->input('id');?>
						
						<div class="form-group">
							<?= $this->Form->input('code', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__('code'))); ?>
						</div>

						<div class="form-group">
							<?= $this->Form->input('name', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__('name'))); ?>
						</div>

						<div class="form-group">
							<?= $this->Form->input('value', array('class' => 'form-control', 'type' => 'number', 'label' => __d('payment', 'method_value'))); ?>
						</div>

                        <div class="form-group">
                            <?php 
                                echo $this->element('images_upload_customize', array(
                                    'name' => 'PaymentMethod.image_upload',
                                    'label' => "<font color='red'>*</font>" . __('image').' (Width: 1080 px - Height: 510 px - Ratio: 2.2)',
                                    'img_review_url' => isset($this->request->data[$model]['image']) ? $this->request->data[$model]['image'] : ''
                                ));
                            ?>
                        </div>

						<div class="form-group">
                            <?php echo $this->Form->input('type', array(
                                'class' => 'form-control selectpicker',
                                'title' => __('please_select'),
                                'data-live-search' => true,
                                'multiple' => false,
                                'required' => true,
                                'id' => 'types',
                                'label' => '<font color="red">*</font>'.__('type'),
                                'options' => $type,
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