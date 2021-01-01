<?php
App::uses('CinemaAppModel', 'Cinema.Model');

class HallDetail extends CinemaAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'Hall' => array(
			'className' => 'Cinema.Hall',
			'foreignKey' => 'hall_id',
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
	);

}
