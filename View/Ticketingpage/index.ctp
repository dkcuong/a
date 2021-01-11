<div class="div-ticketingpage">
    <div class="div-top">
        <div class="div-breadcrumb">
            <div class="title big">Movie</div>
        </div>
        <div class="div-active-date">
            <a href="" class="date-active content smallest light-brown"><?= $current_schedule['label'] ?></a>
            <div class="div-option-date hidden">
                <?php 
                    $active = "active";
                    foreach($schedule['Schedule'] as $key => $value) {
                ?>
                        <a href="" class="ticketing content smallest <?= $active ?>" data-target="<?= $key ?>"> <?= $value['label'] ?></a>
                <?php 
                        $active = "";
                    }
                ?>
            </div>
        </div>
    </div>
    <div class="div-movie-list">
        <div class="div-movie-list-container">
            <?php 
                foreach($current_schedule['Movie'] as $cur_schedule) {
                    $tmp_title = $cur_schedule['title'].' ('.$cur_schedule['rating'].')';
                    $tmp_title_zho = $list_name_movie[$cur_schedule['movie_id']]['zho']['name'].' ('.$cur_schedule['rating'].')';
                    
                    $title = (strlen($tmp_title) > 60) ? substr($tmp_title, 0, 60)."..." : $tmp_title;
                    $title_zho = (strlen($tmp_title_zho) > 60) ? substr($tmp_title_zho, 0, 60)."..." : $tmp_title_zho;

                    ?>
                    <div class="div-movie-item">
                        <a class="link-schedule-detail"  data-id="<?= $cur_schedule['id'] ?>" href="<?= Router::url(array( 'controller' => 'schedulingpage', 'action' => 'index', $cur_schedule['id'], date('Y-m-d'))) ?>">
                            <img src="<?= $webroot.$cur_schedule['poster'] ?>" class="img-poster">
                            <div class="div-movie-title content smallest">
                                <?= $title ?> </br>
                                <?= $title_zho ?>
                            </div>
                            <div class="div-movie-type content smallest"><?= $cur_schedule['movie_type'] ?></div>
                        </a>
                    </div>
            <?php 
                    
                }
            ?>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        COMMON.url_schedule_detail = '<?= Router::url(array('controller' => 'schedulingpage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.url_get_schedule = '<?= Router::url(array('plugin' => 'movie', 'controller' => 'schedules', 'action' => 'get_schedule', 'api' => true), true); ?>';
        COMMON.token = '<?= $staff['Staff']['token'] ?>';
        COMMON.staff_id = '<?= $staff['Staff']['id'] ?>';
        COMMON.schedule = '<?= $schedule_json; ?>';
        COMMON.webroot = '<?= $webroot; ?>';
        COMMON.init_page();
    });
</script>