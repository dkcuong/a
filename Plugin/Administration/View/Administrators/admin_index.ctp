<style>
	.error-message {
		color: red;
	}
</style>
<?php echo $this->Html->css('datatables/dataTables.bootstrap'); ?>
<div class="row">
    <div class="col-xs-12">
	<?php echo $this->element('Administration.administrator_filter', array()); ?>
    <div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo __d('administration','administrators'); ?></h3>
			<div class="box-tools pull-right">
                <?php 
                    if(isset($permissions['Administrator']['add']) && ($permissions['Administrator']['add'] == true)) {
                        echo $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__d('administration','add_administrator'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); 
                    }
                ?>
            </div>
		</div>	
			<div class="box-body">
                <div class="table-responsive">
                    <table id="Administrators" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="text-center"><?php echo $this->Paginator->sort('id',__d('administration','id')); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('name',__d('administration','name')); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('roles',__d('administration','role')); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('email',__d('administration','email')); ?></th>
                                <th class="text-center"><?php echo $this->Paginator->sort('last_logged_in',__d('administration','last_logged_in')); ?></th>
                                <th class="text-center"><?php echo __('enabled'); ?></th>
                                <th class="text-center"><?php echo __('operation'); ?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($administrators as $administrator): ?>
                                <tr>
                                    <td class="text-center"><?php echo h($administrator['Administrator']['id']); ?></td>
                                    <td class="text-center"><?php echo h($administrator['Administrator']['name']); ?></td>
                                    <td class="text-center">
                                        <?php if( !empty($administrator['Role']) ){ ?>
                                            <?= reset($administrator['Role'])['name'] ?>
                                        <?php } ?>
                                    </td>
                                    <td class="text-center"><?php echo h($administrator['Administrator']['email']); ?></td>
                                    <td class="text-center"><?php echo h($administrator['Administrator']['last_logged_in']); ?></td>
                                    <td class="text-center">
                                        <?php echo $this->element('view_check_ico',array('_check'=>$administrator['Administrator']['enabled'])) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-eye-open"></i>'), array('action' => 'view', $administrator['Administrator']['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view'))); ?>
                                        <?php 
                                            if(isset($permissions['Administrator']['edit']) && $permissions['Administrator']['edit']) { ?>
                                            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-pencil"></i>'), array('action' => 'edit', $administrator['Administrator']['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit'))); ?>
                                            <?php echo $this->Html->link(__('<i class="glyphicon glyphicon-info-sign"></i>'), array('action' => 'editPassword', $administrator['Administrator']['id']), array('class' => 'btn btn-success btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit_password'))); ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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