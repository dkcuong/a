<?php
App::uses('MemberAppModel', 'Member.Model');
/**
 * MemberNotification Model
 *
 * @property Member $Member
 * @property Notification $Notification
 */
class MemberNotification extends MemberAppModel {

	public $actsAs = array('Containable');

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'notification_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Member' => array(
			'className' => 'Member.Member',
			'foreignKey' => 'member_id',
			'conditions' => '',
			'fields' => array('name', 'phone', 'email'),
			'order' => ''
		),
		'Notification' => array(
			'className' => 'Member.Notification',
			'foreignKey' => 'notification_id',
			'conditions' => '',
			'fields' => array(),
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

	public function get_data_export($conditions, $page, $limit, $lang){
		$model = $this->alias;
		$languages_model = 'NotificationLanguage';

		$all_settings = array(
			'fields' => array(
				'MemberNotification.*',
				$languages_model.".title",
                'Member.phone',
				'Member.name',
				'Notification.push_method'
			),
			'conditions' => $conditions,
			'joins' => array(
                array(
					'alias' => 'Notification',
					'table' => Environment::read('table_prefix') . 'notifications',
					'type' => 'left',
					'conditions' => array(
						'Notification.id = '.$model.'.notification_id',
					),
				),
				array(
					'alias' => $languages_model,
					'table' => Environment::read('table_prefix') . 'notification_languages',
					'type' => 'left',
					'conditions' => array(
						'Notification.id = '.$languages_model.'.notification_id',
						$languages_model.'.language' => $lang
					),
                ),
                array(
					'alias' => 'Member',
					'table' => Environment::read('table_prefix') . 'members',
					'type' => 'left',
					'conditions' => array(
						'Member.id = '.$model.'.member_id',
					),
				)
			),
			'order' => array( 'MemberNotification.id' => 'desc' ),
			'limit' => $limit,
			'page' => $page
		);

		return $this->find('all', $all_settings);
	}

	public function format_data_export($data, $row){
		$model = $this->alias;
		$notification_model = 'Notification';
		$languages_model = 'NotificationLanguage';
		$member_model = 'Member';

		return array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row[$languages_model]["title"]) ?  $row[$languages_model]["title"] : ' ',
			!empty($row[$notification_model]["push_method"]) ?  $row[$notification_model]["push_method"] : ' ',
			!empty($row[$member_model]["name"]) ?  $row[$member_model]["name"] : ' ',
			!empty($row[$member_model]["phone"]) ?  $row[$member_model]["phone"] : ' '
		);
	}
}
