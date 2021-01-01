<?php echo $this->Html->css('datepicker/datepicker3'); ?>
<?php echo $this->Html->css('chartjs/Chart.min'); ?>
<?php echo $this->Html->css('dashboard'); ?>

<?php
	echo $this->Html->script('plugins/datepicker/bootstrap-datepicker', array('inline' => false));
	echo $this->Html->script('plugins/chartjs/Chart.min', array('inline' => false));
	echo $this->Html->script('CakeAdminLTE/pages/admin_dashboard', array('inline' => false));
?>
<?= $this->element('Dashboard.dashboard_filter', array(
//    'data_search' => $data_search
)); ?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php //echo __('dashboard'); ?></h3>
                <div class="box-tools pull-right">
                </div>
            </div>

            <!--Total Amount-->
            <div class="box-body table-responsive">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active">
                            <a href="#info-tab" aria-controls="tab" role="tab" data-toggle="tab">
                                <?= __d('dashboard','total_amount') ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="info-tab">
                        <table id="MemberNotification" class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <td width="30%"><strong><?= __d('dashboard','total_amount_ticket'); ?></strong></td>
                                <td>
                                    <?php echo number_format($total_amount_ticket_by_day,2); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?= __d('dashboard','total_amount_tuckshop'); ?></strong></td>
                                <td>
                                    <?php echo number_format($total_amount_tuckshop_by_day,2); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?= __d('dashboard','total_amount_member'); ?></strong></td>
                                <td>
                                    <?php echo number_format($total_amount_member_by_day,2); ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div> <!-- close tabpanel -->
                </div> <!-- close tab-content -->
            </div>

            <!--Number Ticket-->
            <div class="box-body table-responsive">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active">
                            <a href="#info-tab" aria-controls="tab" role="tab" data-toggle="tab">
                                <?= __d('dashboard', 'total_ticket') ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="info-tab">
                        <table id="MemberNotification" class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <td width="30%"><strong><?= __d('dashboard','total_ticket'); ?></strong></td>
                                <td>
                                    <?php echo number_format($total_ticket); ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div> <!-- close tabpanel -->
                </div> <!-- close tab-content -->
            </div>

            <!--Payment Method-->
            <div class="box-body table-responsive">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active">
                            <a href="#info-tab" aria-controls="tab" role="tab" data-toggle="tab">
                                <?= __d('dashboard','statistic_payment_method') ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="info-tab">
                        <table id="MemberNotification" class="table table-bordered table-striped">
                            <tbody>
                            <?php foreach ($payment_list as $k=>$v) { ?>
                                <tr>
                                    <td width="30%"><strong><?php echo $k; ?></strong></td>
                                    <td>
                                        <?php echo number_format($v,2); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div> <!-- close tabpanel -->
                </div> <!-- close tab-content -->
            </div>

            <!--Movie-->
            <div class="box-body table-responsive">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active">
                            <a href="#info-tab" aria-controls="tab" role="tab" data-toggle="tab">
                                <?= __d('dashboard','statistic_movie') ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="info-tab">
                        <table id="MemberNotification" class="table table-bordered table-striped">
                            <tbody>
                            <?php foreach ($list_map_movie_schedule as $k=>$v) { ?>
                                <tr>
                                    <td width="30%"><strong><?php echo $v['movie_name']; ?></strong></td>
                                    <td>
                                        <?php echo "Total Amount : ". number_format($v['grand_total'],2); ?> <br>
                                        <?php echo "Total Ticket : ". number_format($v['total_ticket']); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div> <!-- close tabpanel -->
                </div> <!-- close tab-content -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		// ADMIN_DASHBOARD.url_gender_member = '<?= Router::url(array('plugin' => 'dashboard', 'controller' => 'dashboard', 'action' => 'report_gender_member')); ?>';
		// ADMIN_DASHBOARD.url_birthday_member = '<?= Router::url(array('plugin' => 'dashboard', 'controller' => 'dashboard', 'action' => 'report_birthday_member')); ?>';
		// ADMIN_DASHBOARD.url_report_high_spending = '<?= Router::url(array('plugin' => 'dashboard', 'controller' => 'dashboard', 'action' => 'report_high_spending')); ?>';
		// ADMIN_DASHBOARD.url_report_visit = '<?= Router::url(array('plugin' => 'dashboard', 'controller' => 'dashboard', 'action' => 'report_visit')); ?>';
       
		// ADMIN_DASHBOARD.init_page();
	});
</script>