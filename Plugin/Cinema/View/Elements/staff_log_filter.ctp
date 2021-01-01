<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create('Cinema.filter', array(
				'url' => array('controller' => 'staff_logs', 'action' => 'index', 'admin' => true, 'prefix' => 'admin'),
                'class' => 'form_filter',
                'type' => 'get',
			));
		?>
		<div class="action-buttons-wrapper border-bottom">
			<div class="row">
                <div class="col-md-3 col-sm-4">
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
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-4">
                    <div class="form-group">
                        <?php
                             echo $this->element('datetime_picker', array(
                                'id' => 'clock_in_from',
                                'label' => __('clock_in_from'),
                                'field_name' => 'clock_in_from',
                                'value' => isset($data_search["clock_in_from"]) ? $data_search["clock_in_from"] : '',
                            ));
                        ?>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4">
                    <div class="form-group">
                        <?php
                             echo $this->element('datetime_picker', array(
                                'id' => 'clock_in_to',
                                'label' => __('clock_in_to'),
                                'field_name' => 'clock_in_to',
                                'value' => isset($data_search["clock_in_to"]) ? $data_search["clock_in_to"] : '',
                            ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-4">
                    <div class="form-group">
                        <?php
                             echo $this->element('datetime_picker', array(
                                'id' => 'clock_out_from',
                                'label' => __('clock_out_from'),
                                'field_name' => 'clock_out_from',
                                'value' => isset($data_search["clock_out_from"]) ? $data_search["clock_out_from"] : '',
                            ));
                        ?>
                    </div>
                </div>
                <div class="col-md-3 col-sm-4">
                    <div class="form-group">
                        <?php
                             echo $this->element('datetime_picker', array(
                                'id' => 'clock_out_to',
                                'label' => __('clock_out_to'),
                                'field_name' => 'clock_out_to',
                                'value' => isset($data_search["clock_out_to"]) ? $data_search["clock_out_to"] : '',
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
                                'plugin' => 'cinema', 'controller' => 'staff_logs', 'action' => 'index',
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
				</div>
			</div>
		</div>

        <?php echo $this->Form->end(); ?>
	</div>
</div>
