<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php  echo __('Log'); ?></h3>
            </div>

            <div class="box-body table-responsive">
                <table id="Logs" class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <td><strong><?= __('id'); ?></strong></td>
                        <td>
                            <?= h($log['Log']['id']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('plugin'); ?></strong></td>
                        <td>
                            <?= h($log['Log']['plugin']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('controller'); ?></strong></td>
                        <td>
                            <?= h($log['Log']['controller']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('action'); ?></strong></td>
                        <td>
                            <?= h($log['Log']['action']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('remote_ip'); ?></strong></td>
                        <td>
                            <?= h($log['Log']['remote_ip']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('agent'); ?></strong></td>
                        <td>
                            <?= h($log['Log']['agent']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('browser'); ?></strong></td>
                        <td>
                            <?= h($log['Log']['browser']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('version'); ?></strong></td>
                        <td>
                            <?= h($log['Log']['version']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('platform'); ?></strong></td>
                        <td>
                            <?= h($log['Log']['platform']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('enabled'); ?></strong></td>
                        <td>
                            <?= $this->element('view_check_ico', array('_check' => $log['Log']['enabled'])); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('archived'); ?></strong></td>
                        <td>
                            <?= $this->element('view_check_ico', array('_check' => $log['Log']['archived'])); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('created'); ?></strong></td>
                        <td>
                            <?= h($log['Log']['created']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('created_by'); ?></strong></td>
                        <td>
                            <?= $log['Log']['created'] == 0 ? 'Cronjob' : h($log['CreatedBy']['email']); ?>
                        </td>
                    </tr>
                    </tbody>
                </table><!-- /.table table-striped table-bordered -->
                <hr />
                <?php if($old_data || $new_data){ ?>
                    <div class="panel panel-primary ">
                        <div class="panel-heading">
                            <h3 class="panel-title title-log-detail">Data Changed (OLD DATA - NEW DATA)</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-6 error-area">
                                    <?php if(is_array($old_data)){ ?>
                                        <?php
                                            echo $this->element('Log.log_array', array(
                                                'data_render' => $old_data,
                                            )); 
                                        ?>
                                    <?php }else if(is_object($old_data)){ ?>
                                        <?php
                                            echo $this->element('Log.log_object', array(
                                                'data_render' => $old_data,
                                            )); 
                                        ?>
                                    <?php }else { ?>
                                        <?= $old_data; ?>
                                    <?php } ?>
                                </div>
                                <div class="col-xs-6 success-area">
                                    <?php if(is_array($new_data)){ ?>
                                        <?php
                                            echo $this->element('Log.log_array', array(
                                                'data_render' => $new_data,
                                            )); 
                                        ?>
                                    <?php }else if(is_object($new_data)){ ?>
                                        <?php
                                            echo $this->element('Log.log_object', array(
                                                'data_render' => $new_data,
                                            )); 
                                        ?>
                                    <?php }else { ?>
                                        <?= $new_data; ?>
                                    <?php } ?>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php if($success_data){ ?>
                    <div class="panel panel-primary ">
                        <div class="panel-heading">
                            <h3 class="panel-title title-log-detail">LOG SUCCESS</h3>
                        </div>
                        <div class="panel-body">
                            <?php $number = 1; ?>
                            <?php foreach($success_data as $item){ ?>
                                <?php if(is_array($item)){ ?>
                                    <?php
                                        echo $this->element('Log.log_array', array(
                                            'data_render' => $item,
                                            'number' => $number
                                        )); 
                                        $number += count($item);
                                    ?>
                                <?php }else if(is_object($item)){ ?>
                                    <?php
                                        echo $this->element('Log.log_object', array(
                                            'data_render' => $item,
                                        )); 
                                    ?>
                                <?php }else { ?>
                                    <?= $item; ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if($error_data){ ?>
                    <div class="panel panel-warning ">
                        <div class="panel-heading">
                            <h3 class="panel-title title-log-detail">LOG ERROR</h3>
                        </div>
                        <div class="panel-body"> 
                            <?php $number_error = 1; ?>
                            <?php foreach($error_data as $item){ ?>
                                <?php if(is_array($item)){ ?>
                                    <?php
                                        echo $this->element('Log.log_array', array(
                                            'data_render' => $item,
                                            'number' => $number_error
                                        )); 
                                        $number_error += count($item);
                                    ?>
                                <?php }else if(is_object($item)){ ?>
                                    <?php
                                        echo $this->element('Log.log_object', array(
                                            'data_render' => $item,
                                        ));
                                    ?>
                                <?php }else { ?>
                                    <?= $item; ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div><!-- /.table-responsive -->
        </div><!-- /.view -->
    </div><!-- /#page-content .span9 -->
</div><!-- /#page-container .row-fluid -->

<?php
	echo $this->Html->script('CakeAdminLTE/pages/admin_log', array('inline' => false));
?>

<script type="text/javascript">
	$(document).ready(function(){
        ADMIN_LOG.init_highlight_changed_data();
    });
</script>