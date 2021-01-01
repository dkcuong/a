<div class="row filter-panel">
	<div class="col-md-12">
		<?php
			echo $this->Form->create('Member.filter', array(
				'url' => array('controller' => 'member_notifications', 'action' => 'index', 'admin' => true),
                'class' => 'form_filter',
                'type' => 'get',
			));
		?>
		<div class="action-buttons-wrapper border-bottom">
			<div class="row">
                <div class="col-md-3 col-xs-6">
                    <div class="form-group">
                        <?= $this->Form->input('title', 
                            array('class' => 'form-control', 'label' => __('title'), 'placeholder'=> __('title'), 'value' => isset($data_search["title"]) ? $data_search["title"] : '') ); ?>
                    </div>
                </div>
				<div class="col-md-3 col-xs-6">
					<div class="form-group">
                        <?php
                            echo $this->Form->input('pushMethod', array(
                                'class' => 'form-control selectpicker',
                                'data-live-search' => true,
                                'empty' => __("please_select"),
                                'label' => __('push_method'),
                                'selected' => isset($data_search["pushMethod"]) && $data_search["pushMethod"] ? array($data_search["pushMethod"]) : array(),
                                'options' => $pushMethods,
                            ));
                        ?>
                    </div>
				</div>
                <div class="col-md-3 col-xs-6">
					<div class="form-group">
                        <?= $this->Form->input('name', 
                            array('class' => 'form-control', 'label' => __('name'), 'placeholder'=> __('name'), 'value' => isset($data_search["name"]) ? $data_search["name"] : '') ); ?>
                    </div>
				</div>	
				<div class="col-md-3 col-xs-6">
					<div class="form-group">
                        <?= $this->Form->input('phone', 
                            array('class' => 'form-control', 'label' => __('phone'), 'placeholder'=> __('phone'), 'value' => isset($data_search["phone"]) ? $data_search["phone"] : '') ); ?>
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
                            echo $this->Html->link(__('reset'), array('action' => 'index', 'admin' => true ), array( 'class' => 'btn btn-danger btn-sm filter-button' ));
                        ?>
						<div class="action-buttons-wrapper border-top">
                            <?php                 
                                echo $this->Form->input(__('export_excel'), array(
                                    'div' => false,
                                    'label' => false,
                                    'type' => 'submit',
                                    'name' => 'button[exportExcel]',
                                    'class' => 'btn btn-warning btn-sm filter-button',
                                ));            
                            ?>
                            <span class="spinner" style="display: none;"><i class="fa fa-spinner fa-spin"></i> Sending...</span>
                        </div>
                    </div>
                </div>
			</div>
		</div>

        <?= $this->Form->end(); ?>
	</div>
</div>
