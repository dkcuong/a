<?php
App::uses('MovieAppModel', 'Movie.Model');

class Schedule extends MovieAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'CreatedBy' => array(
			'className' => 'Administration.Administrator',
			'foreignKey' => 'created_by',
			'conditions' => '',
			'fields' => array('email','name'),
			'order' => ''
		),
		'UpdatedBy' => array(
			'className' => 'Administration.Administrator',
			'foreignKey' => 'updated_by',
			'conditions' => '',
			'fields' => array('email','name'),
			'order' => ''
		),
	);

	public $hasMany = array(
        'ScheduleDetail' => array(
			'className' => 'Movie.ScheduleDetail',
			'foreignKey' => 'schedule_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);

	public function get_schedule_detail_layout($data) {

		$conditions = [
			"ScheduleDetail.id" => $data['schedule_detail_id']
		];

		$option = array(
			'fields' => array(
				$this->alias.".id",
				$this->alias.".movies_movie_type_id",
				$this->alias.".hall_id",
			),	
			'contain' => array(
				'ScheduleDetail' => array(
					'fields' => array(
						'ScheduleDetail.id',
						'ScheduleDetail.schedule_id',
						'ScheduleDetail.date',
						'ScheduleDetail.time',
						'ScheduleDetail.capacity',
						'ScheduleDetail.attendance_rate',
					),
					'ScheduleDetailLayout' => array(
						'fields' => array(
							'ScheduleDetailLayout.id',
							'ScheduleDetailLayout.schedule_detail_id',
							'ScheduleDetailLayout.column_number',
							'ScheduleDetailLayout.row_number',
							'ScheduleDetailLayout.status',
							'ScheduleDetailLayout.order_time',
						)
					)	
				)
			),
			'joins' => array(
				array(
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'acx_schedule_details',
					'type' => 'inner',
					'conditions' => array(
						'ScheduleDetail.schedule_id = '.$this->alias.'.id',
					),
				),
			),
			'conditions' => $conditions,
		);

		$result = $this->find('all', $option);

		return $result;
	}


	public function get_schedule_detail($data) {

		$conditions = [
			$this->alias.".id" => $data['id']
		];

		$option = array(
			'fields' => array(
				$this->alias.".id",
				$this->alias.".movie_type_id",
				$this->alias.".hall_id",
			),	
			'contain' => array(
				'ScheduleDetail' => array(
					'fields' => array(
						'ScheduleDetail.id',
						'ScheduleDetail.schedule_id',
						'ScheduleDetail.date',
						'ScheduleDetail.time',
						'ScheduleDetail.capacity',
						'ScheduleDetail.attendance_rate',
					)	
				)
			),
			'conditions' => $conditions,
		);

		$result = $this->find('all', $option);

		return $result;
	}


	public function get_schedule($data, $parm_date = null, $is_today = true) {
		if (empty($parm_date) || $is_today) {
			$filter_date = date('Y-m-d');
			$current_time = date('Y-m-d H:i'); 
			$filter_time = date('H:i', strtotime($current_time.' -30 minutes'));
			$sql_where = " date = '".$filter_date."' and time >= '".$filter_time."' ";
		} else {
			$filter_date = date('Y-m-d', strtotime($parm_date));
			$sql_where = " date = '".$filter_date."' ";
		}
		
		$language = ( isset($data['language']) && in_array($data['language'] , ['eng', 'zho', 'chi']) ) 
					? $data['language'] : Environment::read('site.default_language');

		$prefix = Environment::read('database.prefix');

		$sqlstr_drop = "DROP TEMPORARY TABLE IF EXISTS " . $prefix . "mytable_details";
		$this->query($sqlstr_drop);

		$sqlstr = "CREATE TEMPORARY TABLE IF NOT EXISTS " . $prefix . "mytable_details AS (".
		"select schedule_id, count(id) as total_schedule " .
		"from " . $prefix . "schedule_details " .
		"where " . $sql_where .
		"group by schedule_id)";
		$this->query($sqlstr);

		$option = array(
			'fields' => array(
				"Schedule.id",
				"MytableDetail.total_schedule",
				"MoviesMovieType.id",
				"MoviesMovieType.movie_id",
				"MoviesMovieType.movie_type_id",
				"Movie.rating",
				"Movie.poster",
				"MovieType.name",
				'MovieLanguage.name'
			),	
			'joins' => array(
				array(
					'alias' => 'Schedule',
					'table' => Environment::read('table_prefix') . 'schedules',
					'type' => 'left',
					'conditions' => array(
						'Schedule.movie_id = MoviesMovieType.movie_id',
						'Schedule.movie_type_id = MoviesMovieType.movie_type_id',
					),
				),
				array(
					'alias' => 'MytableDetail',
					'table' => Environment::read('table_prefix') . 'mytable_details',
					'type' => 'left',
					'conditions' => array(
						'MytableDetail.schedule_id = Schedule.id',
					),
				),
				array(
					'alias' => 'Movie',
					'table' => Environment::read('table_prefix') . 'movies',
					'type' => 'left',
					'conditions' => array(
						'Movie.id = MoviesMovieType.movie_id',
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
						'MovieLanguage.language' => $language
					),
				),
			),
			'order' => array(
				'MovieLanguage.name' => 'ASC'
			),
			'group' => array('MoviesMovieType.id')
		);


		$objMoviesMovieType = ClassRegistry::init('Movie.MoviesMovieType');			

		$option['conditions'] = array(
			'MoviesMovieType.start_date <=' => $filter_date,
			'MoviesMovieType.end_date >=' => $filter_date,
			'MytableDetail.total_schedule >' => 0
		);

		$result = $objMoviesMovieType->find('all', $option);

		$format_list = array();
		foreach($result as $k=>$v) {
			$format_list[] = array(
				'id' => (isset($v['Schedule']['id']) && !empty($v['Schedule']['id'])) ? $v['Schedule']['id'] : 0,
				'title' => $v['MovieLanguage']['name'],
				'poster' => $v['Movie']['poster'],
				'rating' =>$v['Movie']['rating'],
				'movie_type' =>$v['MovieType']['name']
			);
		}
		

		return $format_list;
	}

	public function get_data_schedule_detail($lang, $movie_id, $movie_type_id, $parm_date = null, $is_today = true) {
		if (empty($parm_date) || $is_today) {
			$filter_date = date('Y-m-d');

			$current_time = date('Y-m-d H:i'); 
			$filter_time = date('H:i', strtotime($current_time.' -30 minutes'));

			$sql_where = array(
				"ScheduleDetail.date" => $filter_date,
				"ScheduleDetail.time >= " => $filter_time,
			);
		} else {
			$filter_date = date('Y-m-d', strtotime($parm_date));
			$sql_where = array(
				"ScheduleDetail.date" => $filter_date,
			);
		}

		$option_schedule_detail = array(
			'fields' => array(
				'Schedule.id',
				'ScheduleDetail.id',
				'ScheduleDetail.date',
				'ScheduleDetail.time',
				'Hall.code',
				'ScheduleDetailTicketType.price'
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
					'alias' => 'ScheduleDetailTicketType',
					'table' => Environment::read('table_prefix') . 'schedule_detail_ticket_types',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetail.id = ScheduleDetailTicketType.schedule_detail_id',
					),
				),
				array(
					'alias' => 'TicketType',
					'table' => Environment::read('table_prefix') . 'ticket_types',
					'type' => 'left',
					'conditions' => array(
						'TicketType.id = ScheduleDetailTicketType.ticket_type_id',
					),
				),
				array(
					'alias' => 'Hall',
					'table' => Environment::read('table_prefix') . 'halls',
					'type' => 'left',
					'conditions' => array(
						'Schedule.hall_id = Hall.id',
					),
				),
			),
			'conditions' => array(
				'Schedule.movie_id' => $movie_id,
				'Schedule.movie_type_id' => $movie_type_id,
				'TicketType.is_main' => 1,
				$sql_where
			),
			'order' => array('ScheduleDetail.date' => 'asc', 'ScheduleDetail.time' => 'asc')
		);

		$data_schedule_details = $this->find('all', $option_schedule_detail);

		$dbo = $this->getDatasource();
		$logs = $dbo->getLog();
		$lastLog = end($logs['log']);

		$schedule = array();
		foreach($data_schedule_details as $detail) {
			$schedule_detail = array();
			$schedule_detail['id'] = $detail['ScheduleDetail']['id'];
			$schedule_detail['time'] = date('H:i', strtotime($detail['ScheduleDetail']['time']));
			$schedule_detail['status'] = 1;
			$schedule_detail['price'] = number_format($detail['ScheduleDetailTicketType']['price'],1);
			$schedule_detail['hall'] = $detail['Hall']['code'];
			$schedule[] = $schedule_detail;
		}
		
		return $schedule;
	}

}


