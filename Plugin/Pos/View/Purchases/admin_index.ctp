<?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>


<?= $this->element('Pos.purchase_filter', array(
	'data_search' => $data_search
)); ?>

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<div class="box-tools pull-right">
                <?php /*if(isset($permissions[$model]['add']) && ($permissions[$model]['add'] == true)){ */?><!--
                    <?/*= $this->Html->link( 'Scan QR Code', array('action' => 'scan_qrcode'), array('class' => 'btn btn-primary', 'escape' => false)); */?>
                <?php /*} */?>
				<?php /*if(isset($permissions[$model]['view']) && ($permissions[$model]['view'] == true)){ */?>
                    <?/*= $this->Html->link( 'Generate HKBO Report', array('action' => 'generate_hkbo_report'), array('class' => 'btn btn-primary', 'escape' => false)); */?>
                <?php /*} */?>
                <?php /*if(isset($permissions[$model]['view']) && ($permissions[$model]['view'] == true)){ */?>
                    <?/*= $this->Html->link( 'Generate Today Ticket Sales', array('action' => 'generate_ticket_sales_report'), array('class' => 'btn btn-primary', 'escape' => false)); */?>
                <?php /*} */?>
                <?php /*if(isset($permissions[$model]['view']) && ($permissions[$model]['view'] == true)){ */?>
                    <?/*= $this->Html->link( 'Generate Today Tuck Shop Sales', array('action' => 'generate_tuckshop_sales_report'), array('class' => 'btn btn-primary', 'escape' => false)); */?>
                --><?php /*} */?>
                </div>
			</div>	

			<div class="box-body table-responsive">
				<table id="Orders" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?= $this->Paginator->sort('id', __('id')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('date', __('date')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('inv_number', __('inv_number')); ?></th>
                            <th class="text-center"><?= __d('member','item_title'); ?></th>
                            <th class="text-center"><?= __d('purchase','amount_of_item'); ?></th>
							<!--<th class="text-center"><?/*= __('status') */?></th>-->
                            <th class="text-center"><?= __d('payment', 'method') ?></th>
                            <th class="text-center"><?= __('status') ?></th>
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
								<td class="text-center"><?= h($dbdata[$model]['id']); ?>&nbsp;</td>     
								<td class="text-center">
									<?= h($dbdata[$model]['date']); ?>
								</td>		             
								<td class="text-center">
									<?= h($dbdata[$model]['inv_number']); ?>
								</td>
                                <td class="text-center">
                                    <?php
                                    if (!empty($dbdata[$model]['member_id'])) {

                                        echo $this->Html->link($dbdata['Member']['name'], array(
                                            'plugin' => 'member', 'controller' => 'members',
                                            'action' => 'view', 'admin' => true, 'prefix' => 'admin', $dbdata[$model]['member_id']
                                        ));
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $dbdata[0]['amount_of_item']; ?>
                                </td>
                                <!--<td class="text-center">
									<?/*= $status[$dbdata[$model]['qr']]; */?>
                                    <?php /*echo strtoupper($dbdata[$model]['qrcode_status']); */?>
								</td>-->
                                <td class="text-center">
                                    <?/*= $status[$dbdata[$model]['qr']]; */?>
                                    <?php echo strtoupper($dbdata[0]['payment_method_group']); ?>
                                </td>
                                <td class="text-center">
                                    <?php echo $status[$dbdata[$model]['status']]; ?>
                                </td>
                                <td class="text-center"><?= h($dbdata[$model]['updated']); ?>&nbsp;</td>
								<td class="text-center">
									<?= $this->Html->link('<i class="glyphicon glyphicon-eye-open"></i>', array('action' => 'view', $dbdata[$model]['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view'))); ?>
									<!--
                                    <?php if(isset($permissions[$model]['edit']) && ($permissions[$model]['edit'] == true)){ ?>
                                        <?= $this->Html->link('<i class="glyphicon glyphicon-pencil"></i>', array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit'))); ?>
                                    <?php } ?>
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