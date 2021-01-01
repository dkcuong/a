<?php
	if (isset($show_no_schedule) && !$show_no_schedule) {	
		$movie_language =$data_schedule['MovieLanguage'];
		$movie =$data_schedule['Movie'];
		$schedule_detail = $data_schedule['ScheduleDetail'];
		$hall = $data_schedule['Hall'];
		$seats = $data_schedule['seats'];
	} else {
		$movie['poster'] = '';
		$movie_language['MovieLanguage']['name'] = '';
		$movie['rating'] = '';
		$movie['duration'] = '';
		$schedule_detail['time'] = '';
		$seats = array();
	}
?>
<section class="dashboard-tv">
	<?php 
		$content_class = "d-xl-none d-lg-none";
		$warning_class = "";
		if (isset($show_no_schedule) && !$show_no_schedule) {
			$content_class = "";
			$warning_class = "d-xl-none d-lg-none";
		}
	?>
	<div class="container-fluid p-0 main-contain <?= $content_class ?>">
		<div class="row mx-0 movie-item">
			<div class="col-2 px-0 poster">
				<?=$this->Html->image(Environment::read('web.url_img').$movie['poster'])?>
			</div>
			<div class="col-10 px-0 py-3 bg-white">
				<div class="row mx-0">
					<div class="col-7 px-0">
						<div class="row m-0 summary">
							<div class="col-md-12 movie-title">
								<h1><?=reset($movie_language)['MovieLanguage']['name']?></h1>
								<h3><?=end($movie_language)['MovieLanguage']['name']?></h3>
							</div>
							<div class="col-md-12 d-flex align-items-center">
								<div class="rating mr-3">
									<?=$movie['rating']?>
								</div>
								<div class="duration mr-3">
									<?=$movie['duration']?> min
								</div>
							</div>
						</div>
					</div>
					<div class="col-5 px-0">
						<div class="row mx-0 justify-content-end">
							<h1 class="col-12 text-right schedule-time">今日，<span><?=date('h:i A', strtotime( date('Y-m-d').' '.$schedule_detail['time']))?></span></h1>
							<h3 class="col-12 text-right schedule-time">Today,<span><?=date('h:i A', strtotime( date('Y-m-d').' '.$schedule_detail['time']))?></span></h3>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row mx-0 schedule-layout mt-5">
			<div class="col-3 left-col">
				<div class="row m-0 align-items-center text-white layout">
					<div class="col-md-12 mb-5">
						<h1 class="text-white hall">
							<?=$hall['code']?>
						</h1>
					</div>
					<div class="col-md-12 d-flex">
						<div class="seat item selected text-center mr-2"></div>
						<div class="text">
							<p class="m-0">可選座位</p>
							<p>Available</p>
						</div>
					</div>
					<div class="col-md-12 d-flex">
						<div class="seat disability item text-center mr-2">
							<i class="fas fa-wheelchair"></i>
						</div>
						<div class="text">
							<p class="m-0">可選輪椅座位</p>
							<p>Wheelchair</p>
						</div>
					</div>
					<div class="col-md-12 d-flex">
						<div class="seat item text-center disabled mr-2">
							<i class="fas fa-times text-white"></i>
						</div>
						<div class="text">
							<p class="m-0">已售</p>
							<p>Sold</p>
						</div>
					</div>
				</div>
			</div>
			<div class="col-9 right-col">
				<div class="row justify-content-center text-white">
					<h3 class="text-center">銀 Screen 幕</h3>
				</div>
				<div class="row justify-content-center">
					<div class="curve"></div>
				</div>
				<div class="row schedule-layout justify-content-center">
					<div class="layout seats">
						<?php foreach ($seats as $id_row => $row) {?>
							<div class="row m-0 clearfix justify-content-center">
								<div class="seat seat-title mr-2">
									<h3><?=$row[$id_row]['title']?></h3>
								</div>
								<?php foreach ($row as $id_col => $col) {?>
									<?php if($col['enabled'] && $col['status']==1){ 
											if($col['disability']){ ?>
											<div class="seat disability item text-center">
												<i class="fas fa-wheelchair"></i>
											</div>
										<?php }else{ ?>
											<div class="seat item text-center">
												<h3><?=$col['label']?></h3>
											</div>
										<?php } }else{ ?>
										<div class="seat item text-center disabled">
											<i class="fas fa-times text-white"></i>
										</div>
									<?php } ?>
								<?php } ?>
								<div class="seat seat-title mr-2">
									<h3><?=$row[$id_row]['title']?></h3>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="main-warning <?= $warning_class ?>" style="width: 100%; height: 100%; display: flex; text-align:center;color:#FFFFFF;justify-content:center;align-items:center">
		<h1>No available movie schedule right now</h1>
	</div>
</section>

<!-- Modal warning -->
<div class="modal acx-modal-warning border-style close-bottom" data-backdrop="static" data-keyboard="false" id="modal_warning">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-body">
				<h1 class="text-white text-center">全院滿座</h1>
				<h3 class="text-white text-center text-uppercase">Full House</h3>
			</div>
		</div>
	</div>
</div>


<?php
    echo $this->Html->script('dashboard.js?v=1'); 
?>
<script type="text/javascript">
    $(document).ready(function() {
		DASHBOARD.action = '<?= $action ?>';
		DASHBOARD.counter = 1;
		DASHBOARD.is_full = <?= (isset($data_schedule['is_full'])) ? $data_schedule['is_full'] : 0; ?>;
		DASHBOARD.webroot = '<?= Environment::read('web.url_img') ?>';
		DASHBOARD.url_get_data_dashboard = '<?= Router::url(array('controller' => 'dashboardpage', 'action' => 'get_data_dashboard', 'api' => false), true); ?>';
        DASHBOARD.init_page();
    });
</script>