<!-- 
	thongnd
	- add radio button all
	- add new variables for check button when submit search
-->
<div class="row filter-panel main-panel-filter">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create('purchasingpage_filter', array(
				'url' => array('controller' => 'purchasingpage', 'action' => 'index', 'admin' => false),
                'class' => 'form_filter',
                'type' => 'get',
			));
		?>
		<div class="action-buttons-wrapper border-bottom">
			<div class="row">
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php
                            echo $this->Form->input('inv_number', array(
                                'class' => 'form-control',
                                'label' => __('inv_number'),
                                'value' => isset($data_search["inv_number"]) ? $data_search["inv_number"] : '',
                            ));
                        ?>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php echo $this->element('datetime_picker',array(
                            'format' => 'DD/MM/YYYY',
                            'field_name' => 'date', 
                            'label' => __('date'),
                            'id' => 'date', 
                            'value' => isset($data_search["date"]) ? $data_search["date"] : '',
                        )); ?>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php
                            echo $this->Form->input('item', array(
                                'class' => 'form-control selectpicker',
                                'title' => __('please_select'),
                                'empty' => __("please_select"),
                                'data-live-search' => true,
                                'multiple' => false,
                                'required' => false,
                                'id' => 'items',
                                'label' =>  __('item'),
                                'options' => $items,
                                'selected' => ( isset($data_search['item']) && !empty($data_search['item']) ) ? $data_search['item'] : 0
                            ));
                        ?>
                    </div>
                </div>
                <!--
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php
                            echo $this->Form->input('member', array(
                                'class' => 'form-control',
                                'label' => __('member_name'),
                                'value' => isset($data_search["member"]) ? $data_search["member"] : '',
                            ));
                        ?>
                    </div>
                </div>
                -->
			</div>
			<div class="row main-container">
				<div class="col-md-12">
                    <div class="pull-right vtl-buttons">
                        <?php
                            echo $this->Form->submit(__('submit'), array(
                                'class' => 'btn btn-primary btn-sm filter-button',
                            ));
                        ?>
                        <?php
                            echo $this->Html->link(__('reset'), array(
                                'controller' => 'purchasingpage', 'action' => 'index',
                                'admin' => false
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
