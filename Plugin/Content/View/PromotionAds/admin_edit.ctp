<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= __d('promotion_ad', 'edit_item'); ?></h3>
            </div>

            <div class="box-body">
                <?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'promotion-ad-edit-form')); ?>
                <fieldset>
                    <?=$this->Form->input('id');?>

                    <div class="form-group">
                        <?php
                        echo $this->element('images_upload_customize', array(
                            'name' => 'PromotionAd.image',
                            'label' => "<font color='red'>* </font>" . __('image')
                                .'<br> Web : (Width: 920 px - Height: 615 px - Ratio: 1.5)'
                                .'<br> Mobile : (Width: 1080 px - Height: 1520 px - Ratio: 0.7)',
                            'img_review_url' => isset($this->request->data['PromotionAd']['image']) ? $this->request->data['PromotionAd']['image'] : ''
                        ));
                        ?>
                    </div><!-- .form-group -->

                    <div class="form-group">
                        <?= $this->Form->input('link', array('class' => 'form-control', 'label' => '<font color="red">* </font>'.__d('promotion_ad','link'))); ?>
                    </div>

                    <div class="form-group">
                        <?= $this->Form->input('description', array('class' => 'form-control', 'label' => __('description'))); ?>
                    </div>

                    <div class="form-group">
                        <?php echo $this->Form->input('display', array(
                            'class' => 'form-control display',
                            //'empty' => __('please_select'),
                            'data-live-search' => true,
                            'multiple' => false,
                            'required' => true,
                            'id' => 'display',
                            'label' => '<font color="red">*</font>'.__d('promotion_ad', 'display'),
                            'options' => $display_list,
                        )); ?>
                    </div>

                    <div class="form-group">
                        <?=$this->Form->input('enabled', array('label' => __('enabled'))); ?>
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