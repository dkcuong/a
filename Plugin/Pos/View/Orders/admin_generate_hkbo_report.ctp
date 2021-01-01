<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"> Scan QR Code</h3>
			</div>

            <div class="box-body">
                <?php 
                    echo $this->Form->create('Report', array('role' => 'form','type' => 'file')); 
                ?>
					<fieldset>

                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
                                    <?php echo $this->Form->input('type', array(
                                        'class' => 'form-control',
                                        'required' => true,
                                        'id' => 'types',
                                        'label' => '<font color="red">*</font>'.__('type'),
                                        'options' => $types,
                                    )); ?>
								</div>
                            </div>
                        </div>
                        <div class="row hourly-input">
                            <div class="col-sm-3 col-xs-12">
								<div class="form-group">
                                    <?php echo $this->element('datetime_picker',array(
										'format' => 'DD/MM/YYYY',
										'field_name' => 'report_date', 
										'label' => __('report_date'),
										'id' => 'report_date', 
										'value' => (isset($this->request->data['Report']['report_date']) && !empty($this->request->data['Report']['report_date'])) ? $this->request->data['Report']['report_date'] : ''
									)); ?>
								</div>
                            </div>
                            <div class="col-sm-3 col-xs-12">
								<div class="form-group">
                                    <?php echo $this->element('datetime_picker',array(
                                        'format' => 'HH:mm',
                                        'field_name' => 'time_report', 
                                        'label' => __('time_report'),
                                        'value' => (isset($this->request->data['Report']['time_report']) && !empty($this->request->data['Report']['time_report'])) ? $this->request->data['Report']['time_report'] : '',
                                        'id' => 'time_report',  
                                    )); ?>	
								</div>
                            </div>
                        </div>

                        <?= $this->Form->submit(__('exit'), array('class' => 'btn btn-large btn-primary pull-right', 'id' => 'btn-exit')); ?>
                        <?= $this->Form->submit(__('generate_report'), array('class' => 'btn btn-large btn-primary pull-right btn-exit', 'id' => 'btn-generate-report')); ?>

					</fieldset>
				<?php echo $this->Form->end(); ?>
			</div>

			<div class="box-body ">
                <div class="form-group">
                    <label for="errlog">Report Content : </label>
                    <textarea class="form-control" rows="10" id="report_content" readonly>
                        <?php
                            pr($report_result);
                        ?>
                    </textarea>
                </div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
	});
</script>