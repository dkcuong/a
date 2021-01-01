<?php 
	echo $this->Html->css('datatables/dataTables.bootstrap');
?>
<div class="row">
    <div class="col-xs-12">
	<?php 
		echo $this->element('Administration.permission_filter', array()); 
	?>
    <div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?= __d('administration','permissions'); ?></h3>
			<div class="box-tools pull-right">
				<?php
					echo $this->Form->create('Administration.filter', array(
						'url' => array(
							'plugin' => 'administration', 
							'controller' => 'permissions', 
							'action' => 'index', 
							'admin' => true, 
							'prefix' => 'admin'),
						'class' => 'form_filter',
						'type' => 'get',
					));
					
					echo $this->Form->button('<i class="glyphicon glyphicon-link"></i>' . __('show_duplicate_permission'), array(
						'class' => 'btn btn-primary filter-button',
						'type' => 'submit',
						'name' => 'show_duplicate_permission',
					));
		
                	if( isset($permissions['Permission']['add']) && ($permissions['Permission']['add'] == true) ){ 
                        //echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> ' . __('add'), 
                        //    array('action' => 'add'), array('class' => 'btn btn-primary filter-button', 'escape' => false));
                        echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> ' . __d('administration','add_full_permission'), 
                            array('action' => 'add_all'), array('class' => 'btn btn-primary filter-button', 'escape' => false)); 
					}
					
					echo $this->Form->end();
                ?>
            </div>

			</div>	
				<div class="box-body">
	                <table id="Permissions" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th class="text-center"><?= __d('administration','p_plugin') ?></th>
								<th class="text-center"><?= __d('administration','p_controller'); ?></th>
								<th class="text-center"><?= __d('administration','p_model'); ?></th>
								<th class="text-center"><?= __d('administration','name'); ?></th>
								<th class="text-center"><?= __('slug'); ?></th>
								<th class="text-center"><?= __d('administration','action'); ?></th>
								
								<?php if (isset($this->request->query['show_duplicate_permission'])) : ?>
									<th class="text-center"><?= __('operation'); ?></th>
                                <?php endif; ?>
                                
                                <?php if( isset($permissions['Permission']['delete']) && ($permissions['Permission']['delete'] == true) ){ ?>
                                    <th class="text-center"><?= __('operation'); ?></th>
                                <?php } ?>
							</tr>
						</thead>
					<tbody>

					<?php 
					$flag_plugin = "";
                    $flag_controller = ""; 
                    foreach ($permissions_records as $item): 
                        $permission = $item['Permission']; ?>
						<tr>
                            <?php 
                                $first_time_items = array();
                            ?>
                            <?php 
                                if($permission['p_plugin'] != $flag_plugin || $permission['p_controller'] != $flag_controller) {
                                    $flag_plugin = $permission['p_plugin'];
                                    $flag_controller = $permission['p_controller']; 
                                    $first_time_items = array_filter($permissions_records, function($item) use ($flag_plugin, $flag_controller){
                                        return $item['Permission']['p_plugin'] == $flag_plugin && $item['Permission']['p_controller'] == $flag_controller;
                                    });
                                    $num_row_span = count($first_time_items);
							?>
                                    <td class="text-center" style="vertical-align: middle" rowspan="<?= $num_row_span; ?>">
                                        <?= h($permission['p_plugin']); ?></td>
                                    <td class="text-center" style="vertical-align: middle" rowspan="<?= $num_row_span; ?>">
                                        <?= h($permission['p_controller']); ?></td>
							<?php } ?>
                            <td class="text-center"><?= h($permission['p_model']); ?></td>
							<td class="text-center"><?= h($permission['name']); ?></td>
							<td class="text-center"><?= h($permission['slug']); ?></td>
							<td class="text-center"><?= h( __($permission['action']) ); ?></td>
							
							<?php 
								if (isset($this->request->query['show_duplicate_permission'])) : ?>			
									<td class="text-center"> 
                                        <?php
											// delete button
											if( isset($permissions['Permission']['delete']) && ($permissions['Permission']['delete'] == true) ){
												echo $this->Form->postLink(__('<i class="glyphicon glyphicon-trash"></i>'), 
													array('action' => 'delete', $permission['id']), 
													array(
														'class' => 'btn btn-danger btn-xs', 
														'escape' => false, 
														'data-toggle'=>'tooltip', 
														'title' => __('delete')), 
														__('are_you_sure_to_delete', $permission['id']));
												echo "";
											}
										?>
									</td>
								<?php endif; 
							?>
                            <?php 
                            if( isset($permissions['Permission']['delete']) && ($permissions['Permission']['delete'] == true) ){
                                if(count($first_time_items) > 0) {	
                                    $num_row_span = count($first_time_items);
							?>
                                    <td class="text-center" style="vertical-align: middle" rowspan="<?= $num_row_span; ?>">
                                        <?= $this->Form->create('Administration.deletedata', 	// plugin.nameform
                                            array(
                                                'url' => array (
                                                    'plugin' => 'administration',		
                                                    'controller' => 'permissions', 		
                                                    'action' => 'deleteall',
                                                    'admin' => true,
                                                ),
                                                'onsubmit' => "return confirm('是否確認刪除這個p_model?')",
                                            )); 
                                        ?>
                                        <div class="form-group">
                                            <?php 
                                                foreach ($first_time_items as $per)
                                                {
                                                    echo $this->Form->input('id', array(
                                                        'type' => 'hidden',
                                                        'class' => 'form-control',
                                                        'label'=>'<font color="red">*</font>' . __('id'),
                                                        'name' => 'ids[]',
                                                        'value' => $per['Permission']['id'],
                                                        'required' => 'required',
                                                    ));
                                                }
                                                echo $this->Form->button('<i class="glyphicon glyphicon-trash"></i>',
                                                    [
                                                        'escape' => false,
                                                        'type' => 'submit', 
                                                        'class' => 'btn btn-large btn-danger',
                                                    ]);
                                                
                                            ?>
                                        </div>
                                        <?= $this->Form->end(); ?>
                                    </td>
							<?php } ?>
							<?php } ?>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<div class="row">
					<div class="col-xs-6">
						<div class="dataTables_info">
							<?= $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')));?>
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
