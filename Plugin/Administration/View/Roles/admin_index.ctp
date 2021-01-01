<?php 
	echo $this->Html->css('datatables/dataTables.bootstrap');
?>
<div class="row">
    <div class="col-xs-12">
	<?php 
		echo $this->element('Administration.role_filter', array(

		)); 
	?>

    <div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo __d('administration','roles'); ?></h3>
			<div class="box-tools pull-right">
                <?php 
                	if( isset($permissions['Role']['add']) && ($permissions['Role']['add'] == true) ){
                		echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__d('administration','add_role'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); 
                	}
                ?>
            </div>
		</div>
			<div class="box-body table-responsive">
                <table id="Roles" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?php echo $this->Paginator->sort('id',__('id')); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('slug',__('slug')); ?></th>
							<th class="text-center"><?php echo $this->Paginator->sort('name',__d('administration','name')); ?></th>
							<th class="text-center"><?php echo __('operation'); ?></th>
						</tr>
					</thead>

					<tbody>
						<?php foreach ($roles as $role): ?>
							<tr>
								<td class="text-center"><?php echo h($role['Role']['id']); ?></td>
								<td class="text-center"><?php echo h($role['Role']['slug']); ?></td>
								<td class="text-center"><?php echo h($role['Role']['name']); ?></td>
								<td class="text-center">
									<?php 
										if( isset($permissions['Role']['view']) && ($permissions['Role']['view'] == true) ){
											echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $role['Role']['id']), array('class' => 'btn btn-primary btn-xs filter-button', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view')));
										}
										
										if( isset($permissions['Role']['edit']) && ($permissions['Role']['edit'] == true) ){
											echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $role['Role']['id']), array('class' => 'btn btn-warning btn-xs filter-button', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit')));
										}

										if( isset($permissions['Role']['delete']) && ($permissions['Role']['delete'] == true) ){
											echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), array('action' => 'delete', $role['Role']['id']), array('class' => 'btn btn-danger btn-xs filter-button', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('delete')), __('are_you_sure_to_delete', $role['Role']['name']));
										}
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<div class="row">
					<div class="col-xs-6">
						<div class="dataTables_info">
							<?php echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')));?>
						</div>
					</div>

					<div class="col-xs-6">
						<?php
							$params = $this->Paginator->params();
							if ($params['pageCount'] > 1) {
						?>
							<div class="dataTables_paginate paging_bootstrap">
								<ul class="pagination">
									<?php
										echo $this->Paginator->prev('&larr; Previous', array('class' => 'prev','tag' => 'li','escape' => false), '<a onclick="return false;">&larr; Previous</a>', array('class' => 'prev disabled','tag' => 'li','escape' => false));
										echo $this->Paginator->numbers(array('separator' => '','tag' => 'li','currentClass' => 'active','currentTag' => 'a', 'modulus' => 5));
										echo $this->Paginator->next('Next &rarr;', array('class' => 'next','tag' => 'li','escape' => false), '<a onclick="return false;">Next &rarr;</a>', array('class' => 'next disabled','tag' => 'li','escape' => false));
									?>
								</ul>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	
	</div>

</div>

<?php
	echo $this->Html->script('plugins/datatables/jquery.dataTables');
	echo $this->Html->script('plugins/datatables/dataTables.bootstrap');
?>
<script type="text/javascript">
	$(document).ready(function(){
    });
</script>