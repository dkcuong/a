<div class="row">
    <div class="col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title"><?= __('schedule'); ?></h3>

				<div class="box-tools pull-right">
                    <?php
                        if(isset($permissions[$model]['edit']) && $permissions[$model]['edit']){
                            echo $this->Html->link('<i class="glyphicon glyphicon-pencil"></i> '. __d('schedule', 'edit_item'), array('action' => 'edit', $dbdata[$model]['id'], date('Y-m-d', strtotime($dbdata['ScheduleDetail']['date']))), array('class' => 'btn btn-primary', 'escape' => false));
                        } 
                    ?>
	            </div>
			</div>
			<div class="box-body table-responsive">
                <div role="tabpanel">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role=<?= $model ?> class="active">
                            <a href="#info-tab" aria-controls="tab" role="tab" data-toggle="tab">
                                <?= __('schedule') ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="info-tab">
                        <?php if($dbdata[$model]){ ?>
                            <table id="schedule" class="table table-bordered table-striped">
                                <tbody>
                                    <tr>
                                        <td><strong><?= __('id'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata[$model]['id']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('movie'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata['Movie']['code']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('movie_type'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata['MovieType']['name']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong><?= __('hall'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata['Hall']['code']); ?>
                                        </td>
                                    </tr>                                                            
                                    <tr>
                                        <td><strong><?= __('date'); ?></strong></td>
                                        <td>
                                            <?= h($dbdata['ScheduleDetail']['date']); ?>
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
                            <?php
                                foreach($data_detail as $data_detail) {
                            ?>
                                <div class="div-container">
                                    <div class="div-detail">
                                        <div class="div-time"><strong><?= __('time') ?></strong> : <?= date('H:i', strtotime($data_detail['ScheduleDetail']['time'])) ?></div>
                                        <div class="div-price">
                                            <strong><?= __('ticket_price') ?></strong>
                                            <ul>
                                            <?php
                                                foreach($data_detail['ScheduleDetailTicketType'] as $price) {
                                            ?>
                                                    <li><?= $price['TicketType']['code'] ?> : <?= $price['price'] ?></li>
                                            <?php
                                                }
                                            ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="div-layout">
                                        <label><?= __('user_seat_layout') ?></label>
                                        <div id="panel-seat-layout" class="seat-layout panel-seat-layout">
                                            <?php
                                                foreach($data_detail['ScheduleDetailLayout'] as $seat_row) {
                                                    
                                            ?>
                                                <div class='row-seat'><div class='row-title'><?= $seat_row[0]['title'] ?></div>    
                                            <?php
                                                    foreach($seat_row as $seat) {
                                                        // $enabled_style = ($seat['enabled'] == 1) ? 'enabled' :  '';
                                                        $enabled_style = '';
                                                        $label = $seat['label'];
                                                        if ($seat['enabled'] == 1) {
                                                            $enabled_style = 'enabled';
                                                            if($seat['status'] > 1) {
                                                                $label = 'X';
                                                                $enabled_style = ($seat['status'] == 2) ? 'reserved' :  'sold';
                                                            }
                                                        }

                                                        $vegetabled_style = '';
                                                        if ($seat['vegetable'] == 1)
                                                        {
                                                            $vegetabled_style = 'vegetable';
                                                        }

                                                        $blocked_style = '';
                                                        if ($seat['blocked'] == 1)
                                                        {
                                                            $blocked_style = 'blocked';
                                                        }
                                            ?>
                                                        <div class='div-seat <?= $enabled_style ?> <?= $vegetabled_style ?> <?= $blocked_style ?>'><?= $label ?></div>
                                            <?php
                                                    }
                                                    echo('</div>');
                                                }
                                                
                                            ?>
                                        </div> 
                                        <div class="div-legend">
                                            <div class="div-item available"><div></div> <?= __('available') ?> </div>
                                            <div class="div-item reserved"><div></div> <?= __('reserved') ?> </div>
                                            <div class="div-item sold"><div></div> <?= __('sold') ?> </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                }
                            ?>
                        <?php } ?>
                    </div> <!-- close tabpanel -->
                </div> <!-- close tab-content -->
			</div>
		</div>
	</div>
</div>
	