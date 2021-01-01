<?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>

<?= $this->element('Cinema.cinema_filter', array(
	'data_search' => $data_search
)); ?> 

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<div class="box-tools pull-right">
                <?php if(isset($permissions[$model]['add']) && ($permissions[$model]['add'] == true)){ ?>
                    <?= $this->Html->link( '<i class="glyphicon glyphicon-plus"></i> ' . __d('place', 'add_cinema'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                <?php } ?>
				</div>
			</div>	

			<div class="box-body table-responsive">
				<table id="Companies" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?= $this->Paginator->sort('id', __('id')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('code', __('code')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('address', __('address')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('location', __d('place', 'location')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('description', __('description')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('phone', __('phone')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('email', __('email')); ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('enabled',__('enabled')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('updated',__('updated')); ?></th>
							<th class="text-center"><?= __('operation'); ?></th>
						</tr>
					</thead>

					<tbody>
                        <?php foreach ($dbdatas as $dbdata): ?>
							<tr>
								<td class="text-center"><?= h($dbdata[$model]['id']); ?>&nbsp;</td>

								<td class="text-center">
									<?= h($dbdata[$model]['code']); ?>
								</td>			        

								<td class="text-center">
									<?= h($dbdata[$model]['address']); ?>
								</td>			        

								<td class="text-center">
									<?= h($dbdata[$model]['location']); ?>
								</td>			         

								<td class="text-center">
									<?= h($dbdata[$model]['description']); ?>
								</td>			         

								<td class="text-center">
									<?= h($dbdata[$model]['phone']); ?>
								</td>			         

								<td class="text-center">
									<?= h($dbdata[$model]['email']); ?>
								</td>			         

								<td class="text-center">
									<?= $this->element('view_check_ico', array('_check' => $dbdata[$model]['enabled'])); ?>
								</td>

								<td class="text-center"><?= h($dbdata[$model]['updated']); ?>&nbsp;</td>
								<td class="text-center">
									<?= $this->Html->link('<i class="glyphicon glyphicon-eye-open"></i>', array('action' => 'view', $dbdata[$model]['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view'))); ?>
                                    <?php if(isset($permissions[$model]['edit']) && ($permissions[$model]['edit'] == true)){ ?>
                                        <?= $this->Html->link('<i class="glyphicon glyphicon-pencil"></i>', array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit'))); ?>
                                    <?php } ?>
                                    <?php if(isset($permissions[$model]['delete']) && ($permissions[$model]['delete'] == true)){ ?>
                                        <?= $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', array('action' => 'delete', $dbdata[$model]['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('delete')), __('are_you_sure_to_delete', $dbdata[$model]['id'])); ?>
                                    <?php } ?>
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