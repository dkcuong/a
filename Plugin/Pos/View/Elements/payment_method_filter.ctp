<!-- 
	thongnd
	- add radio button all
	- add new variables for check button when submit search
-->
<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create('Pos.payment_method_filter', array(
				'url' => array('controller' => 'payment_methods', 'action' => 'index', 'admin' => true, 'prefix' => 'admin'),
                'class' => 'form_filter',
                'type' => 'get',
			));
		?>
		<div class="action-buttons-wrapper border-bottom">
			<div class="row">
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php
                            echo $this->Form->input('code', array(
                                'class' => 'form-control',
                                'label' => __('code'),
                                'value' => isset($data_search["code"]) ? $data_search["code"] : '',
                            ));
                        ?>
                    </div>
                </div>
				<div class="col-md-3 col-sm-6">
                    <div><label><?php echo __('status'); ?></label></div>
					<div class="btn-group btn-group-sm" data-toggle="buttons" >
						<label class="btn btn-default">
							<input type="radio" name="enabled" value="" autocomplete="off" 
								<?php echo !isset($data_search['enabled']) || $data_search['enabled'] === "" ? 'checked="checked"' : ''; ?>>
							<?php echo __('all'); ?>
						</label>
						<label class="btn btn-default">
							<input type="radio" name="enabled" value="1" autocomplete="off" 
								<?php echo isset($data_search['enabled']) && $data_search['enabled']  === "1" ? 'checked="checked"' : '';?> >
							<?php echo __('enabled'); ?>
						</label>
						<label class="btn btn-default">
							<input type="radio" name="enabled" value="0" autocomplete="off" 
								<?php echo isset($data_search['enabled']) && $data_search['enabled'] === "0" ? 'checked="checked"' : ''; ?> >
							<?php echo __('disabled'); ?>
						</label>
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
                                'plugin' => 'pos', 'controller' => 'payment_methods', 'action' => 'index',
                                'admin' => true, 'prefix' => 'admin'
                            ), array(
                                'class' => 'btn btn-danger btn-sm filter-button',
                            ));
                        ?>

                    </div>
				</div> <!-- col-md-4 -->
			</div> <!-- row -->
		</div>

        <?php echo $this->Form->end(); ?>
	</div>
</div>
