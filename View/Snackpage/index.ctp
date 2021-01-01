<div class="div-snackpage">
    <div class="div-purchase-container">
        <div class="div-item-container">
            <div class="div-item-slide">
                <div class="div-item-slide-gallery">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="div-item-list-container">
                            <?php
                                $counter = 0;
                                foreach($data_items as $item) {
                                    $counter++;
                                    if ($counter > 6) {
                                        $counter = 1;
                                        echo('</div>');
                                        echo('</div>');
                                        echo('<div class="swiper-slide">');
                                        echo(' <div class="div-item-list-container">');
                                    }
                            ?>
                                    <a data-id="<?= $item['Item']['id'] ?>" data-code="<?= $item['Item']['code'] ?>" data-price="<?= $item['Item']['price'] ?>">
                                        <div class="div-item-box">
                                            <img src="<?= $webroot.$item['Item']['image'] ?>">
                                            <div class="div-item-info">
                                                <div class="div-item-code content super-small"><?= $item['ItemLanguage']['name'] ?></div>
                                            </div>
                                        </div>
                                    </a>
                            <?php
                                }
                            ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="div-navigator-pagination">
                    <div class="btn-prev"><img src="<?= $webroot ?>general/btn-prev.png"></div>
                    <div class="swiper-pagination"></div>
                    <div class="btn-next"><img src="<?= $webroot ?>general/btn-next.png"></div>
                </div>
            </div>
        </div>
        <div class="div-checkout-container">
            <div class="div-summary-checkout">
                <div class="div-summary-title title">CHECK OUT</div>
                <div class="div-summary-list content light smallest">
                    <div class="div-discount-member content light">
                        <div class="div-summary-item content light smallest hidden">
                            <div class="div-movie-title">Member Discount</div>
                            <div class="div-amount">HKD - </div>
                        </div>
                    </div>
                    <!--
                    <div class="div-summary-item">
                        <div class="div-movie-title">COMBO #1</div>
                        <div class="div-qty">2x</div>
                        <div class="div-amount">HKD 90.0</div>
                    </div>
                    -->
                </div>
                <!--
                <div class="div-service-charge content light">
                    <div class="div-summary-item content light smallest">
                        <div class="div-movie-title">Service Charge</div>
                        <div class="div-amount">HKD 0</div>
                    </div>
                </div>
                -->
                <div class="div-summary-amount">
                    <div class="div-amount content black-brown"><span>Total:</span>HKD 0.0</div>
                    <button type="button" class="btn-snack-checkout title small narrow">CHECK OUT</button>
                </div>
            </div>
            <div class="div-button-bottom">
                <button class="btn-set-member title smaller light-brown">MEMBER CODE</button>
                <button class="btn-clear-all title smaller light-red">CLEAR ALL</button>
            </div>
        </div>
    </div>

</div>


<div class="div-dialog-container dialog-set-member snackpage-member hidden">
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

<?php
    echo $this->Html->script('purchase.js?v=1'); 
?>
<script type="text/javascript">
    $(document).ready(function() {        
        COMMON.webroot = '<?= $webroot ?>';
        COMMON.token = '<?= $staff['Staff']['token'] ?>';
        COMMON.staff_id = '<?= $staff['Staff']['id'] ?>';
        COMMON.order_id = <?= (isset($data_purchase['Purchase']['id']) && !empty($data_purchase['Purchase']['id'])) ? $data_purchase['Purchase']['id'] : 0 ?>;
        COMMON.order = '<?= json_encode($data_purchase) ?>';
        COMMON.item_bought = [];
        COMMON.service_charge_percentage = <?= (isset($service_charge_percentage) && !empty($service_charge_percentage)) ? $service_charge_percentage : 0 ?>;
        COMMON.url_get_member = '<?= Router::url(array('plugin' => 'member','controller' => 'members', 'action' => 'get_member_by_code', 'api' => true), true); ?>';
        COMMON.url_payment = '<?= Router::url(array('controller' => 'paymentpage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.url_create_trans = '<?= Router::url(array('plugin' => 'pos', 'controller' => 'purchases', 'action' => 'create_purchase_trans', 'api' => true), true); ?>';
        COMMON.init_page();
        COMMON.restore_purchase_trans();
        PURCHASE.init_page();
    });
</script>