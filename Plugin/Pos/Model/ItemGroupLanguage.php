<?php
App::uses('PosAppModel', 'Pos.Model');

class ItemGroupLanguage extends PosAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'ItemGroup' => array(
			'className' => 'Pos.ItemGroup',
			'foreignKey' => 'item_group_id',
			'conditions' => '',
			'order' => ''
		),
	);
}
