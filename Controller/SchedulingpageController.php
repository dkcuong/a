<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 */
class SchedulingpageController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

	public function index($id = null, $active_date = null) {

		$current_date = date('Y-m-d');
		if (empty($active_date)) {
			$active_date = $current_date;
		}

		$objSetting = ClassRegistry::init('Setting.Setting');
		$preorder_day = $objSetting->get_value('preorder-day');

		if ($preorder_day <= 0) {
			$preorder_day = 3;
		}

		$list = array();
		for ($i = 0; $i < $preorder_day; $i++) {
			$new_date = date('Y-m-d', strtotime($current_date . ' +' . $i . ' days'));
			$label = ($i == 0) ? 'Today - ' . date('d/m/Y', strtotime($new_date)) : date('d/m/Y', strtotime($new_date)) . date('(D)', strtotime($new_date));
			$list[$new_date] = array('label' => $label);
		}

		$objSchedule = ClassRegistry::init('Movie.Schedule');
		$option_schedule = array(
			'fields' => array(
				'Schedule.id',
				'Schedule.movie_id',
				'Schedule.movie_type_id',
				'MovieLanguage.name',
				'MovieType.name',
			),
			'joins' => array(
				array(
					'alias' => 'Movie',
					'table' => Environment::read('table_prefix') . 'movies',
					'type' => 'left',
					'conditions' => array(
						'Movie.id = Schedule.movie_id',
					),
				),
				array(
					'alias' => 'MoviesMovieType',
					'table' => Environment::read('table_prefix') . 'movies_movie_types',
					'type' => 'left',
					'conditions' => array(
						'MoviesMovieType.movie_id = Movie.id',
						'MoviesMovieType.movie_type_id = Schedule.movie_type_id',
					),
				),
				array(
					'alias' => 'MovieType',
					'table' => Environment::read('table_prefix') . 'movie_types',
					'type' => 'left',
					'conditions' => array(
						'MovieType.id = MoviesMovieType.movie_type_id',
					),
				),
				array(
					'alias' => 'MovieLanguage',
					'table' => Environment::read('table_prefix') . 'movie_languages',
					'type' => 'left',
					'conditions' => array(
						'MovieLanguage.movie_id = Movie.id',
						'MovieLanguage.language' => $this->lang18
					),
				),
			),
			'conditions' => array(
				'Schedule.id' => $id,
				'MovieType.id >' => 0
			),
		);

		$data_schedule = $objSchedule->find('first', $option_schedule);

		if (!isset($data_schedule['Schedule']['id']) || empty($data_schedule['Schedule']['id'])) {
			$this->redirect(array('controller' => 'Ticketingpage','action' => 'index'));
		}

		$schedule = array();
		$schedule['Movie']['name'] = strtoupper($data_schedule['MovieLanguage']['name'] . "(".$data_schedule['MovieType']['name'] .")");
		$schedule['Schedule'] = $list;

		$is_today = ($active_date == $current_date) ? 1 : 0;

		$movie_id = $data_schedule['Schedule']['movie_id'];
		$movie_type_id = $data_schedule['Schedule']['movie_type_id'];

		$staff = $this->Session->read('staff');
		$user_roles = $staff['Staff']['role'];
		$is_manager = ($staff['Staff']['role'] == 'manager') ? 1 : 0;

		$data_schedule_details = $objSchedule->get_data_schedule_detail(
					$this->lang18, 
					$movie_id ,
					$movie_type_id,
					$active_date,
					$is_manager,
					$is_today
				);

		// $data_detail = $data_schedule_details[$active_date]['Schedule'];
		$data_detail = $data_schedule_details;
		$schedule['Schedule'][$active_date]['Schedule'] = $data_detail;

		$current_schedule = $schedule['Schedule'][$active_date];
		$schedule_json = json_encode($schedule['Schedule']);


		$this->set(compact('schedule', 'current_schedule', 'schedule_json', 'active_date', 'movie_id', 'movie_type_id'));	
	}
}