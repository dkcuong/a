<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('staff', 'staff_title'); ?></h3>

				<div class="box-tools pull-right">
                    <?php
                        if(isset($permissions[$model]['edit']) && $permissions[$model]['edit']){
                            echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '. __d('staff', 'edit_staff'), array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-primary', 'escape' => false));
                        } 
                    ?>
	            </div>
			</div>
			<div class="box-body table-responsive">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role=<?= $model ?> class="active">
                            <a href="#info-tab" aria-controls="tab" role="tab" data-toggle="tab">
                                <?= __d('staff', 'staff_title'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="info-tab">
                        <?php if($dbdata[$model]){ ?>
                            <table id="member" class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <td><strong><?= __('id'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['id']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('name'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['name']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('code'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['code']); ?>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td><strong><?= __('role'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['role']); ?>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td><strong><?= __('username'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['username']); ?>
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
                                        <td><strong><?= __('enabled'); ?></strong></td>
                                        <td>
                                            <?= $this->element('view_check_ico',array('_check' => $dbdata[$model]['enabled'])) ?>
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="margin-top-15">
                                        <?php echo $this->element('content_view',array(
                                            'languages' => $languages,
                                            'language_input_fields' => $language_input_fields,
                                        )); ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div> <!-- close tabpanel -->
                </div> <!-- close tab-content -->
			</div>
		</div>
	</div>
</div>
	