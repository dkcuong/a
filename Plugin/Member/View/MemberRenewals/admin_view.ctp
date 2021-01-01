<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('member', 'renewal_item') ?></h3>

				<div class="box-tools pull-right">
                    <?php
                        // if(isset($permissions[$model]['edit']) && $permissions[$model]['edit']){
                        //     echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '. __d('member_renewal', 'edit_item'), array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-primary', 'escape' => false));
                        // } 
                    ?>
	            </div>
			</div>
			<div class="box-body table-responsive">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role=<?= $model ?> class="active">
                            <a href="#info-tab" aria-controls="tab" role="tab" data-toggle="tab">
                                <?= __d('member', 'renewal_item') ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="info-tab">
                        <?php if($dbdata[$model]){ ?>
                            <table id="MovieType" class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <td><strong><?= __('id'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['id']); ?>
                                        </td>
                                    </tr>       
                                    <tr>
                                        <td><strong><?= __('name'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$member_model]['name']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('phone'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$member_model]['phone']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __d('member', 'expired_date'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[0]['lastest_expired_date']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('updated'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['updated']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('updated_by'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata['UpdatedBy']['email']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('created'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['created']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('created_by'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata['CreatedBy']['email']); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        <?php } ?>
                    </div> <!-- close tabpanel -->
                </div> <!-- close tab-content -->

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title"><?= __d('member', 'history_payment'); ?></h3>
                    </div>

                    <?php if( isset($log_payment) && !empty($log_payment) ){ ?>
                        <div class="box-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center"><?= __('id'); ?></th>
                                    <th class="text-center"><?= __('date'); ?></th>
                                    <th class="text-center"><?= __d('member','payment_type'); ?></th>
                                    <th class="text-center"><?= __d('member','expired_date'); ?></th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($log_payment as $kLogPayment => $vLogPayment) { ?>
                                    <tr>
                                        <td class="text-center"><?= $vLogPayment[$payment_log_model]['id']; ?></td>
                                        <td class="text-center"><?= $vLogPayment[$payment_log_model]['date']; ?></td>
                                        <td class="text-center"><?= strtoupper($vLogPayment[$payment_log_model]['payType']); ?></td>
                                        <td class="text-center"><?= $vLogPayment[$model]['expired_date']; ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div><!-- /.related -->
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
	});
</script>	
	