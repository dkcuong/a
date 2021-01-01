<!-- 
	thongnd
	- add radio button all
	- add new variables for check button when submit search
-->
<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create('Member.filter', array(
				'url' => array('controller' => 'member_coupons', 'action' => 'index', 'admin' => true, 'prefix' => 'admin'),
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
                <div class="col-md-2 col-sm-4">
                    <?php 
                        echo $this->Form->input('coupon_type', array(
                            'class' => 'form-control',
                            'empty' => __("please_select"),
                            'options' => $types,
                            'selected' =>  isset($data_search['coupon_type']) ? $data_search['coupon_type'] : '',
                            'label' => __d('coupon', 'type'),
                        ));
                    ?>
                </div>
                <div class="col-md-2 col-sm-4">
                    <?php 
                        echo $this->Form->input('member', array(
                            'class' => 'form-control',
                            'empty' => __("please_select"),
                            'options' => $members,
                            'selected' =>  isset($data_search['member']) ? $data_search['member'] : '',
                            'label' => __d('member', 'item_title'),
                        ));
                    ?>
                </div>
                <div class="col-md-2 col-sm-4">
                    <?php 
                        echo $this->Form->input('status', array(
                            'class' => 'form-control',
                            'empty' => __("please_select"),
                            'options' => $statuses,
                            'selected' =>  isset($data_search['status']) ? $data_search['status'] : '',
                            'label' => __('status'),
                        ));
                    ?>
                </div>
                <div class="col-md-2 col-sm-4">
                    <?php echo $this->element('date_picker', array(
                            'field_name' => 'expired_date', 
                            'label' => __d('coupon', 'expiry_date'),
                            'value' => isset($data_search["expired_date"]) ? $data_search["expired_date"] : '',
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
                                'plugin' => 'member', 'controller' => 'member_coupons', 'action' => 'index',
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
                    </div>
				</div> <!-- col-md-4 -->
			</div> <!-- row -->
		</div>

        <?php echo $this->Form->end(); ?>
	</div>
</div>
