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
<?php

$reset_list_map_movie_schedule = array_values($list_map_movie_schedule);
$list_movie = Hash::extract(array_values($reset_list_map_movie_schedule), '{n}.movie_name');
$list_movie = json_encode($list_movie);
$list_sale = Hash::extract(array_values($reset_list_map_movie_schedule), '{n}.grand_total');
$list_sale = json_encode($list_sale);
$list_ticket = Hash::extract(array_values($reset_list_map_movie_schedule), '{n}.total_ticket');
$list_ticket = json_encode($list_ticket);

$list_title_sale = json_encode(array("Ticket", "Tuckshop", "Member"));
$list_statistic_sale = json_encode(array ($total_amount_ticket_by_day, $total_amount_tuckshop_by_day, $total_amount_member_by_day));
?>
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
            <?php
//            if (! empty($total_amount_ticket_by_day)
//                && ! empty($total_amount_tuckshop_by_day)
//                && ! empty($total_amount_member_by_day)
//            )
//            {
            ?>
            <div class="box-body" style="width:50%">
                <canvas id="bar-chart" height="130"></canvas>
            </div>
            <?php //}  ?>
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
            <!--<div class="box-body table-responsive">
                <div role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="active">
                            <a href="#info-tab" aria-controls="tab" role="tab" data-toggle="tab">
                                <?/*= __d('dashboard','statistic_payment_method') */?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="info-tab">
                        <table id="MemberNotification" class="table table-bordered table-striped">
                            <tbody>
                            <?php /*foreach ($payment_list as $k=>$v) { */?>
                                <tr>
                                    <td width="30%"><strong><?php /*echo $k; */?></strong></td>
                                    <td>
                                        <?php /*echo number_format($v,2); */?>
                                    </td>
                                </tr>
                            <?php /*} */?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>-->

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

            <?php
            //if (! empty($list_map_movie_schedule) )
            //            {
            ?>
                <div class="box-body" style="width:100%">
                    <canvas id="bar-chart-movie" height="130"></canvas>
                </div>
            <?php //}  ?>
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

        new Chart(document.getElementById("bar-chart"), {
            type: 'bar',
            data: {
                labels: <?= $list_title_sale?>,
                datasets: [
                    {
                        label: "Total sale (HK)",
                        //backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f"],
                        backgroundColor: "#3e95cd",
                        data: <?= $list_statistic_sale?>
                    }
                ]
            },
            options: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Statistic Sales'
                },
                scales: {
                    xAxes: [{
                        barPercentage: 0.25,
                        //categoryPercentage: 0.1
                    }],
                    yAxes: [{
                        ticks: {
                            suggestedMin: 0
                        },
                    }],
                },
                responsive: true,
                //maintainAspectRatio: false
            }
        });

        function getRandomColor() {
            var letters = '0123456789ABCDEF'.split('');
            var color = '#';
            for (var i = 0; i < 6; i++ ) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }
        new Chart(document.getElementById("bar-chart-movie"), {
            type: 'bar',
            data: {
                labels: <?= $list_movie?>,
                datasets: [{
                    label: "Total Sale (HK)",
                    yAxisID: 'A',
                    //fillColor:getRandomColor(),
                    //backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f"],
                    backgroundColor: "#3e95cd",
                    data: <?= $list_sale?>
                }, {
                    label: "Number of ticket",
                    yAxisID: 'B',
                    //fillColor:getRandomColor(),
                    backgroundColor: "#3cba9f",

                    // backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f"],
                    data: <?= $list_ticket?>
                }]
            },
            options: {
                title: {
                    display: true,
                    text: 'Statistic By Movie'
                },
                legend: { display: true },
                scales: {
                    xAxes: [{
                        barPercentage: 0.45,
                        //categoryPercentage: 0.1
                    }],
                    yAxes: [{
                        id: 'A',
                        //type: 'linear',
                        position: 'left',
                        ticks: {
                            // max: 1,
                            //min: 0,
                            suggestedMin: 0
                        }
                    }, {
                        id: 'B',
                        //type: 'linear',
                        position: 'right',
                        ticks: {
                             // max: 1,
                             //min: 0,
                            suggestedMin: 0
                        }
                    }],
                    responsive: true,
                    //maintainAspectRatio: false
                }

            }
        });

	});
</script>