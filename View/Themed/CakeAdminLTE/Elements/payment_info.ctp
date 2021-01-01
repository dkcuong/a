<?php if ((isset($currencies) && !empty($currencies))): ?>
<div class="row">
    <div class="col-xs-12">
		<div class="well well-sm">
            <h4 style="color: #999;"> <?= __d('dictionary', 'currency') ?> </h4>
	    	<?php foreach ($currencies as $currency_id => $currency): ?>
	    		<?= $this->Html->link( 
	    			$currency ,
	    			array(
	    				'plugin' => 'dictionary',
	    				'controller' => 'currencies',
	    				'action' => 'view',
	    				$currency_id
	    			),
	    			array(
	    				'target'=>'_BLANK',
	    				'style' =>'font-size:16px; padding-right:10px;',
	    			)
	    		); ?>
	    	<?php endforeach ?>
		</div>
	</div>
</div>
<?php endif ?>

<?php if ((isset($payments) && !empty($payments))): ?>
<div class="row">
    <div class="col-xs-12">
		<div class="well well-sm">
	    	<h4 style="color: #999;"> <?= __d('dictionary', 'payment_method') ?> </h4>
	    	<?php foreach ($payments as $payment_id => $payment): ?>
	    		<?= $this->Html->link( 
	    			$payment ,
	    			array(
	    				'plugin' => 'payment_method',
	    				'controller' => 'payment_methods',
	    				'action' => 'view',
	    				$payment_id
	    			),
	    			array(
	    				'target'=>'_BLANK',
	    				'style' =>'font-size:16px; padding-right:10px;',
	    			)
	    		); ?>
	    	<?php endforeach ?>
		</div>
	</div>
</div>
<?php endif ?>

<?php if ((isset($services) && !empty($services))): ?>
<div class="row">
    <div class="col-xs-12">
		<div class="well well-sm">
	    	<h4 style="color: #999;"> <?= __('service') ?> </h4>
	    	<?php foreach ($services as $service_id => $service): ?>
	    		<?= $this->Html->link( 
	    			$service ,
	    			array(
	    				'plugin' => 'service',
	    				'controller' => 'services',
	    				'action' => 'view',
	    				$service_id
	    			),
	    			array(
	    				'target'=>'_BLANK',
	    				'style' =>'font-size:16px; padding-right:10px;',
	    			)
	    		); ?>
	    	<?php endforeach ?>
		</div>
	</div>
</div>
<?php endif ?>

<?php if ((isset($positems) && !empty($positems))): ?>
<div class="row">
    <div class="col-xs-12">
		<div class="well well-sm">
	    	<h4 style="color: #999;"> <?= __d('dictionary', 'service') ?></h4>
	    	<?php foreach ($positems as $positem_id => $positem): ?>
	    		<?= $positem; ?>
	    	<?php endforeach ?>
		</div>
	</div>
</div>
<?php endif ?>