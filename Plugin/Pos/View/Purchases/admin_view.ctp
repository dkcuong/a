
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php  echo __d('purchase', 'item_title'); ?></h3>

                <div class="box-tools pull-right">
                    <?php
                   /* if( isset($permissions['Purchase']['edit']) && ($permissions['Purchase']['edit'] == true) ){
                        echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '.__d('purchase', 'edit_item'), array('action' => 'edit', $dbdata['Purchase']['id']), array('class' => 'btn btn-primary', 'escape' => false));
                    }*/
                    ?>
                </div>
            </div>

            <div class="box-body">

                <table id="Purchase" class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <td><strong><?= __('id'); ?></strong></td>
                        <td>
                            <?= h($dbdata['Purchase']['id']); ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('date'); ?></strong></td>
                        <td>
                            <?= h($dbdata['Purchase']['date']); ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('inv_number'); ?></strong></td>
                        <td>
                            <?= h($dbdata['Purchase']['inv_number']); ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __d('member', 'item_title'); ?></strong></td>
                        <td>
                            <?= h($dbdata['Member']['name']); ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __d('purchase', 'amount_of_item'); ?></strong></td>
                        <td>
                            <?= h($dbdata[0]['amount_of_item']); ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __d('payment', 'method'); ?></strong></td>
                        <td>
                            <?= h($dbdata[0]['payment_method_group']); ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('updated_by'); ?></strong></td>
                        <td>
                            <?= h($dbdata['Purchase']['updated']); ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('updated'); ?></strong></td>
                        <td>
                            <?= h($dbdata['UpdatedBy']['email']); ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('created'); ?></strong></td>
                        <td>
                            <?= h($dbdata['Purchase']['created']); ?>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?= __('created_by'); ?></strong></td>
                        <td>
                            <?= h($dbdata['CreatedBy']['email']); ?>
                            &nbsp;
                        </td>
                    </tr>
                    </tbody>
                </table><!-- /.table table-striped table-bordered -->
            </div>
        </div><!-- /.view -->

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= __d('item', 'item_title'); ?></h3>
            </div>

            <?php if( isset($dbdata['PurchaseDetail']) && !empty($dbdata['PurchaseDetail']) ){ ?>
                <div class="box-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th class="text-center"><?= __('id'); ?></th>
                            <th class="text-center"><?= __('name'); ?></th>
                            <th class="text-center"><?= __('quantity'); ?></th>
                            <th class="text-center"><?= __('price'); ?></th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($dbdata['PurchaseDetail'] as $key => $value) { ?>
                            <tr>
                                <td class="text-center">
                                    <?php
                                    echo $this->Html->link($value['id'], array(
                                        'plugin' => 'pos', 'controller' => 'items',
                                        'action' => 'view', 'admin' => true, 'prefix' => 'admin', $value['item_id']
                                    ));
                                    ?>
                                </td>
                                <td class="text-center"><?= $value['Item']['ItemLanguage'][0]['name']; ?></td>
                                <td class="text-center"><?= $value['qty']; ?></td>
                                <td class="text-center"><?= $value['Item']['price']; ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div><!-- /.related -->
    </div><!-- /#page-content .span9 -->
</div><!-- /#page-container .row-fluid -->

