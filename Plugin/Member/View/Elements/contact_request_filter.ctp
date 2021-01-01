<!-- 
	thongnd
	- add radio button all
	- add new variables for check button when submit search
-->
<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create('Member.filter', array(
				'url' => array('controller' => 'contact_requests', 'action' => 'index', 'admin' => true, 'prefix' => 'admin'),
                'class' => 'form_filter',
                'type' => 'get',
			));
		?>
		<div class="action-buttons-wrapper border-bottom">
			<div class="row">
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php
                            echo $this->Form->input('name', array(
                                'class' => 'form-control',
                                'label' => __('name'),
                                'value' => isset($data_search["name"]) ? $data_search["name"] : '',
                            ));
                        ?>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('email', array(
                            'class' => 'form-control',
                            'label' => __('email'),
                            'value' => isset($data_search["email"]) ? $data_search["email"] : '',
                        ));
                        ?>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php
                        echo $this->Form->input('phone', array(
                            'class' => 'form-control',
                            'label' => __('phone'),
                            'value' => isset($data_search["phone"]) ? $data_search["phone"] : '',
                        ));
                        ?>
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
                                'plugin' => 'cinema', 'controller' => 'contact_requests', 'action' => 'index',
                                'admin' => true, 'prefix' => 'admin'
                            ), array(
                                'class' => 'btn btn-danger btn-sm filter-button',
                            ));
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
				</div> <!-- col-md-4 -->
			</div> <!-- row -->
		</div>

        <?php echo $this->Form->end(); ?>
	</div>
</div>
