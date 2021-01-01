<?php echo $this->Html->css('datatables/dataTables.bootstrap'); ?>
<?php 
    echo $this->Form->create('Log.filter', array(
        'url' => array(
            'plugin' => 'log', 
            'controller' => 'logs', 
            'action' => 'index', 
            'admin' => true, 
            'prefix' => 'admin'
        ),
        'class' => 'form_filter',
        'type' => 'get',
    ));
?>

<?php echo $this->element('Log.log_filter', array(
	'data_search' => $data_search
)); ?>
<div class="row">
    <div class="col-xs-12">
        <div  class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __('Logs'); ?></h3>

                <div class="box-tools pull-right">
                    <?php // echo $this->Html->link(__('<i class="glyphicon glyphicon-plus"></i> New Log'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table id="Logs" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('id', __('id')); ?></th>
                        <th class="text-center"><?php echo h(__d('log', 'action')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('remote_ip', __d('log', 'remote_ip')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('agent', __d('log', 'agent')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('browser', __d('log', 'browser')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('version', __('version')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('platform', __d('log', 'platform')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('created_by', __('administrator')); ?></th>
                        <th class="text-center"><?php echo $this->Paginator->sort('created', __('created')); ?></th>
                        <th class="text-center"><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="text-center">  
                                <input type="checkbox" name="choose_id[]" value="<?php echo($log['Log']['id']); ?>" />
                            </td>
                            <td class="text-center"> <?php echo h($log['Log']['id']); ?> </td>
                            <td class="text-center">
                                <?= $log['Log']['plugin'] . '-' . $log['Log']['controller'] . '-' . $log['Log']['action'] ?>
                            </td>
                            <td class="text-center"><?php echo h($log['Log']['remote_ip']); ?></td>
                            <td class="text-center"><?php echo h($log['Log']['agent']); ?></td>
                            <td class="text-center"><?php echo h($log['Log']['browser']); ?></td>
                            <td class="text-center"><?php echo h($log['Log']['version']); ?></td>
                            <td class="text-center"><?php echo h($log['Log']['platform']); ?></td>
                            <td class="text-center"><?php echo h($log['CreatedBy']['email']); ?></td>
                            <td class="text-center"><?php echo h($log['Log']['created']); ?></td>
                            <td class="text-center">
                                <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('plugin' => 'log', 'controller' => 'logs', 'action' => 'view', $log['Log']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => 'view')); ?>
                            </td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.index -->
        <?php echo $this->element('Paginator'); ?>	</div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->

<?php echo $this->Form->end(); ?>

<?php
    echo $this->Html->script('plugins/datatables/jquery.dataTables');
    echo $this->Html->script('plugins/datatables/dataTables.bootstrap');
?>


<script type="text/javascript">
	$(document).ready(function(){
		COMMON.module_name = '_logs';
		COMMON.url_update_cache = '<?= Router::url(array('prefix' => 'admin', 'plugin' => '', 'controller' => 'redis', 'action' => 'update_column_cache'), true) ?>';
		COMMON.column_cache = <?= $column_cache; ?>;
        COMMON.init_visible_column_table();
    });
</script>