<?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>

<?= $this->element('Member.member_notification_filter', array(
	'data_search' => $data_search
)); ?>

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<div class="box-tools pull-right">
                <?php if(isset($permissions[$model]['add']) && ($permissions[$model]['add'] == true)){ ?>
                    <?= $this->Html->link( '<i class="glyphicon glyphicon-plus"></i> ' . __d('notification', 'add_item'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                <?php } ?>
				</div>
			</div>	

			<div class="box-body table-responsive">
				<table id="Companies" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?= $this->Paginator->sort('id', __('id')); ?></th>	
							<th class="text-center"><?= $this->Paginator->sort("NotificationLanguage.title", __("title")) ?></th>
							<th class="text-center"><?= $this->Paginator->sort('Notification.push_method', __('push_method')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('Member.name', __('name')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('Member.phone', __('phone')); ?></th>							
                            <th class="text-center"><?= $this->Paginator->sort('updated',__('updated')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('created',__('created')); ?></th>
							<th class="text-center"><?= __('operation'); ?></th>
						</tr>
					</thead>

					<tbody>
                        <?php foreach ($dbdatas as $dbdata): ?>
							<tr>
								<td class="text-center"><?= h($dbdata[$model]['id']); ?>&nbsp;</td>
								<td class="text-center">
									<?= h($dbdata['NotificationLanguage']['title']); ?>
								</td>		                        
								<td class="text-center">
									<?= h($dbdata['Notification']['push_method']); ?>
								</td>		                        
								<td class="text-center">
									<?= h($dbdata['Member']['name']); ?>
								</td>			         
								<td class="text-center">
									<?= h($dbdata['Member']['phone']); ?>
								</td>			        

								<td class="text-center"><?= h($dbdata[$model]['updated']); ?>&nbsp;</td>
								<td class="text-center"><?= h($dbdata[$model]['created']); ?>&nbsp;</td>
								<td class="text-center">
									<?= $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $dbdata[$model]['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view'))); ?>
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