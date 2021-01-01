<?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>

<?= $this->element('Member.member_filter', array(
	'data_search' => $data_search
)); ?>

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<div class="box-tools pull-right">
                <?php if(isset($permissions[$model]['add']) && ($permissions[$model]['add'] == true)){ ?>
                    <?= $this->Html->link( '<i class="glyphicon glyphicon-plus"></i> ' . __d('member', 'add_item'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                <?php } ?>
                <?php if(isset($permissions[$model]['view']) && ($permissions[$model]['view'] == true)){ ?>
                    <?= $this->Html->link( 'Generate Today Report', array('action' => 'generate_member_sales_report'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                <?php } ?>
                </div>
            </div>

			<div class="box-body table-responsive">
				<table id="Buses" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?= $this->Paginator->sort('id', __('id')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('name', __('name')); ?></th>
							<th class="text-center"><?= __d('member','month_of_birth'); ?></th>
                            <th class="text-center"><?= __d('member', 'phone_verified'); ?>
                            <th class="text-center"><?= __d('member', 'email_verified'); ?>
                            <th class="text-center"><?= __d('member', 'renewal_status'); ?>
                            <th class="text-center"><?= __d('member','expired_date'); ?>
                            <!--<th class="text-center"><?/*= $this->Paginator->sort('country_code',__d('member','country_code')); */?></th>
							<th class="text-center"><?/*= $this->Paginator->sort('phone',__('phone')); */?></th>
							<th class="text-center"><?/*= $this->Paginator->sort('email',__('email')); */?></th>-->
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
									<?= h($dbdata[$model]['name']); ?>
								</td>		             

								<td class="text-center">
									<?= isset($dobMonths[$dbdata[$model]['birth_month']]) ? $dobMonths[$dbdata[$model]['birth_month']] : '' ?>
								</td>

                                <td class="text-center">
                                    <?php
                                    /*$is_phone_verified = true;
                                    if (empty($dbdata[$model]['phone_verified'])) {
                                        $is_phone_verified = false;
                                    };*/
                                    echo $this->element('view_verify_stick_color_icon',array('check' => $dbdata[$model]['is_phone_verified']));
                                    ?>
                                </td>

                                <td class="text-center">
                                    <?php
                                    /*$is_email_verified = true;
                                    if (empty($dbdata[$model]['email_verified'])) {
                                        $is_email_verified = false;
                                    };*/
                                    echo $this->element('view_verify_stick_color_icon',array('check' => $dbdata[$model]['is_email_verified']));
                                    ?>
                                </td>

                                <td class="text-center">
                                    <?php
                                    /*$is_renewal = true;
                                    $now = date('Y-m-d');
                                    if (empty($dbdata['MemberRenewal'])
                                    || ( isset($dbdata['MemberRenewal'][0]) && $dbdata['MemberRenewal'][0]['expired_date'] < $now)
                                    ) {
                                        $is_renewal = false;
                                    }*/
                                    echo $this->element('view_verify_stick_color_icon',array('check' => $dbdata[$model]['is_renewal']));
                                    ?>
                                </td>

                                <td class="text-center">
                                    <?php
//                                    $expired_date = '';
//
//                                    if (!empty($dbdata['MemberRenewal'])) {
//                                        $expired_date = $dbdata['MemberRenewal'][0]['expired_date'];
//                                        $expired_date = date('Y-m-d', strtotime($expired_date));
//                                    };
//                                    echo $expired_date;

                                    echo $dbdata['MemberRenewal']['expired_date']
                                    ?>
                                </td>

								<!--<td class="text-center">
									<?/*= h($dbdata[$model]['country_code']); */?>
								</td>		             

								<td class="text-center">
									<?/*= h($dbdata[$model]['phone']); */?>
								</td>		             

								<td class="text-center">
									<?/*= h($dbdata[$model]['email']); */?>
								</td>		             
-->
								<td class="text-center"><?= h($dbdata[$model]['updated']); ?>&nbsp;</td>

								<td class="text-center">
									<?= $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $dbdata[$model]['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view'))); ?>
                                    <?php if(isset($permissions[$model]['edit']) && ($permissions[$model]['edit'] == true)){ ?>
                                        <?= $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit'))); ?>
                                        <?php echo $this->Html->Link('<i class="fa fa-qrcode"></i>', array('action' => 'view_qr_code', $dbdata[$model]['id']), array('class' => 'btn btn-success btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __d('member', 'view_qr_code'))); ?>
                                    <?php } ?>
                                    <?php if(isset($permissions[$model]['delete']) && ($permissions[$model]['delete'] == true)){ ?>
                                        <?= $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $dbdata[$model]['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('delete')), __('are_you_sure_to_delete', $dbdata[$model]['id'])); ?>
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