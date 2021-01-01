<!-- 
	thongnd
	- add radio button all
	- add new variables for check button when submit search
-->
<div class="row filter-panel main-panel-filter">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create('transactionpage_filter', array(
				'url' => array('controller' => 'transactionpage', 'action' => 'index', 'admin' => false),
                'class' => 'form_filter',
                'type' => 'get',
			));
		?>
		<div class="action-buttons-wrapper border-bottom">
			<div class="row">
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php
                            /*
                            echo $this->Form->input('movie_code', array(
                                'class' => 'form-control',
                                'label' => __d('movie', 'movie_code'),
                                'value' => isset($data_search["movie_code"]) ? $data_search["movie_code"] : '',
                            ));
                            */
                            echo $this->Form->input('movie', array(
                                'class' => 'form-control selectpicker',
                                'title' => __('please_select'),
                                'empty' => __("please_select"),
                                'data-live-search' => true,
                                'multiple' => false,
                                'required' => false,
                                'id' => 'movies',
                                'label' =>  __('movie'),
                                'options' => $movies,
                                'selected' => ( isset($data_search['movie']) && !empty($data_search['movie']) ) ? $data_search['movie'] : 0
                            ));
                        ?>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php echo $this->element('datetime_picker',array(
                            'format' => 'DD/MM/YYYY',
                            'field_name' => 'show_date', 
                            'label' => __('show_date'),
                            'id' => 'show_date', 
                            'value' => isset($data_search["show_date"]) ? $data_search["show_date"] : '',
                        )); ?>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php 
                            $filter_time = (isset($data_search["show_time"]) && !empty($data_search["show_time"])) ? date('Y-m-d') . ' ' . $data_search["show_time"] : '';

                            echo $this->element('datetime_picker',array(
                                'format' => 'HH:mm',
                                'field_name' => 'show_time', 
                                'label' => __('show_time'),
                                'id' => 'show_time',
                                'value' => $filter_time,
                            )); 
                        ?>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php
                            echo $this->Form->input('seat_number', array(
                                'class' => 'form-control',
                                'label' => __d('movie', 'seat_number'),
                                'value' => isset($data_search["seat_number"]) ? $data_search["seat_number"] : '',
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
                                'controller' => 'transactionpage', 'action' => 'index',
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
