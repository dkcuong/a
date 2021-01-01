<div class="row">
	<div class="col-xs-12">
		<div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?= "Member Sales Report"; ?></h3>
            </div>

            <div class="box-body">
                <?php 
                    echo $this->Form->create('Report', array('role' => 'form','type' => 'file')); 
                ?>
					<fieldset>

<!--                        <div class="row">
                            <div class="col-sm-6 col-xs-12">
								<div class="form-group">
                                    <?php /*echo $this->Form->input('type', array(
                                        'class' => 'form-control',
                                        'required' => true,
                                        'id' => 'types',
                                        'label' => '<font color="red">*</font>'.__('type'),
                                        'options' => $types,
                                    )); */?>
								</div>
                            </div>
                        </div>-->
                        <div class="row hourly-input">
                            <div class="col-sm-3 col-xs-12">
								<div class="form-group">
                                    <?php echo $this->element('datetime_picker',array(
										'format' => 'DD/MM/YYYY',
										'field_name' => 'report_date_from',
										'label' => __('report_date_from'),
										'id' => 'report_date_from',
										'value' => (isset($this->request->data['Report']['report_date_from']) && !empty($this->request->data['Report']['report_date'])) ? $this->request->data['report_date_from']['report_date'] : ''
									)); ?>
								</div>
                            </div>
                            <div class="col-sm-3 col-xs-12">
                                <div class="form-group">
                                    <?php echo $this->element('datetime_picker',array(
                                        'format' => 'DD/MM/YYYY',
                                        'field_name' => 'report_date_to',
                                        'label' => __('report_date_to'),
                                        'id' => 'report_date_to',
                                        'value' => (isset($this->request->data['Report']['report_date_to']) && !empty($this->request->data['Report']['report_date'])) ? $this->request->data['Report']['report_date_to'] : ''
                                    )); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div><?= $this->Form->submit(__('exit'), array('class' => 'btn btn-large btn-primary pull-right', 'id' => 'btn-exit')); ?></div>
                            <div><?= $this->Form->submit(__('generate_report'), array('class' => 'btn btn-large btn-primary pull-right btn-exit', 'id' => 'btn-generate-report')); ?></div>
                        </div>

					</fieldset>
				<?php echo $this->Form->end(); ?>
			</div>

			<!--<div class="box-body ">
                <div class="form-group">
                    <label for="errlog">Report Content : </label>
                    <textarea class="form-control" rows="10" id="report_content" readonly>
                        <?php
/*                            pr($report_result);
                        */?>
                    </textarea>
                </div>
			</div>-->
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
	});
</script>