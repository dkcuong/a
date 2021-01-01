<?php
App::uses('MemberAppModel', 'Member.Model');
/**
 * Notification Model
 *
 * @property MemberNotification $MemberNotification
 * @property NotificationLanguage $NotificationLanguage
 */
class Notification extends MemberAppModel {

	public $actsAs = array('Containable');
	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'MemberNotification' => array(
			'className' => 'Member.MemberNotification',
			'foreignKey' => 'notification_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'NotificationLanguage' => array(
			'className' => 'Member.NotificationLanguage',
			'foreignKey' => 'notification_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public $push_method = array(
		'send-to-all' => 'send-to-all', 
		'send-to-spesific-person' => 'send-to-spesific-person'
	);
}
