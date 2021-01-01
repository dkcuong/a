<div class="div-transactionpage">
    <?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>
    
    <?= $this->element('transactionpage_filter', array(
        'data_search' => $data_search,
        //'company' => $company
    )); ?>

    <div class="row main-container">
        <div class="col-xs-12">
            <div class="box box-primary">

                <div class="box-body table-responsive">
                    <table id="Orders" class="table table-bordered table-striped content light super-small">
                        <thead>
                            <tr>
                                <th class="text-center"><?= $this->Paginator->sort('date', __('date')); ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('inv_number', __('inv_number')); ?></th>
                                <th class="text-center"><?= __d('member','item_title'); ?></th>
                                <th class="text-center"><?= __d('movie','item_title'); ?></th>
                                <th class="text-center"><?= __d('schedule','item_title'); ?></th>
                                <th class="text-center"><?= __('seat'); ?></th> 
                                <th class="text-center"><?= __('status') ?></th>
                                <th class="text-center"><?= __('payment_method') ?></th>
                                <th class="text-center"><?= __('void') ?></th>
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
                                    <td class="text-center">
                                        <?= date('d/m/Y H:i', strtotime($dbdata['Order']['date'])); ?>
                                    </td>		             
                                    <td class="text-center">
                                        <?= h($dbdata['Order']['inv_number']); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        if (!empty($dbdata['Order']['member_id'])) {
                                            echo $dbdata['Member']['name'];
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        if (!empty($dbdata['MovieLanguage']['name'])) {
                                            echo $dbdata['MovieLanguage']['name'] . ' (' . $dbdata['MovieType']['name'] . ') <br/> (' . $dbdata['Movie']['code'] . ')';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?= date('d/m/Y', strtotime($dbdata['ScheduleDetail']['date'])) . " " . substr($dbdata['ScheduleDetail']['time'], 0, 5); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            if (isset($arr_seats[$dbdata['Order']['id']]) && !empty($arr_seats[$dbdata['Order']['id']])) {
                                                echo implode(',', $arr_seats[$dbdata['Order']['id']]); 
                                            } else {
                                                echo('');
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $status[$dbdata['Order']['status']].'('.$dbdata['Order']['status'].')'; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $dbdata['Mytable']['payment_method']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $dbdata['Order']['void']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            if ($dbdata['Order']['status'] == 3 || $dbdata['Order']['status'] == 6) {
                                        ?>
                                                <?= $this->Html->link('重印 Ticket', '#', array('class' => 'link-reprint print-ticket btn btn-primary btn-xs', 'escape' => false, 'data-id'=> $dbdata['Order']['id'], 'data-inv_number'=> $dbdata['Order']['inv_number'], 'data-toggle'=>'tooltip', 'title' => __('reprint'))); ?>
                                                <?= $this->Html->link('重印 Receipt', '#', array('class' => 'link-reprint print-receipt btn btn-primary btn-xs', 'escape' => false, 'data-id'=> $dbdata['Order']['id'], 'data-inv_number'=> $dbdata['Order']['inv_number'], 'data-toggle'=>'tooltip', 'title' => __('reprint'))); ?>
                                        <?php 
                                            }
                                            // if($staff['Staff']['id'] == 1 || $staff['Staff']['id'] == 3) {
                                            if(($dbdata['Order']['void'] == 0) && ($staff['Staff']['id'] == 1 || $staff['Staff']['id'] == 3)) {
                                                $label = ($dbdata['Order']['status'] == 6) ? 'Release' : 'Void';
                                        ?>
                                                <?= $this->Html->link($label, '#', array('class' => 'link-void btn btn-warning btn-xs', 'escape' => false, 'data-id'=> $dbdata['Order']['id'], 'data-inv_number'=> $dbdata['Order']['inv_number'], 'data-toggle'=>'tooltip', 'title' => __('void'))); ?>
                                        <?php 
                                            }
                                        ?>
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
</div>

<div class="div-dialog-container div-dialog-reprint hidden">
    <div class="div-dialog-verification">
        <div class="content taller light black-brown">
            Are you sure to reprint <span class="print_type"></span> for <strong><span class="inv_number"></span></strong> ?
        </div>
        <div class="div-verification-button">
            <button type='button' class='btn-cancel title small'>
                CANCEL
            </button>
            <?php 
                $count = -1;
                $display_counter = false;
                if (count($printer_address) > 1) {
                    $display_counter = true;
                }
                foreach($printer_address as $printer) { 
                    $count++;
                    $count_display = ($display_counter) ? $count+1 : '';
            ?> 
                    <button type='button' data-count="<?= $count ?>" data-printer_name="<?= $printer['printer_name'] ?>" data-printer_address="<?= $printer['printer_address'] ?>" data-printer_port="<?= $printer['printer_port'] ?>" class='btn-reprint btn-payprint-<?= $count ?> title small light-brown'>
                        RE-PRINT <?= $count_display ?>
                    </button>
            <?php } ?>
        </div>
    </div>
</div>

<div class="div-dialog-container div-dialog-void hidden">
    <div class="div-dialog-verification">
        <div class="content taller light black-brown">
            Are you sure to VOID ticket for <strong><span></span></strong> ? <br/>
            <strong>Warning : voided transaction CAN NOT BE UN-VOID</strong>
        </div>
        <div class="div-verification-button">
            <button type='button' class='btn-cancel title small'>
                CANCEL
            </button>
            <button type='button' class='btn-void title small light-brown'>
                VOID
            </button>
        </div>
    </div>
</div>


<?php
    echo $this->Html->script('epos-2.14.0.js'); 
    echo $this->Html->script('ticket_print.js?v=1'); 
?>
<script type="text/javascript">
    $(document).ready(function() {
        COMMON.token = '<?= $staff['Staff']['token'] ?>';
        COMMON.staff_id = '<?= $staff['Staff']['id'] ?>';
        COMMON.url_void = '<?= Router::url(array('plugin' => 'pos', 'controller' => 'orders', 'action' => 'do_void_trans', 'api' => true), true); ?>';
        // COMMON.url_void = '<?= Router::url(array('controller' => 'transactionpage', 'action' => 'do_void_trans', 'api' => false), true); ?>';
        COMMON.url_get_data_reprint = '<?= Router::url(array('plugin' => 'pos', 'controller' => 'orders', 'action' => 'do_get_data_print', 'api' => true), true); ?>';
        COMMON.url_home = '<?= Router::url(array('controller' => 'homepage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.init_page();
    });
</script>