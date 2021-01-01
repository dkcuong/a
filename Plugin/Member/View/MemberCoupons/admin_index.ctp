<?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>

<?= $this->element('Member.member_coupon_filter', array(
	'data_search' => $data_search
)); ?>

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">

			<div class="box-body table-responsive">
				<table id="Items" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?= $this->Paginator->sort('id', __('id')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('coupon_id', __d('coupon', 'type')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('member_id', __d('member', 'item_title')); ?></th>
							<th class="text-center"><?= __d('member', 'phone'); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('created', __d('member_coupon', 'obtained_date')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('code', __('code')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('expired_date', __d('coupon', 'expiry_date')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('status',__('status')); ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('updated',__('updated')); ?></th>
                            <th class="text-center"><?= __('operation'); ?></th>
						</tr>
					</thead>

					<tbody>
                        <?php foreach ($dbdatas as $dbdata): ?>
							<tr>
								<td class="text-center"><?= h($dbdata[$model]['id']); ?>&nbsp;</td>     
								<td class="text-center">
									<?= h($types[ $dbdata['Coupon']['type'] ]); ?>
								</td>	
								<td class="text-center">
									<?= h($dbdata['Member']['name']); ?>
								</td>	
								<td class="text-center">
									<?= h($dbdata['Member']['phone']); ?>
								</td>	
	            
								<td class="text-center"><?= h($dbdata[$model]['created']); ?>&nbsp;</td>

								<td class="text-center">
									<?= h($dbdata[$model]['code']); ?>
								</td>

								<td class="text-center">
									<?= h((isset($dbdata[$model]['expired_date']) && !empty($dbdata[$model]['expired_date'])) ? $dbdata[$model]['expired_date'] : ''); ?>
								</td>	

								<td class="text-center">
									<?= h($statuses[$dbdata[$model]['status']]); ?>
								</td>

								<td class="text-center"><?= h($dbdata[$model]['updated']); ?>&nbsp;</td>

                                <td class="text-center">
                                    <?= $this->Html->link('<i class="glyphicon glyphicon-eye-open"></i>', array('action' => 'view', $dbdata[$model]['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view'))); ?>
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