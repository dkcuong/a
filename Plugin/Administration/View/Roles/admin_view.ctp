<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?php  echo __('Role'); ?></h3>
				<div class="box-tools pull-right">
	                <?php 
	                	if( isset($permissions['Role']['edit']) && ($permissions['Role']['edit'] == true) ){
	                		echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '.__d('administration','edit_role'), array('action' => 'edit', $role['Role']['id']), array('class' => 'btn btn-primary', 'escape' => false));
	                	}
	                ?>
	            </div>
			</div>
			
			<div class="box-body">
                <table id="Roles" class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td><strong><?= __('id'); ?></strong></td>
							<td>
								<?= h($role['Role']['id']); ?>
								
							</td>
						</tr>
						<tr>
							<td><strong><?= __('slug'); ?></strong></td>
							<td>
								<?= h($role['Role']['slug']); ?>
								
							</td>
						</tr>
						<tr>
							<td><strong><?= __('name'); ?></strong></td>
							<td>
								<?= h($role['Role']['name']); ?>
								
							</td>
						</tr>
						<tr>
							<td><strong><?= __('updated_by'); ?></strong></td>
							<td>
								<?= h($role['Role']['updated_by']); ?>
								
							</td>
						</tr>
						<tr>
							<td><strong><?= __('updated'); ?></strong></td>
							<td>
								<?= h($role['Role']['updated']); ?>
								
							</td>
						</tr>
						<tr>
							<td><strong><?= __('created_by'); ?></strong></td>
							<td>
								<?= h($role['Role']['created_by']); ?>
								
							</td>
						</tr>
						<tr>
							<td><strong><?= __('created'); ?></strong></td>
							<td>
								<?= h($role['Role']['created']); ?>
								
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
					
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __('permissions'); ?></h3>
			</div>

			<!-- vilh (2019/03/26) 
					- add message info user with no data case
			-->
			<?php if (empty($role_permissions)): ?>
				<div class="alert alert-danger">
					<strong> <?= __('no_data'); ?> </strong> 
				</div>
			<?php endif; ?>
			
			<?php if (!empty($role_permissions)): ?>
				<div class="box-body table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
                                <th class="text-center"><?= __d('administration','model') ?></th>
                                <?php foreach($distinct_actions as $action){ ?>
                                    <th class="text-center"><?= __($action) ?></th>
                                <?php } ?>
							</tr>
						</thead>
						<tbody>
						    <?php foreach ($role_permissions as $item):  ?>
                                <tr>
                                    <td class="text-center"><?= $item['p_model'] ?></td>
                                    <?php foreach($distinct_actions as $action){ ?>
                                        <th class="text-center">
											<?php 
												if (isset($item[$action])) {
													echo $this->element('view_check_ico', array('_check'=> $item[$action] == 1 ? true : false)); ?>	
										<?php } ?>
									<?php } ?>
                                </tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

