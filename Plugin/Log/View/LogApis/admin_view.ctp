<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php  echo __('log_api'); ?></h3>
            </div>

            <div class="box-body table-responsive">
                <table id="Logs" class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <td><strong><?= __('id'); ?></strong></td>
                        <td>
                            <?= h($log['LogApi']['id']); ?>
                        </td>
                    </tr>
                    <tr>
                    <td><strong><?= __('company'); ?></strong></td>
                        <td>
                            <?= isset($list_index_companies[$log['LogApi']['company_id']]) ? 
                                $this->Html->link($list_index_companies[$log['LogApi']['company_id']], 
                                    array('plugin' => 'company', 'controller' => 'companies', 'action' => 'view', $log['LogApi']['company_id']), array('class' => '')) : ''; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('plugin'); ?></strong></td>
                        <td>
                            <?= h($log['LogApi']['plugin']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('controller'); ?></strong></td>
                        <td>
                            <?= h($log['LogApi']['controller']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('action'); ?></strong></td>
                        <td>
                            <?= h($log['LogApi']['action']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('enabled'); ?></strong></td>
                        <td>
                            <?= $this->element('view_check_ico', array('_check' => $log['LogApi']['enabled'])); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('archived'); ?></strong></td>
                        <td>
                            <?= $this->element('view_check_ico', array('_check' => $log['LogApi']['archived'])); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('created'); ?></strong></td>
                        <td>
                            <?= h($log['LogApi']['created']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('created_by'); ?></strong></td>
                        <td>
                            <?= h($log['CreatedMember']['email']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('received_params'); ?></strong></td>
                        <td>
                        <?php if(is_array($received_params)){ ?>
                                <?php
                                    echo $this->element('Log.log_array', array(
                                        'data_render' => $received_params,
                                    )); 
                                ?>
                            <?php }else if(is_object($received_params)){ ?>
                                <?php
                                    echo $this->element('Log.log_object', array(
                                        'data_render' => $received_params,
                                    )); 
                                ?>
                            <?php }else { ?>
                                <?= $received_params; ?>
                            <?php } ?>
                        </td>
                    </tr>
                    </tbody>
                </table><!-- /.table table-striped table-bordered -->
                <hr />
                <?php if($success_data){ ?>
                    <div class="panel panel-success ">
                        <div class="panel-heading">
                            <h3 class="panel-title title-log-detail">LOG SUCCESS</h3>
                        </div>
                        <div class="panel-body"> 
                            <?php if(is_array($success_data)){ ?>
                                <?php
                                    echo $this->element('Log.log_array', array(
                                        'data_render' => $success_data,
                                    )); 
                                ?>
                            <?php }else if(is_object($success_data)){ ?>
                                <?php
                                    echo $this->element('Log.log_object', array(
                                        'data_render' => $success_data,
                                    )); 
                                ?>
                            <?php }else { ?>
                                <?= $success_data; ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if($old_data || $new_data){ ?>
                    <div class="panel panel-primary ">
                        <div class="panel-heading">
                            <h3 class="panel-title title-log-detail">Data Changed</h3>
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

                <?php if($error_data){ ?>
                    <div class="panel panel-warning ">
                        <div class="panel-heading">
                            <h3 class="panel-title title-log-detail">LOG ERROR</h3>
                        </div>
                        <div class="panel-body"> 
                            <?php if(is_array($error_data)){ ?>
                                <?php
                                    echo $this->element('Log.log_array', array(
                                        'data_render' => $error_data,
                                    )); 
                                ?>
                            <?php }else if(is_object($error_data)){ ?>
                                <?php
                                    echo $this->element('Log.log_object', array(
                                        'data_render' => $error_data,
                                    )); 
                                ?>
                            <?php }else { ?>
                                <?= $error_data; ?>
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