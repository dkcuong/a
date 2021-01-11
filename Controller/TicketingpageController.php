<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 */
class TicketingpageController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

	public function index() {

		$data   = $this->request->query;

		$objSetting = ClassRegistry::init('Setting.Setting');
		$preorder_day = $objSetting->get_value('preorder-day');

		if ($preorder_day <= 0) {
			$preorder_day = 3;
		}

		$current_date = date('Y-m-d');
		$list = array();
		for ($i = 0; $i < $preorder_day; $i++) {
			$new_date = date('Y-m-d', strtotime($current_date . ' +' . $i . ' days'));
			$label = ($i == 0) ? 'Today - ' . date('d/m/Y', strtotime($new_date)) : date('d/m/Y', strtotime($new_date)) . date('(D)', strtotime($new_date));
			$list[$new_date] = array('label' => $label);
		}

		$staff = $this->Session->read('staff');
		$user_roles = $staff['Staff']['role'];
		$is_manager = ($staff['Staff']['role'] == 'manager') ? 1 : 0;

		$objSchedule = ClassRegistry::init('Movie.Schedule');
		$data_schedule =  $objSchedule->get_schedule($data, $current_date, $is_manager, true);

        $list_movie_id = Hash::extract($data_schedule, '{n}.movie_id');
        // get movie name
        $objMovie = ClassRegistry::init('Movie.Movie');
        $list_name_movie = $objMovie->get_movie_name($list_movie_id);

		$schedule['Schedule'] = $list;
		$schedule['Schedule'][$current_date]['Movie'] = $data_schedule;

		$current_schedule = $schedule['Schedule'][$current_date];
		$schedule_json = json_encode($schedule['Schedule']);

		$display_link_signout = true;

		$this->set(compact('schedule', 'current_schedule', 'schedule_json', 'display_link_signout', 'list_name_movie'));
	}
}