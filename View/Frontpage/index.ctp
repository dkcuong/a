<div class="div-frontpage">
    <img src="<?= $webroot ?>frontpage/logo.png" class="img-logo">
    <div class="div-login-box">
        <div class="div-login-title title biggest white">Staff Login</div>
        <?= $this->Form->create('Login', array('role' => 'form', 'id' => 'login-add-form')); ?>
            <?= $this->Form->input('username', array('class' => 'form-control content super-small light white', 'placeholder' => 'Staff ID', 'label' => false)); ?>
            <?= $this->Form->input('password', array('class' => 'form-control content super-small light white', 'placeholder' => 'Password', 'type' =>'password', 'label' => false)); ?>
            <button type="button" class="btn-login title small narrow">LOG IN</button>
        <?= $this->Form->end(); ?>
    </div>
</div>
<?php
    //echo $this->Html->script('frontpage.js?v=1'); 
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#LoginUsername').focus();
        COMMON.url_do_login = '<?= Router::url(array('plugin' => 'cinema','controller' => 'staffs', 'action' => 'login', 'api' => true), true); ?>';
        COMMON.err_user_pass_wrong = '<?= sprintf(__('username_password_not_found')) ?>';
        COMMON.url_home = '<?= Router::url(array('controller' => 'homepage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.init_page();
    });
</script>