<!-- 
    thongnd
    - add radio button all
    - add new variables for check button when submit search
-->
<div class="row filter-panel">
    <div class="col-md-12">
        <?php 
            echo $this->Form->create('Content.promotion_ad_filter', array(
                'url' => array('controller' => 'promotion_ads', 'action' => 'index', 'admin' => true, 'prefix' => 'admin'),
                'class' => 'form_filter',
                'type' => 'get',
            ));
        ?>
        <div class="action-buttons-wrapper border-bottom">
            <div class="row">
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php
                            echo $this->Form->input('link', array(
                                'class' => 'form-control',
                                'label' => __d('promotion_ad', 'link'),
                                'value' => isset($data_search["link"]) ? $data_search["link"] : '',
                            ));
                        ?>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4">
                    <div class="form-group">
                        <?php
                            echo $this->Form->input('description', array(
                                'class' => 'form-control',
                                'label' => __('description'),
                                'value' => isset($data_search["description"]) ? $data_search["description"] : '',
                            ));
                        ?>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div><label><?php echo __('status'); ?></label></div>
                    <div class="btn-group btn-group-sm" data-toggle="buttons" >
                        <label class="btn btn-default">
                            <input type="radio" name="enabled" value="" autocomplete="off" 
                                <?php echo !isset($data_search['enabled']) || $data_search['enabled'] === "" ? 'checked="checked"' : ''; ?>>
                            <?php echo __('all'); ?>
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" name="enabled" value="1" autocomplete="off" 
                                <?php echo isset($data_search['enabled']) && $data_search['enabled']  === "1" ? 'checked="checked"' : '';?> >
                            <?php echo __('enabled'); ?>
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" name="enabled" value="0" autocomplete="off" 
                                <?php echo isset($data_search['enabled']) && $data_search['enabled'] === "0" ? 'checked="checked"' : ''; ?> >
                            <?php echo __('disabled'); ?>
                        </label>
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
                                'plugin' => 'content', 'controller' => 'promotion_ads', 'action' => 'index',
                                'admin' => true, 'prefix' => 'admin'
                            ), array(
                                'class' => 'btn btn-danger btn-sm filter-button',
                            ));
                        ?>
                        <!--
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
                        -->
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
                        <?php if(isset($permissions[$model]['edit']) && ($permissions[$model]['edit'] == true)){ ?>
                            <?= $this->Html->link(__('import_excel'), array('action' => 'import'), array('class' => 'btn btn-primary btn-sm filter-button', 'escape' => false, 'data-toggle'=>'tooltip', 'title' => __('import_excel')), ""); ?>
                        <?php } ?>
                    </div>
                </div> <!-- col-md-4 -->
            </div> <!-- row -->
        </div>

        <?php echo $this->Form->end(); ?>
    </div>
</div>
