<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= __d('mobile_menu', 'add_item'); ?></h3>
            </div>

            <div class="box-body ">
                <?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'mobile-menu-add-form')); ?>
                <fieldset>

                    <div class="row">
                        <div class="col-sm-6 col-xs-12">
                            <div class="form-group">
                                <?php
                                echo $this->element('images_upload_customize', array(
                                    'name' => 'MobileMenu.image',
                                    'label' => "<font color='red'>*</font>" . ' ' .  __( 'image').' (Width: 1080 px - Height: 1520 px - Ratio: 0.7)',
                                    'required' => true,
                                    'img_review_url' => isset($this->request->data['MobileMenu']['image']) ? $this->request->data['MobileMenu']['image'] : ''
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <?= $this->Form->input('link', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'. ' ' .__d('mobile_menu', 'link'))); ?>
                    </div>

                    <div class="form-group">
                        <?= $this->Form->input('description', array('class' => 'form-control', 'required' => true, 'label' => __('description'))); ?>
                    </div>

                    <div class="form-group">
                        <?=$this->Form->input('enabled', array('checked' => 'checked', 'label' => __('enabled'))); ?>
                    </div>

                    <?= $this->Form->submit(__('submit'), array('class' => 'btn btn-large btn-primary pull-right', 'id' => 'btn-submit-data')); ?>
                </fieldset>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
    });
</script>