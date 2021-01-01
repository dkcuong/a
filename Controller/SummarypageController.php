<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 */
class SummarypageController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

	public function index($id = 0) {
		$order_id = $id;

		$option = array(
			'fields' => array(
				'Order.id',
				'Order.inv_number',
				'Order.status',
				'Order.member_id',
				'Order.is_member_register',
				'Order.registration_fee',
				'Order.country_code_registration',
				'Order.phone_registration',
				'Order.total_amount',
				'Order.grand_total',
				'Order.discount_amount',
				'ScheduleDetail.id',
				'ScheduleDetail.date',
				'ScheduleDetail.time',
				'Hall.code',
			),
			'joins' => array(
				array(
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetail.id = Order.schedule_detail_id',
					),
				),
				array(
					'alias' => 'Schedule',
					'table' => Environment::read('table_prefix') . 'schedules',
					'type' => 'left',
					'conditions' => array(
						'Schedule.id = ScheduleDetail.schedule_id',
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
				'Order.id' => $order_id
			),
		);

		$objOrder = ClassRegistry::init('Pos.Order');
		$summary = $objOrder->find('first', $option);

		if (!isset($summary['Order']['id']) || empty($summary['Order']['id'])) {
			$this->redirect(array('controller' => 'Ticketingpage','action' => 'index'));
		}

		$is_paid = false;
		$data_print = array();
		if ($summary['Order']['status'] == 3) {
			$is_paid = true;

			$reprint = false;
			//$reprint = ($data_trans[$model]['printed'] > 1) ? true : false;
			$data_print = $objOrder->get_data_print_order($summary['Order']['id'], $this->lang18, $reprint);
		}

		$option_ticket_type = array(
			'fields' => array(
				'MovieLanguage.movie_id',
				'MovieLanguage.name',
				'MovieType.name',
				'TicketTypeLanguage.name',
				'sum(OrderDetail.price) as OrderDetail__total_price',
				'sum(OrderDetail.service_charge) as OrderDetail__total_service_charge',
				'sum(OrderDetail.qty) as OrderDetail__total_qty',
				'sum(OrderDetail.subtotal) as OrderDetail__total_subtotal'
			),
			'joins' => array(
				array(
					'alias' => 'ScheduleDetailTicketType',
					'table' => Environment::read('table_prefix') . 'schedule_detail_ticket_types',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetailTicketType.id = OrderDetail.schedule_detail_ticket_type_id',
					),
				),
				array(
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetail.id = ScheduleDetailTicketType.schedule_detail_id',
					),
				),
				array(
					'alias' => 'Schedule',
					'table' => Environment::read('table_prefix') . 'schedules',
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
						'MovieLanguage.movie_id = Schedule.movie_id',
						'MovieLanguage.language' => $this->lang18,
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
					'alias' => 'TicketTypeLanguage',
					'table' => Environment::read('table_prefix') . 'ticket_type_languages',
					'type' => 'left',
					'conditions' => array(
						'TicketTypeLanguage.ticket_type_id = ScheduleDetailTicketType.ticket_type_id',
						'TicketTypeLanguage.language' => $this->lang18,
					),
				),
			),
			'conditions' => array(
				'OrderDetail.order_id' => $order_id
			),
			'group' => array(
				'OrderDetail.schedule_detail_ticket_type_id'
			)
		);

		$objOrderDetail = ClassRegistry::init('Pos.OrderDetail');
		$objOrderDetail->virtualFields['total_qty'] = 'sum(OrderDetail.qty)';
		$objOrderDetail->virtualFields['total_subtotal'] = 'sum(OrderDetail.subtotal)';
		$objOrderDetail->virtualFields['total_price'] = 'sum(OrderDetail.price)';
		$objOrderDetail->virtualFields['total_service_charge'] = 'sum(OrderDetail.service_charge)';
		$data_order_detail = $objOrderDetail->find('all', $option_ticket_type);

		if (isset($data_order_detail[0]['MovieLanguage']['movie_id']) && !empty($data_order_detail[0]['MovieLanguage']['movie_id'])) {
			$option_movie_language = array(
				'fields' => array('MovieLanguage.name'),
				'conditions' => array(
					'MovieLanguage.movie_id' => $data_order_detail[0]['MovieLanguage']['movie_id']
				)
			);

			$objMovieLanguage = ClassRegistry::init('Movie.MovieLanguage');
			$data_movie_language = $objMovieLanguage->find('list', $option_movie_language);

			foreach($data_order_detail as &$data_dtl) {
				$data_dtl['MovieLanguage']['name'] = implode(',', $data_movie_language);
			}
		}

		$option_seat = array(
			'fields' => array(
				'ScheduleDetailLayout.*',
			),
			'joins' => array(
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
				'OrderDetail.order_id' => $order_id
			),
		);

		$data_order_detail_layout = $objOrderDetail->find('all', $option_seat);

		$summary['ScheduleDetailTicketType'] = $data_order_detail;

		foreach($data_order_detail_layout as &$layout) {
			$layout['ScheduleDetailLayout']['seat_number'] = $this->Common->getCellID($layout['ScheduleDetailLayout']['row_number']).$layout['ScheduleDetailLayout']['label'];
		}

		$data_seat = Hash::extract($data_order_detail_layout, '{n}.ScheduleDetailLayout.seat_number');
		$summary['Order']['seats'] = implode(" , ", $data_seat);

		$now = date_create(date('Y-m-d'));
		$show_date = date_create(date('Y-m-d', strtotime($summary['ScheduleDetail']['date'])));
		$diff = date_diff($now,$show_date);
		$number_diff = $diff->format('%a') * 1;

		$summary['ScheduleDetail']['date_label'] = ($number_diff == 0) ? 'Today - ' : (($number_diff == 1) ? 'Tomorrow - ' : 'Overmorrow - ');
		$summary['ScheduleDetail']['date_label'] = $summary['ScheduleDetail']['date_label'] . date('m/d/Y', strtotime($summary['ScheduleDetail']['date']));

		$data_user = array();
		if (isset($summary['Order']['member_id']) && ($summary['Order']['member_id'] > 0)) {
			$objMember = ClassRegistry::init('Member.Member');
			$tmp_data_user = $objMember->get_member_by_field($summary['Order']['member_id'], 'id');

			$data_renewal = $objMember->MemberRenewal->check_renewal($tmp_data_user['Member']['id']);
			if (isset($data_renewal['MemberRenewal']) && !empty($data_renewal['MemberRenewal'])) {
				$tmp_data_user['Member']['expired_date'] = $data_renewal['MemberRenewal']['expired_date'];
				$tmp_data_user['Member']['expired_date_label'] = date('m/d/Y', strtotime($data_renewal['MemberRenewal']['expired_date']));
				$tmp_data_user['Member']['birthday_label'] = date('m/d/Y', strtotime($tmp_data_user['Member']['birthday']));

				$tmp_data_user['Member']['discount_member'] = $summary['Order']['discount_amount'];
				$tmp_data_user['Member']['registration_fee'] = 0;
				$tmp_data_user['Member']['is_member_register'] = 0;
				$data_user = $tmp_data_user;
			}
		} else if (isset($summary['Order']['is_member_register']) && ($summary['Order']['is_member_register'] > 0)) {
			$tmp_data_user['Member']['expired_date'] = '';
			$tmp_data_user['Member']['expired_date_label'] = '';
			$tmp_data_user['Member']['birthday_label'] = '';

			$tmp_data_user['Member']['code'] = '';
			$tmp_data_user['Member']['name'] = 'NEW MEMBER';
			$tmp_data_user['Member']['country_code'] = $summary['Order']['country_code_registration'];
			$tmp_data_user['Member']['phone'] = $summary['Order']['phone_registration'];
			$tmp_data_user['Member']['discount_member'] = $summary['Order']['discount_amount'];
			$tmp_data_user['Member']['registration_fee'] = $summary['Order']['registration_fee'];
			$tmp_data_user['Member']['is_member_register'] = $summary['Order']['is_member_register'];
			$data_user = $tmp_data_user;
		}

		$printer_setting = Environment::read('site.printer_setting');
		$printer_address = Environment::read('site.'.$printer_setting.'.ticketing');


		$this->set(compact('order_id', 'summary', 'data_user', 'is_paid', 'data_print', 'printer_address'));
	}
}