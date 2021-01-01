<div class="div-seatingpage">
    <div class="div-top">
        <div class="div-breadcrumb">
            <a href="<?= Router::url(array( 'controller' => 'ticketingpage', 'action' => 'index')) ?>" class="title big light-brown">Film</a>
            <img src="<?= $webroot ?>general/arrow.png">
            <a href="<?= Router::url(array( 'controller' => 'schedulingpage', 'action' => 'index', $schedule_detail_id)) ?>" class="title big light-brown">Schedule</a>
            <img src="<?= $webroot ?>general/arrow.png">
            <div class="title big">Seats</div>
        </div>
    </div>

    <div class="div-seatingpage-subsection">
        <div class="div-movie-info">
            <div class="div-title-page title big"><?= $movie['MovieLanguage']['name'] . ' (' . $movie['MovieType']['name'] . ')' ?></div>
            <div class="div-info-row">
                <div class="div-info-item first">
                    <div class="div-info-label title smaller light">Date</div>
                    <div class="div-info content"><?= date('m/d/Y' , strtotime($movie['ScheduleDetail']['date'])) ?></div>
                </div>
                <div class="div-info-item info-middle">
                    <div class="div-info-label title smaller light">Time</div>
                    <div class="div-info content"><?= date('H:i' , strtotime($movie['ScheduleDetail']['time'])) ?></div>
                </div>
                <div class="div-info-item last">
                    <div class="div-info-label title smaller light">Screen No.</div>
                    <div class="div-info content"><?= $movie['Hall']['code'] ?></div>
                </div>
            </div>
            <div class="div-info-row">
                <div class="div-info-item first">
                    <div class="div-info-label title smaller light">Duration</div>
                    <div class="div-info content"><?= $movie['Movie']['duration'] ?></div>
                </div>
                <div class="div-info-item info-middle">
                    <div class="div-info-label title smaller light">Language</div>
                    <div class="div-info content"><?= $movie['Movie']['language'] ?></div>
                </div>
                <div class="div-info-item last">
                    <div class="div-info-label title smaller light">Subtitle</div>
                    <div class="div-info content"><?= $movie['Movie']['subtitle'] ?></div>
                </div>
            </div>
            <?php
                $hidden_class = "hidden";
                if ($movie['Movie']['rating'] == "III") {
                    $hidden_class = "";
                }
            ?>
            <div class="div-info-warning content super-small <?= $hidden_class ?>">
                <?= __('warning_level_III') ?>
            </div>
        </div>

        <div class="div-panel-seats">
            <?php 
                $full_capacity_class = "hidden";
                if ($is_full) {
                    $full_capacity_class = "";
                }
            ?>
            <div class="div-screen content super-small light-grey">
                SCREEN
                <div></div>
            </div>
            <div class="div-seat-container">
                <div class="div-full-capacity-cover content light-red middle <?= $full_capacity_class ?>"> FULL HOUSE </div>
                <?php
                    foreach($seat_layout as $seats) {
                        echo("<div class='div-row-seat'>");
                        foreach($seats as $seat) {
                            $enabled_style = ($seat['enabled'] == 1) ? 'enabled' :  '';
                            $disability_style = ($seat['disability'] == 1) ? 'disability' :  '';
                            $blocked_style = ($seat['blocked'] == 1) ? 'blocked' :  '';

                            if (in_array($seat['id'], $selected_seat)) {
                                $status_style = 'selected';
                                $label = $seat['title'] . $seat['label'];
                            } else {
                                $status_style = ($seat['status'] == 3) ? 'sold' :  (($seat['status'] == 2) ? 'sold' : '');
                                $label = (($seat['enabled'] == 1) && ($seat['status'] == 1) && ($seat['blocked'] == 0)) ? $seat['title'] . $seat['label'] :  (($seat['status'] > 1 || $seat['blocked'] == 1) ? 'X' : '');
                            }
                ?>
                            <button type='button' class='btn-pos-seat content super-small light light-grey <?= $enabled_style ?> <?= $disability_style ?> <?= $blocked_style ?> <?= $status_style ?>' data-disability='<?= $seat['disability'] ?>' data-id='<?= $seat['id'] ?>'>
                                <?= $label ?>
                            </button>
                <?php
                        }
                        echo("</div>");
                    }
                ?>
            </div>
            <!--
            <div class="div-screen content super-small light-grey">
                SCREEN
                <div></div>
            </div>
            -->
            <div class="div-legend-container content smallest light light-grey">
                <div class="div-legend-item">
                    <div class="available"></div> Available
                </div>
                <div class="div-legend-item">
                    <div class="disability"></div> Disability
                </div>
                <div class="div-legend-item">
                    <div class="sold"></div> Sold
                </div>
                <div class="div-legend-item">
                    <div class="selected"></div> Selected
                </div>
                <div class="div-legend-item">
                    <div class="not-for-sale"></div> Not for sell
                </div>
            </div>
        </div>
    </div>

    <button type='button' class='btn-pos-submit title small narrow disabled'>
        NEXT
    </button>
