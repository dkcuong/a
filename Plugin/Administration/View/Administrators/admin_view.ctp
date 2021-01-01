
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?php  echo __d('administration','administrator'); ?></h3>

				<div class="box-tools pull-right">
	                <?php 
	                	if( isset($permissions['Administration']['edit']) && ($permissions['Administration']['edit'] == true) ){
	                		echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '.__d('administration','edit_administrator'), array('action' => 'edit', $administrator['Administrator']['id']), array('class' => 'btn btn-primary', 'escape' => false));
                		}
                	?>
	            </div>
			</div>
			
			<div class="box-body">
                <table id="Administrators" class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td><strong><?= __d('administration','id'); ?></strong></td>
							<td>
								<?= h($administrator['Administrator']['id']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?= __d('administration','name'); ?></strong></td>
							<td>
								<?= h($administrator['Administrator']['name']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?= __d('administration','email'); ?></strong></td>
							<td>
								<?= h($administrator['Administrator']['email']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?= __d('administration','phone'); ?></strong></td>
							<td>
								<?= h($administrator['Administrator']['phone']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?= __d('administration','last_logged_in'); ?></strong></td>
							<td>
								<?= h($administrator['Administrator']['last_logged_in']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?= __('updated_by'); ?></strong></td>
							<td>
								<?= h($administrator['Administrator']['updated']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?= __('updated'); ?></strong></td>
							<td>
								<?= h($administrator['UpdatedBy']['email']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?= __('created'); ?></strong></td>
							<td>
								<?= h($administrator['Administrator']['created']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?= __('created_by'); ?></strong></td>
							<td>
								<?= h($administrator['CreatedBy']['email']); ?>
								&nbsp;
							</td>
						</tr>
					</tbody>
				</table><!-- /.table table-striped table-bordered -->
			</div>
		</div><!-- /.view -->

		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('administration','role'); ?></h3>
			</div>

			<?php if( isset($administrator['Role']) && !empty($administrator['Role']) ){ ?>
				<div class="box-body">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th class="text-center"><?= __d('administration','id'); ?></th>
								<th class="text-center"><?= __d('administration','name'); ?></th>
							</tr>
						</thead>
					
						<tbody>
							<?php foreach ($administrator['Role'] as $key => $role) { ?>
								<tr>
									<td class="text-center">
										<?php
											echo $this->Html->link($role['id'], array(
												'plugin' => 'administration', 'controller' => 'roles',
												'action' => 'view', 'admin' => true, 'prefix' => 'admin', $role['id']
											));
										?>
									</td>
									<td class="text-center"><?= $role['name']; ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			<?php } ?>
		</div><!-- /.related -->
	</div><!-- /#page-content .span9 -->
</div><!-- /#page-container .row-fluid -->
