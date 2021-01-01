<div class="contactRequests form">
<?php echo $this->Form->create('ContactRequest'); ?>
	<fieldset>
		<legend><?php echo __('Admin Add Contact Request'); ?></legend>
	<?php
		echo $this->Form->input('title');
		echo $this->Form->input('name');
		echo $this->Form->input('email');
		echo $this->Form->input('country_code');
		echo $this->Form->input('phone');
		echo $this->Form->input('message');
		echo $this->Form->input('updated_by');
		echo $this->Form->input('created_by');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Contact Requests'), array('action' => 'index')); ?></li>
	</ul>
</div>
