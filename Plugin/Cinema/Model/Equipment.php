<?php
App::uses('CinemaAppModel', 'Cinema.Model');
/**
 * Equipment Model
 *
 * @property BookingEnquiry $BookingEnquiry
 */
class Equipment extends CinemaAppModel {

    public $actsAs = array('Containable');
/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'equipments';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'code' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
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
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'BookingEnquiry' => array(
			'className' => 'Cinema.BookingEnquiry',
			'joinTable' => 'booking_enquiries_equipments',
			'foreignKey' => 'equipment_id',
			'associationForeignKey' => 'booking_enquiry_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

    public function get_list_equipment($data = array()){
        $conditions = array('Equipment.enabled' => true);
        $result = array();

        if (isset($data['is_api']) && $data['is_api'] == true) {
            $result_temp = $this->find('all', array(
                'fields' => array('id', 'code'),
                'conditions' => $conditions,
            ));

            foreach ($result_temp as $k => $v) {
                $result[$k] = $v['Equipment'];
            }
        } else {
            $result = $this->find('list', array(
                'fields' => array('id', 'code'),
                'conditions' => $conditions,
            ));
        }

        return $result;
    }
}
