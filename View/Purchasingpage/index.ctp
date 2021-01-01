<div class="div-transactionpage">
    <?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>
    
    <?= $this->element('purchasingpage_filter', array(
        'data_search' => $data_search,
        //'company' => $company
    )); ?>

    <div class="row main-container">
        <div class="col-xs-12">
            <div class="box box-primary">

                <div class="box-body table-responsive">
                    <table id="Purchases" class="table table-bordered table-striped content light super-small">
                        <thead>
                            <tr>
                                <th class="text-center"><?= $this->Paginator->sort('date', __('date')); ?></th>
                                <th class="text-center"><?= $this->Paginator->sort('inv_number', __('inv_number')); ?></th>
                                <th class="text-center"><?= __d('member','item_title'); ?></th>
                                <th class="text-center"><?= __('item'); ?></th>
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
                                        <?= date('d/m/Y H:i', strtotime($dbdata['Purchase']['date'])); ?>
                                    </td>		             
                                    <td class="text-center">
                                        <?= h($dbdata['Purchase']['inv_number']); ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        if (!empty($dbdata['Purchase']['member_id'])) {
                                            echo $dbdata['Member']['name'];
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            if (isset($arr_items[$dbdata['Purchase']['id']]) && !empty($arr_items[$dbdata['Purchase']['id']])) {
                                                echo implode(', ', $arr_items[$dbdata['Purchase']['id']]); 
                                            } else {
                                                echo('');
                                            }                                        
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $status[$dbdata['Purchase']['status']].'('.$dbdata['Purchase']['status'].')'; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $dbdata['Mytable']['payment_method']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $dbdata['Purchase']['void']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            if ($dbdata['Purchase']['status'] == 3 || $dbdata['Purchase']['status'] == 6) {
                                        ?>
                                                <?= $this->Html->link('重印 Receipt', '#', array('class' => 'link-reprint print-receipt btn btn-primary btn-xs', 'escape' => false, 'data-id'=> $dbdata['Purchase']['id'], 'data-inv_number'=> $dbdata['Purchase']['inv_number'], 'data-toggle'=>'tooltip', 'title' => __('reprint'))); ?>
                                        <?php 
                                            }
                                            if(($dbdata['Purchase']['void'] == 0) && ($staff['Staff']['id'] == 1 || $staff['Staff']['id'] == 3)) {
                                                $label = ($dbdata['Purchase']['status'] == 6) ? 'Release' : 'Void';
                                        ?>
                                                <?= $this->Html->link($label, '#', array('class' => 'link-void btn btn-warning btn-xs', 'escape' => false, 'data-id'=> $dbdata['Purchase']['id'], 'data-inv_number'=> $dbdata['Purchase']['inv_number'], 'data-toggle'=>'tooltip', 'title' => __('void'))); ?>
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
        COMMON.url_void = '<?= Router::url(array('plugin' => 'pos', 'controller' => 'purchases', 'action' => 'do_void_trans', 'api' => true), true); ?>';
        COMMON.url_get_data_reprint = '<?= Router::url(array('plugin' => 'pos', 'controller' => 'purchases', 'action' => 'do_get_data_print', 'api' => true), true); ?>';
        COMMON.url_home = '<?= Router::url(array('controller' => 'homepage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.init_page();
    });
</script>