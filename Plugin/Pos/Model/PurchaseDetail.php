<?php
App::uses('PosAppModel', 'Pos.Model');

class PurchaseDetail extends PosAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'Item' => array(
			'className' => 'Pos.Item',
			'foreignKey' => 'item_id',
			'conditions' => '',
			'order' => ''
		),
		'Purchase' => array(
			'className' => 'Pos.Purchase',
			'foreignKey' => 'purchase_id',
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
