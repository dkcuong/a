<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create('Report.spending_by_shop_filter', array(
				'url' => array('plugin' => 'report', 'controller' => 'orders', 'action' => 'report_by_shop', 'admin' => true, 'prefix' => 'admin'),
                'class' => 'form_filter',
                'type' => 'get',
			));
		?>
		<div class="action-buttons-wrapper border-bottom">
			<div class="row">
                <div class="col-md-4 col-xs-4">
                    <div class="form-group">
                        <label><?= __d('company', 'shop') ?></label>
						<?= $this->element('multi_select', array(
							'field_name' => 'shop_ids',
							'live_search' => true,
							'multiple' => true,
                            'placeholder' => __('please_select'),
                            'options' => $shops,
							'selecteds' => isset($data_search['shop_ids']) ? $data_search['shop_ids'] : array()
						)); ?>
                    </div>
                </div>
			</div>
			<div class="row">
				<div class="col-md-12">
                    <div class="pull-right vtl-buttons">
                        <?php
                            echo $this->Form->submit(__('submit'), array(
                                'class' => 'btn btn-primary btn-sm filter-button',
                            ));
                        ?>
                        <?php
                            echo $this->Html->link(__('reset'), array(
                                'plugin' => 'report', 'controller' => 'orders', 'action' => 'report_by_shop',
                                'admin' => true, 'prefix' => 'admin'
                            ), array(
                                'class' => 'btn btn-danger btn-sm filter-button',
                            ));
                        ?>
                    </div>
				</div>
			</div>
		</div>

        <?php echo $this->Form->end(); ?>
	</div>
</div>
