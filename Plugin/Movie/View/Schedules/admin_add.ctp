<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('schedule', 'add_item'); ?></h3>
			</div>

			<div class="box-body ">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'schedule-add-form')); ?>
					<fieldset>
						<div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?php echo $this->Form->input('movie_id', array(
										'class' => 'form-control selectpicker',
										'title' => __('please_select'),
										'data-live-search' => true,
										'multiple' => false,
										'required' => true,
										'id' => 'movie_id',
										'label' => '<font color="red">*</font>'.__d('movie', 'item_title'),
										'options' => $movies,
									)); ?>
								</div>
                            </div>
							<div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?php echo $this->Form->input('movie_type_id', array(
										'class' => 'form-control selectpicker',
										'title' => __('please_select'),
										'data-live-search' => true,
										'multiple' => false,
										'required' => true,
										'id' => 'movie_type_id',
										'label' => '<font color="red">*</font>'.__d('movie', 'movie_type')
									)); ?>
								</div>
                            </div>
                        </div>
						<div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
									<?php echo $this->Form->input('hall_id', array(
										'class' => 'form-control selectpicker',
										'title' => __('please_select'),
										'data-live-search' => true,
										'multiple' => false,
										'required' => true,
										'id' => 'hall_id',
										'label' => '<font color="red">*</font>'.__d('place','hall_title'),
										'options' => $halls,
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

<?php echo $this->Html->script('pages/admin_schedule', array('inline' => false)); ?>
<script type="text/javascript">
	$(document).ready(function(){
		ADMIN_SCHEDULE.url_get_movie_type = '<?= Router::url(array('controller' => 'movie_types', 'action' => 'get_movie_type', 'admin' => true), true); ?>';
		ADMIN_SCHEDULE.init_page();
	});
</script>