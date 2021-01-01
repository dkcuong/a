<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 */
class DashboardpageController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

	public $total_slide = 4;

	public function index() {
		return $this->redirect( Router::url( array(
			'controller' => 'dashboardpage',
			'action' => 'tv1',
			'admin' => false
		), true));
	}

	public function tv1($counter = 1) {
		$this->layout = 'dashboard';

		/*
		if (isset($counter) && !empty($counter)) {
			$new_counter = $counter + 1;
		} else if (isset($counter) && $counter <= 0) {
			$new_counter = 2;
		}

		if ($new_counter > $this->total_slide) {
			$new_counter = 1;
		}
		*/

		$action = 'tv1';
		$data_schedule = $this->get_data_schedule($action, $counter);

		$show_no_schedule = false;
		if (!isset($data_schedule) || empty($data_schedule)) {
			$show_no_schedule = true;
		}

		$this->set(compact('action', 'data_schedule', 'show_no_schedule'));
	}

	public function tv2($counter = 1) {
		$this->layout = 'dashboard';

		/*
		if (isset($counter) && !empty($counter)) {
			$new_counter = $counter + 1;
		} else if (isset($counter) && $counter <= 0) {
			$new_counter = 2;
		}

		if ($new_counter > $this->total_slide) {
			$new_counter = 1;
		}
		*/

		$action = 'tv2';
		$data_schedule = $this->get_data_schedule($action, $counter);

		$show_no_schedule = false;
		if (!isset($data_schedule) || empty($data_schedule)) {
			$show_no_schedule = true;
		}

		$this->set(compact('action', 'data_schedule', 'show_no_schedule'));
	}

	public function get_data_dashboard() {
        $this->Api->init_result();
		
		if( $this->request->is('post') ) {
			$this->disableCache();
            $data = $this->request->data;
			
			$valid = true;
			$message = __('retrieve_data_successfully');
			$result_data = array();
			$status = 0;

			if (!isset($data['action']) || empty($data['action'])) {
				$message = __('missing_parameter') . __('action');
				$valid = false;
			} else if (!isset($data['counter']) || empty($data['counter'])) {
				$message = __('missing_parameter') . __('counter');
				$valid = false;
            } else {
				$action = $data['action'];
				$counter = $data['counter'];

				$result_data = $this->get_data_schedule($action, $counter);
			}

			$this->Api->set_result($valid, $message, $result_data);
		}
		
		$this->Api->output();		
	}

	public function get_data_schedule($action, $counter_parm) {
		/*
			1. find the hall based on counter
			2. find the schedule and schedule id based on that hall on this date and time bigger than now, and limit into 1,1 or 1,2
				also query all the data about this movie.	
		*/

		$counter = $counter_parm;
		if ($action == 'tv2') {
			$counter = $this->total_slide + $counter;
		}

		$hall_index = ceil($counter/2);

		$option_hall = array(
			'conditions' => array(
				'enabled' => 1
			),
			'order' => array('code' => 'asc'),
			'limit' => 1,
			'offset' => ($hall_index - 1)
		);

		$objHall = ClassRegistry::init('Cinema.Hall');
		$data_hall = $objHall->find('first', $option_hall);

		$hall_id = $data_hall['Hall']['id'];

		$option_schedule = array(
			'fields' => array(
				'Schedule.*',
				'ScheduleDetail.*',
				'Movie.*',
				'Hall.*',
				'MovieType.*',
			),
			'joins' => array(
				array(
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetail.schedule_id = Schedule.id',
					),
				),
				array(
					'alias' => 'Hall',
					'table' => Environment::read('table_prefix') . 'halls',
					'type' => 'left',
					'conditions' => array(
						'Hall.id = Schedule.hall_id',
					),
				),
				array(
					'alias' => 'Movie',
					'table' => Environment::read('table_prefix') . 'movies',
					'type' => 'left',
					'conditions' => array(
						'Movie.id = Schedule.movie_id',
					),
				),
				array(
					'alias' => 'MovieType',
					'table' => Environment::read('table_prefix') . 'movie_types',
					'type' => 'left',
					'conditions' => array(
						'MovieType.id = Schedule.movie_type_id',
					),
				),
			),
			'conditions' => array(
				'Schedule.hall_id' => $hall_id,
				'Hall.enabled' => 1,
				'ScheduleDetail.date' => date('Y-m-d'),
				'ScheduleDetail.time >=' => date('H:i'),
			),
			'order' => array(
				'ScheduleDetail.time' => 'asc'
			),
			'limit' => 2
		);

		$objSchedule = ClassRegistry::init('Movie.Schedule');
		$data_schedule = $objSchedule->find('all', $option_schedule);

		$deduction_factor = 0;
		if ($counter_parm > ($this->total_slide / 2)) {
			$deduction_factor = ($this->total_slide / 2);
		}

		$data_index = $counter_parm - $deduction_factor - 1;

		$data_result = array();
		if (isset($data_schedule[$data_index]['Schedule']['id']) && !empty($data_schedule[$data_index]['Schedule']['id'])) {
			$data_result = $data_schedule[$data_index];

			$data_result['ScheduleDetail']['time_display'] = date('h:i A', strtotime( date('Y-m-d').' '.$data_result['ScheduleDetail']['time']));

			$option_schedule_layout = array(
				'conditions' => array(
					'schedule_detail_id' => $data_result['ScheduleDetail']['id'],
				),
				'order' => array(
					'column_number' => 'asc',
					'row_number' => 'asc'
				),
			);

			$objScheduleLayout = ClassRegistry::init('Movie.ScheduleDetailLayout');
			$data_schedule_layout = $objScheduleLayout->find('all', $option_schedule_layout);

			$seat_layout = array();
			$data_result['is_full'] = 1;
			foreach($data_schedule_layout as $seat_array) 
			{
				$status = $seat_array['ScheduleDetailLayout']['status'];
				if ($seat_array['ScheduleDetailLayout']['is_blocked_seat']) {
					$status = 2;
				}
				$seat_layout[$seat_array['ScheduleDetailLayout']['row_number']][$seat_array['ScheduleDetailLayout']['column_number']] = array(
					'id' => $seat_array['ScheduleDetailLayout']['id'],
					'title' => $this->Common->getCellID($seat_array['ScheduleDetailLayout']['row_number']),
					'status' => $status,
					'label' => $seat_array['ScheduleDetailLayout']['label'],
					'enabled' => $seat_array['ScheduleDetailLayout']['enabled'],
					'disability' => $seat_array['ScheduleDetailLayout']['is_disability_seat'],
				);

				if (($seat_array['ScheduleDetailLayout']['enabled'] == 1) && 
					($status == 1)) {
					$data_result['is_full'] = 0;
				}
			}


			$data_result['seats'] = $seat_layout;
			
			$option_movie_language = array(
				'conditions' => array(
					'movie_id' => $data_result['Movie']['id'],
				),
			);

			$objMovieLang = ClassRegistry::init('Movie.MovieLanguage');
			$data_movie_lang = $objMovieLang->find('all', $option_movie_language);

			$data_result['MovieLanguage'] = $data_movie_lang;
		}

		return $data_result;
	}
}