
<div class="row">
    <div class="col-xs-12 col-xs-offset-0">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?php echo __d('administration','add_role'); ?></h3>
			</div>

			<div class="box-body">
                <?php echo $this->Form->create('Role', array('role' => 'form')); ?>
					<div class="row">
                        <div class="col-md-12">
                            <?php echo $this->Form->submit(__('submit'), array(
                                    'id' => 'btnAdded',
                                    'class' => 'btn btn-large btn-primary pull-right')); ?>
                        </div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-sm-12 col-md-6">
							<label><?= '<font color="red">*</font> ' . __('slug') ?></label>
							<div class="input-group">
								<span class="input-group-addon">role-</span>
								<?php
									echo $this->Form->input('slug', array(
										'id' => 'txtSlug',
										'class' => 'form-control', 
										'label' => false,
										'type' => 'text',
                                        'required' => 'required',
									));
								?>
							</div>
						</div>
						<div class="form-group col-xs-12 col-sm-12 col-md-6">
							<?php echo $this->Form->input('name', array(
								'class' => 'form-control', 
                                'required' => 'required',
                                'label' => '<font color="red">*</font>' . __d('administration','name')
                            )); ?>
						</div>
					</div>
					<div class="row margin-top-15">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <button type="button" class="btn btn-box-tool btn-primary" id="collapse">
                                    <i class="fa fa-minus"></i> <?php echo __('collapse_all')  ?>
                                </button>
                                <button type="button" class="btn btn-box-tool btn-success" id="expand">
                                    <i class="fa fa-plus"></i> <?php echo __('expand_all')  ?> 
                                </button>
                            </div>
                        </div>
					</div>
					<?php if( isset($permissions_matrix) && !empty($permissions_matrix) ){ ?>
						<fieldset>
							<h4>Permissions Management</h4>
							<div class="row">
                                <?php $flag_row = 0 ?>
								<?php foreach ($permissions_matrix as $key => $matrix) { ?>
									<div class="col-xs-12 col-sm-12 col-md-6 my-box">
										<div class="box box-primary">
											<div class="box-header with-border">
												<h3 class="box-title"><?php echo $key; ?></h3>
												<div class="box-tools pull-right">
													<button type="button" class="btn btn-box-tool" data-widget="collapse">
														<i class="fa fa-plus"></i>
													</button>

													<button type="button" class="btn btn-box-tool" data-widget="remove">
														<i class="fa fa-times"></i>
													</button>
												</div>
											</div>
											<div class="box-body tbl-role-permission">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th class="text-center">#</th>
															<th class="text-center"><?php echo __d('administration', 'rule'); ?></th>
															<th class="text-center" title="<?php echo __d('administration', 'action'); ?>"><?php echo __d('administration', 'action'); ?></th>
															<th class="text-center"><?php 
																	echo $this->Form->input('chk_all', array(
																		'type' => 'checkbox',
                                                                        'label' => false,
																		'hiddenField' => false,
                                                                        'class' => 'chk-all-permission'
																	));
																?></th>
														</tr>
													</thead>
													<tbody>
														<?php foreach ($matrix as $key => $rule) { ?>
															<tr>
																<td class="text-center"><?= $rule['id']; ?></td>
																<td><?= $rule['name']; ?></td>
																<td class="text-center"><?= __($rule['action']) ?></td>
																<td class="text-center"><?php 
																	echo $this->Form->input('Permission.rules.', array(
																		'type' => 'checkbox',
																		'label' => false,
																		'hiddenField' => false,
																		'class' => 'chk-permission-id',
																		'value' => $rule['id']
																	));
																?></td>
															</tr>
														<?php } ?>
													</tbody>
												</table>
											</div>
										</div>
                                    </div>
                                    <?php
                                        $flag_row++;
                                        if($flag_row % 2 == 0){
                                            echo '</div><div class="row">';
                                        }
                                    ?>
								<?php } ?>
							</div>
						</fieldset>
					<?php } ?>

					<div class="row">
                        <div class="col-md-12">
                            <?php echo $this->Form->submit(__('submit'), array(
                                    'id' => 'btnAdded',
                                    'class' => 'btn btn-large btn-primary pull-right')); ?>
                        </div>
					</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>
<?php
    echo $this->Html->script('CakeAdminLTE/pages/admin_role', array('inline' => false));
?>
