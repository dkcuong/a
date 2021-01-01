<?php
App::uses('MovieAppModel', 'Movie.Model');

class ScheduleDetailTicketType extends MovieAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'TicketType' => array(
			'className' => 'Pos.TicketType',
			'foreignKey' => 'ticket_type_id',
			'conditions' => '',
			'order' => ''
		),
		'ScheduleDetail' => array(
			'className' => 'Movie.ScheduleDetail',
			'foreignKey' => 'schedule_detail_id',
			'conditions' => '',
			'order' => ''
		),
	);

	public $hasMany = array(
		'OrderDetail' => array(
			'className' => 'Pos.OrderDetail',
			'foreignKey' => 'schedule_detail_ticket_type_id',
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

	public function get_ticket_type_value_by_id($schedule_detail_id, $id) {
		$option = array(
			'fields' => array(
				'ScheduleDetailTicketType.id',
				'ScheduleDetailTicketType.price',
			),
			'joins' => array(
				array(
					'alias' => 'TicketType',
					'table' => Environment::read('table_prefix') . 'ticket_types',
					'type' => 'left',
					'conditions' => array(
						'TicketType.id = ScheduleDetailTicketType.ticket_type_id',
					),
				),
			),
			'conditions' => array(
				'ScheduleDetailTicketType.id IN' => $id,
				'TicketType.enabled' => 1
			)
		);

		$data_ticket_type = $this->find('list', $option);

		$option_hkbo = array(
			'fields' => array(
				'ScheduleDetailTicketType.id',
				'ScheduleDetailTicketType.price_hkbo',
			),
			'joins' => array(
				array(
					'alias' => 'TicketType',
					'table' => Environment::read('table_prefix') . 'ticket_types',
					'type' => 'left',
					'conditions' => array(
						'TicketType.id = ScheduleDetailTicketType.ticket_type_id',
					),
				),
			),
			'conditions' => array(
				'ScheduleDetailTicketType.id IN' => $id,
				'TicketType.enabled' => 1
			)
		);

		$data_ticket_type_hkbo = $this->find('list', $option_hkbo);

		if (count($data_ticket_type) <> count($id)) {
			return false;
		}

		return array('data_ticket_type' => $data_ticket_type, 'data_ticket_type_hkbo' => $data_ticket_type_hkbo);
	}
}
