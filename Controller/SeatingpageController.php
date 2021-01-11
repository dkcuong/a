<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 */
class SeatingpageController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

	public function index($id = null) {
		$objScheduleDetail = ClassRegistry::init('Movie.ScheduleDetail');
		$option_schedule_details = array(
			'fields' => array(
				'ScheduleDetail.id',
				'ScheduleDetail.date',
				'ScheduleDetail.time',
				'Schedule.movie_type_id',
				'MovieLanguage.name',
				'MovieType.name',
				'Hall.code',
                'Movie.id',
				'Movie.duration',
				'Movie.language',
				'Movie.subtitle',
				'Movie.rating',
			),
			'contain' => array(
				'ScheduleDetailLayout',
				'ScheduleDetailTicketType' => array(
					'TicketType' => array(
						'TicketTypeLanguage' => array(
							'conditions' => array(
								'TicketTypeLanguage.language' => $this->lang18
							)
						)
					)
				)
			),
			'joins' => array(
				array(
					'alias' => 'Schedule',
					'table' => Environment::read('table_prefix') . 'schedules',
					'type' => 'left',
					'conditions' => array(
						'Schedule.id = ScheduleDetail.schedule_id',
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
				array(
					'alias' => 'Hall',
					'table' => Environment::read('table_prefix') . 'halls',
					'type' => 'left',
					'conditions' => array(
						'Hall.id = Schedule.hall_id',
					),
				),
			),
			'conditions' => array(
				'ScheduleDetail.id' => $id
			),
		);

		$data_schedule = $objScheduleDetail->find('first', $option_schedule_details);

		$schedule_detail_id = 0;
		if (!isset($data_schedule['ScheduleDetail']['id']) || empty($data_schedule['ScheduleDetail']['id'])) {
			$this->redirect(array('controller' => 'Ticketingpage','action' => 'index'));
		}

		$schedule_detail_id = $data_schedule['ScheduleDetail']['id'];
		$movie = $data_schedule;
		unset($movie['ScheduleDetailLayout']);
		unset($movie['ScheduleDetailTicketType']);

        $objMovie = ClassRegistry::init('Movie.Movie');
        $list_name_movie = $objMovie->get_movie_name($movie['Movie']['id']);

		$ticket_types = $data_schedule['ScheduleDetailTicketType'];

		$seat_layout = array();
		$is_full = true;
		foreach($data_schedule['ScheduleDetailLayout'] as $seat_array) 
		{
			$seat_layout[$seat_array['row_number']][$seat_array['column_number']] = array(
				'id' => $seat_array['id'],
				'title' => $this->Common->getCellID($seat_array['row_number']),
				'status' => $seat_array['status'],
				'label' => $seat_array['label'],
				'enabled' => $seat_array['enabled'],
				'disability' => $seat_array['is_disability_seat'],
				'blocked' => $seat_array['is_blocked_seat'],
			);

			if (($seat_array['enabled'] == 1) && ($seat_array['is_blocked_seat'] == 0) && ($seat_array['status'] == 1)) {
				$is_full = false;
			}

		}

		$staff = $this->Session->read('staff');
		$staff_id = $staff['Staff']['id'];

		$option_order = array(
			'contain' => array(
				'OrderDetail' => array(
				)
			),
			'conditions' => array(
				'staff_id' => $staff_id,
				'status' => 1,
				'is_paid' => 0,
				'void' => 0,
				'DATE_ADD(date, INTERVAL 15 MINUTE) >=' => date('Y-m-d H:i:s')
			),
			'order' => array('id' => 'desc'),
			'limit' => 1
		);

		$objOrder = ClassRegistry::init('Pos.Order');
		$data_order = $objOrder->find('first', $option_order);

		$selected_seat = array();
		if (isset($data_order['Order']['id']) && ($data_order['Order']['id'])) {
			$selected_seat = Hash::extract($data_order['OrderDetail'], '{n}.schedule_detail_layout_id');
		}

		$this->set(compact('schedule_detail_id', 'movie', 'seat_layout', 'ticket_types', 'data_order', 'selected_seat', 'is_full', 'list_name_movie'));
	}
}