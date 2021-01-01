<?php
App::uses('MovieAppModel', 'Movie.Model');

class ScheduleDetailLayout extends MovieAppModel {

	public $status = array(
		1 => 'available',
		2 => 'reserved',
		3 => 'sold'
	);

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'ScheduleDetail' => array(
			'className' => 'Movie.ScheduleDetail',
			'foreignKey' => 'schedule_detail_id',
			'conditions' => '',
			'order' => ''
		),
	);

	public function check_seat_schedule_detail_relation($schedule_detail_id, $id) {
		$option_check = array(
			'conditions' => array(
				'id IN' => $id,
				'schedule_detail_id <>' => $schedule_detail_id
			)
		);

		$check_result = $this->find('count', $option_check);
		return (($check_result > 0) ? false : true);
	}

	public function check_seat_status_availability($id) {
		$option_check = array(
			'conditions' => array(
				'id IN' => $id,
				'status >' => 1 
			)
		);

		$check_result = $this->find('count', $option_check);

		return (($check_result > 0) ? false : true);
	}

}
