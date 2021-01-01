<style>
	.error-message 
	{
		color: red;
	}
</style>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0">
		<div class="box box-primary">
			<div class="box-header">
			    <h3 class="box-title"><?php echo __d('movie', 'edit_star'); ?></h3>
			</div>
			<div class="box-body">
			    <?php echo $this->Form->create('Star', array('role' => 'form', 'type' => 'file')); ?>
                    <fieldset>
                        <?php echo $this->Form->input('id', array('class' => 'form-control')); ?>

                        <?php echo $this->element('language_input', array(
                            'languages_model' => $languages_model,
                            'languages_list' => $languages_list,
                            'language_input_fields' => $language_input_fields,
                            'languages_edit_data' => isset($this->request->data[$languages_model]) ? $this->request->data[$languages_model] : false,
                        )); ?>

                        <div class="form-group">
                            <?php echo $this->Form->input('first_name', array(
                                'class' => 'form-control',
                                'required' => 'required',
                                'label' => '<font color="red">*</font>'  . __d('movie', 'code_first_name')
                            )); ?>
                        </div>

                        <div class="form-group">
                            <?php echo $this->Form->input('surname', array(
                                'class' => 'form-control',
                                'required' => 'required',
                                'label' => '<font color="red">*</font>'  . __d('movie', 'code_surname')
                            )); ?>
                        </div>

                        <div class="form-group">
                            <?php 
                                echo $this->element('images_upload_customize', array(
                                    'name' => 'Star.photo',
                                    'label' => __d('movie', 'photo').'  (Width: 1080 px - Height: 1520 px - Ratio: 0.7)',
                                    'required' => false,
                                    'img_review_url' => isset($this->request->data[$model]['image_url']) ? $this->request->data[$model]['image_url'] : ''
                                ));
                            ?>
                        </div><!-- .form-group -->

                        <div class="pull-right">
                            <?php echo $this->Form->submit(__('submit'), array(
                                'id' => 'checkBtn',
                                'class' => 'btn btn-large btn-primary')); ?>
                        </div>
                    </fieldset>
                <?php echo $this->Form->end(); ?>
			</div>
		</div><!-- /.form -->
	</div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->

<script type="text/javascript">
	$(document).ready(function(){
	});
</script>