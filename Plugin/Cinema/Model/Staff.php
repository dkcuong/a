<?php
App::uses('CinemaAppModel', 'Cinema.Model');

class Staff extends CinemaAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $hasMany = array(
		'StaffLog' => array(
			'className' => 'StaffLog',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	public $belongsTo = array(
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

	public $role = array(
		'staff' => 'staff',
		'manager' => 'manager',
	);

	public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
			'fields' => array(
				'Staff.*'
			),
			'contain' => array(
                'CreatedBy',
                'UpdatedBy'
			),
            'conditions' => $conditions,
            'order' => array( 'Staff.name' => 'asc' ),
            'limit' => $limit,
            'page' => $page
        );

		return $this->find('all', $all_settings);
	}

	public function format_data_export($data, $row){
		$model = $this->alias;

        return array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row[$model]["name"]) ?  $row[$model]["name"] : ' ',
			!empty($row[$model]["code"]) ?  $row[$model]["code"] : ' ',
            $row[$model]['enabled'] == 1 ? 'Y' : 'N',
        );
	}

	public function login($data, $lang) {

		$log_data = array();
		$log_data['data'] = $data;

        $status = false;
		$params = array();
		$message = '';
		
		$options = array(
			'conditions' => array(
				'username' => $data['username'],
			),
			'recursive' => -1
		);

		$model = $this->alias;
		$data_result = $this->find('first', $options);



		if (isset($data_result[$model]) && !empty($data_result[$model])) {

			if (md5($data['password']) != $data_result[$model]['password']) {
				$message = __('wrong_password');
				$log_data['message'] = $message;

				goto return_data;
			}

			if($data_result[$model]['enabled'] == 0) {
				$message =  __('this_account_was_disabled');
				$log_data['message'] = $message;

				goto return_data;
			}

			$dbo = $this->getDataSource();
			$dbo->begin();

			//check token, if its empty, create it ... 
			if ($data_result[$model]['token'] == '') {
				$data_result[$model]['token'] = $this->generateToken();
				if (!$this->save($data_result)) {
					$dbo->rollback();
					$message =  __('token_creation_failed');
					$log_data['message'] = $message;

					goto return_data;
				}
			}

			$objStaffLog = ClassRegistry::init('Cinema.StaffLog');
			if ($objStaffLog->create_staff_log($data, $data_result[$model]['id'])) {
				$dbo->commit();
			} else {
				$dbo->rollback();
				$message =  __('save_log_failed');
				$log_data['message'] = $message;

				goto return_data;
			}

			$status = true;
			$params = $data_result;
			$message = $model.' is found';
			$log_data['message'] = $message;
		} else {
			$message = __('user_not_found');
			$log_data['message'] = $message;
		}
		
		return_data:
		return array(
			'status' => $status,
			'message' => $message,
            'params' => $params,
			'log_data' => $log_data,
		);
	}	

	public function get_staff_by_conditions($condition) {
		$options = array(
			'conditions' => $condition,
			'recursive' => -1
		);

		return $this->find('first', $options);
	}	
}
