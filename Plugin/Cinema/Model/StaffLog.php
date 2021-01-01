<?php
App::uses('CinemaAppModel', 'Cinema.Model');
/**
 * StaffLog Model
 *
 * @property Staff $Staff
 */
class StaffLog extends CinemaAppModel {

	public $actsAs = array('Containable');
	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
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
		)
	);

	public function get_data_export($conditions, $page, $limit, $lang){
		$model = $this->alias;
		$staff_model = "Staff";
	

		$all_settings = array(
			'fields' => array(
				$model.'.*',
				$staff_model.'.name'
			),
			'contain' => array(
				'Staff' => [
					'fields' => ['name']
				],
                'CreatedBy',
                'UpdatedBy'
			),
            'conditions' => $conditions,
            'order' => array( $staff_model.'.name' => 'asc' ),
            'limit' => $limit,
            'page' => $page
        );

		return $this->find('all', $all_settings);
	}

	public function format_data_export($data, $row){
		$model = $this->alias;
		$staff_model = "Staff";

        return array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row[$staff_model]["name"]) ?  $row[$staff_model]["name"] : ' ',
			!empty($row[$model]["clock_in"]) ?  $row[$model]["clock_in"] : ' ',
			!empty($row[$model]["clock_out"]) ?  $row[$model]["clock_out"] : ' '            
        );
	}	

	public function create_staff_log($data, $staff_id){
		$valid = false;

		$data_insert = array();
		$data_insert['staff_id'] = $staff_id;
		$data_insert['clock_in'] = date("Y-m-d H:i:s");
		$data_insert['model_code'] = $data['model_code'];

		if ($this->save($data_insert)) {
			$valid = true;
		}

		return $valid;
	}
}
