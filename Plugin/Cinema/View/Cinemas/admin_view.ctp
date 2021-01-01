<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('place', 'cinema_title'); ?></h3>

				<div class="box-tools pull-right">
                    <?php
                        if(isset($permissions[$model]['edit']) && $permissions[$model]['edit']){
                            echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '. __d('place', 'edit_cinema'), array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-primary', 'escape' => false));
                        } 
                    ?>
	            </div>
			</div>
			<div class="box-body">
                <?php if($dbdata[$model]){ ?>
                    <table id="Company" class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <td><strong><?= __('id'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['id']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('code'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['code']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('address'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['address']); ?>
                                </td>
                            </tr>      
                            <tr>
                                <td><strong><?= __d('place', 'location'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['location']); ?>
                                </td>
                            </tr>                                                            
                            <tr>
                                <td><strong><?= __('description'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['description']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?= __('phone'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['phone']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?= __('email'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['email']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong><?= __('enabled'); ?></strong></td>
                                <td>
                                    <?= $this->element('view_check_ico',array('_check' => $dbdata[$model]['enabled'])) ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
	});
</script>	
	