<div class="div-changepasswordpage">

    <div class="div-password-box">
        <div class="div-password-content">
            <div class="div-title title small">Change Password</div>
            <div class="div-line"></div>
            <div class="div-input-box first">
                <div class="div-input-label content light super-small">Current password</div>
                <input class="div-input" id="cur-pass" type="password">
            </div>

            <div class="div-input-box second">
                <div class="div-input-label content light super-small">New password</div>
                <input class="div-input" id="new-pass" type="password">
            </div>

            <div class="div-input-box third">
                <div class="div-input-label content light super-small">Confirm new password</div>
                <input class="div-input" id="confirm-pass" type="password">
            </div>


        </div>
        <div class="div-button-box">
            <button type="button" class="btn-cancel-change title smaller">CANCEL</button>
            <button type="button" class="btn-submit-change title smaller">CHANGE</button>
        </div>
    </div>

</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#cur-pass').focus();
        COMMON.token = '<?= $staff['Staff']['token'] ?>';
        COMMON.staff_id = '<?= $staff['Staff']['id'] ?>';
        COMMON.url_change_password = '<?= Router::url(array('plugin' => 'cinema', 'controller' => 'staffs', 'action' => 'change_password', 'api' => true), true); ?>';
        COMMON.url_home = '<?= Router::url(array('controller' => 'homepage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.init_page();
    });
</script>