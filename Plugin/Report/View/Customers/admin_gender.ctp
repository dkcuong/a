<?php echo $this->Html->css('datepicker/datepicker3'); ?>
<?php echo $this->Html->css('chartjs/Chart.min'); ?>
<?php echo $this->Html->css('dashboard'); ?>

<div class="row">
    <!-- Start Gender Member Section -->
    <div class="col-md-12">
        <div class="box box-all-info">
            <div class="box-body">
                <h2><?php echo __d('dashboard', 'gender_member'); ?></h2>
                <div class="cover-doughnut-chart">
                    <canvas id="gender_member">
                        
                    </canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- End Gender Member Section -->
</div>
<?php
	echo $this->Html->script('plugins/datepicker/bootstrap-datepicker', array('inline' => false));
	echo $this->Html->script('plugins/chartjs/Chart.min', array('inline' => false));
	echo $this->Html->script('CakeAdminLTE/pages/admin_dashboard', array('inline' => false));
?>
<script type="text/javascript">
	$(document).ready(function(){
		ADMIN_DASHBOARD.url_gender_member = '<?= Router::url(array('plugin' => 'report', 'controller' => 'customers', 'action' => 'gender_json')); ?>';
       
		ADMIN_DASHBOARD.init_page();
	});
</script>