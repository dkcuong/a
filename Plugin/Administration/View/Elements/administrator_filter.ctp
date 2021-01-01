<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create('Administration.filter', array(
				'url' => array(
                    'plugin' => 'administration', 
                    'controller' => 'administrators', 
                    'action' => 'index', 
                    'admin' => true, 
                    'prefix' => 'admin'),
                'class' => 'form_filter',
                'type' => 'get',
			));
        ?>
 
		<div class="action-buttons-wrapper border-bottom">
			<div class="row">
                <div class="col-md-4">
                    <?php 
                        echo $this->Form->input('txtName', array(
                            'class' => 'form-control',
                            'label' => __d('administration','name'),
                            'value' => isset($data_search['txtName']) ? $data_search['txtName'] : '', 
                        )); 
                    ?>
                </div>
                <div class="col-md-4">
                    <?php 
                        echo $this->Form->input('cmbRole', array(
                            'class' => 'form-control',
                            'empty' => __("please_select"),
                            'options' => $roles,
                            'selected' =>  isset($data_search['cmbRole']) ? $data_search['cmbRole'] : '',
                            'label' => __d('administration', 'role'),
                        ));
                    ?>
                </div>
            
                <div class="col-md-4">
                    <?php 
                        echo $this->Form->input('txtEmail', array(
                            'class' => 'form-control',
                            'label' => __d('administration','email'),
                            'value' => isset($data_search['txtEmail']) ? $data_search['txtEmail'] : '', 
                        )); 
                    ?>
                </div>
            </div>

            <div class="row" style="margin-top: 20px">
                <div class="col-md-12">
                    <div class="pull-right vtl-buttons">
                        <?php
                            echo $this->Form->submit(__('submit'), array(
                                'class' => 'btn btn-primary btn-sm',
                            ));
                        ?>
                        <?php
                            echo $this->Html->link(__('reset'), array(
                                'plugin' => 'administration', 
                                'controller' => 'administrators', 
                                'action' => 'index',
                                'admin' => true, 
                                'prefix' => 'admin'
                            ), array(
                                'class' => 'btn btn-danger btn-sm filter-button',
                            ));
                        ?>
                        <div class="action-buttons-wrapper border-top">
                            <?php                 
                                echo $this->Form->input(__('export'), array(
                                    'div' => false,
                                    'label' => false,
                                    'type' => 'submit',
                                    'name' => 'button[export]',
                                    'class' => 'btn btn-success btn-sm filter-button',
                                ));            
                            ?>
                            <span class="spinner" style="display: none;"><i class="fa fa-spinner fa-spin"></i> Sending...</span>
                        </div>
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
            <?php echo $this->Form->end(); ?>
		</div>

	</div>
</div>
