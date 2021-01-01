<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __d('movie', 'star'); ?></h3>

                <div class="box-tools pull-right">
                    <?php 
                        if( isset($permissions[$model]['edit']) && ($permissions[$model]['edit'] == true) ){
                            echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '.__d('movie', 'edit_star'), array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-primary', 'escape' => false));
                        }
                    ?>
                </div>
            </div>

            <div class="box-body">

                <table id="Star" class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <td><strong><?= __('id'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['id']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <div class="row">
                            <div class="col-md-12"><strong><?= __('first_name'); ?></strong></div>
                            <div class="col-md-12">
                                <div class="margin-top-15">
                                    <?php echo $this->element('content_view',array(
                                        'languages' => $languages,
                                        'language_input_fields' => ['star_first_name'],
                                    )); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12"><strong><?= __d('movie', 'surname'); ?></strong></div>
                            <div class="col-md-12">
                                <div class="margin-top-15">
                                    <?php echo $this->element('content_view',array(
                                        'languages' => $languages,
                                        'language_input_fields' => ['star_surname'],
                                    )); ?>
                                </div>
                            </div>
                        </div>
                        <tr>
                            <td><strong><?= __d('movie', 'code_first_name'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['first_name']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __d('movie', 'code_surname'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['surname']); ?>
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td><strong><?= __d('movie', 'photo'); ?></strong></td>
                            <td>
                                <?php if (isset($dbdata[$model]['image_url']) && !empty($dbdata[$model]['image_url'])) { ?>
                                        <img height="240" src="<?= $this->webroot . 'img/' . $dbdata[$model]['image_url'];?>" />
                                <?php } ?>
                                &nbsp;
                            </td>
                        </tr>

                        <tr>
                            <td><strong><?= __('updated_by'); ?></strong></td>
                            <td>
                                <?= h($dbdata[$model]['updated']); ?>
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
                                <?= h($dbdata[$model]['created']); ?>
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
                <h3 class="box-title"><?= __d('movie', 'movie'); ?></h3>
            </div>


        </div><!-- /.related -->
    </div><!-- /#page-content .span9 -->
</div><!-- /#page-container .row-fluid -->