</div>

<button id="btn-rotate" class="btn-rotate">ROTATE</button>

<div class="div-dialog-container dialog-ticket-type hidden">
    <div class="div-dialog-payment">
        <div class="div-dialog-header title">TICKETS</div>
        <div class="div-dialog-content">
            <?php 
                foreach($ticket_types as $ticket_type) {
                    $type = ($ticket_type['TicketType']['is_main'] == 1) ? 'main-ticket-type' : (($ticket_type['TicketType']['is_disability'] == 1) ? 'disability-ticket-type' : '');
                    $read_only = ($ticket_type['TicketType']['is_disability'] == 1) ? 'READONLY' : '';
                    $disable = ($ticket_type['TicketType']['is_disability'] == 1) ? 'DISABLED' : '';
            ?>
                    <div class="ticket-row content light">
                        <div class="div-payment-title"><?= $ticket_type['TicketType']['TicketTypeLanguage'][0]['name'] ?></div>
                        <div class="div-payment-input">
                            <button class="btn-decrease minus" data-id="<?= $ticket_type['id'] ?>" <?= $disable ?>><img src="<?= $webroot ?>general/img-minus.png"></button>
                            <input id="txt-number_<?= $ticket_type['id'] ?>" class="txt-number <?= $type ?>" data-id="<?= $ticket_type['id'] ?>" value="0" <?= $read_only ?>>
                            <button class="btn-increase plus" data-id="<?= $ticket_type['id'] ?>" <?= $disable ?>><img src="<?= $webroot ?>general/img-plus.png"></button>
                        </div>
                        <div class="div-payment-amount">HKD <?= $ticket_type['price'] ?></div>
                    </div>
            <?php 
                }
            ?>
        </div>
        <div class="div-dialog-footer">
            <div class="div-summary content smallest light ">2 of 2 ticket(s)</div>
            <div class="div-dialog-footer-button">
                <button class="btn-cancel title small narrow" type="button">CANCEL</button>
                <button class="btn-checkout title small narrow" type="button">CHECKOUT</button>
            </div>
        </div>
    </div>
</div>

<div class="div-dialog-container dialog-seat-unavailable hidden">
    <div class="div-dialog-seat-unavailable">
        <img src="<?= $webroot ?>general/icon-error.png">
        <div class="div-error-notes1 title big">Oopsss! Wait a second</div>
        <div class="div-error-notes2 content light small">
            The seat(s) has been bought by somebody.
            Please choose other seat(s).
        </div>
        <button class="btn-close title small narrow" type="button">SELECT ANOTHER SEAT</button>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        COMMON.staff_id = '<?= $staff['Staff']['id'] ?>';
        COMMON.token = '<?= $staff['Staff']['token'] ?>';
        COMMON.schedule_detail_id = '<?= $schedule_detail_id ?>';
        COMMON.order_id = <?= (isset($data_order['Order']['id']) && !empty($data_order['Order']['id'])) ? $data_order['Order']['id'] : 0 ?>;
        COMMON.booked_seat = [];
        COMMON.booked_seat_disability = [];
        COMMON.url_summary = '<?= Router::url(array('controller' => 'summarypage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.url_create_trans = '<?= Router::url(array('plugin' => 'pos', 'controller' => 'orders', 'action' => 'create_order', 'api' => true), true); ?>';
        COMMON.init_page();
        COMMON.restore_order_trans();
    });
</script>