<?= $this->Html->css('datatables/dataTables.bootstrap', array('inline' => false)); ?>

<?= $this->element('Movie.movie_filter', array(
	'data_search' => $data_search
)); ?>

<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<div class="box-tools pull-right">
                <?php if(isset($permissions[$model]['add']) && ($permissions[$model]['add'] == true)){ ?>
                    <?= $this->Html->link( '<i class="glyphicon glyphicon-plus"></i> ' . __d('movie', 'add_item'), array('action' => 'add'), array('class' => 'btn btn-primary', 'escape' => false)); ?>
                <?php } ?>
				</div>
			</div>	

			<div class="box-body table-responsive">
				<table id="Movies" class="table table-bordered table-striped">
					<thead>
						<tr>
							<th class="text-center"><?= $this->Paginator->sort('id', __('id')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('code', __('code')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('name', __('name')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('slug', __d('movie', 'slug')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('genre', __d('movie', 'genre')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('subtitle', __d('movie', 'subtitle')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('director', __d('movie', 'director')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('writer', __d('movie', 'writer')); ?></th>

							<th class="text-center"><?= $this->Paginator->sort('language', __d('movie', 'language')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('duration', __d('movie', 'duration')); ?></th>
                            <th class="text-center"><?= $this->Paginator->sort('lang_info', __d('movie', 'lang_info')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('rating', __d('movie', 'rating')); ?></th>
							<th class="text-center"><?= $this->Paginator->sort('is_feature',__('is_feature')); ?></th>  

							<th class="text-center"><?= $this->Paginator->sort('movie_type', __d('movie', 'movie_type')); ?></th>

    						<th class="text-center"><?= $this->Paginator->sort('enabled',__('enabled')); ?></th>  
                            <th class="text-center"><?= $this->Paginator->sort('updated',__('updated')); ?></th>
							<th class="text-center"><?= __('operation'); ?></th>
						</tr>
					</thead>

					<tbody>
                        <?php foreach ($dbdatas as $dbdata): ?>
							<tr>
								<td class="text-center"><?= h($dbdata[$model]['id']); ?>&nbsp;</td>

								<td class="text-center">
									<?= h($dbdata[$model]['code']); ?>
								</td>			        

								<td class="text-center">
									<?= h($dbdata['MovieLanguage']['name']); ?>
								</td>

								<td class="text-center">
									<?= h($dbdata[$model]['slug']); ?>
								</td>			        

								<td class="text-center">
									<?= h($dbdata['MovieLanguage']['genre']); ?>
								</td>			        

								<td class="text-center">
									<?= h($dbdata['MovieLanguage']['subtitle']); ?>
								</td>			        

								<td class="text-center">
									<?= h($dbdata['MovieLanguage']['director']); ?>
								</td>			        
								<td class="text-center">
									<?= h($dbdata[$model]['writer']); ?>
								</td>			         

								<td class="text-center">
									<?= h($dbdata['MovieLanguage']['lang_movie']); ?>
								</td>		
								<td class="text-center">
									<?= h($dbdata[$model]['duration']); ?>
								</td>
                                <td class="text-center">
                                    <?= h($dbdata['MovieLanguage']['lang_info']); ?>
                                </td>
                                <td class="text-center">
									<?= h($dbdata[$model]['rating']); ?>
								</td>	        

								<td class="text-center">
									<?= $this->element('view_check_ico', array('_check' => $dbdata[$model]['is_feature'])); ?>
								</td>

								<td class="text-center">
                                    <?php if( !empty($dbdata['MovieType']) ){ ?>
                                        <?= implode(', ', Hash::extract($dbdata['MovieType'],'{n}.name')) ?>
                                    <?php } ?>
								</td>
	                        
								<td class="text-center">
									<?= $this->element('view_check_ico', array('_check' => $dbdata[$model]['enabled'])); ?>
								</td>
								<td class="text-center"><?= h($dbdata[$model]['updated']); ?>&nbsp;</td>

								<td class="text-center">
									<?= $this->Html->link('<i class="glyphicon glyphicon-eye-open"></i>', array('action' => 'view', $dbdata[$model]['id']), array('class' => 'btn btn-primary btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('view'))); ?>
                                    <?php if(isset($permissions[$model]['edit']) && ($permissions[$model]['edit'] == true)){ ?>
                                        <?= $this->Html->link('<i class="glyphicon glyphicon-pencil"></i>', array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-warning btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('edit'))); ?>
                                    <?php } ?>
                                    <?php if(isset($permissions[$model]['delete']) && ($permissions[$model]['delete'] == true)){ ?>
                                        <?= $this->Form->postLink('<i class="glyphicon glyphicon-trash"></i>', array('action' => 'delete', $dbdata[$model]['id']), array('class' => 'btn btn-danger btn-xs', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('delete')), __('are_you_sure_to_delete', $dbdata[$model]['id'])); ?>
                                    <?php } ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?= $this->element('Paginator'); ?>
	</div>
</div>

<?php
	echo $this->Html->script('plugins/datatables/jquery.dataTables', array('inline' => false));
	echo $this->Html->script('plugins/datatables/dataTables.bootstrap', array('inline' => false));
?>
<script type="text/javascript">
	$(document).ready(function(){

	});
</script>