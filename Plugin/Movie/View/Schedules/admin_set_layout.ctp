<?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>

<div class="row">
    <div class="col-xs-12 col-xs-offset-0">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?php echo __d('schedule', 'set_layout'); ?></h3>
			</div>

			<div class="box-body table-responsive">
                <?php echo $this->Form->create('Schedule', array('role' => 'form')); ?>

				<div class="row">
				    <div class="col-md-12">

						<table id="Schedules" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th class="text-center"><?= $this->Paginator->sort('movie', __d('movie', 'movie')); ?></th>
									<th class="text-center"><?= $this->Paginator->sort('date', __('date')); ?></th>
									<th class="text-center"><?= $this->Paginator->sort('time', __('time')); ?></th>
		                            <th class="text-center"><?= $this->Paginator->sort('updated',__('updated')); ?></th>

									<th class="text-center">
										<?php 
											echo $this->Form->input('chk_all', array(
												'type' => 'checkbox',
												'label' => false,
												'hiddenField' => false,
												'class' => 'chk-all-schedule'
											));
										?>
									</th>
								</tr>
							</thead>

							<tbody>
		                        <?php foreach ($schedules as $schedule): ?>
									<tr>
										<td class="text-center"><?= h($schedule['MovieLanguage']['name']); ?>&nbsp;</td>
										<td class="text-center"><?= h($schedule['ScheduleDetail']['date']); ?>&nbsp;</td>
										<td class="text-center"><?= h($schedule['ScheduleDetail']['time']); ?>&nbsp;</td>
										<td class="text-center"><?= h($schedule['Schedule']['updated']); ?>&nbsp;</td>

										<td class="text-center">
											<?php 
												echo $this->Form->input('ScheduleDetails.', array(
													'type' => 'checkbox',
													'label' => false,
													'hiddenField' => false,
													'class' => 'chk-schedule-id',
													'value' => $schedule['ScheduleDetail']['id']
												));
											?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>

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
                    <input type="hidden" name="HallDetail" id="HallDetail">
                </div>

				<div class="row">
				    <div class="col-md-12">
				        <?php echo $this->Form->submit(__('submit'), array(
				            'id' => 'btnSetLayout',
				            'class' => 'btn btn-large btn-primary pull-right')); 
				        ?>
				    </div>
				</div>

				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<?php
	echo $this->Html->script('plugins/datatables/jquery.dataTables', array('inline' => false));
	echo $this->Html->script('plugins/datatables/dataTables.bootstrap', array('inline' => false));
    echo $this->Html->script('CakeAdminLTE/pages/admin_schedule_layout', array('inline' => false));
    echo $this->Html->script('pages/admin_hall.js?v=1', array('inline' => false)); 
?>
<script type="text/javascript">
	$(document).ready(function(){
		ADMIN_HALL.layout = '<?= $layout ?>';
		ADMIN_HALL.init_page();
		ADMIN_HALL.set_layout();
	});
</script>
