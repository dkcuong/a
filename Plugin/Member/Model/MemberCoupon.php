<?php
App::uses('PosAppModel', 'Pos.Model');

class MemberCoupon extends PosAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $status = array(
		1=>'available',
		2=>'redemeed'
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
		'Coupon' => array(
			'className' => 'Pos.Coupon',
			'foreignKey' => 'coupon_id',
			'conditions' => '',
			'order' => ''
		),
		'Member' => array(
			'className' => 'Member.Member',
			'foreignKey' => 'member_id',
			'conditions' => '',
			'order' => ''
		),
	);

	public function get_data_export($conditions, $page, $limit, $lang){
		$prefix = Environment::read('database.prefix');

        $all_settings = array(
			'fields' => array(
				'MemberCoupon.*',
				'Member.*',
				'Coupon.*',
			),
			'contain' => array(
                'CreatedBy',
                'UpdatedBy',
                'Member',
                'Coupon',
			),
            'conditions' => $conditions,
            'order' => array( 'MemberCoupon.code' => 'asc' ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
	}
	
	public function format_data_export($data, $row){
		$model = $this->alias;
        return array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row['Coupon']["type"]) ?  $this->Coupon->type[ $row['Coupon']["type"] ] : ' ',
			!empty($row['Member']["name"]) ?  $row['Member']["name"] : ' ',
			!empty($row['Member']["phone"]) ?  $row['Member']["phone"] : ' ',
			!empty($row[$model]["created"]) ?  $row[$model]["created"] : ' ',
			!empty($row[$model]["code"]) ?  $row[$model]["code"] : ' ',
			!empty($row[$model]["code_path"]) ?  $row[$model]["code_path"] : ' ',
			!empty($row[$model]["expiry_date"]) ?  $row[$model]["expiry_date"] : ' ',
			!empty($row[$model]["status"]) ?  $row[$model]["status"] : ' ',
        );
	}
	
	public function check_coupon_validity($data) {
		$status = false;
		$message = "";
		$params = (object)array();

		/*
			0. validate the token and the staff_id
			1. validate the coupon
				-) validity
		*/

		
		//check staff
		$objStaff = ClassRegistry::init('Cinema.Staff');
		$data_staff = $objStaff->get_staff_by_conditions(array('id' => $data['staff_id'], 'token' => $data['token']));

		if (!isset($data_staff['Staff']['id']) || empty($data_staff['Staff']['id'])) {
			$status = false;
			$message = __('staff_not_valid');
			goto return_result;
		}		

		$option = array(
			'conditions' => array(
				'code' => $data['coupon_code'],
			)
		);

		$data_coupon = $this->find('first', $option);

		if (!isset($data_coupon['MemberCoupon']['id']) || empty($data_coupon['MemberCoupon']['id'])) {
			$message = __('coupon_doesnt_exists');
			goto return_result;
		}

		$expired_date = $data_coupon['MemberCoupon']['expired_date'];
		if (strtotime(date('Y-m-d', strtotime($expired_date))) < strtotime(date('Y-m-d'))) {
			$message = __('coupon_expired');
			goto return_result;
		}

		if (isset($data_coupon['MemberCoupon']['physical_coupon_number']) && !empty($data_coupon['MemberCoupon']['physical_coupon_number'])) {
			$message = __('coupon_has_been_redemeed_cant_be_redeem_again');
			goto return_result;
		}

		$status = true;
		$message = __('coupon_is_valid');
		$params = $data_coupon;

		return_result :

		return array('status' => $status, 'message' => $message, 'params' => $params);		
	}

	public function redeem_ecoupon($data) {
		$status = false;
		$message = "";
		$params = (object)array();

		/*
			0. validate the token and the staff_id
			1. validate the coupon
				-) validity
			2. create record in acx_redeem_coupon
		*/

		$data_coupon = $this->check_coupon_validity($data);
		
		if (!$data_coupon['status']) {
			$message = $data_coupon['message'];
			goto return_result;
		}
		
		$coupon = $data_coupon['params'];
		$coupon['MemberCoupon']['physical_coupon_number'] = $data['physical_coupon_code'];
		$coupon['MemberCoupon']['convert_date'] = date('Y-m-d H:i:s');
		$coupon['MemberCoupon']['status'] = 2;

		if ($this->saveAll($coupon)) {
			$status = true;
			$message = __('coupon_redeem_success');
		} else {
			$message = __('update_data_coupon_failed');
		}
		
		return_result :

		return array('status' => $status, 'message' => $message, 'params' => $params);		
	}

}
