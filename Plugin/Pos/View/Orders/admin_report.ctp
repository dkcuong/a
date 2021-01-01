<?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>


<?php
//echo $this->element('Pos.order_filter', array('data_search' => $data_search)); ?>

<div class="row">

    <div class="col-xs-5">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-tools pull-left">
                    <?php
                    echo $this->element('date_picker', array(
                        'id' => 'date-report',
                        'label' => __('date'),
                        'field_name' => 'date_report',
                        'value' => isset($data_search["date_report"]) ? $data_search["date_report"] : '',
                    ));
                    ?>

                </div>

            </div>
            <div class="box-header">
                <div class="box-tools pull-left">
                    <?php if(isset($permissions[$model]['view']) && ($permissions[$model]['view'] == true)){ ?>
                        <?= $this->Html->link( 'Daily Sales Report for Management', array('action' => 'report?type=1'), array('class' => 'btn btn-primary btn-report', 'escape' => false)); ?>
                    <?php } ?>
                </div>
            </div>
            <div class="box-header">
                <div class="box-tools pull-left">
                    <?php if(isset($permissions[$model]['view']) && ($permissions[$model]['view'] == true)){ ?>
                        <?= $this->Html->link( 'Daily Movie Sales Report', array('action' => 'report?type=2'), array('class' => 'btn btn-primary btn-report btn-report2', 'escape' => false)); ?>
                    <?php } ?>
                </div>
            </div>
            <div class="box-header">
                <div class="box-tools pull-left">
                    <?php if(isset($permissions[$model]['view']) && ($permissions[$model]['view'] == true)){ ?>
                        <?= $this->Html->link( 'Monthly Sales Report', array('action' => 'report?type=3'), array('class' => 'btn btn-primary btn-report', 'escape' => false)); ?>
                    <?php } ?>
                </div>
            </div>
            <div class="box-header">
                <div class="box-tools pull-left">
                    <?php if(isset($permissions[$model]['view']) && ($permissions[$model]['view'] == true)){ ?>
                        <?= $this->Html->link( 'Daily Sales Report for Cinema manager', array('action' => 'report?type=4'), array('class' => 'btn btn-primary btn-report btn-report4', 'escape' => false)); ?>
                    <?php } ?>
                </div>
            </div>
            <div class="box-header">
                <div class="box-tools pull-left">
                    <?php if(isset($permissions[$model]['view']) && ($permissions[$model]['view'] == true)){ ?>
                        <?= $this->Html->link( 'Daily Collection Report', array('action' => 'report?type=5'), array('class' => 'btn btn-primary btn-report btn-report4', 'escape' => false)); ?>
                    <?php } ?>
                </div>
            </div>
            <div class="box-header">
                <div class="box-tools pull-left">
                    <?php if(isset($permissions[$model]['view']) && ($permissions[$model]['view'] == true)){ ?>
                        <?= $this->Html->link( 'Day End Report', array('action' => 'report?type=6'), array('class' => 'btn btn-primary btn-report', 'escape' => false)); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-5">
        <div class="box box-primary">
            <div class="box-header">
                <div class="box-tools pull-left">
                <?php echo $this->element('date_picker',array(
                        //'format' => 'DD/MM/YYYY',
                        'field_name' => 'report_date_from',
                        'label' => __('report_date_from'),
                        'id' => 'report_date_from',
                        'value' => isset($data_search["date_report_from"]) ? $data_search["date_report_from"] : '',
                    )); ?>
                </div>
                <div class="box-tools pull-left">
                <?php echo $this->element('date_picker',array(
                        //'format' => 'DD/MM/YYYY',
                        'field_name' => 'report_date_to',
                        'label' => __('report_date_to'),
                        'id' => 'report_date_to',
                        'value' => isset($data_search["date_report_to"]) ? $data_search["date_report_to"] : '',
                    )); ?>
                </div>
            </div>
            <div class="box-header">
                <div class="box-tools pull-left">
                    <?php if(isset($permissions[$model]['view']) && ($permissions[$model]['view'] == true)){ ?>
                        <?= $this->Html->link( 'Sales Raw Data Report', array('action' => 'report?type=0'), array('class' => 'btn btn-primary btn-report0', 'escape' => false)); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
	echo $this->Html->script('plugins/datatables/jquery.dataTables', array('inline' => false));
	echo $this->Html->script('plugins/datatables/dataTables.bootstrap', array('inline' => false));
?>
<script type="text/javascript">
	$(document).ready(function(){
        $( ".btn-report" ).click(function() {
            event.preventDefault();
            var link = $(this).attr('href');
            var date = $('#date-report').val();
            window.location.href=link + "&date_report="+date;
        });
	});
    $(document).ready(function(){
        $( ".btn-report0" ).click(function() {
            event.preventDefault();
            var link = $(this).attr('href');
            var date_from = $('#report_date_from').val();
            var date_to = $('#report_date_to').val();
            window.location.href=link + "&date_report_from="+date_from+"&date_report_to="+date_to;
        });
    });
</script>