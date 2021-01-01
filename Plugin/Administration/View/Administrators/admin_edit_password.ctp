
<div class="row">
    <div class="col-xs-12 col-xs-offset-0">
		<div class="box box-primary">
			<div class="box-header">
			<h3 class="box-title">
                <?php echo __d('administration','edit_administrator'); ?>
            </h3>
            
			</div>
			<div class="box-body">
		
			<?php echo $this->Form->create('Administrator', array('role' => 'form')); ?>

				<fieldset>

					<div class="form-group">
						<?php echo $this->Form->input('id', array('class' => 'form-control')); ?>
					</div><!-- .form-group -->

					<div class="form-group">
						<?php echo $this->Form->input('old_password', array('class' => 'form-control',
														'type' => 'password',
														'required' => true,
														'label' => __('old_password') . "<span style='color:red'>*</span>"
													)); 
													?>
					</div><!-- .form-group -->
					<div class="form-group">
						<?php echo $this->Form->input('new_password', array('class' => 'form-control',
														'type' => 'password',
														'required' => true,
														'onkeyup' => "onCheck()",
														'label' =>   __('new_password') . "<span style='color:red'>*</span>"
													)); 
													?>
					</div><!-- .form-group -->
					<div class="form-group">
						<?php echo $this->Form->input('confirm_new_password', array('class' => 'form-control',
														'type' => 'password',
														'required' => true,
														'onkeyup' => "onCheck()",
														'label' => __('confirm_new_password') . "<span style='color:red'>*</span>"
													)); 
													?>
					</div><!-- .form-group -->

					<div class="form-group">

						<label id="message" style="color:red">   </label>
						
					</div><!-- .form-group -->
					
					<div class="pull-right">
						<?php echo $this->Form->submit(__('submit'), array(
							'class' => 'btn btn-large btn-primary',
							'id' => 'btnok'
						
						)); ?>
					</div>

				</fieldset>

				<?php echo $this->Form->end(); ?>
			</div>

		</div><!-- /.form -->
			
	</div><!-- /#page-content .col-sm-9 -->

</div><!-- /#page-container .row-fluid -->


<script>

	function onCheck()
	{
		var newpassword = $("#AdministratorNewPassword").val();
		var confirmnewpassword = $("#AdministratorConfirmNewPassword").val();
	
		$("#message").text("");
		$(":submit").removeAttr('disabled');

		if (newpassword != confirmnewpassword) 
		{
			$("#message").text("新的密碼和確定密碼不相同數字");
			$(":submit").attr('disabled', true);
		}
		
	}
	
</script>