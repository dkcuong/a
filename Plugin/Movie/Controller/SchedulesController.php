<?php
App::uses('MovieAppController', 'Movie.Controller');

class SchedulesController extends MovieAppController {

	public $components = array('Paginator');
	private $model = 'Schedule';

	private $filter = array(

	);
	private $rule = array(
		1 => array('required'),
		2 => array('required'),
		3 => array('required'),
		4 => array('required','enum'),
	);
	private $rule_spec = array(
		4 => array('N', 'Y', 'y', 'n')
	);

	private $api_rules = array(
		// 'token' => array(
		// 	'required' => true,
		// 	'type' => 'string',
		// ),
		'language' => array(
			'required' => true,
			'type' => 'string',
			'in' => ['zho', 'eng', 'chi'],
		),
	);

	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('schedule', 'item_title'));
	}

	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;

		if (isset($data_search) && !empty($data_search['date']))
		{
			$date = $data_search['date'];
			unset($data_search['date']);
		}
		else
		{
			$date = null;
		}

		$conditions = $this->Common->get_filter_conditions($data_search, $model, null, $this->filter);

		if (!is_null($date))
		{
			$data_search['date'] = $date;
		}
		if ($data_search)
		{
			if( isset($data_search['date']) && !empty($data_search['date']) )
			{
				$objScheduleDetails = ClassRegistry::init('Movie.ScheduleDetail');
				$option = array(
					'fields' => array('schedule_id'),
					'recursive' => -1,
					'conditions' => array(
						'date =' => $data_search['date']
					)
				);
				$scheduleDetails = $objScheduleDetails->find('all', $option);
				$schedule_ids = Hash::extract( $scheduleDetails, "{n}.ScheduleDetail.schedule_id" );

				if (empty($schedule_ids))
				{
					// return nothing
					$conditions["Schedule.id <"] = 0;
				}
				else
				{
					$conditions["Schedule.id IN"] = $schedule_ids;
				}
			}
		}

		$this->Paginator->settings = array(
			'fields' => array(
				$model.".*", 
				'ScheduleDetail.date',
				'MovieType.name',
				'Movie.code',
				'Hall.code',
                'GROUP_CONCAT(DISTINCT(MovieLanguage.name) SEPARATOR " ,,,,, ") as movie_name'
			),
			'joins' => array(
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
					'alias' => 'MoviesMovieType',
					'table' => Environment::read('table_prefix') . 'movies_movie_types',
					'type' => 'left',
					'conditions' => array(
						'MoviesMovieType.movie_id = Schedule.movie_id',
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
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'Schedule.id = ScheduleDetail.schedule_id',
					),
				),
                array(
                    'alias' => 'MovieLanguage',
                    'table' => Environment::read('table_prefix') . 'movie_languages',
                    'type' => 'left',
                    'conditions' => array(
                        'MovieLanguage.movie_id = Movie.id',
                    ),
                ),
			),
			'conditions' => array(
				'ScheduleDetail.schedule_id > 0',
				$conditions
			),
            'limit' => Environment::read('web.limit_record'),
			'order' => array(
				'ScheduleDetail.date' => 'DESC',
				'Movie.code' => 'ASC'
			),
			'group' => array('Schedule.id', 'ScheduleDetail.date'),	
		);
		
		$objMovie = ClassRegistry::init('Movie.Movie');
		$movies = $objMovie->find('list', array(
            'fields' => array('Movie.id', 'MovieLanguage.name'),
			'joins' => array(
				array(
					'alias' => 'MovieLanguage',
					'table' => Environment::read('table_prefix') . 'movie_languages',
					'type' => 'left',
					'conditions' => array(
						'Movie.id = '.'MovieLanguage.movie_id',
						'MovieLanguage.language' => $this->lang18
					),
				),
			),
		));

		$objHall = ClassRegistry::init('Cinema.Hall');
		$halls = $objHall->find('list', array(
            'fields' => array('id', 'code'),
		));
        $lang18 = $this->lang18;
        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'data_search', 'movies', 'halls', 'lang18' ));
	}

    public function admin_past_index() {
        $data_search = $this->request->query;
        $model = $this->model;
        $now = date('Y-m-d');
        $this->set('title_for_layout', __d('schedule', 'past_item_title'));

        if (isset($data_search) && !empty($data_search['date']))
        {
            $date = $data_search['date'];
            unset($data_search['date']);
        }
        else
        {
            $date = null;
        }


        $conditions = $this->Common->get_filter_conditions($data_search, $model, null, $this->filter);
        $conditions['DATE(ScheduleDetail.date) <'] = $now;

        if (!is_null($date))
        {
            $data_search['date'] = $date;
        }
        if ($data_search)
        {
            if( isset($data_search['date']) && !empty($data_search['date']) )
            {
                $objScheduleDetails = ClassRegistry::init('Movie.ScheduleDetail');
                $option = array(
                    'fields' => array('schedule_id'),
                    'recursive' => -1,
                    'conditions' => array(
                        'date =' => $data_search['date']
                    )
                );
                $scheduleDetails = $objScheduleDetails->find('all', $option);
                $schedule_ids = Hash::extract( $scheduleDetails, "{n}.ScheduleDetail.schedule_id" );

                if (empty($schedule_ids))
                {
                    // return nothing
                    $conditions["Schedule.id <"] = 0;
                }
                else
                {
                    $conditions["Schedule.id IN"] = $schedule_ids;
                }
            }
        }

        $this->Paginator->settings = array(
            'fields' => array(
                $model.".*",
                'ScheduleDetail.date',
                'MovieType.name',
                'Movie.code',
                'Hall.code',
                'GROUP_CONCAT(DISTINCT(MovieLanguage.name) SEPARATOR " ,,,,, ") as movie_name'
            ),
            'joins' => array(
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
                    'alias' => 'MoviesMovieType',
                    'table' => Environment::read('table_prefix') . 'movies_movie_types',
                    'type' => 'left',
                    'conditions' => array(
                        'MoviesMovieType.movie_id = Schedule.movie_id',
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
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'Schedule.id = ScheduleDetail.schedule_id',
                    ),
                ),
                array(
                    'alias' => 'MovieLanguage',
                    'table' => Environment::read('table_prefix') . 'movie_languages',
                    'type' => 'left',
                    'conditions' => array(
                        'MovieLanguage.movie_id = Movie.id',
                    ),
                ),
            ),
            'conditions' => array(
                'ScheduleDetail.schedule_id > 0',
                $conditions
            ),
            'limit' => Environment::read('web.limit_record'),
            'order' => array(
                'ScheduleDetail.date' => 'DESC',
                'Movie.code' => 'ASC'
            ),
            'group' => array('Schedule.id', 'ScheduleDetail.date'),
        );

        $objMovie = ClassRegistry::init('Movie.Movie');
        $movies = $objMovie->find('list', array(
            'fields' => array('Movie.id', 'MovieLanguage.name'),
            'joins' => array(
                array(
                    'alias' => 'MovieLanguage',
                    'table' => Environment::read('table_prefix') . 'movie_languages',
                    'type' => 'left',
                    'conditions' => array(
                        'Movie.id = '.'MovieLanguage.movie_id',
                        'MovieLanguage.language' => $this->lang18
                    ),
                ),
            ),
        ));

        $objHall = ClassRegistry::init('Cinema.Hall');
        $halls = $objHall->find('list', array(
            'fields' => array('id', 'code'),
        ));
        $lang18 = $this->lang18;
        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'data_search', 'movies', 'halls' ,'lang18'));
    }

	public function admin_add() {
		$model = $this->model;

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

			$schedule = $data[$model];
			unset($schedule['ScheduleDetail']);
			//$schedule_detail = $data['ScheduleDetail'];

			$hall_id = $data[$model]['hall_id'];
			$option_hall = array(
				'fields' => array(
					'column_number',
					'row_number',
					'enabled',
					'is_disability_seat',
					'is_blocked_seat',
				),
				'conditions' => array(
					'hall_id' => $hall_id
				),
				'order' => array(
					'row_number' => 'ASC',
					'column_number' => 'ASC',
				),
			);
			$objHallDetail = ClassRegistry::init('Cinema.HallDetail');
			$hall_layout = $objHallDetail->find('all', $option_hall);

			//find the schedule, if exists, we dont need to insert schedule anymore, 
			//just insert the schedule detail is enough...
			$option_schedule = array(
				'conditions' => array(
					'hall_id' => $hall_id,
					'movie_type_id' => $data[$model]['movie_type_id'],
					'movie_id' => $data[$model]['movie_id'],
				),
			);
			$data_schedule = $this->$model->find('first', $option_schedule);
			if (isset($data_schedule[$model]['id']) && !empty($data_schedule[$model]['id'])) {
				$schedule['id'] = $data_schedule[$model]['id'];
			}

			$dbo = $this->$model->getDataSource();
			$dbo->begin();
			$valid = true;
			//saving schedule
			if ($this->$model->save($schedule)) {
				
				$schedule_id = $this->$model->id;
				foreach($data['ScheduleDetail'] as &$detail) {
					//saving Schedule Detail and Schedule Detail Ticket Type
					$detail['schedule_id'] = $schedule_id;
					$detail['date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data[$model]['date'])));

					$schedule_detail_layout = array();
					$label = 0;
					$row_number = 0;
					$capacity = 0;
					$max_label = array();
					$max_label[$row_number] = 0;
					foreach($hall_layout as $layout) {
						if($row_number <> $layout['HallDetail']['row_number']) {
							$max_label[$layout['HallDetail']['row_number']] = 0;
						}
						if ($layout['HallDetail']['enabled'] == 1) {
							$max_label[$layout['HallDetail']['row_number']] = $max_label[$layout['HallDetail']['row_number']] + 1;
						}
						$row_number = $layout['HallDetail']['row_number'];
					}

					$row_number = 0;
					$label = $max_label[$row_number] + 1;
					foreach($hall_layout as $layout) {
						$seat = array();
						if($row_number <> $layout['HallDetail']['row_number']) {
							$label = $max_label[$layout['HallDetail']['row_number']] + 1;
						}
						if ($layout['HallDetail']['enabled'] == 1) {
							$label--;
							$capacity++;
						}
						$seat['column_number'] = $layout['HallDetail']['column_number'];
						$seat['row_number'] = $layout['HallDetail']['row_number'];
						$seat['label'] = ($layout['HallDetail']['enabled'] == 1) ? $label : '';
						$seat['status'] = $layout['HallDetail']['enabled'];
						$seat['enabled'] = $layout['HallDetail']['enabled'];
						$seat['is_disability_seat'] = $layout['HallDetail']['is_disability_seat'];
						$seat['is_blocked_seat'] = $layout['HallDetail']['is_blocked_seat'];
						$schedule_detail_layout[] = $seat;
						$row_number = $layout['HallDetail']['row_number'];
					}

					$detail['capacity'] = $capacity;

					$schedule_detail = array();
					$schedule_detail['ScheduleDetail'] = $detail;
					$schedule_detail['ScheduleDetailTicketType'] = $detail['ScheduleDetailTicketType'];
					unset($schedule_detail['ScheduleDetail']['ScheduleDetailTicketType']);

					if ($this->$model->ScheduleDetail->saveAll($schedule_detail)) {
						//copy layout from hall details
						$schedule_detail_id = $this->$model->ScheduleDetail->id;


						/*
						//if performance is slow because of copying layout from hall details to time schedule
						//then try to use raw query, open this remark, and remark the method to insert the seat one by one
						$prefix = Environment::read('database.prefix');

						$strsql = "set @label := 0";
						$this->$model->query($strsql);

						$strsql = "set @row_number := 0";
						$this->$model->query($strsql);

						$sqlstr = "INSERT INTO " . $prefix . "schedule_detail_layouts(schedule_detail_id, column_number, row_number, label, status, updated, created) ".
								  "select schedule_detail_id, column_number, row_number, label, status, now(), now() " .
								  "from (" .
										"select " . $schedule_detail_id . " as schedule_detail_id, column_number, row_number, if(@row_number <> row_number, @label := 0, 0) as tmp, " .
												"if(enabled = 1, @label := @label + 1, '') as label, enabled, " .
												"(@row_number := row_number) as tmp_col_number, 0 as status " . 
										"from " . $prefix . "hall_details a " .
										"where hall_id = " . $hall_id . " " .
										"order by row_number asc, column_number) a";
				
						$myvalue = $this->$model->query($sqlstr);
						*/

						foreach($schedule_detail_layout as &$detail_layout) {
							$detail_layout['schedule_detail_id'] = $schedule_detail_id;
						}

						if ($this->$model->ScheduleDetail->ScheduleDetailLayout->saveAll($schedule_detail_layout)) {
							//do nothing
						} else {
							$valid = false;
							$dbo->rollback();
							$this->Session->setFlash(__('data_is_not_saved') . ' ' . 'schedule_detail_layout_data_invalid', 'flash/error');
							break;
						}

					} else {
						$valid = false;
						$dbo->rollback();
						$this->Session->setFlash(__('data_is_not_saved') . ' ' . 'schedule_detail_data_invalid', 'flash/error');
						break;
					}
				}

				if ($valid) {
					$dbo->commit();
					$this->Session->setFlash(__('data_is_saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}
			} else {
				$dbo->rollback();
				$this->Session->setFlash(__('data_is_not_saved') . ' ' . 'schedule_data_invalid', 'flash/error');
			}

		}


		$this->set(compact('model', 'data_search'));

		$this->load_data();
	}

	public function admin_view($id, $date) {
		$model = $this->model;

		$options = array(
			'fields' => array(
				$model.".*", 
				'ScheduleDetail.date',
				'MovieType.name',
				'Movie.code',
				'Hall.code',
			),
			'joins' => array(
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
					'alias' => 'MoviesMovieType',
					'table' => Environment::read('table_prefix') . 'movies_movie_types',
					'type' => 'left',
					'conditions' => array(
						'MoviesMovieType.movie_id = Schedule.movie_id',
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
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'Schedule.id = ScheduleDetail.schedule_id',
					),
				),
			),
			'contain' => array(
				'UpdatedBy',
				'CreatedBy'
			),
			'conditions' => array(
				'ScheduleDetail.schedule_id > 0',
				'ScheduleDetail.date' => $date,
				'Schedule.id' => $id,
			),
            'limit' => Environment::read('web.limit_record'),
			'group' => array('Schedule.id', 'ScheduleDetail.date'),	
		);
		$model_data = $this->$model->find('first', $options);

		if (!$model_data) {
			throw new NotFoundException(__('invalid_data'));
		}

		$option_detail = array(
			'fields' => array(
				'ScheduleDetail.*',
			),
			'contain' => array(
				'ScheduleDetailTicketType' => array(
					'TicketType'
				),
				'ScheduleDetailLayout'
			),
			'conditions' => array(
				'ScheduleDetail.schedule_id' => $id,
				'ScheduleDetail.date' => $model_data['ScheduleDetail']['date'],
			),
		);
		$data_detail = $this->$model->ScheduleDetail->find('all', $option_detail);

		foreach($data_detail as &$detail) {
			$seat_array = array();
			foreach($detail['ScheduleDetailLayout'] as $seat_layout) {
				$seat_array[$seat_layout['row_number']][$seat_layout['column_number']] = array(
					'title' => $this->Common->getCellID($seat_layout['row_number']),
					'status' => $seat_layout['status'],
					'label' => $seat_layout['label'],
					'enabled' => $seat_layout['enabled'],
					'vegetable' => $seat_layout['is_disability_seat'],
					'blocked' => $seat_layout['is_blocked_seat']
				);
			}
			unset($detail['ScheduleDetailLayout']);
			$detail['ScheduleDetailLayout'] = $seat_array;
		}

		$this->set('dbdata', $model_data);
        $this->set(compact('model', 'data_detail'));
	}

    public function admin_past_view($id, $date) {
//        $id= 14;
//        $date = '2020-11-29';
        $model = $this->model;
        $this->set('title_for_layout', __d('schedule', 'past_item_title'));
        $options = array(
            'fields' => array(
                $model.".*",
                'ScheduleDetail.date',
                'MovieType.name',
                'Movie.code',
                'Hall.code',
            ),
            'joins' => array(
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
                    'alias' => 'MoviesMovieType',
                    'table' => Environment::read('table_prefix') . 'movies_movie_types',
                    'type' => 'left',
                    'conditions' => array(
                        'MoviesMovieType.movie_id = Schedule.movie_id',
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
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'Schedule.id = ScheduleDetail.schedule_id',
                    ),
                ),
            ),
            'contain' => array(
                'UpdatedBy',
                'CreatedBy'
            ),
            'conditions' => array(
                'ScheduleDetail.schedule_id > 0',
                'ScheduleDetail.date' => $date,
                'Schedule.id' => $id,
            ),
            'limit' => Environment::read('web.limit_record'),
            'group' => array('Schedule.id', 'ScheduleDetail.date'),
        );
        $model_data = $this->$model->find('first', $options);

        if (!$model_data) {
            throw new NotFoundException(__('invalid_data'));
        }

        $option_detail = array(
            'fields' => array(
                'ScheduleDetail.*',
            ),
            'contain' => array(
                'ScheduleDetailTicketType' => array(
                    'TicketType'
                ),
                'ScheduleDetailLayout',
            ),
            'conditions' => array(
                'ScheduleDetail.schedule_id' => $id,
                'ScheduleDetail.date' => $model_data['ScheduleDetail']['date'],
            ),
        );
        $data_detail = $this->$model->ScheduleDetail->find('all', $option_detail);
        $list_schedule_detail = Hash::extract( $data_detail, "{n}.ScheduleDetail.id" );

        foreach($data_detail as &$detail) {
            $seat_array = array();
            foreach($detail['ScheduleDetailLayout'] as $seat_layout) {
                $seat_array[$seat_layout['row_number']][$seat_layout['column_number']] = array(
                    'title' => $this->Common->getCellID($seat_layout['row_number']),
                    'status' => $seat_layout['status'],
                    'label' => $seat_layout['label'],
                    'enabled' => $seat_layout['enabled'],
                    'vegetable' => $seat_layout['is_disability_seat'],
                    'blocked' => $seat_layout['is_blocked_seat']
                );
            }
            unset($detail['ScheduleDetailLayout']);
            $detail['ScheduleDetailLayout'] = $seat_array;
        }

        // get info transaction
        $past_schedule_info = $this->get_sales_past_schedule($list_schedule_detail);
        $this->set('dbdata', $model_data);
        $this->set(compact('model', 'data_detail', 'past_schedule_info'));
    }

    public function get_sales_past_schedule($list_schedule_detail) {
        $result = array();

        $objOrder= ClassRegistry::init('Pos.Order');
        // get all schedule detail
        $conditions = array(
            'Order.schedule_detail_id' => $list_schedule_detail,
            'Order.status' => 3,
            'Order.void' => 0
        );

        $option = array(
            'fields' => array(
                "Order.*",
                "OrderDetail.*",
                "TicketType.*"
//                "Staff.*",
//                "OrderPaymentLog.*",
//                "Member.*",
//                "group_concat(PaymentMethod.name) as payment_method_group"
            ),
            'conditions' => array($conditions),
            'joins' => array(
                array(
                    'alias' => 'OrderDetail',
                    'table' => Environment::read('table_prefix') . 'order_details',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetail.order_id = Order.id'
                    ),
                ),
                array(
                    'alias' => 'ScheduleDetailTicketType',
                    'table' => Environment::read('table_prefix') . 'schedule_detail_ticket_types',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetailTicketType.id = OrderDetail.schedule_detail_ticket_type_id',
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
            ),
            //'limit' => Environment::read('web.limit_record'),
            'order' => array('Order.date' => 'DESC'),
//            'limit' => $limit,
//            'page' => $page
        );

        $list_order =$objOrder->find('all', $option);

        $list_total_amount = array();
        $result = array();
        foreach ($list_order as $k=>$v) {
            $key_schedule_detail_id = $v['Order']['schedule_detail_id'];
            $key_order = $v['Order']['id'];


            $result[$key_schedule_detail_id]['total'][$key_order] = $v['Order']['grand_total'];
            $code_ticket_type = $v['TicketType']['code'];

            if (!isset($result[$key_schedule_detail_id][$code_ticket_type])) {
                $result[$key_schedule_detail_id][$code_ticket_type] = 1;
            } else {
                $result[$key_schedule_detail_id][$code_ticket_type] += 1;
            }

            /*if ($code_ticket_type == 'A') {
                if (!isset($result[$key_schedule_detail_id]['total_adult'])) {
                    $result[$key_schedule_detail_id]['total_adult'] = 1;
                } else {
                    $result[$key_schedule_detail_id]['total_adult'] += 1;
                }
            }
            if ($code_ticket_type == 'S') {
                if (!isset($result[$key_schedule_detail_id]['total_student'])) {
                    $result[$key_schedule_detail_id]['total_student'] = 1;
                } else {
                    $result[$key_schedule_detail_id]['total_student'] += 1;
                }
            }
            if ($code_ticket_type == 'SE') {
                if (!isset($result[$key_schedule_detail_id]['total_senior'])) {
                    $result[$key_schedule_detail_id]['total_senior'] = 1;
                } else {
                    $result[$key_schedule_detail_id]['total_senior'] += 1;
                }
            }
            if ($code_ticket_type == 'C') {
                if (!isset($result[$key_schedule_detail_id]['total_child'])) {
                    $result[$key_schedule_detail_id]['total_child'] = 1;
                } else {
                    $result[$key_schedule_detail_id]['total_child'] += 1;
                }
            }*/
        }
        foreach ($result as $k=>$v) {
            $result[$k]['total'] = array_sum($v['total']);
        }

        return $result;
    }

	public function admin_edit($id = null, $date) {
		$model = $this->model;

		$options = array(
			'contain' => array(
				'ScheduleDetail' => array(
					'fields' => array(
						'ScheduleDetail.date',
						'ScheduleDetail.time',
						'ScheduleDetail.id'
					),
					'conditions' => array(
						'ScheduleDetail.date' => $date
					)
				),
				'UpdatedBy',
				'CreatedBy'
			),
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
			'recursive' => 1
		);
		$old_item = $this->$model->find('first', $options);

		if (!$old_item || !$old_item['ScheduleDetail']) {
			throw new NotFoundException(__('invalid_data'));
		}

		foreach ($old_item['ScheduleDetail'] as &$schedule_detail_old) {
			$option_ticket_type = array(
				'fields' => array(
					'TicketType.id', 
					'TicketTypeLanguage.name',
					'ScheduleDetailTicketType.id',
					'ScheduleDetailTicketType.price',
					'ScheduleDetailTicketType.price_hkbo',
				),
				'joins' => array(
					array(
						'alias' => 'TicketTypeLanguage',
						'table' => Environment::read('table_prefix') . 'ticket_type_languages',
						'type' => 'left',
						'conditions' => array(
							'TicketTypeLanguage.ticket_type_id = TicketType.id',
							'TicketTypeLanguage.language' => $this->lang18
						),
					),
					array(
						'alias' => 'ScheduleDetailTicketType',
						'table' => Environment::read('table_prefix') . 'schedule_detail_ticket_types',
						'type' => 'left',
						'conditions' => array(
							'ScheduleDetailTicketType.ticket_type_id = TicketType.id',
							'ScheduleDetailTicketType.schedule_detail_id' => $schedule_detail_old['id'],
						),
					),
				),
				'conditions' => array(
					'enabled' => 1
				)
			);

			$objTicketType = ClassRegistry::init('Pos.TicketType');
			$data_ticket_type = $objTicketType->find('all', $option_ticket_type);
			$schedule_detail_old['ScheduleDetailTicketType'] = $data_ticket_type;
		}

		$hall_id = $old_item['Schedule']['hall_id'];

		// $seat_layout = array();
		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;
			// $seat_layout = $data['HallDetail'];

			$schedule = $data[$model];

			$hall_layout = array();
			
			$dbo = $this->$model->getDataSource();
			$dbo->begin();
			$valid = true;
			try {
				$schedule_id = $schedule['id'];

				if (isset($data['remove_schedule_detail']) && !empty($data['remove_schedule_detail'])) {
					$condition_delete = array(
						'ScheduleDetail.id IN' => $data['remove_schedule_detail']
					);
					if (!$this->$model->ScheduleDetail->deleteAll($condition_delete, false)) {
						$valid = false;
						$dbo->rollback();
						$this->Session->setFlash(__('data_is_not_saved') . ' ' . 'unable_to_delete_schedule_detail', 'flash/error');
					}
				}


				if ($valid) {

					$option_hall = array(
						'fields' => array(
							'column_number',
							'row_number',
							'enabled',
							'is_disability_seat',
							'is_blocked_seat',
						),
						'conditions' => array(
							'hall_id' => $hall_id
						),
						'order' => array(
							'row_number' => 'ASC',
							'column_number' => 'ASC',
						),
					);
					$objHallDetail = ClassRegistry::init('Cinema.HallDetail');
					$hall_layout = $objHallDetail->find('all', $option_hall);


					
					foreach($data['ScheduleDetail'] as &$detail) {
						//saving Schedule Detail and Schedule Detail Ticket Type
						$detail['date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data[$model]['date'])));

						$copy_layout = false;
						if (!isset($detail['id'])) {
							$detail['schedule_id'] = $schedule_id;
							$copy_layout = true;
						
							$schedule_detail_layout = array();
							$label = 0;
							$row_number = 0;
							$capacity = 0;
							$max_label = array();
							$max_label[$row_number] = 0;
							foreach($hall_layout as $layout) {
								if($row_number <> $layout['HallDetail']['row_number']) {
									$max_label[$layout['HallDetail']['row_number']] = 0;
								}
								if ($layout['HallDetail']['enabled'] == 1) {
									$max_label[$layout['HallDetail']['row_number']] = $max_label[$layout['HallDetail']['row_number']] + 1;
								}
								$row_number = $layout['HallDetail']['row_number'];
							}

							$row_number = 0;
							$label = $max_label[$row_number] + 1;
							foreach($hall_layout as $layout) {
								$seat = array();
								if($row_number <> $layout['HallDetail']['row_number']) {
									$label = $max_label[$layout['HallDetail']['row_number']] + 1;
								}
								if ($layout['HallDetail']['enabled'] == 1) {
									$label--;
									$capacity++;
								}
								$seat['column_number'] = $layout['HallDetail']['column_number'];
								$seat['row_number'] = $layout['HallDetail']['row_number'];
								$seat['label'] = ($layout['HallDetail']['enabled'] == 1) ? $label : '';
								$seat['status'] = $layout['HallDetail']['enabled'];
								$seat['enabled'] = $layout['HallDetail']['enabled'];
								$seat['is_disability_seat'] = $layout['HallDetail']['is_disability_seat'];
								$seat['is_blocked_seat'] = $layout['HallDetail']['is_blocked_seat'];
								$schedule_detail_layout[] = $seat;
								$row_number = $layout['HallDetail']['row_number'];
							}
							$detail['capacity'] = $capacity;
						}

						$schedule_detail = array();
						$schedule_detail['ScheduleDetail'] = $detail;
						$schedule_detail['ScheduleDetailTicketType'] = $detail['ScheduleDetailTicketType'];
						unset($schedule_detail['ScheduleDetail']['ScheduleDetailTicketType']);


						if ($this->$model->ScheduleDetail->saveAll($schedule_detail)) {
							//copy layout from hall details
							if ($copy_layout) {
								$schedule_detail_id = $this->$model->ScheduleDetail->id;

								/*
								//if performance is slow because of copying layout from hall details to time schedule
								//then try to use raw query, open this remark, and remark the method to insert the seat one by one
								$prefix = Environment::read('database.prefix');

								$strsql = "set @label := 0";
								$this->$model->query($strsql);

								$strsql = "set @row_number := 0";
								$this->$model->query($strsql);

								$sqlstr = "INSERT INTO " . $prefix . "schedule_detail_layouts(schedule_detail_id, column_number, row_number, label, status, updated, created) ".
											"select schedule_detail_id, column_number, row_number, label, status, now(), now() " .
											"from (" .
												"select " . $schedule_detail_id . " as schedule_detail_id, column_number, row_number, if(@row_number <> row_number, @label := 0, 0) as tmp, " .
														"if(enabled = 1, @label := @label + 1, '') as label, enabled, " .
														"(@row_number := row_number) as tmp_col_number, 0 as status " . 
												"from " . $prefix . "hall_details a " .
												"where hall_id = " . $hall_id . " " .
												"order by row_number asc, column_number) a";
						
								$myvalue = $this->$model->query($sqlstr);
								*/

								foreach($schedule_detail_layout as &$detail_layout) {
									$detail_layout['schedule_detail_id'] = $schedule_detail_id;
								}

								if ($this->$model->ScheduleDetail->ScheduleDetailLayout->saveAll($schedule_detail_layout)) {
									//do nothing
								} else {
									$valid = false;
									$dbo->rollback();
									$this->Session->setFlash(__('data_is_not_saved') . ' ' . 'schedule_detail_layout_data_invalid', 'flash/error');
									break;
								}

							}

						} else {
							$valid = false;
							$dbo->rollback();
							$this->Session->setFlash(__('data_is_not_saved') . ' ' . 'schedule_detail_data_invalid', 'flash/error');
							break;
						}
					}

					if ($valid) {
						// $dbo->rollback();
						$dbo->commit();
						$this->Session->setFlash(__('data_is_saved'), 'flash/success');
						$this->redirect(array('action' => 'index'));
					}
				}
			} catch (Exception $ex) {
				$dbo->rollback();
				$this->Session->setFlash(__('data_is_not_saved') . ' msg : ' . $ex->getMessage(), 'flash/error');
			}

		} else {

			$this->request->data = $old_item;
			// $seat_layout = $old_item['HallDetail'];
			
		}

		// $layout = json_encode($seat_layout);

		//$this->set(compact('model', 'layout'));
		$this->set(compact('model'));

		$this->load_data();
	}

	public function admin_copy($id = null, $date) {
		$model = $this->model;

		$options = array(
			'contain' => array(
				'ScheduleDetail' => array(
					'fields' => array(
						'ScheduleDetail.date',
						'ScheduleDetail.time',
						'ScheduleDetail.id'
					),
					'conditions' => array(
						'ScheduleDetail.date' => $date
					)
				),
				'UpdatedBy',
				'CreatedBy'
			),
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
			'recursive' => 1
		);
		$old_item = $this->$model->find('first', $options);

		if (!$old_item || !$old_item['ScheduleDetail']) {
			throw new NotFoundException(__('invalid_data'));
		}

		foreach ($old_item['ScheduleDetail'] as &$schedule_detail_old) {
			$option_ticket_type = array(
				'fields' => array(
					'TicketType.id', 
					'TicketTypeLanguage.name',
					'ScheduleDetailTicketType.id',
					'ScheduleDetailTicketType.price',
					'ScheduleDetailTicketType.price_hkbo',
				),
				'joins' => array(
					array(
						'alias' => 'TicketTypeLanguage',
						'table' => Environment::read('table_prefix') . 'ticket_type_languages',
						'type' => 'left',
						'conditions' => array(
							'TicketTypeLanguage.ticket_type_id = TicketType.id',
							'TicketTypeLanguage.language' => $this->lang18
						),
					),
					array(
						'alias' => 'ScheduleDetailTicketType',
						'table' => Environment::read('table_prefix') . 'schedule_detail_ticket_types',
						'type' => 'left',
						'conditions' => array(
							'ScheduleDetailTicketType.ticket_type_id = TicketType.id',
							'ScheduleDetailTicketType.schedule_detail_id' => $schedule_detail_old['id'],
						),
					),
				),
				'conditions' => array(
					'enabled' => 1
				)
			);

			$objTicketType = ClassRegistry::init('Pos.TicketType');
			$data_ticket_type = $objTicketType->find('all', $option_ticket_type);
			$schedule_detail_old['ScheduleDetailTicketType'] = $data_ticket_type;
		}

		$hall_id = $old_item['Schedule']['hall_id'];

		// $seat_layout = array();
		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;
			// $seat_layout = $data['HallDetail'];

			$schedule = $data[$model];
			$schedule_id = $data[$model]['id'];

			$hall_layout = array();
			
			$option_hall = array(
				'fields' => array(
					'column_number',
					'row_number',
					'enabled',
					'is_disability_seat',
					'is_blocked_seat',
				),
				'conditions' => array(
					'hall_id' => $hall_id
				),
				'order' => array(
					'row_number' => 'ASC',
					'column_number' => 'ASC',
				),
			);
			$objHallDetail = ClassRegistry::init('Cinema.HallDetail');
			$hall_layout = $objHallDetail->find('all', $option_hall);

			$dbo = $this->$model->getDataSource();
			$dbo->begin();
			$valid = true;
			try {
				foreach($data['ScheduleDetail'] as &$detail) {
					//saving Schedule Detail and Schedule Detail Ticket Type

					$detail['id'] = null;
					$detail['schedule_id'] = $schedule_id;
					$detail['date'] = date('Y-m-d', strtotime(str_replace('/', '-', $data[$model]['date'])));
					//$detail['capacity'] = 0;

					$schedule_detail_layout = array();
					$label = 0;
					$row_number = 0;
					$capacity = 0;
					$max_label = array();
					$max_label[$row_number] = 0;					
					foreach($hall_layout as $layout) {
						if($row_number <> $layout['HallDetail']['row_number']) {
							$max_label[$layout['HallDetail']['row_number']] = 0;
						}
						if ($layout['HallDetail']['enabled'] == 1) {
							$max_label[$layout['HallDetail']['row_number']] = $max_label[$layout['HallDetail']['row_number']] + 1;
						}
						$row_number = $layout['HallDetail']['row_number'];
					}

					$row_number = 0;
					$label = $max_label[$row_number] + 1;					
					foreach($hall_layout as $layout) {
						$seat = array();
						if($row_number <> $layout['HallDetail']['row_number']) {
							$label = $max_label[$layout['HallDetail']['row_number']] + 1;
						}
						if ($layout['HallDetail']['enabled'] == 1) {
							$label--;
							$capacity++;
						}
						$seat['column_number'] = $layout['HallDetail']['column_number'];
						$seat['row_number'] = $layout['HallDetail']['row_number'];
						$seat['label'] = ($layout['HallDetail']['enabled'] == 1) ? $label : '';
						$seat['status'] = $layout['HallDetail']['enabled'];
						$seat['enabled'] = $layout['HallDetail']['enabled'];
						$seat['is_disability_seat'] = $layout['HallDetail']['is_disability_seat'];
						$seat['is_blocked_seat'] = $layout['HallDetail']['is_blocked_seat'];
						$schedule_detail_layout[] = $seat;
						$row_number = $layout['HallDetail']['row_number'];
					}					

					$detail['capacity'] = $capacity;

					foreach($detail['ScheduleDetailTicketType'] as &$detail_ticket_type) { 
						$detail_ticket_type['id'] = null;
					}

					$schedule_detail = array();
					$schedule_detail['ScheduleDetail'] = $detail;
					$schedule_detail['ScheduleDetailTicketType'] = $detail['ScheduleDetailTicketType'];
					unset($schedule_detail['ScheduleDetail']['ScheduleDetailTicketType']);

					// pr($schedule_detail);

					if ($this->$model->ScheduleDetail->saveAll($schedule_detail)) {
						//copy layout from hall details
						$schedule_detail_id = $this->$model->ScheduleDetail->id;

						
						/*
						//if performance is slow because of copying layout from hall details to time schedule
						//then try to use raw query, open this remark, and remark the method to insert the seat one by one
						$prefix = Environment::read('database.prefix');

						$strsql = "set @label := 0";
						$this->$model->query($strsql);

						$strsql = "set @row_number := 0";
						$this->$model->query($strsql);

						$sqlstr = "INSERT INTO " . $prefix . "schedule_detail_layouts(schedule_detail_id, column_number, row_number, label, status, updated, created) ".
									"select schedule_detail_id, column_number, row_number, label, status, now(), now() " .
									"from (" .
										"select " . $schedule_detail_id . " as schedule_detail_id, column_number, row_number, if(@row_number <> row_number, @label := 0, 0) as tmp, " .
												"if(enabled = 1, @label := @label + 1, '') as label, enabled, " .
												"(@row_number := row_number) as tmp_col_number, 0 as status " . 
										"from " . $prefix . "hall_details a " .
										"where hall_id = " . $hall_id . " " .
										"order by row_number asc, column_number) a";
				
						$myvalue = $this->$model->query($sqlstr);
						*/


						foreach($schedule_detail_layout as &$detail_layout) {
							$detail_layout['schedule_detail_id'] = $schedule_detail_id;
						}

						if ($this->$model->ScheduleDetail->ScheduleDetailLayout->saveAll($schedule_detail_layout)) {
							//do nothing
						} else {
							$valid = false;
							$dbo->rollback();
							$this->Session->setFlash(__('data_is_not_saved') . ' ' . 'schedule_detail_layout_data_invalid', 'flash/error');
							break;
						}

						

					} else {
						$valid = false;
						$dbo->rollback();
						$this->Session->setFlash(__('data_is_not_saved') . ' ' . 'schedule_detail_data_invalid', 'flash/error');
						break;
					}
				}

				if ($valid) {
					$dbo->commit();
					$this->Session->setFlash(__('data_is_saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				}



			} catch (Exception $ex) {
				$dbo->rollback();
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
			}

		} else {

			$this->request->data = $old_item;
			// $seat_layout = $old_item['HallDetail'];
			
		}

		// $layout = json_encode($seat_layout);

		//$this->set(compact('model', 'layout'));
		$this->set(compact('model'));

		$this->load_data();
	}

	public function load_data() {
		$objMovie = ClassRegistry::init('Movie.Movie');
		$movies = $objMovie->find('list', array(
            'fields' => array('Movie.id', 'MovieLanguage.name'),
			'joins' => array(
				array(
					'alias' => 'MovieLanguage',
					'table' => Environment::read('table_prefix') . 'movie_languages',
					'type' => 'left',
					'conditions' => array(
						'Movie.id = '.'MovieLanguage.movie_id',
						'MovieLanguage.language' => $this->lang18
					),
				),
			),
		));

		$option_movie = array(
			'fields' => array('id', 'code'),
			'conditions' => array(
				'enabled' => 1
			),
		);

		$option_hall = $option_movie;
		$objHall = ClassRegistry::init('Cinema.Hall');
		$halls = $objHall->find('list', $option_hall);

		$option_movie_type = array(
			'fields' => array('id', 'name'),
			'conditions' => array(
				'enabled' => 1
			),
		);
		$objMovieType = ClassRegistry::init('Movie.MovieType');
		$movie_types = $objMovieType->find('list', $option_movie_type);

		$add_new_time_schedule_url = Router::url(array('plugin'=>'movie', 'controller'=>'Schedules', 'action'=>'admin_add_new_time_schedule'),true);
		$detail_model = 'ScheduleDetail';
		$detail_ticket_type_model = 'Schedule.ScheduleDetailTicketType';

		$this->set(compact('movies', 'halls', 'movie_types', 'add_new_time_schedule_url', 'detail_model', 'detail_ticket_type_model'));
	}

	public function admin_add_new_time_schedule(){
		$data = $this->request->data;

		if ($data) {
			$detail_ticket_type_model = $data['detail_ticket_type_model'];
			$detail_model = $data['detail_model'];
            $count = $data['count'];

			$option_ticket_type = array(
				'fields' => array("TicketType.id", 'TicketTypeLanguage.name'),
				'joins' => array(
					array(
						'alias' => 'TicketTypeLanguage',
						'table' => Environment::read('table_prefix') . 'ticket_type_languages',
						'type' => 'left',
						'conditions' => array(
							'TicketTypeLanguage.ticket_type_id = TicketType.id',
							'TicketTypeLanguage.language' => $this->lang18
						),

					),
				),
				'conditions' => array(
					'enabled' => 1
				)
			);

			$objTicketType = ClassRegistry::init('Pos.TicketType');
			$data_ticket_type = $objTicketType->find('all', $option_ticket_type);

			$this->set(compact('detail_model', 'detail_ticket_type_model', 'data_ticket_type', 'count'));

			$this->render('Pages/add_new_time_schedule');
		}else{
			return 'NULL';
		}
	}

	public function admin_set_layout() {
		$model   = $this->model;

		$hall_id = $this->request->params['pass'][0];

		if (!$hall_id) {
			throw new NotFoundException(__('invalid_data'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

			if (!isset($data['Schedule']) || !isset($data['HallDetail']))
			{
				$this->Session->setFlash(__d('schedule', 'no_hall_selected'), 'flash/error');
				$this->redirect(array('action' => 'index'));
			}

			$scheduleDetailIds = $data['Schedule']['ScheduleDetails'];
			$hallDetail        = isset($data['HallDetail'])? json_decode($data['HallDetail'], true) : array();
			$objScheduleLayout = ClassRegistry::init('Movie.ScheduleDetailLayout');
			$objHallDetail     = ClassRegistry::init('Cinema.HallDetail');

			$entries = array();
			$oldHallLayouts = array();

			foreach ($hallDetail as $seat) 
			{
				$oldHallLayout = $objHallDetail->find('first', [
					'conditions' => [
						'hall_id'       => $hall_id,
						'row_number'    => $seat['row_number'],
						'column_number' => $seat['column_number'],
					],
				]);

				$oldHallLayout['HallDetail']['enabled'] = $seat['enabled'];
				$oldHallLayout['HallDetail']['is_disability_seat'] = $seat['vegetable'];
				$oldHallLayouts[] = $oldHallLayout;

				foreach ($scheduleDetailIds as $scheduleDetailId) 
				{
					$entry = $objScheduleLayout->find('first', [
						'conditions' => [
							'schedule_detail_id' => $scheduleDetailId,
							'row_number'         => $seat['row_number'],
							'column_number'      => $seat['column_number'],
						],
					]);

					$entry['ScheduleDetailLayout']['enabled'] = $seat['enabled'];
					$entry['ScheduleDetailLayout']['is_disability_seat'] = $seat['vegetable'];

					$entries[] = $entry;
				}
			}

			$dbo = $this->$model->getDataSource();
			$dbo->begin();
			if ($objScheduleLayout->saveAll($entries) && $objHallDetail->saveAll($oldHallLayouts)) {
				$dbo->commit();
				$this->Session->setFlash(__('data_is_saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$dbo->rollback();
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
			}
		}

		$option = array(
			'fields' => array($model.'.*', 'ScheduleDetail.*', 'MovieLanguage.*'),
            'joins' => array(
                array(
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.schedule_id = '.$model.'.id',
                    ),
                ),
				array(
					'alias' => 'MovieLanguage',
					'table' => Environment::read('table_prefix') . 'movie_languages',
					'type' => 'left',
					'conditions' => array(
						$model.'.movie_id = '.'MovieLanguage.movie_id',
						'MovieLanguage.language' => $this->lang18
					),
				),
            ),
			'conditions' => array(
				'hall_id' => $hall_id,
			),
		);
		$schedules = $this->$model->find('all', $option);

		$objHall = ClassRegistry::init('Cinema.Hall');
		$options = array(
			'conditions' => array('Hall.' . $objHall->primaryKey => $hall_id),
			'recursive' => 1
		);
		$hall = $objHall->find('first', $options);

		if (!$hall) {
			throw new NotFoundException(__('invalid_data'));
		}

		$seat_layout = $hall['HallDetail'];
		$layout = json_encode($seat_layout);

		$this->set(compact('schedules', 'layout'));
	}

	public function api_get_schedule_detail_layout() {
		$this->Api->init_result();

		if ($this->request->is('post')) 
		{
			$this->disableCache();
			$data   = $this->request->data;

			$url_params = $this->request->params;
			$this->Api->set_post_params($url_params, $data);
			$this->Api->set_save_log(true);

			$custom_rules = array(
				'schedule_detail_id' => array(
					'required' => true,
					'type' => 'number'
				),
			);

			$custom_rules = array_merge($this->api_rules, $custom_rules);

			$valid = $this->Api->validate_data($data, $custom_rules);
			if ( !$valid ) {
				goto return_api;
			}

			$list = $this->Schedule->get_schedule_detail_layout($data);

			$this->Api->set_result(true, $this->model." ".__('found'), $list);
		}

		return_api :
		$this->Api->output();
	}

	public function api_get_schedule_detail() {
		$this->Api->init_result();

		if ($this->request->is('post')) 
		{
			$this->disableCache();
			$data   = $this->request->data;

			$url_params = $this->request->params;
			$this->Api->set_post_params($url_params, $data);
			$this->Api->set_save_log(true);

			$custom_rules = array(
				'id' => array(
					'required' => true,
					'type' => 'number'
				),
			);

			$custom_rules = array_merge($this->api_rules, $custom_rules);

			$valid = $this->Api->validate_data($data, $custom_rules);
			if ( !$valid ) {
				goto return_api;
			}

			$list = $this->Schedule->get_schedule_detail($data);

			$this->Api->set_result(true, $this->model." ".__('found'), $list);
		}

		return_api :
		$this->Api->output();
	}

	public function api_get_schedule() {
        $this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
            
            if (!isset($data['token']) || empty($data['token'])) {
				$message = __('missing_parameter') .  __('token');
            } else if (!isset($data['staff_id']) || empty($data['staff_id'])) {
				$message = __('missing_parameter') .  __('staff_id');
			} else if (!isset($data['parm_date']) || empty($data['parm_date'])) {
                $message = __('missing_parameter') .  __('parm_date');
            } else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                //check staff
                $objStaff = ClassRegistry::init('Cinema.Staff');
                $data_staff = $objStaff->get_staff_by_conditions(array('id' => $data['staff_id'], 'token' => $data['token']));

                if (!isset($data_staff['Staff']['id']) || empty($data_staff['Staff']['id'])) {
                    $status = false;
                    $message = __('staff_not_valid');
                    goto return_result;
                }

				$current_date = date('Y-m-d');
				$is_today = ($data['parm_date'] == $current_date) ? true : false;
				$is_manager = ($data_staff['Staff']['role'] == 'manager') ? 1 : 0;
                $result = $this->Schedule->get_schedule($data, $data['parm_date'], $is_manager, $is_today);

                $status = true;
				$message = '';

                if($status){
					$params = $result;
                    if (!$params) {
                        $params = (object)array();
                    }

                }else{
                    if(isset($result['log_data']) && $result['log_data']){
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
			}


            return_result :
            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();		
	}

	public function api_get_data_schedule_detail() {
		$this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
            
            if (!isset($data['movie_id']) || empty($data['movie_id'])) {
				$message = __('missing_parameter') .  __('movie_id');
            } else if (!isset($data['movie_type_id']) || empty($data['movie_type_id'])) {
				$message = __('missing_parameter') .  __('movie_type_id');
			} else if (!isset($data['parm_date']) || empty($data['parm_date'])) {
                $message = __('missing_parameter') .  __('parm_date');
            } else {
				$this->Api->set_language($this->lang18);

				$is_manager = 0;

				if (isset($data['token']) && !empty($data['token'])) {
					//check staff
					$objStaff = ClassRegistry::init('Cinema.Staff');
					$data_staff = $objStaff->get_staff_by_conditions(array('id' => $data['staff_id'], 'token' => $data['token']));

					if (!isset($data_staff['Staff']['id']) || empty($data_staff['Staff']['id'])) {
						$status = false;
						$message = __('staff_not_valid');
						goto return_result;
					}

					$is_manager = ($data_staff['Staff']['role'] == 'manager') ? 1 : 0;
				}

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

				$current_date = date('Y-m-d');
				$is_today = ($data['parm_date'] == $current_date) ? true : false;
                $result = $this->Schedule->get_data_schedule_detail(
					$this->Api->get_language(), 
					$data['movie_id'],
					$data['movie_type_id'],
					$data['parm_date'], 
					$is_manager,
					$is_today);

                $status = true;
				$message = '';

                if($status){
					$params = $result;
                    if (!$params) {
                        $params = (object)array();
                    }

                }else{
                    if(isset($result['log_data']) && $result['log_data']){
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
			}


            return_result :
            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();	
	}

}
