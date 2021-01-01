<div class="div-redeemticketpage">
    <div class="div-main-title title ultra-big middle">Redeem Ticket</div>

    
    
    <div class="div-scanning-box div-ticket">
        <img src="<?= $webroot ?>summarypage/qrcode.png" class="img-qrcode">
        <input type="text" class="edit-qrcode-input content smallest light" placeHolder="P031XXXX48291">
        <div class="div-qrcode-notes1 title">Scan or input the QR code</div>
        <div class="div-qrcode-notes2 content light small">Scan the QR code to make sure that the customer are eligible to get the ticket</div>
        <div class="div-qrcode-label content biggest light-brown">SCANNING...</div>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function() {
        COMMON.token = '<?= $staff['Staff']['token'] ?>';
        COMMON.staff_id = '<?= $staff['Staff']['id'] ?>';
        COMMON.url_payment = '<?= Router::url(array('controller' => 'paymentpage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.url_summary = '<?= Router::url(array('controller' => 'summarypage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.url_check_qrcode_validity = '<?= Router::url(array('plugin' => 'pos','controller' => 'orders', 'action' => 'check_qrcode_validity', 'api' => true), true); ?>';
        COMMON.init_page();
    });
</script>