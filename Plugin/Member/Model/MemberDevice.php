<?php
App::uses('MemberAppModel', 'Member.Model');
/**
 * MemberDevice Model
 *
 * @property Member $Member
 * @property DeviceType $DeviceType
 */
class MemberDevice extends MemberAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'enabled' => array(
			'boolean' => array(
				'rule' => array('boolean'),
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
			'className' => 'Member',
			'foreignKey' => 'member_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DeviceType' => array(
			'className' => 'DeviceType',
			'foreignKey' => 'device_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function get_devices($member_ids){

		$user_id_condition = array();
		if (isset($member_ids) && !empty($member_ids)) {
			$user_id_condition = array('MemberDevice.member_id' => $member_ids);
		}


		$member_devices = $this->find('all',array(
			'fields' => array('MemberDevice.member_id','MemberDevice.device_type_id','MemberDevice.token'),
			'conditions' => array(
				'MemberDevice.enabled' => true,
				$user_id_condition
			),
            'contain' => array(
                'DeviceType' => array(
                    'fields' => array( 'DeviceType.id','DeviceType.slug'),
                    'conditions' => array( 'DeviceType.enabled' => true ),
                )
            )
		));
	}

	public function create_new_device($data, $member_id){
		$valid = false;

		$objDeviceType = ClassRegistry::init('Member.DeviceType');
		$device_type = $objDeviceType->find('first', array(
			'conditions' => array(
				'slug' => $data['device_type'],
				'enabled' => 1
			),
			'recursive' => -1
		)); 

		if (!isset($device_type['DeviceType']) || empty($device_type['DeviceType'])) {
			goto return_data;
		}

		$options = array(
			'conditions' => array(
				'member_id' => $member_id,
				'token' => $data['device_token'],
			),
			'recursive' => -1
		);

		$sum_device = $this->find('count', $options);
		if ($sum_device > 0) {
			$valid = true;
		} else {
			$data_insert = array();
			$data_insert['member_id'] = $member_id;
			$data_insert['device_type_id'] = $device_type['DeviceType']['id'];
			$data_insert['token'] = $data['device_token'];
			$data_insert['model_code'] = $data['model_code'];
			$data_insert['os_version'] = $data['os_version'];
			$data_insert['enabled'] = 1;

			if ($this->save($data_insert)) {
				$valid = true;
			}
		}

		return_data : 
		return $valid;
	}
}
