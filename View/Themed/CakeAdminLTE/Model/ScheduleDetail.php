<?php
App::uses('MovieAppModel', 'Movie.Model');

class ScheduleDetail extends MovieAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'Schedule' => array(
			'className' => 'Movie.Schedule',
			'foreignKey' => 'schedule_id',
			'conditions' => '',
			'order' => ''
		),
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
		'ScheduleDetailTicketType' => array(
			'className' => 'Movie.ScheduleDetailTicketType',
			'foreignKey' => 'schedule_detail_id',
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
		'ScheduleDetailLayout' => array(
			'className' => 'Movie.ScheduleDetailLayout',
			'foreignKey' => 'schedule_detail_id',
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
}
