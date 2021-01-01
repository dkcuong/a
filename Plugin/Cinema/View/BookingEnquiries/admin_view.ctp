<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __d('booking_enquiry', 'item_title'); ?></h3>

				<div class="box-tools pull-right">
                    <?php
                    /*    if(isset($permissions[$model]['edit']) && $permissions[$model]['edit']){
                            echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '. __d('place', 'edit_cinema'), array('action' => 'edit', $dbdata[$model]['id']), array('class' => 'btn btn-primary', 'escape' => false));
                        }*/
                    ?>
	            </div>
			</div>
			<div class="box-body">
                <?php if($dbdata[$model]){ ?>
                    <table id="Company" class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <td><strong><?= __('id'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['id']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('title'); ?></strong></td>
                                <td>
                                    <?= __(h($dbdata[$model]['title'])); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('name'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['name']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('email'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['email']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('country_code'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['country_code']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('phone'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['phone']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('date'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['date']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __d('booking_enquiry', 'time_from'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['time_from']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __d('booking_enquiry', 'time_to'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['time_to']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __d('booking_enquiry', 'event_purpose'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['event_purpose']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __d('booking_enquiry', 'movie_name'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['movie_name']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __d('booking_enquiry', 'no_of_attendee'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['no_of_attendee']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __d('place', 'hall_title'); ?></strong></td>
                                <td>
                                    <?= h($dbdata['Hall']['code']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __d('booking_enquiry', 'special_request'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['special_request']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __d('booking_enquiry', 'equipment'); ?></strong></td>
                                <td>
                                    <?= implode(', ', Hash::extract( $dbdata['Equipment'], "{n}.code" )); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __d('booking_enquiry', 'item'); ?></strong></td>
                                <td>
                                    <?= implode(', ', Hash::extract( $dbdata['Item'], "{n}.code" )); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('updated'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['updated']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('updated_by'); ?></strong></td>
                                <td>
                                    <?= h($dbdata['UpdatedBy']['email']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('created'); ?></strong></td>
                                <td>
                                    <?= h($dbdata[$model]['created']); ?>
                                </td>
                            </tr>

                            <tr>
                                <td><strong><?= __('created_by'); ?></strong></td>
                                <td>
                                    <?= h($dbdata['CreatedBy']['email']); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
	});
</script>	
	