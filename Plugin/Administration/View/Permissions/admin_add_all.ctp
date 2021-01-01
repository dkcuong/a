
<div class="row">
    <div class="col-xs-12 col-xs-offset-0">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?php echo __d('administration','add_full_permission'); ?></h3>
			</div>

			<div class="box-body">
				<?php echo $this->Form->create('Permission', array('role' => 'form')); ?>
					<fieldset>
						<div class="form-group">
							<?php echo $this->Form->input('name', array(
								'class' => 'form-control', 
                                'required' => 'required',
								'label' => '<font color="red">*</font>'  . __d('administration', 'name'))); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('plugin', array(
								'class' => 'form-control', 
                                'required' => 'required',
								'label'=>'<font color="red">*</font>' . __d('administration', 'p_plugin'))); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('controller', array(
								'class' => 'form-control', 
                                'required' => 'required',
								'label'=>'<font color="red">*</font>' . __d('administration', 'p_controller'))); ?>
						</div>
						<div class="form-group">
							<?php echo $this->Form->input('model', array(
								'class' => 'form-control', 
                                'required' => 'required',
								'label'=>'<font color="red">*</font>' . __d('administration', 'model'))); ?>
						</div>
						<div class="pull-right">
							<?php echo $this->Form->submit(__('submit'), array('class' => 'btn btn-large btn-primary')); ?>
						</div>
					</fieldset>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
    </div>
</div>