<?php
App::uses('PosAppModel', 'Pos.Model');

class TicketTypeLanguage extends PosAppModel {

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
	);
}
