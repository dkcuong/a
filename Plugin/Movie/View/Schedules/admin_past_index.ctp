<?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>

<?= $this->element('Movie.schedule_past_filter', array(
	'data_search' => $data_search,
)); ?>

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<div class="box-tools pull-right">
                <?php if(isset($permissions[$model]['add']) && ($permissions[$model]['add'] == true)){ ?>
                    <?php //echo $this->Html->link( '<i class="glyphicon glyphicon-plus"></i> ' . __d('schedule', 'add_item'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                <?php } ?>
				</div>
			</div>	

			<div class="box-body table-responsive">
				<table id="schedules" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?= $this->Paginator->sort('movie', __d('movie', 'item_title')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('movie_type', __d('movie', 'movie_type')); ?></th>

							<th class="text-center"><?= $this->Paginator->sort('hall', __d('place', 'hall_title')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('date', __('date')); ?></th>

                            <th class="text-center"><?= $this->Paginator->sort('updated',__('updated')); ?></th>
							<th class="text-center"><?= __('operation'); ?></th>
						</tr>
					</thead>

					<tbody>
						<?php
							// pr($dbdatas);
							// exit;
						?>
                        <?php foreach ($dbdatas as $dbdata): ?>
							<tr>
								<td class="text-center"><?= h($dbdata['Movie']['code']); ?>&nbsp;</td>
								<td class="text-center"><?= h($dbdata['MovieType']['name']); ?>&nbsp;</td>
								<td class="text-center"><?= h($dbdata['Hall']['code']); ?>&nbsp;</td>
								<td class="text-center"><?= h($dbdata['ScheduleDetail']['date']); ?>&nbsp;</td>

								<td class="text-center"><?= h($dbdata[$model]['updated']); ?>&nbsp;</td>
								<td class="text-center">
									<?= $this->Html->link('<i class="glyphicon glyphicon-eye-open"></i>', array('action' => 'past_view', $dbdata[$model]['id'], date('Y-m-d', strtotime($dbdata['ScheduleDetail']['date']))), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view'))); ?>
                                    <?php if(isset($permissions[$model]['edit']) && ($permissions[$model]['edit'] == true)){ ?>
                                        <?php //echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i>', array('action' => 'edit', $dbdata[$model]['id'], date('Y-m-d', strtotime($dbdata['ScheduleDetail']['date']))), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit'))); ?>
										<?php //echo $this->Html->Link('<i class="fa fa-copy"></i>', array('action' => 'copy', $dbdata[$model]['id'], date('Y-m-d', strtotime($dbdata['ScheduleDetail']['date']))), array('class' => 'btn btn-success btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('copy_schedule'))); ?>
                                    <?php } ?>
									<!--
                                    <?php if(isset($permissions[$model]['delete']) && ($permissions[$model]['delete'] == true)){ ?>
                                        <?= $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', array('action' => 'delete', $dbdata[$model]['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('delete')), __('are_you_sure_to_delete', $dbdata[$model]['id'])); ?>
                                    <?php } ?>
									-->
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?= $this->element('Paginator'); ?>
	</div>
</div>

<?php
	echo $this->Html->script('plugins/datatables/jquery.dataTables', array('inline' => false));
	echo $this->Html->script('plugins/datatables/dataTables.bootstrap', array('inline' => false));
?>
<script type="text/javascript">
	$(document).ready(function(){

	});
</script>