<?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">

			<div class="box-body table-responsive">
				<table id="Items" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?= $this->Paginator->sort('id', __('id')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('type', __('type')); ?></th>
							<!--<th class="text-center"><?/*= $this->Paginator->sort('expiry_date', __('expiry_date')); */?></th>-->
							<th class="text-center"><?= $this->Paginator->sort('expiry_range', __('expiry_range')); ?></th>
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
									<?= h($types[ $dbdata[$model]['type'] ]); ?>
								</td>	             
								<!--<td class="text-center">
									<?/*= h((isset($dbdata[$model]['expiry_date']) && !empty($dbdata[$model]['expiry_date'])) ? $dbdata[$model]['expiry_date'] : ''); */?>
								</td>-->
								<td class="text-center">
									<?= h($dbdata[$model]['expiry_range']); ?>
								</td>

								<td class="text-center">
									<?= $this->element('view_check_ico', array('_check' => $dbdata[$model]['enabled'])); ?>
								</td>

								<td class="text-center"><?= h($dbdata[$model]['updated']); ?>&nbsp;</td>
								<td class="text-center">
                                    <?php if(isset($permissions[$model]['edit']) && ($permissions[$model]['edit'] == true)){ ?>
                                        <?= $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit'))); ?>
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