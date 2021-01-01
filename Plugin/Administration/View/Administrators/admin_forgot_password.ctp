<style type="text/css" media="screen">
	.box-widget {
		border: none;
		position: relative;
	}

	.widget-user .widget-user-header {
		padding: 20px;
		height: 120px;
		border-top-right-radius: 3px;
		border-top-left-radius: 3px;
	}

	.bg-aqua-active, .modal-info .modal-header, .modal-info .modal-footer {
		background-color: #00a7d0 !important;
	}

	.widget-user .widget-user-username {
		margin-top: 0;
		margin-bottom: 5px;
		font-size: 25px;
		font-weight: 400;
		text-shadow: 0 1px 1px rgba(0,0,0,0.2);
		color: #FFFFFF;
	}

	.widget-user .widget-user-desc {
		margin-top: 0;
	}

	.widget-user .widget-user-image {
		border-radius: 50%;
		width: 110px;
		border: solid 3px #4dc08a;
		height: 110px;
		line-height: 100px;
		text-align: center;
		margin: -60px auto 0 auto;
		background: #fafafa;
	}

	.widget-user .widget-user-image>img {
		width: 100px;
		height: auto;
	}

	.widget-user .box-footer.without-border {
		padding-top: 0px;
		border: none;
	}

	.widget-user .box-footer.poweredby{
		text-align: center; line-height: 25px; margin-top: 30px;
	}

	.widget-user .box-footer.poweredby a{
		display: inline-block;
	}

	.widget-user .box-footer.poweredby img.logo{
		background: none; height: 25px; margin: -3px 0 0 5px;
	}

	.widget-user .has-feedback label~.form-control-feedback{
		right: 17px;
	}
</style>

<div class="row">
	<div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
		<div class="box box-widget widget-user">
			<div class="widget-user-header bg-aqua-active">
				<h3 class="widget-user-username text-center">
					<?php echo Environment::read("site.name"); ?>
				</h3>
				<h5 class="widget-user-desc"></h5>
			</div>

			<div class="widget-user-image">
				<?php echo $this->Html->image('logo/horizontal-logo.png', array("class"=>"img-circle")); ?>
			</div>

			<div class="box-footer without-border">
				<div class="row">
					<?php echo $this->Form->create('Administrator', array('role' => 'form')); ?>
						<fieldset>
							<div class="form-group has-feedback col-xs-12">
								<?php 
									echo $this->Form->input('email', array(
										'class' => 'form-control',
										'placeholder' => __d('administration','email'),
										'after' => '<span class="glyphicon glyphicon-envelope form-control-feedback"></span>',
										'label' => __d('administration','email')
									));
								?>
							</div>

                            <div class="form-group col-xs-8">
                            </div>
							<div class="form-group col-xs-4">
								<?php
									echo $this->Form->submit(__('submit'), array(
										'class' => 'btn btn-primary btn-block btn-flat'
									));
								?>
							</div>
						</fieldset>
                    <?php echo $this->Form->end(); ?>
				</div>
			</div>

			<div class="box-footer poweredby text-center">
				<?php
					echo __('powered_by');
					echo $this->Html->link(
						$this->Html->image('vtl/logo.png', array(
							'class' => 'logo',
						)), 
						Environment::read('poweredby.website'),
						array(
							'escape' => false,
							'alt' => Environment::read('poweredby.name'),
							'title' => Environment::read('poweredby.name'),
							'target' => '_blank'
						)
					);
				?>
			</div>
		</div>
	</div>
</div>