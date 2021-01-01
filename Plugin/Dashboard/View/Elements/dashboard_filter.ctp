<!-- 
	thongnd
	- add radio button all
	- add new variables for check button when submit search
-->
<div class="row filter-panel">
	<div class="col-md-12">
		<?php
			echo $this->Form->create('Dashboard.filter', array(
				'url' => array('controller' => 'dashboard', 'action' => 'index', 'admin' => true, 'prefix' => 'admin'),
                'class' => 'form_filter',
                'type' => 'get',
			));
		?>
		<div class="action-buttons-wrapper border-bottom">
            <div class="row">
                <div class="col-md-3 col-xs-6">
                    <?php
                    echo $this->element('date_picker', array(
                        'id' => 'date_from',
                        'label' => __('date_from'),
                        'field_name' => 'date_from',
                        'value' => isset($data_search["date_from"]) ? $data_search["date_from"] : date('Y-m-d'),
                    ));
                    ?>
                </div>
                <div class="col-md-3 col-xs-6">
                    <?php
                    echo $this->element('date_picker', array(
                        'id' => 'date_to',
                        'label' => __('date_to'),
                        'field_name' => 'date_to',
                        'value' => isset($data_search["date_to"]) ? $data_search["date_to"] : date('Y-m-d'),
                    ));
                    ?>
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
                            echo $this->Html->link(__('today'), array(
                                //'plugin' => 'dashboard', 'controller' => 'dashboard', 'action' => 'index?date_from='.date('Y-m-d')."&date_to=".date('Y-m-d'),
                                'plugin' => 'dashboard', 'controller' => 'dashboard', 'action' => 'index',
                                'admin' => true, 'prefix' => 'admin'
                            ), array(
                                'class' => 'btn btn-danger btn-sm filter-button',
                            ));
                        ?>
                        <!-- <div class="action-buttons-wrapper border-top">
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
                        </div> -->
                        <div class="action-buttons-wrapper border-top">
                        </div>
                        <!-- <?php if(isset($permissions[$model]['edit']) && ($permissions[$model]['edit'] == true)){ ?>
                            <?= $this->Html->link(__('import_excel'), array('action' => 'import'), array('class' => 'btn btn-primary btn-sm filter-button', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('import_excel')), ""); ?>
                        <?php } ?> -->
                    </div>
				</div> <!-- col-md-4 -->
			</div> <!-- row -->
		</div>

        <?php echo $this->Form->end(); ?>
	</div>
</div>
