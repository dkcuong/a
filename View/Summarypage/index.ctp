<?php
    $member_hidden = 'hidden';
    $member_visible = '';
    $registration_hidden = 'hidden';

    if ((isset($data_user['Member']['id']) && !empty($data_user['Member']['id'])) || 
        (isset($data_user['Member']['is_member_register']) && !empty($data_user['Member']['is_member_register']))) {
        $member_hidden = '';
        $member_visible = 'hidden';

        if (isset($data_user['Member']['is_member_register']) && !empty($data_user['Member']['is_member_register'])) {
            $registration_hidden = '';
        }


    }

?>
<div class="div-summarypage">
    <div class="div-top">
        <div class="div-breadcrumb">
            <a href="<?= Router::url(array( 'controller' => 'ticketingpage', 'action' => 'index')) ?>" class="title big light-brown">Film</a>
            <img src="<?= $webroot ?>general/arrow.png">
            <a href="<?= Router::url(array( 'controller' => 'schedulingpage', 'action' => 'index', $summary['ScheduleDetail']['id'])) ?>" class="title big light-brown">Schedule</a>
            <img src="<?= $webroot ?>general/arrow.png">
            <a href="<?= Router::url(array( 'controller' => 'seatingpage', 'action' => 'index', $summary['ScheduleDetail']['id'])) ?>" class="title big light-brown">Seats</a>
            <img src="<?= $webroot ?>general/arrow.png">
            <div class="title big">Checkout</div>
        </div>
    </div>

    <div class="div-summary-container">
        <div class="div-summary-content">
            <div class="div-summary-ticket">
                <div class="div-summary-cinema title smaller light">ACX Cinemas@Harbour North</div>
                <div class="div-summary-title title big">SUMMARY</div>
                <div class="div-top content">
                    <div class="div-summary-date"><div class="title smaller light">Date</div><?= $summary['ScheduleDetail']['date_label'] ?></div>
                    <div class="div-summary-time"><div class="title smaller light">Time</div><?= date('H:i', strtotime($summary['ScheduleDetail']['time'])) ?></div>
                </div>
                <div class="div-bottom content">
                    <div class="div-summary-seat"><div class="title smaller light">Seats</div><?= $summary['Order']['seats'] ?></div>
                    <div class="div-summary-hall"><div class="title smaller light">Screen No.</div><?= $summary['Hall']['code'] ?></div>
                </div>
            </div>
            <div class="div-summary-member">
                <div class="div-summary-title title big">CUSTOMER</div>

                <div class="div-member-content">
                    <div class="div-member-empty content smallest light <?= $member_visible ?>">No customer information</div>
                    <div class="div-member-info <?= $member_hidden ?>">
                        <div class="div-member-avatar">
                            <img src="<?= $webroot ?>general/cust_avatar.png">
                        </div>
                        <div class="div-member-name title small">
                            <span><?= $data_user['Member']['name'] ?></span>
                            <div class="div-member-id content super-small">ID : <?= $data_user['Member']['code'] ?></div>
                        </div>
                        <div class="div-member-desc content smallest">
                            <div>
                                <div class="content smallest light">Full name:</div><span class="member-info-fullname"><?= $data_user['Member']['name'] ?></span>
                            </div>
                            <div>
                                <div class="content smallest light">Phone:</div><span class="member-info-phone"><?= $data_user['Member']['country_code'] . '-' . '****' . substr($data_user['Member']['phone'], 4) ?></span>
                            </div>
                            <div>
                                <div class="content smallest light">Expiry:</div><span class="member-info-expired_date"><?= $data_user['Member']['expired_date_label'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                    if(!$is_paid) {
                ?>
                        <div class="div-btn-member">
                            <a class="btn-remove-member title small narrow light-red <?= $member_hidden ?>">REMOVE</a>
                            <button type='button' class='btn-set-member title small narrow light-brown <?= $member_visible ?>'>
                                MEMBER CODE
                            </button>
                            <button type='button' class='btn-create-member title small narrow light-brown <?= $member_visible ?>'>
                                NEW MEMBER
                            </button>
                        </div>
                <?php
                    }
                ?>
            </div>
        </div>
        <div class="div-summary-right">
            <div class="div-summary-checkout">
                <div class="div-summary-title title big">CHECK OUT</div>
                <div class="div-summary-list content light">
                    <?php 
                        $total_service_charge = 0;
                        foreach($summary['ScheduleDetailTicketType'] as $detail) {
                            $total_service_charge +=  $detail['OrderDetail']['total_service_charge'];
                    ?>
                    <div class="div-summary-item">
                        <div class="div-movie-title"><?= $detail['MovieLanguage']['name'] . ' ' . $detail['MovieType']['name'] . ' - ' . $detail['TicketTypeLanguage']['name']?></div>
                        <div class="div-qty"><?= $detail['OrderDetail']['total_qty'] ?>x</div>
                        <div class="div-amount">HKD <?= $detail['OrderDetail']['total_price'] ?></div>
                    </div>
                    <?php 
                        }
                    ?>
                </div>
                <div class="div-service-charge content light">
                    <div class="div-summary-item content light smallest">
                        <?php
                            if ($total_service_charge > 0) {
                        ?>
                                <div class="div-movie-title">Service Charge</div>
                                <div class="div-amount">HKD <?= $total_service_charge ?></div>
                        <?php
                            }
                        ?>
                    </div>
                </div>
                <div class="div-discount-member content light">
                    <div class="div-summary-item content light smallest <?= $member_hidden ?>">
                        <div class="div-movie-title">Member Discount</div>
                        <div class="div-amount">HKD - <?= $data_user['Member']['discount_member'] ?></div>
                    </div>
                </div>

                <div class="div-discount-member div-registration-member content light">
                    <div class="div-summary-item content light smallest <?= $member_hidden ?> <?= $registration_hidden ?>">
                        <div class="div-movie-title">Registration Fee</div>
                        <div class="div-registration-amount">HKD - <?= $data_user['Member']['registration_fee'] ?></div>
                    </div>
                </div>

                <div class="div-summary-amount">
                    <div class="div-amount content black-brown"><span>Total:</span>HKD <?= $summary['Order']['grand_total'] ?></div>
                    <?php
                        if($is_paid) {
                            $count = -1;
                            $display_counter = false;
                            if (count($printer_address) > 1) {
                                $display_counter = true;
                            }
                            foreach($printer_address as $printer) { 
                                $count++;
                                $count_display = ($display_counter) ? $count+1 : '';

                    ?>
                                <button type="button" data-count="<?= $count ?>" data-printer_name="<?= $printer['printer_name'] ?>" data-printer_address="<?= $printer['printer_address'] ?>" data-printer_port="<?= $printer['printer_port'] ?>" class="btn-summary-print btn-payprint-<?= $count ?> title small narrow">PRINT TICKET <?= $count_display ?></button>
                    <?php 
                            }
                        } else {
                    ?>      
                            <button type="button" class="btn-summary-checkout title small narrow">CHECK OUT</button>
                    <?php 
                        }  
                    ?>                                
                </div>
            </div>
            <div class="div-blocked-container">
                <textarea class="input-remark txt-remarks"></textarea>
                <button type="button" class="btn-summary-hold title small narrow">HOLD</button>
            </div>
        </div>
    </div>

</div>


<div class="div-dialog-container dialog-set-member hidden">
    <div class="div-dialog-member">
        <a class="link-member-close">
            <img src="<?= $webroot ?>general/img-close.png" class="img-member-close">
        </a>
        <img src="<?= $webroot ?>summarypage/qrcode.png" class="img-member-qrcode">
        <input type="text" class="edit-member-input content smallest light" placeHolder="P031XXXX48291">
        <div class="div-member-notes1 title">Scan or input QR Member code</div>
        <div class="div-member-notes2 content light small">Scan the QR code on ACX mobile app</div>
        <div class="div-member-label content biggest light-brown">scanning...</div>
    </div>
</div>

<div class="div-dialog-container dialog-payment-success hidden">
    <div class="div-dialog-payment-success">
        <img src="<?= $webroot ?>general/icon-success.png">
        <div class="div-success-notes1 title big">Checkout confirmation</div>
        <div class="div-success-notes2 content light small">
            The  #Ref: <?= $summary['Order']['inv_number'] ?> has been completed. Well done!
        </div>
        <button class="btn-close title small narrow" type="button">GO BACK TO MENU MOVIES</button>
    </div>
</div>

<div class="div-dialog-container dialog-create-member hidden">
    <div class="div-dialog-member">
        <a class="link-member-close">
            <img src="<?= $webroot ?>general/img-close.png" class="img-member-close">
        </a>
        <div class="div-input-phone">
            <input type="text" class="edit-country-code content smallest light" placeHolder="+852">
            <input type="text" class="edit-phone content smallest light" placeHolder="XXXX7823">
        </div>
        <div class="div-member-notes1 title">Register new member</div>
        <div class="div-member-notes2 content light small">Input new member's phone to register, <br/>and sytem will send sms of registration link</div>
        <div class="div-button-member">
            <button type='button' class='btn-cancel title small narrow light-brown'>
                CANCEL
            </button>
            <button type='button' class='btn-register title small narrow light-brown'>
                REGISTER
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
        COMMON.retry = 3;
        COMMON.order_id = '<?= $order_id ?>';
        COMMON.token = '<?= $staff['Staff']['token'] ?>';
        COMMON.staff_id = '<?= $staff['Staff']['id'] ?>';
        COMMON.order = '<?= json_encode($summary['Order']) ?>';
        COMMON.data_print = '<?= json_encode($data_print) ?>'; //used for printing ticket for paid trans when redeeming ticket
        COMMON.grand_total = <?= (isset($summary['Order']['grand_total']) && !empty($summary['Order']['grand_total'])) ? $summary['Order']['grand_total'] : 0 ?>;
        COMMON.total_amount = <?= (isset($summary['Order']['total_amount']) && !empty($summary['Order']['total_amount'])) ? $summary['Order']['total_amount'] : 0 ?>;
        COMMON.member_id = <?= (isset($summary['Order']['member_id']) && !empty($summary['Order']['member_id'])) ? $summary['Order']['member_id'] : 0 ?>;

        COMMON.country_code_registration = '<?= (isset($summary['Order']['country_code_registration']) && !empty($summary['Order']['country_code_registration'])) ? $summary['Order']['country_code_registration'] : '' ?>';
        COMMON.phone_registration = '<?= (isset($summary['Order']['phone_registration']) && !empty($summary['Order']['phone_registration'])) ? $summary['Order']['phone_registration'] : '' ?>';
        COMMON.is_member_register = <?= (isset($summary['Order']['is_member_register']) && !empty($summary['Order']['is_member_register'])) ? $summary['Order']['is_member_register'] : 0 ?>;
        COMMON.registration_fee = 0;

        COMMON.url_get_member = '<?= Router::url(array('plugin' => 'member','controller' => 'members', 'action' => 'get_member_by_code', 'api' => true), true); ?>';
        COMMON.url_check_phone_registration = '<?= Router::url(array('plugin' => 'member','controller' => 'members', 'action' => 'check_phone_registration', 'api' => true), true); ?>';
        COMMON.url_update_order_member = '<?= Router::url(array('plugin' => 'pos','controller' => 'orders', 'action' => 'update_membership_order', 'api' => true), true); ?>';
        COMMON.url_payment = '<?= Router::url(array('controller' => 'paymentpage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.url_home = '<?= Router::url(array('controller' => 'ticketingpage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.url_hold_order = '<?= Router::url(array('plugin' => 'pos','controller' => 'orders', 'action' => 'hold_order', 'api' => true), true); ?>';
        COMMON.init_page();
    });
</script>