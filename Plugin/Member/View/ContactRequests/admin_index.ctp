<?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>

<?=
$this->element('Member.contact_request_filter', array(
    'data_search' => $data_search
));
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">

            <div class="box-body table-responsive">
                <table id="Companies" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th class="text-center"><?= $this->Paginator->sort('id', __('id')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('title', __('title')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('name', __('name')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('email', __('email')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('country_code', __('country_code')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('phone', __('phone')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('message', __( 'message')); ?></th>
                        <th class="text-center"><?= $this->Paginator->sort('updated',__('updated')); ?></th>
                        <th class="text-center"><?= __('operation'); ?></th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($dbdatas as $dbdata): ?>
                        <tr>
                            <td class="text-center"><?= h($dbdata[$model]['id']); ?>&nbsp;</td>

                            <td class="text-center">
                                <?= __(h($dbdata[$model]['title'])); ?>
                            </td>

                            <td class="text-center">
                                <?= h($dbdata[$model]['name']); ?>
                            </td>

                            <td class="text-center">
                                <?= h($dbdata[$model]['email']); ?>
                            </td>

                            <td class="text-center">
                                <?= h($dbdata[$model]['country_code']); ?>
                            </td>

                            <td class="text-center">
                                <?= h($dbdata[$model]['phone']); ?>
                            </td>

                            <td class="text-center">
                                <?= h($dbdata[$model]['message']); ?>
                            </td>

                            <td class="text-center"><?= h($dbdata[$model]['updated']); ?>&nbsp;</td>
                            <td class="text-center">
                                <?= $this->Html->link('<i class="glyphicon glyphicon-eye-open"></i>', array('action' => 'view', $dbdata[$model]['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view'))); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?= $this->element('Paginator'); ?>
    </div>
</div>

<?php
echo $this->Html->script('plugins/datatables/jquery.dataTables', array('inline' => false));
echo $this->Html->script('plugins/datatables/dataTables.bootstrap', array('inline' => false));
?>
<script type="text/javascript">
    $(document).ready(function(){

    });
</script>