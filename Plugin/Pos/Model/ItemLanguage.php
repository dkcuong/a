<?php
App::uses('PosAppModel', 'Pos.Model');

class ItemLanguage extends PosAppModel {

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
	);
}
