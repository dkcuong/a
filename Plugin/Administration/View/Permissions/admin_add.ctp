
<div class="row">
    <div class="col-xs-12 col-xs-offset-0">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?php echo __d('administration','add_permission'); ?></h3>
			</div>

			<div class="box-body">
		
				<?php echo $this->Form->create('Permission', array('role' => 'form')); ?>
					<fieldset>
                        <div class="form-group">
                            <div>
                                <label><?= '<font color="red">*</font> ' . __('slug') ?></label>
                                <div class="input-group">
                                    <span class="input-group-addon" >perm-admin-</span>
                                    <?php 
                                        echo $this->Form->input('slug', array(
                                            'class' => 'form-control',
                                            'id' => 'txtSlug',
                                            'label' => false,
                                            'type' => 'text',
                                            'required' => 'required',
                                        ));
                                    ?>
                                </div>
                            </div>
                            <!-- /.input group -->
                            <label id="slug_error" style="color:red"> </label>
                        </div>
                        
						<div class="form-group">
                            <?php 
                                echo $this->Form->input('name', array(
                                    'class' => 'form-control', 
                                    'id' => 'txtName',
                                    'required' => 'required',
                                    'label' => '<font color="red">*</font>'  . __d('administration','name')
                                )); 
                            ?>
                            <label id="name_error" style="color:red"> </label>
						</div>
						
						<div class="form-group">
                            <?php
                                echo $this->Form->input('action_id', array(
                                    'class' => 'form-control selectpicker',
                                    'data-live-search' => true,
                                    'empty' => __("please_select"),
                                    'required' => 'required',
                                    'label' => '<font color="red">*</font>'  . __d('administration','action'),
                                ));
                            ?>
                        </div>
                        
						<div class="form-group">
							<?php echo $this->Form->input('p_plugin', array(
								'class' => 'form-control',
                                'required' => 'required',
								'label'=>'<font color="red">*</font>'  . __d('administration','p_plugin'))); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('p_controller', array(
								'class' => 'form-control',
                                'required' => 'required',
								'label'=>'<font color="red">*</font>'  . __d('administration','p_controller'))); ?>
						</div>

						<div class="form-group">
							<?php echo $this->Form->input('p_model', array(
								'class' => 'form-control',
                                'required' => 'required',
								'label'=>'<font color="red">*</font>'  . __d('administration','p_model'))); ?>
                        </div>
                        
						<div class="pull-right">
							<?php echo $this->Form->submit(__('submit'), array(
								'id' => 'btnAdded',
								'class' => 'btn btn-large btn-primary')); ?>
						</div>

					</fieldset>

				<?php echo $this->Form->end(); ?>

			</div>
			
		</div>

	</div>
</div>
<?php
    echo $this->Html->script('CakeAdminLTE/pages/admin_permission', array('inline' => false));
?>
<script type="text/javascript">
	$(document).ready(function(){
        ADMIN_PERMISSION.slugs = JSON.parse('<?= $slugs; ?>');
        ADMIN_PERMISSION.names = JSON.parse('<?= $names; ?>');

        ADMIN_PERMISSION.init_page();
	});
</script>
