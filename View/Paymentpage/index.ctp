<?php
    $had_paid = '';
    $print_ticket = 'hidden';
    $disable_payment = '';
    if ($data_trans[$model]['status'] == 3) {
        $had_paid = 'hidden';
        $print_ticket = '';
        $disable_payment = 'disabled';
    }

    $had_print = '';
    if ($model == 'Order' && $data_trans[$model]['print_count'] > 0) {
        $had_print = 'hidden';
    }
?>
<div class="div-paymentpage">
    <div class="div-title-page title ultra-big">PAYMENT METHOD</div>
    <div class="div-payment-container">
        <?php
            foreach($payment_methods as $method) {
                $custom_style = (strlen($method['PaymentMethod']['code']) > 12) ? 'custom' : '';
        ?>
                <div class="div-payment-item-box">
                    <a class="type_<?= $method['PaymentMethod']['type'] ?>  <?= $disable_payment ?> " data-id="<?= $method['PaymentMethod']['id'] ?>" data-type="<?= $method['PaymentMethod']['type'] ?>" data-value="<?= $method['PaymentMethod']['value'] ?>">
                        <div class="div-payment-item">
                            <div class="div-payment-icon">
                                <img src="<?= $webroot.$method['PaymentMethod']['image'] ?>">
                            </div>
                            <div class="title <?= $custom_style ?>"><?= $method['PaymentMethod']['code'] ?></div>
                        </div>
                    </a>
                </div>
        <?php
            }
        ?>
    </div>
    <div class="div-payment-number hidden">
    <?php
        foreach($payment_methods as $method) {
            if ($method['PaymentMethod']['type'] > 1) {
    ?>
                <div id="coupon-number_<?= $method['PaymentMethod']['id'] ?>" class="div-coupon-number hidden">
                    <div class="div-coupon-title title small"><?= $method['PaymentMethod']['code'] ?></div>
                    <div class="div-coupon-number-input">
                        <div class="input-label content light super-small">Input the coupon code or scan the QR code </div>
                        <div class="input-container">
                            <input class="content light super-small" placeholder="0112321525">
                            <button type="button" class="btn-submit-number title smaller narrow disabled" data-id="<?= $method['PaymentMethod']['id'] ?>" data-type="<?= $method['PaymentMethod']['type'] ?>">SUBMIT</button>
                        </div>
                    </div>
                    <div class="div-coupon-number-list middle">
                        <div class="div-coupon-number-empty content light super-small">There is no valid coupon yet</div>
                        <ul class="hidden">
                            <!--
                            <li>
                                <img src="<?= $webroot ?>/general/img-checked.png" class="img-checked">
                                <div class="list-number content smallest light">Coupon Number 1</div>
                                <div class="coupon-number-content content smallest light">1232543210</div>
                                <a><img src="<?= $webroot ?>/general/img-close.png" class="img-close"></a>
                            </li>
                            -->
                        </ul>
                    </div>
                </div>
    <?php
            }
        }
    ?>
    </div>
    <div class="div-payment-summary">
        <div class="div-amount-total content black-brown">Total:&nbsp <?= $data_trans[$model]['grand_total'] ?></div>
        <button type='button' class='btn-pay title small narrow disabled <?= $had_paid ?>'>
            PAY BILL
        </button>
        <!--
        <button type='button' class='btn-print title small narrow disabled <?= $print_ticket ?> <?= $had_print ?>'>
            PRINT TICKET
        </button>
        -->
    </div>
</div>

<div class="div-dialog-container dialog-verification div-payment-verification hidden">
    <div class="div-dialog-verification">
        <div class="content taller light black-brown">
            Are you sure the customer will pay the check with <strong><span>Visa</span></strong> ?
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
                    <button type='button' data-count="<?= $count ?>" data-printer_name="<?= $printer['printer_name'] ?>" data-printer_address="<?= $printer['printer_address'] ?>" data-printer_port="<?= $printer['printer_port'] ?>" class='btn-pay-print btn-payprint-<?= $count ?> title small light-brown'>
                        PAY AND PRINT <?= $count_display ?>
                    </button>
            <?php } ?>
        </div>
    </div>
</div>

<div class="div-dialog-container dialog-payment-success hidden">
    <div class="div-dialog-payment-success">
        <img src="<?= $webroot ?>general/icon-success.png">
        <div class="div-success-notes1 title big">Checkout confirmation</div>
        <div class="div-success-notes2 content light small">
            The  #Ref: <?= $data_trans[$model]['inv_number'] ?> has been completed. Well done!
        </div>
        <button class="btn-close title small narrow" type="button">GO BACK TO MENU <?= ($model == 'Order') ? 'MOVIES' : 'SNACKS'; ?></button>
    </div>
</div>



<?php
    echo $this->Html->script('epos-2.14.0.js'); 
    echo $this->Html->script('ticket_print.js?v=1'); 
?>
<script type="text/javascript">
    $(document).ready(function() {        
        COMMON.order_id = '<?= $data_trans[$model]['id'] ?>'; //also can be used as a purchase_id
        COMMON.grand_total = <?= (isset($data_trans[$model]['grand_total']) && !empty($data_trans[$model]['grand_total'])) ? $data_trans[$model]['grand_total'] : 0 ?>;
        COMMON.total_ticket_bought = '<?= count($order_detail) ?>'; //not used for purchasing
        COMMON.order = '<?= json_encode($data_trans[$model]) ?>'; 
        COMMON.order_detail = '<?= json_encode($order_detail) ?>'; //not used for purchasing
        COMMON.data_print = '<?= json_encode($data_print) ?>'; //not used for purchasing
        COMMON.token = '<?= $staff['Staff']['token'] ?>';
        COMMON.staff_id = '<?= $staff['Staff']['id'] ?>';
        COMMON.retry = 3;
        COMMON.webroot = '<?= $webroot ?>';
        COMMON.main_payment_method = '';
        COMMON.payment_data = [];
        COMMON.model = '<?= $model ?>';
        COMMON.url_do_payment = '<?= Router::url(array('plugin' => 'pos', 'controller' => $controller_payment, 'action' => 'do_payment', 'api' => true), true); ?>';
        COMMON.url_home = '<?= Router::url(array('controller' => $controller_home, 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.init_page();
    });
</script>