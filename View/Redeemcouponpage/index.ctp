<div class="div-redeempage">
    <div class="div-main-title title ultra-big middle">Redeem Coupon</div>

    
    
    <div class="div-scanning-box div-ecoupon">
        <img src="<?= $webroot ?>summarypage/qrcode.png" class="img-qrcode">
        <input type="text" class="edit-qrcode-input content smallest light" placeHolder="P031XXXX48291">
        <div class="div-qrcode-notes1 title">Scan or input the QR code</div>
        <div class="div-qrcode-notes2 content light small">Scan the QR code to make sure that the customer are eligible to get the coupon</div>
        <div class="div-qrcode-label content biggest light-brown">SCANNING...</div>
    </div>
    
    <div class="div-scanning-box div-coupon hidden">
        <div class="div-qrcode-notes1 title">One more Step!</div>
        <div class="div-qrcode-notes2 content light small">
            Scan the QR code or input the code <br/>
            to record the process
        </div>
        <img src="<?= $webroot ?>summarypage/qrcode.png" class="img-qrcode">
        <input type="text" class="edit-qrcode-coupon-input content smallest light" placeHolder="P031XXXX48291">
        <div class="div-qrcode-label content biggest light-brown">SCANNING...</div>
    </div>
</div>

<div class="div-dialog-container dialog-scan-ecoupon-success middle hidden">
    <div class="div-dialog-scan-ecoupon-success">
        <div class="div-dialog-title title big">Completed!</div>
        <div class="div-dialog-notes1 content light small">
            Scanning process has been done. <br/>
            It’s eligible to redeem the coupon.
        </div>
        <div class="div-dialog-notes2 content small">
            Confirm to redeem?
        </div>
        <div class="div-dialog-button">
            <button class="btn-close title small narrow" type="button">CANCEL</button>
            <button class="btn-redeem title small narrow" type="button"><div class="box-shadow"></div>REDEEM</button>
        </div>
    </div>
</div>

<div class="div-dialog-container dialog-redeem-coupon-success middle hidden">
    <div class="div-dialog-redeem-coupon-success">
        <img src="<?= $webroot ?>general/icon-success.png">
        <div class="div-success-notes1 title big">Redemption Success!</div>
        <div class="div-success-notes2 content light small">
            The #ref: <span class="coupon-code"></span> has been redeemed.<br/>
            Well done!
        </div>
        <button class="btn-exit title small narrow" type="button">OK</button>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        COMMON.token = '<?= $staff['Staff']['token'] ?>';
        COMMON.staff_id = '<?= $staff['Staff']['id'] ?>';
        COMMON.url_check_coupon = '<?= Router::url(array('plugin' => 'member','controller' => 'member_coupons', 'action' => 'check_coupon', 'api' => true), true); ?>';
        COMMON.url_redeem_ecoupon = '<?= Router::url(array('plugin' => 'member','controller' => 'member_coupons', 'action' => 'redeem_ecoupon', 'api' => true), true); ?>';
        COMMON.init_page();
    });
</script>