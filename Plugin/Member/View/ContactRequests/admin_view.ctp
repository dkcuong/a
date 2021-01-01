<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= __d('member', 'contact_request_item'); ?></h3>

                <div class="box-tools pull-right">
                    <?php
                    /*    if(isset($permissions[$model]['edit']) && $permissions[$model]['edit']){
                            echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '. __d('place', 'edit_cinema'), array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-primary', 'escape' => false));
                        }*/
                    ?>
                </div>
            </div>
            <div class="box-body">
                <?php if($dbdata[$model]){ ?>
                    <table id="Company" class="table table-bordered table-striped">
                        <tbody>
                        <tr>
                            <td><strong><?= __('id'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['id']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong><?= __('title'); ?></strong></td>
                            <td>
                                <?= __(h($dbdata[$model]['title'])); ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong><?= __('name'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['name']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong><?= __('email'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['email']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong><?= __('country_code'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['country_code']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong><?= __('phone'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['phone']); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __( 'message'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['message']); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __('updated'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['updated']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong><?= __('updated_by'); ?></strong></td>
                            <td>
                                <?= h($dbdata['UpdatedBy']['email']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong><?= __('created'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['created']); ?>
                            </td>
                        </tr>

                        <tr>
                            <td><strong><?= __('created_by'); ?></strong></td>
                            <td>
                                <?= h($dbdata['CreatedBy']['email']); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
    });
</script>
