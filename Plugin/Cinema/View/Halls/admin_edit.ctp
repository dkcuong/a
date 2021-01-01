<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('place', 'edit_hall'); ?></h3>
			</div>

			<div class="box-body">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'hall-edit-form')); ?>
					<fieldset>
                        <?=$this->Form->input('id');?>
						
						<div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?php echo $this->Form->input('cinema_id', array(
										'class' => 'form-control selectpicker',
										'title' => __('please_select'),
										'data-live-search' => true,
										'multiple' => false,
										'required' => true,
										'id' => 'cinema_ids',
										'label' => '<font color="red">*</font>'.__d('place', 'cinema'),
										'options' => $cinema,
									)); ?>
								</div>
                            </div>
                        </div>

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
									<?= $this->Form->input('max_seat', array('class' => 'form-control', 'required' => true, 'readonly' => 'readonly', 'label' => '<font color="red">*</font>'.__d('place', 'max_seat'))); ?>
								</div>
							</div>
                        </div>

						<div class="row pull-to-bottom">
                            <div class="col-sm-2 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('row', array(
										'class' => 'form-control', 
										'id' => 'row_number', 
										'label' => '<font color="red">*</font>'.__d('place', 'row'),
										'type' => 'number',
										'min' => '1',
										'step' => '1',
										'oninput' => "validity.valid||(value='');",

									)); ?>
								</div>
							</div>
							<div class="col-sm-2 col-xs-12">
								<div class="form-group">
									<?= $this->Form->input('column', array('class' => 'form-control', 'id' => 'column_number', 'label' => '<font color="red">*</font>'.__d('place', 'column'), 'type' => 'number',
										'min' => '1',
										'step' => '1',
										'oninput' => "validity.valid||(value='');",)); ?>
								</div>
							</div>
							<div class="col-sm-2 col-xs-12">
								<div class="form-group">
									<button type="button" id="btn-seat-generate" class="btn btn-large btn-primary"><?= __('generate') ?></button>
								</div>
							</div>
                        </div>
						
						<div class="row">
                            <div class="col-sm-12 col-xs-12">
								<label><font color="red">*</font><?= __d('place', 'seat_layout') ?></label>
								<div id="panel-seat-edit" class="seat-layout"></div>
							</div>
						</div>
						<div class="row">
                            <div class="col-sm-12 col-xs-12">
								<label><?= __d('place', 'user_seat_layout') ?></label>
								<div id="panel-seat-layout" class="seat-layout panel-seat-layout"></div>
							</div>
						</div>

						<div class="form-group">
                            <?=$this->Form->input('enabled', array('label' => __('enabled'))); ?>
                        </div>

                        <div class="form-group">
                            <input type="hidden" name="HallDetail" id="HallDetail">
                        </div>

						<?= $this->Form->submit(__('submit'), array('class' => 'btn btn-large btn-primary pull-right', 'id' => 'btn-submit-data')); ?>
					</fieldset>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>
<?php
    echo $this->Html->script('pages/admin_hall.js?v=1', array('inline' => false)); 
?>
<script type="text/javascript">
	$(document).ready(function(){
		ADMIN_HALL.layout = '<?= $layout ?>';
		ADMIN_HALL.init_page();
		ADMIN_HALL.set_layout();
	});
</script>