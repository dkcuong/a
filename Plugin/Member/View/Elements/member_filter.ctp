<!-- 
	thongnd
	- add radio button all
	- add new variables for check button when submit search
-->
<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create('Member.filter', array(
				'url' => array('controller' => 'members', 'action' => 'index', 'admin' => true, 'prefix' => 'admin'),
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
                <div class="col-md-2 col-xs-6">
						<?= $this->element('multi_select', array(
							'field_name' => 'dob_months',
							'live_search' => true,
							'multiple' => true,
                            'label' => __('month'),
                            'options' => $dobMonths,
							'placeholder' => __('please_select'),
							'selecteds' => isset($data_search['dob_months']) ? $data_search['dob_months'] : array()
						)); ?>
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
            </div>


            <div class="row">
                <div class="col-md-2 col-xs-6">
                    <?php
                    echo $this->element('multi_select', array(
                        'field_name' => 'phone_verified',
                        'multiple' => false,
                        'live_search' => false,
                        'label' => __d('member', 'phone_verified'),
                        'options' => $list_status,
                        'placeholder' => __('please_select'),
                        'selecteds' => isset($data_search['phone_verified']) ? $data_search['phone_verified'] : array()
                    )); ?>
                </div>
                <div class="col-md-2 col-xs-6">
                    <?php
                    echo $this->element('multi_select', array(
                        'field_name' => 'email_verified',
                        'multiple' => false,
                        'live_search' => false,
                        'label' => __d('member', 'email_verified'),
                        'options' => $list_status,
                        'placeholder' => __('please_select'),
                        'selecteds' => isset($data_search['email_verified']) ? $data_search['email_verified'] : array()
                    )); ?>
                </div>
                <div class="col-md-2 col-xs-6">
                    <?php
                    echo $this->element('multi_select', array(
                        'field_name' => 'renewal_status',
                        'multiple' => false,
                        'live_search' => false,
                        'label' => __d('member', 'renewal_status'),
                        'options' => $list_status,
                        'placeholder' => __('please_select'),
                        'selecteds' => isset($data_search['renewal_status']) ? $data_search['renewal_status'] : array()
                    )); ?>
                </div>
                <div class="col-md-2 col-xs-6">
                    <?php
                    echo $this->element('date_picker', array(
                        'id' => 'expired_date',
                        'label' => __('expired_date'),
                        'field_name' => 'expired_date',
                        'value' => isset($data_search["expired_date"]) ? $data_search["expired_date"] : '',
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
                                'plugin' => 'member', 'controller' => 'members', 'action' => 'index',
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
