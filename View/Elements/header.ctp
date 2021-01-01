<div class="div-header">
    <?php
        if(isset($display_link_signout) && $display_link_signout) {
    ?>
            <a href="<?= Router::url(array( 'controller' => 'homepage', 'action' => 'index')) ?>" class="link-signout">
                <img src="<?= $webroot ?>general/arrow-left.png">
                <span class="span-cat title small">Category</span>
            </a>
    <?php
        }
    ?>
    <a href="<?= Router::url(array( 'controller' => 'homepage', 'action' => 'index')) ?>"><img src="<?= $webroot ?>frontpage/logo.png" class="img-logo"></a>
    <div class="div-welcome">
        <div class="div-welcome-content content super-small grey">
            <span class="title light dark-grey smaller">Welcome,</span> <?= $staff['Staff']['name'] ?>
        </div>
        <div class="div-avatar" style="background-image: url(<?= $staff['Staff']['image'] ?>)">
        </div>
        <div class="header-menu content light smallest">
            <a href="<?= Router::url(array( 'controller' => 'purchasingpage', 'action' => 'index')) ?>">
                <div>Purchasing</div>
            </a>
            <a href="<?= Router::url(array( 'controller' => 'transactionpage', 'action' => 'index')) ?>">
                <div>Transactions</div>
            </a>
            <a href="<?= Router::url(array( 'controller' => 'changepasswordpage', 'action' => 'index')) ?>">
                <div>Change Password</div>
            </a>
            <a href="<?= Router::url(array( 'controller' => 'frontpage', 'action' => 'do_logout')) ?>">
                <div>Sign Out</div>
            </a>
        </div>
    </div>
</div>