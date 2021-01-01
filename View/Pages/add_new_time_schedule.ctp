<div class="well well-sm">
	<div class="row images-upload-row">
		<div class="col-sm-5 col-xs-12">
			<div class="form-group">
				<?php echo $this->element('datetime_picker',array(
					'format' => 'HH:mm',
					'field_name' => $detail_model.'.'.$count.'.time', 
					'label' => __('time'),
					'required' => true,
					'id' => 'time_'.$count, 
				)); ?>
			</div>
		</div>

		<div class="col-sm-12 col-xs-12">
			<div class="form-group row">
				<?php
					$ticket_price_count = -1;
					foreach($data_ticket_type as $ticket_type) {
						$ticket_price_count++;
				?>
						<div class="col-md-2 col-xs-12">
							<?= $this->Form->input($detail_model.'.'.$count.'.ScheduleDetailTicketType.'.$ticket_price_count.'.ticket_type_id', array('class' => 'form-control', 'type' => 'hidden', 'value' => $ticket_type['TicketType']['id'])); ?>
							<?= $this->Form->input($detail_model.'.'.$count.'.ScheduleDetailTicketType.'.$ticket_price_count.'.price', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.$ticket_type['TicketTypeLanguage']['name'].' '.__('price'))); ?>
						</div>
							
				<?php
					}
				?>
			</div>		
		</div>

		<div class="col-sm-12 col-xs-12">
			<div class="form-group row">
				<?php
					$ticket_price_count = -1;
					foreach($data_ticket_type as $ticket_type) {
						$ticket_price_count++;
				?>
						<div class="col-md-2 col-xs-12">
							<?= $this->Form->input($detail_model.'.'.$count.'.ScheduleDetailTicketType.'.$ticket_price_count.'.ticket_type_id', array('class' => 'form-control', 'type' => 'hidden', 'value' => $ticket_type['TicketType']['id'])); ?>
							<?= $this->Form->input($detail_model.'.'.$count.'.ScheduleDetailTicketType.'.$ticket_price_count.'.price_hkbo', array('class' => 'form-control', 'required' => true, 'label' => '<font color="red">*</font>'.$ticket_type['TicketTypeLanguage']['name'].' '.__('price_hkbo'))); ?>
						</div>
							
				<?php
					}
				?>
			</div>		
		</div>

		<div class="col-xs-1 images-buttons text-right custom-button-top-right">
			<?php
				echo $this->Html->link('<i class="glyphicon glyphicon-remove"></i>', '#', array(
					'class' => 'btn-remove-image',
					'escape' => false
				));
			?>
		</div>

		<div class="form-group-label col-xs-12">
			<span class="image-type-limitation"></span>
		</div>
	</div>
</div>