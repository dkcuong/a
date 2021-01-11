<div class="div-schedulingpage">
    <div class="div-top">
        <div class="div-breadcrumb">
            <a href="<?= Router::url(array( 'controller' => 'ticketingpage', 'action' => 'index')) ?>" class="title big light-brown">Movie</a>
            <img src="<?= $webroot ?>general/arrow.png">
            <div class="title big">Schedule</div>
        </div>
        <div class="div-active-date">
            <a href="" class="date-active content smallest light-brown"><?= $current_schedule['label'] ?></a>
            <div class="div-option-date hidden">
                <?php 
                    
                    foreach($schedule['Schedule'] as $key => $value) {
                        $active = "";
                        if($active_date == $key) {
                            $active = "active";
                        }
                ?>
                        <a href="" class="scheduling content smallest <?= $active ?>" data-target="<?= $key ?>"> <?= $value['label'] ?></a>
                <?php 
                        $active = "";
                    }
                ?>
            </div>
        </div>
    </div>
    <div class="div-title-page title ultra-big">
        <?= $schedule['Movie']['name'] ?></br>
        <?= $schedule['Movie']['name_zho'] ?>
    </div>
    <div class="div-option-time">
        <div class="div-schedule-container">
            <?php 
                $is_movie_showing = false;
                foreach($current_schedule['Schedule'] as $cur_schedule) {
                    $disabled = ($cur_schedule['status'] == 0) ? 'disabled' : '';
                    if ($cur_schedule['status'] != 0) {
                        $is_movie_showing = true;
            ?>
                        <a href="<?= Router::url(array( 'controller' => 'seatingpage', 'action' => 'index', $cur_schedule['id'])) ?>">
                            <div class="div-schedule-item content biggest black-brown <?= $disabled ?>">
                                <div class="div-schedule-item-cover"></div>
                                <?= $cur_schedule['time'] ?>
                                <div class="div-hall-code content light super-small"><?= $cur_schedule['hall'] ?></div>
                            </div>
                        </a>
            <?php 
                    }
                }

                if (!$is_movie_showing) {
            ?>
                <div> No Schedule </div>
            <?php   
                }
            ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        COMMON.token = '<?= $staff['Staff']['token'] ?>';
        COMMON.staff_id = '<?= $staff['Staff']['id'] ?>';
        COMMON.url_schedule_detail = '<?= Router::url(array('controller' => 'seatingpage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.url_get_schedule_detail = '<?= Router::url(array('plugin' => 'movie', 'controller' => 'schedules', 'action' => 'get_data_schedule_detail', 'api' => true), true); ?>';
        COMMON.schedule = '<?= $schedule_json; ?>';
        COMMON.movie_type_id = <?= (isset($movie_type_id) && !empty($movie_type_id)) ? $movie_type_id : 0 ?>;
        COMMON.movie_id = <?= (isset($movie_id) && !empty($movie_id)) ? $movie_id : 0 ?>;
        COMMON.init_page();
    });
</script>