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
			    <h3 class="box-title"><?php echo __d('administration','edit_administrator'); ?></h3>
			</div>
			<div class="box-body">
			    <?php echo $this->Form->create('Administrator', array('role' => 'form')); ?>
                    <fieldset>
                        <?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="input select">
                                        <label for="ddl_role_id"><font color="red">*</font><?= __d('administration','role') ?></label>
                                        <select name="data[Administrator][role_id]" class="form-control selectpicker" 
                                                data-live-search="1" title="<?= __("please_select") ?>" id="ddl_role_id" 
                                                required="required">
                                            <?php foreach($roles as $key => $role): ?>
                                                <option value="<?= $key ?>"
                                                        <?= isset($this->request->data['Administrator']['role_id']) && $this->request->data['Administrator']['role_id'] == $key ? 'selected="selected"' : '' ?>>
                                                    <?= $role ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div> <!-- end col-md-3 -->
                        </div> <!-- end row -->

                        <div class="form-group">
                            <?php echo $this->Form->input('name', array(
                                'class' => 'form-control',
                                'label' => __d('administration','name'),
                                'required' => 'required',
                                'label' => '<font color="red">*</font>'  . __d('administration','name')
                            )); ?>
                        </div><!-- .form-group -->

                        <div class="form-group">
                            <?php 
                                echo $this->Form->input('email', array(
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'placeholder' => 'e.g. admin@'.Environment::read('company.email_domain'),
                                    'label' => '<font color="red">*</font>'  . __d('administration','email')
                                )); 
                            ?>
                        </div><!-- .form-group -->

                        <div class="form-group">
                            <?php 
                                echo $this->Form->input('phone', array(
                                    'class' => 'form-control',
                                    'id' => 'txt_phone',
                                    'required' => 'required',
                                    'label' => '<font color="red">*</font>'  . __d('administration','phone'),
                                    'placeholder' => 'e.g. 34666778',
                                )); 
                            ?>
                        </div><!-- .form-group -->

						<div class="form-group">
							<?php echo $this->Form->input('enabled', array('class' => 'form-control', 'label' => __('enabled'))); ?>
                        </div>

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
<?php
	echo $this->Html->script('pages/admin_administrator', array('inline' => false));
?>
<script type="text/javascript">
	$(document).ready(function(){
        COMMON.init_element_number($('#txt_phone'));
        ADMIN_ADMINISTRATOR.init_page();
	});
</script>