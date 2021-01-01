<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('schedule', 'edit_item'); ?></h3>
			</div>

			<div class="box-body ">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'schedules-edit-form')); ?>
					<fieldset>
                        <?=$this->Form->input('id');?>
						
						<div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?php echo $this->Form->input('movie_id', array(
										'type' => 'hidden',
										'value' => $this->request->data[$model]['movie_id']
									)); ?>
									<?php echo $this->Form->input('movie_id_tmp', array(
										'class' => 'form-control selectpicker',
										'title' => __('please_select'),
										'data-live-search' => true,
										'multiple' => false,
										'required' => true,
										'disabled' => true,
										'id' => 'movie_id',
										'label' => '<font color="red">*</font>'.__('movie'),
										'options' => $movies,
										'selected' => $this->request->data[$model]['movie_id']
									)); ?>
								</div>
							</div>
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?php echo $this->Form->input('movie_type_id', array(
										'type' => 'hidden',
										'value' => $this->request->data[$model]['movie_type_id']
									)); ?>
									<?php echo $this->Form->input('movie_type_id_tmp', array(
										'class' => 'form-control selectpicker',
										'title' => __('please_select'),
										'data-live-search' => true,
										'multiple' => false,
										'required' => true,
										'disabled' => true,
										'id' => 'movie_type_id_tmp',
										'label' => '<font color="red">*</font>'.__('movie_type'),
										'options' => $movie_types,
										'selected' => $this->request->data[$model]['movie_type_id']
									)); ?>
								</div>
                            </div>
						</div>
						
						<div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?php echo $this->Form->input('hall_id', array(
										'type' => 'hidden',
										'value' => $this->request->data[$model]['hall_id']
									)); ?>								
									<?php echo $this->Form->input('hall_id_tmp', array(
										'class' => 'form-control selectpicker',
										'title' => __('please_select'),
										'data-live-search' => true,
										'multiple' => false,
										'required' => true,
										'disabled' => true,
										'id' => 'hall_id',
										'label' => '<font color="red">*</font>'.__('hall'),
										'options' => $halls,
										'selected' => $this->request->data[$model]['hall_id']
									)); ?>
								</div>
                            </div>
                        </div>

						<div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?php echo $this->element('datetime_picker',array(
										'format' => 'DD/MM/YYYY',
										'field_name' => 'date', 
										'required' => true,
										'label' => __('date'),
										'id' => 'date', 
										'value' => $this->request->data['ScheduleDetail'][0]['date'],
                                    )); ?>
								</div>
                            </div>
                        </div>

						<?php
							echo $this->element('schedule_detail_list_input',array(
								'add_new_time_schedule_url' => $add_new_time_schedule_url,
								'detail_model' => $detail_model,
								'base_model' => $model,
								//'ticket_type' => $ticket_type,
                            ));
						?>			

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