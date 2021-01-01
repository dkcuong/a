<div class="form-group images-upload">

<?php 
	$count = 0;

	if( isset($this->request->data[$detail_model]) && !empty($this->request->data[$detail_model]) ){
		//its edit
		$count = -1;

		foreach ($this->request->data[$detail_model] as $key => $value) :
			$count++;
			if(isset($value['id']) && $value['id']){
?>
			<div class="well well-sm">
				<div class="row images-upload-row">
					<?= $this->Form->input($detail_model.'.'.$count.'.id', array('type' => 'hidden', 'value' => $value['id'])); ?>
					<?= $this->Form->input($detail_model.'.'.$count.'.schedule_id', array('type' => 'hidden', 'value' => $value['schedule_id'])); ?>
					
					<div class="col-sm-5 col-xs-12">
					<div class="form-group">
							<?php echo $this->element('datetime_picker',array(
								'format' => 'HH:mm',
								'field_name' => $detail_model.'.'.$count.'.time', 
								'label' => __('time'),
								'required' => true,
								'value' => date('Y-m-d', strtotime($value['date'])) . ' ' . date('H:i', strtotime($value['time'])), 
								'id' => 'time_'.$count,  
							)); ?>							
						</div>
					</div>

					<div class="col-sm-12 col-xs-12">
						<div class="form-group row">
							<?php
								$ticket_price_count = -1;
								foreach($value['ScheduleDetailTicketType'] as $ticket_type) {
									$ticket_price_count++;
							?>
									<div class="col-md-2 col-xs-12">
										<?= $this->Form->input($detail_model.'.'.$count.'.ScheduleDetailTicketType.'.$ticket_price_count.'.id', array('class' => 'form-control', 'type' => 'hidden', 'value' => $ticket_type['ScheduleDetailTicketType']['id'])); ?>
										<?= $this->Form->input($detail_model.'.'.$count.'.ScheduleDetailTicketType.'.$ticket_price_count.'.ticket_type_id', array('class' => 'form-control', 'type' => 'hidden', 'value' => $ticket_type['TicketType']['id'])); ?>
										<?= $this->Form->input($detail_model.'.'.$count.'.ScheduleDetailTicketType.'.$ticket_price_count.'.price', array('class' => 'form-control', 'required' => true, 'value' => $ticket_type['ScheduleDetailTicketType']['price'], 'label' => '<font color="red">*</font>'.$ticket_type['TicketTypeLanguage']['name'].' '.__('price'))); ?>
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
								foreach($value['ScheduleDetailTicketType'] as $ticket_type) {
									$ticket_price_count++;
							?>
									<div class="col-md-2 col-xs-12">
										<?= $this->Form->input($detail_model.'.'.$count.'.ScheduleDetailTicketType.'.$ticket_price_count.'.id', array('class' => 'form-control', 'type' => 'hidden', 'value' => $ticket_type['ScheduleDetailTicketType']['id'])); ?>
										<?= $this->Form->input($detail_model.'.'.$count.'.ScheduleDetailTicketType.'.$ticket_price_count.'.ticket_type_id', array('class' => 'form-control', 'type' => 'hidden', 'value' => $ticket_type['TicketType']['id'])); ?>
										<?= $this->Form->input($detail_model.'.'.$count.'.ScheduleDetailTicketType.'.$ticket_price_count.'.price_hkbo', array('class' => 'form-control', 'required' => true, 'value' => $ticket_type['ScheduleDetailTicketType']['price_hkbo'], 'label' => '<font color="red">*</font>'.$ticket_type['TicketTypeLanguage']['name'].' '.__('price_hkbo'))); ?>
									</div>
										
							<?php
								}
							?>
						</div>		
					</div>

					<div class="col-xs-1 images-buttons text-right custom-button-top-right">
						<?php
							print $this->Html->link('<i class="glyphicon glyphicon-remove"></i>', '#', array(
								'class' => 'btn-remove-uploaded-image',
								'data-image-id' => $value['id'],
								'escape' => false
							));
						?>
					</div>
				</div>
			</div>
<?php
			}
		endforeach;
	}
?>

	<div class="row images-upload-row">
		<div class="col-xs-12 text-center">
			<label><font color="red">*</font></label>
			<?php
				print $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__d('schedule','add_time_schedule'), '#', array(
					'class' => 'btn btn-primary btn-new-image',
					'escape' => false
				));
			?>
		</div>
	</div>
</div><!-- .form-group -->


<script type="text/javascript" charset="utf-8">
	var article_images = { count: <?= $count ?> };

	$(document).ready(function(){
		article_images.count = $('.images-upload > .well').length;

		$('.btn-remove-image').on('click', function( e ){
			e.preventDefault();

			article_images.count--;

			$(this).closest(".well").remove();
		});

		$('.btn-remove-uploaded-image').on('click', function( e ){
			e.preventDefault();

			var station_id = $(this).data('image-id');

			var remove_hidden_input = '<input type="hidden" name="data[remove_schedule_detail][]" value="'+station_id+'">';

			article_images.count--;
			
			$(this).parents('.images-upload').append( remove_hidden_input );
			$(this).closest(".well").remove();
		});

		$('.btn-new-image').on('click', function( e ){
			e.preventDefault();

			var url = '<?php echo $add_new_time_schedule_url; ?>';

			COMMON.call_ajax({
				type: "POST",
				url: url,
				dataType: 'html',
				cache: false,
				data: {
					count: article_images.count,
					detail_ticket_type_model: '<?php echo $detail_ticket_type_model; ?>',
					detail_model: '<?php echo $detail_model; ?>',
					base_model: '<?php echo isset($base_model) ? $base_model : ''; ?>',
				},
				success: function( result ){
					var counter = (article_images.count - 1);

					if( counter < 0 ){
						$('.images-upload > .images-upload-row').before( result );
					} else {
						$('.images-upload > .well').eq( counter ).after( result );
					}

					article_images.count++;

					$('.btn-remove-image').on('click', function( e ){
						e.preventDefault();

						article_images.count--;

						$(this).closest(".well").remove();
					});
				},
				error: function( result ){
					// console.log('error :');
					// console.log( result );
				}
			});



		});
	});

</script>