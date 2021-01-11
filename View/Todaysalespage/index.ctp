<div class="div-schedulingpage">
    <div class="div-top">
    </div>
    <div class="div-title-page title ultra-big"><?= 'Sales Today (' . $now  . ")  &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp Total : $" . number_format($total_sum,2)?> </div>
    <div class="div-option-time">
        <div class="div-schedule-container">
            <?php 
                $is_movie_showing = false;
                foreach($list_payment_method as $payment_method) {
                    if ($payment_method['enabled'] == 0 && ! isset($list_sale[$payment_method_id])) {
                        continue;
                    }
                    $disabled = 'disabled';
                    $payment_method_id = $payment_method['id'];
            ?>
                    <div class="div-today-sale-item content biggest black-brown <?= $disabled ?>">
                        <div class="div-schedule-item-cover"></div>
                        <?php
                            $str_amount = ($payment_method == 1) ? ' ($0)' : ' (0)';
                            if (isset($list_sale[$payment_method_id])) {
                                $str_amount = " (" . number_format($list_sale[$payment_method_id][0]['sum_item'],0) .")";
                                if ($payment_method['type'] == 1) {
                                    $str_amount = " ($" . number_format($list_sale[$payment_method_id][0]['sum_payment'],0) .")";
                                }
                            }

                        ?>
                        <?= $payment_method['name'] . $str_amount ?>
                    </div>
            <?php
            }

            if (!$is_movie_showing) {
            ?>
            <?php
            }
            ?>
        </div>
    </div>
    <div class="div-today-sale-footer ultra-big"><?= 'As of '.date('Y-m-d H:i:s') ?></div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        COMMON.token = '<?= $staff['Staff']['token'] ?>';
        COMMON.staff_id = '<?= $staff['Staff']['id'] ?>';
        COMMON.url_schedule_detail = '<?= Router::url(array('controller' => 'seatingpage', 'action' => 'index', 'admin' => false), true); ?>';
        COMMON.url_get_schedule_detail = '<?= Router::url(array('plugin' => 'movie', 'controller' => 'schedules', 'action' => 'get_data_schedule_detail', 'api' => true), true); ?>';
        //COMMON.schedule = '<?//= $schedule_json; ?>//';
        COMMON.movie_type_id = <?= (isset($movie_type_id) && !empty($movie_type_id)) ? $movie_type_id : 0 ?>;
        COMMON.movie_id = <?= (isset($movie_id) && !empty($movie_id)) ? $movie_id : 0 ?>;
        COMMON.init_page();
    });
</script>