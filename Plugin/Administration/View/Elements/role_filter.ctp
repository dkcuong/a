<!-- 
	vilh (2019-03-19) 
	- add new variables for check button when submit search
-->

<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create('Administration.filter', array(
				'url' => array(
                    'plugin' => 'administration', 
                    'controller' => 'roles', 
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
                    echo $this->Form->input('txtSlug', array(
                        'class' => 'form-control',
                        'label' => __('slug'),
                        'value' => isset($txtSlug) ? $txtSlug : '', 
                    )); 
                    
                    ?>
                </div>
			</div>

			<div class="row">
                <div class="col-md-12">
                    <div class="pull-right vtl-buttons">
                        <?php
                            echo $this->Form->submit(__('submit'), array(
                                'class' => 'btn btn-primary btn-sm filter-button ',
                            ));
                        ?>
                        <?php
                            echo $this->Html->link(__('reset'), array(
                                'plugin' => 'administration', 'controller' => 'roles', 'action' => 'index',
                                'admin' => true, 'prefix' => 'admin'
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
                </div> <!-- .col-md-4 -->
                
                <?php echo $this->Form->end(); ?>
			</div> <!-- row -->
		</div>

	</div>
</div>
