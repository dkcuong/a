<div class="div-homepage">
    <div class="div-menu-container">
        <a href="<?= Router::url(array( 'controller' => 'ticketingpage', 'action' => 'index')) ?>">
            <div class="div-menu-item">
                <div class="div-menu-cover"></div>
                <img src="<?= $webroot ?>homepage/ticket.png">
                <div class="title">
                    FILM TICKETS
                </div>
            </div>
        </a>
        <a href="<?= Router::url(array( 'controller' => 'snackpage', 'action' => 'index')) ?>">
            <div class="div-menu-item">
                <div class="div-menu-cover"></div>
                <img src="<?= $webroot ?>homepage/snack.png">
                <div class="title">
                    TUCK SHOP
                </div>
            </div>
        </a>
        <a href="<?= Router::url(array( 'controller' => 'redeemcouponpage', 'action' => 'index')) ?>">
            <div class="div-menu-item">
                <div class="div-menu-cover"></div>
                <img src="<?= $webroot ?>homepage/coupon.png">
                <div class="title">
                    REDEEM COUPON
                </div>
            </div>
        </a>
        <a href="<?= Router::url(array( 'controller' => 'redeemticketpage', 'action' => 'index')) ?>">
            <div class="div-menu-item">
                <div class="div-menu-cover"></div>
                <img src="<?= $webroot ?>homepage/ticket.png">
                <div class="title">
                    REDEEM TICKET
                </div>
            </div>
        </a>
    </div>
</div>