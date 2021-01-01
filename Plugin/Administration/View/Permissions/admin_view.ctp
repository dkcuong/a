
<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?php  echo __d('administration','permission'); ?></h3>
				<div class="box-tools pull-right">
	                <?php 
	                	if( isset($permissions['Permission']['edit']) && ($permissions['Permission']['edit'] == true) ){
	                		echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '.__d('administration','edit_permission'), array('action' => 'edit', $permission['Permission']['id']), array('class' => 'btn btn-primary', 'escape' => false));
	                	}
	                ?>
	            </div>
			</div>
			
			<div class="box-body">
                <table id="Permissions" class="table table-bordered table-striped">
					<tbody>
						<tr>
							<td><strong><?php echo __('id'); ?></strong></td>
							<td>
								<?php echo h($permission['Permission']['id']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __('slug'); ?></strong></td>
							<td>
								<?php echo h($permission['Permission']['slug']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __d('administration','name'); ?></strong></td>
							<td>
								<?php echo h($permission['Permission']['name']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __d('administration','p_model'); ?></strong></td>
							<td>
								<?php echo h($permission['Permission']['p_model']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __d('administration','action'); ?></strong></td>
							<td>
								<?php echo h($permission['Permission']['action']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __('updated'); ?></strong></td>
							<td>
								<?php echo h($permission['Permission']['updated']); ?>
								&nbsp;
							</td>
						</tr>
						<tr>
							<td><strong><?php echo __('created'); ?></strong></td>
							<td>
								<?php echo h($permission['Permission']['created']); ?>
								&nbsp;
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

