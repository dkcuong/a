<?php
App::uses('MemberAppModel', 'Member.Model');
/**
 * NotificationLanguage Model
 *
 * @property Notification $Notification
 */
class NotificationLanguage extends MemberAppModel {

	public $actsAs = array('Containable');
	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Notification' => array(
			'className' => 'Member.Notification',
			'foreignKey' => 'notification_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
