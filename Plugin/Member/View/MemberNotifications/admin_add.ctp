<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('notification', 'add_item'); ?></h3>
			</div>

			<div class="box-body ">
				<?= $this->Form->create($model, array('role' => 'form', 'type' => 'file', 'id' => 'notification-add-form')); ?>
					<fieldset>

						<!-- Push Method -->
						<div class="well">
							<div class="row">
								<div class="col-xs-2">
                                    <div class="form-group">
                                        <div class="input select required">
                                            <label for="push_method"><?= '<font color="red">*</font>'.__('push_method') ?></label>
                                            <select name="data[Notification][push_method]" class="form-control" id="push_method" required="required">
                                                <?php foreach($pushMethods as $item){ ?>
                                                    <option value="<?= $item?>" data-slug="<?= $item?>" 
                                                            <?= isset($this->request->data['Notification']['push_method']) && $this->request->data['Notification']['push_method'] == $item ? 'selected="selected"': '' ?>>
                                                        <?= $item?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
									</div>
								</div>
								<div class="col-xs-10 col-xs-offset-2 push-to-someone">
									<div class="form-group">
                                        <input type="text" class="form-control member-autocomplete" placeholder="<?= __('search_by_phone') ?>"/>
                                    </div><!-- .form-group -->
                                    <div class="form-group list-member-name">

                                    </div>
								</div>
							</div> <!-- end row -->
						</div> <!-- end well -->

						<!-- Push Method  -->
						<!--
                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <?=$this->Form->input('Notification.image', array('type' => 'file', 'class' => 'form-control','label' => __('image'))); ?>
                                </div>
                            </div>
                        </div>
						-->
						
						<?php echo $this->element('language_input', array(
								'languages_model' => $languages_model,
								'languages_list' => $languages_list,
								'language_input_fields' => $language_input_fields,
								'languages_edit_data' => isset($this->request->data[$languages_model]) ? $this->request->data[$languages_model] : false,
						)); ?>
						

						<?= $this->Form->submit(__('submit'), array('class' => 'btn btn-large btn-primary pull-right', 'id' => 'btn-submit-data')); ?>
					</fieldset>
				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<?php echo $this->Html->script('pages/admin_push', array('inline' => false)); ?>
<script type="text/javascript">
	$(document).ready(function(){
        ADMIN_PUSH.model = '<?= $model ?>';
        ADMIN_PUSH.person_field = 'member_id';
        ADMIN_PUSH.url_get_members = '<?php echo Router::url(array('plugin'=>'member', 'controller' => 'members', 'action' => 'get_data_select', 'admin' => true, 'prefix' => 'admin'), true); ?>';
        ADMIN_PUSH.message_confirm_push_all = '<?php echo __('confirm_to_push_to_all');?>';
        ADMIN_PUSH.message_must_choose_member = '<?php echo __('must_choose_member');?>';
        ADMIN_PUSH.init_page();
	});
</script>