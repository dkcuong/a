<style>
    a.dropdown-item {
        display: block;
        padding: 5px;
    }
</style>

<div class="row filter-panel">
	<div class="col-md-12">
		<?php 
			echo $this->Form->create('Movie.filter', array(
				'url' => array('controller' => 'schedules', 'action' => 'index', 'admin' => true, 'prefix' => 'admin'),
                'class' => 'form_filter',
                'type' => 'get',
			));
		?>
		<div class="action-buttons-wrapper border-bottom">
			<div class="row">
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                    <?php 
                        echo $this->Form->input('movie_id', array(
                            'class' => 'form-control',
                            'empty' => __("please_select"),
                            'options' => $movies,
                            'selected' =>  isset($data_search['movie_id']) ? $data_search['movie_id'] : '',
                            'label' => __d('movie', 'item_title'),
                        ));
                    ?>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                    <?php 
                        echo $this->Form->input('hall_id', array(
                            'class' => 'form-control',
                            'empty' => __("please_select"),
                            'options' => $halls,
                            'selected' =>  isset($data_search['hall_id']) ? $data_search['hall_id'] : '',
                            'label' => __d('place', 'hall_title'),
                        ));
                    ?>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <?php echo $this->element('date_picker', array(
                        'field_name' => 'date', 
                        'label' => __('date'),
                        'value' => isset($data_search["date"]) ? $data_search["date"] : '',
                    )); 
                    ?>
                </div>
			</div>
			<div class="row">
				<div class="col-md-12">
                    <div class="pull-left dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= __d('schedule', 'set_layout'); ?></button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <?php foreach($halls as $id => $code): ?>
                                <?php
                                    echo $this->Html->link($code, array(
                                        'plugin' => 'movie', 'controller' => 'schedules', 'action' => 'set_layout', $id,
                                        'admin' => true, 'prefix' => 'admin'
                                    ), array(
                                        'class' => 'dropdown-item',
                                    ));
                                ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="pull-right vtl-buttons">
                        <?php
                            echo $this->Form->submit(__('submit'), array(
                                'class' => 'btn btn-primary btn-sm filter-button',
                            ));
                        ?>
                        <?php
                            echo $this->Html->link(__('reset'), array(
                                'plugin' => 'movie', 'controller' => 'schedules', 'action' => 'index',
                                'admin' => true, 'prefix' => 'admin'
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
