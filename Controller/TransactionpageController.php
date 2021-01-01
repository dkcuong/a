<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 */
class TransactionpageController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'RequestHandler');

	public function index() {
		$data_search = $this->request->query;
		$lang = 'zho';
		$objOrder = ClassRegistry::init('Pos.Order');

		$alphabet = range('A', 'Z');
		$page = 1;
		if (isset($this->request->params['named']['page']) && !empty($this->request->params['named']['page'])) {
			$page = $this->request->params['named']['page'];
		}

		$conditions = array();

		if( isset($data_search['movie']) && !empty($data_search['movie']) ) {
			// $conditions['Movie.code LIKE'] = '%' . $data_search['movie_code'] . '%';
			$conditions['Movie.id'] = $data_search['movie'];
		}

		if( isset($data_search['show_date']) && !empty($data_search['show_date']) ) {
			$filter_date = DateTime::createFromFormat('d/m/Y', $data_search['show_date']);
			$conditions['ScheduleDetail.date'] = date_format($filter_date, 'Y-m-d');
			$data_search['show_date'] = date_format($filter_date, 'm/d/Y');
		}

		if( isset($data_search['show_time']) && !empty($data_search['show_time']) ) {
			$filter_time = date('H:i', strtotime($data_search['show_time']));
			$conditions['ScheduleDetail.time'] = $filter_time;
		}

		if( isset($data_search['phone']) && !empty($data_search['phone']) ) {
			$conditions['Order.phone LIKE'] = '%' . $data_search['phone'] . '%';
		}

		if( isset($data_search['seat_number']) && !empty($data_search['seat_number']) ) {
			$seat_number = trim($data_search['seat_number']);
			$row_letter = strtoupper($seat_number[0]);
			$row_number = array_search($row_letter, $alphabet);
			$label = substr($seat_number, 1);

			$option_seat = array(
				'id' => array(
					'Order.id',
				),
				'joins' => array(
					array(
						'alias' => 'OrderDetail',
						'table' => Environment::read('table_prefix') . 'order_details',
						'type' => 'left',
						'conditions' => array(
							'Order.id = OrderDetail.order_id',
						),
					),
					array(
						'alias' => 'ScheduleDetailLayout',
						'table' => Environment::read('table_prefix') . 'schedule_detail_layouts',
						'type' => 'left',
						'conditions' => array(
							'ScheduleDetailLayout.id = OrderDetail.schedule_detail_layout_id',
						),
					),
				),
				'conditions' => array(
					'ScheduleDetailLayout.row_number' => $row_number,
					'ScheduleDetailLayout.label' => $label,
				)
			);

			$data_order_seat = $objOrder->find('list', $option_seat);
			
			if (!isset($data_order_seat) || empty($data_order_seat)) {
				$data_order_seat = array(0);
			}

			$conditions['Order.id IN'] = $data_order_seat;
		}

		$prefix = Environment::read('database.prefix');

		$sqlstr = "CREATE TEMPORARY TABLE IF NOT EXISTS " . $prefix . "mytables AS (".
		"select a.id, if(a.is_pos = 1, b.code, c.payType) as payment_method ".
		"from " . $prefix . "orders a ".
			"left join ( ".
				"select a.order_id, b.code ".
				"from " . $prefix . "order_detail_payments a ".
					"left join " . $prefix . "payment_methods b on b.id = a.payment_method_id ".
				"where b.type = 1 ) b on b.order_id = a.id ".
			"left join ( ".
				"select merRef, upper(payType) as payType ".
				"from " . $prefix . "order_payment_logs ".
				"group by merRef) c on c.merRef = a.inv_number ".
		")";

		$objOrder->query($sqlstr);

		$option = array(
			'fields' => array(
				'Order.*',
				'Member.name',
				'ScheduleDetail.date',
				'ScheduleDetail.time',
				'Movie.code',
				'MovieLanguage.name',
				'MovieType.name',
				'Member.name',
				'Mytable.payment_method'
			),
			'joins' => array(
				array(
					'alias' => 'Member',
					'table' => Environment::read('table_prefix') . 'members',
					'type' => 'left',
					'conditions' => array(
						'Order.member_id = Member.id',
					),
				),
				array(
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'Order.schedule_detail_id = ScheduleDetail.id',
					),
				),
				array(
					'alias' => 'Schedule',
					'table' => Environment::read('table_prefix') . 'schedules',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetail.schedule_id = Schedule.id',
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
					'alias' => 'MovieLanguage',
					'table' => Environment::read('table_prefix') . 'movie_languages',
					'type' => 'left',
					'conditions' => array(
						'Movie.id = MovieLanguage.movie_id',
						'MovieLanguage.language' => $lang
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
				array(
					'alias' => 'Mytable',
					'table' => Environment::read('table_prefix') . 'mytables',
					'type' => 'left',
					'conditions' => array(
						'Mytable.id = Order.id',
					),
				),
			),
			'conditions' => $conditions,
			'order' => array('Order.id' => 'desc'),
			'limit' => 20,
			'page' => $page
		);

		$data_order = $objOrder->find('all', $option);

		$order_ids = Hash::extract($data_order, '{n}.Order.id');

		$option_detail = array(
			'fields' => array(
				'Order.id',
				'ScheduleDetailLayout.row_number',
				'ScheduleDetailLayout.label',
			),
			'joins' => array(
				array(
					'alias' => 'OrderDetail',
					'table' => Environment::read('table_prefix') . 'order_details',
					'type' => 'left',
					'conditions' => array(
						'Order.id = OrderDetail.order_id',
					),
				),
				array(
					'alias' => 'ScheduleDetailLayout',
					'table' => Environment::read('table_prefix') . 'schedule_detail_layouts',
					'type' => 'left',
					'conditions' => array(
						'OrderDetail.schedule_detail_layout_id = ScheduleDetailLayout.id',
					),
				),
			),
			'conditions' => array(
				'Order.id' => $order_ids,
				'NOT' => array('OrderDetail.id' => null)
			),
			'order' => array('ScheduleDetailLayout.row_number' => 'asc', 'ScheduleDetailLayout.column_number' => 'desc')
		);

		$data_order_detail = $objOrder->find('all', $option_detail);

		$arr_seats = array();
		foreach($data_order_detail as $detail) {
			if (isset($detail['ScheduleDetailLayout']['row_number'])) {
				$arr_seats[$detail['Order']['id']][] = $alphabet[$detail['ScheduleDetailLayout']['row_number']].$detail['ScheduleDetailLayout']['label'];
			}
		}

		foreach($data_order as &$order) {
			if (isset($arr_seats[$order['Order']['id']]) && !empty($arr_seats[$order['Order']['id']])) {
				$order['Order']['seat'] = implode(',', $arr_seats[$order['Order']['id']]);
			}
		}

		// $dbdatas = $data_order;

		$this->Paginator->settings = $option;
		$dbdatas = $this->paginate($objOrder);

		$printer_setting = Environment::read('site.printer_setting');
		$printer_address = Environment::read('site.'.$printer_setting.'.ticketing');
		$printer_port = Environment::read('site.'.$printer_setting.'.ticketing');

		$status = $objOrder->status_zho;

		$option_movie = array(
			'fields' => array(
				'Movie.id',
				'Movie.code'
			),
			'conditions' => array(
				'enabled' => 1
			)
		);
		$objMovie = ClassRegistry::init('Movie.Movie');
		$movies = $objMovie->find('list', $option_movie);

		$this->set(compact('dbdatas', 'movies', 'arr_seats', 'printer_address', 'printer_port', 'status', 'data_search'));	
	}
}