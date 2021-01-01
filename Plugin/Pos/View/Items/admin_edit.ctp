<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('item', 'edit_item'); ?></h3>
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
                            <?php echo $this->Form->input('item_group_id', array(
                                'class' => 'form-control selectpicker',
                                'title' => __('please_select'),
                                'data-live-search' => true,
                                'multiple' => false,
                                'required' => true,
                                'id' => 'item_group_ids',
                                'label' => '<font color="red">*</font>'.__d('item_group', 'item_title'),
                                'options' => $item_groups,
                                'selected' => $current_item_groups,
                            )); ?>
                        </div>

						<div class="form-group">
							<?= $this->Form->input('code', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.__('code'))); ?>
						</div>

						<div class="form-group">
							<?= $this->Form->input('price', array('class' => 'form-control', 'type' => 'number', 'label' => __('price'))); ?>
						</div>

						<!--
						<div class="form-group">
							<?= $this->Form->input('availability', array('class' => 'form-control', 'type' => 'number', 'label' => __d('item', 'availability'))); ?>
						</div>
						-->
						
						<div class="form-group">
							<?= $this->Form->input('material', array('class' => 'form-control', 'label' => __('material'))); ?>
						</div>

                        <div class="form-group">
                            <?php
                                echo $this->element('images_upload_customize', array(
                                    'name' => 'Item.image',
                                    'label' => "<font color='red'>*</font>" . __d('item', 'item').' (Width: 1080 px - Height: 430 px - Ratio: 2.5)',
                                    'img_review_url' => isset($this->request->data['Item']['image']) ? $this->request->data['Item']['image'] : ''
                                ));
                            ?>
                        </div><!-- .form-group -->

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