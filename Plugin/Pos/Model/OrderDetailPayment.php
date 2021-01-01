<?php
App::uses('PosAppModel', 'Pos.Model');

class OrderDetailPayment extends PosAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'Order' => array(
			'className' => 'Pos.Order',
			'foreignKey' => 'order_id',
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
