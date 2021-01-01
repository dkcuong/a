<?php
App::uses('MemberAppModel', 'Member.Model');
/**
 * MemberRenewal Model
 *
 * @property Member $Member
 */
class MemberRenewal extends MemberAppModel {

	public $actsAs = array('Containable');
/**
 * Use table
 *
 * @var mixed False or table name
 */

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

	public $status = array(
		1 => 'processing',
		2 => 'not-paid',
		3 => 'paid',
		4 => 'timeout',
		5 => 'cancel',
	);

	public function get_data_export($conditions, $page, $limit, $lang){
		$model = $this->alias;

		$all_settings = array(
			'fields' => array(
				$model.'.*'
			),
			'contain' => array(
				'Member' => [ 'id', 'name', 'phone'],
                'CreatedBy',
                'UpdatedBy'
			),
            'conditions' => $conditions,
            'order' => array( 'Member.id' => 'desc' ),
            'limit' => $limit,
            'page' => $page
        );

		return $this->find('all', $all_settings);
	}

	public function format_data_export($data, $row){
		$model = $this->alias;
		$model_name = 'Member';

        return array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row[$model_name]["name"]) ?  $row[$model_name]["name"] : ' ',
			!empty($row[$model_name]["phone"]) ?  $row[$model_name]["phone"] : ' ',
			!empty($row[$model]["renewal_date"]) ?  $row[$model]["renewal_date"] : ' ',
			!empty($row[$model]["expired_date"]) ?  $row[$model]["expired_date"] : ' '
        );
	}
	
	public function check_renewal($member_id) {
		$now = date('Y-m-d');
		$option = array(
			'conditions' => array(
				'member_id' => $member_id,
				'renewal_date <=' => $now,
				'expired_date >=' => $now ,
				'status >=' => 3,
			)
		);

		return $this->find('first', $option);
	}

	public function get_unused_renewal_record($member_id) {
		$option = array(
			'conditions' => array(
				'member_id' => $member_id,
				'renewal_date' => null,
				'expired_date' => null,
				'status' => 1,
			)
		);

		return $this->find('first', $option);
	}

	public function create_member_renewal_trans($data, $lang) {
		$status = false;
		$message = "";
		$params = (object)array();

		/*
			1. check the validity of member and check if he has active membership
			2. create the transaction order
		*/

		$objMember = ClassRegistry::init('Member.Member');		
		$member_id = $objMember->get_id_by_token($data);
		if ($member_id <= 0) {
			$status = false;
			$message = __('member_not_found');

			goto return_result;	
		}

		$data_renewal = $objMember->MemberRenewal->check_renewal($member_id);
		if (isset($data_renewal['MemberRenewal']) && !empty($data_renewal['MemberRenewal'])) {
			$status = false;
			$message = __('user_already_have_active_membership');

			goto return_result;				
		}

		$renewal_id = null;
		$renewal_record = $objMember->MemberRenewal->get_unused_renewal_record($member_id);
		if (isset($renewal_record['MemberRenewal']) && !empty($renewal_record['MemberRenewal'])) {
			$renewal_id = $renewal_record['MemberRenewal']['id'];
		}

		$objSetting = ClassRegistry::init('Setting.Setting');
		$member_renewa_cost = $objSetting->get_value('member-renewal');

		$data_insert = array();
		$data_insert['MemberRenewal']['id'] = $renewal_id;
		$data_insert['MemberRenewal']['member_id'] = $member_id;
		$data_insert['MemberRenewal']['date'] = date('Y-m-d H:i:s');
		$data_insert['MemberRenewal']['inv_number'] = '';
		$data_insert['MemberRenewal']['amount'] = $member_renewa_cost;
		$data_insert['MemberRenewal']['status'] = 1;
		$data_insert['MemberRenewal']['token'] = $this->generateToken();

		$dbo = $this->getDataSource();
		$dbo->begin();
		try {

			if ($this->saveAll($data_insert)) {
				$member_renewal_id = $this->id;
				$data_insert['MemberRenewal']['id'] = $member_renewal_id;

				$inv_number = Environment::read('site.prefix.renewal').str_pad($member_renewal_id, 7, '0', STR_PAD_LEFT);
				$conditions = array(
					"MemberRenewal.id" => $member_renewal_id
				);
				$updates = array(
					"MemberRenewal.inv_number" => "'".$inv_number."'"
				);

				if ($this->updateAll($updates, $conditions)) {
					$data_insert['MemberRenewal']['inv_number'] = $inv_number;
					$params = $data_insert;
					$dbo->commit();
					$status = true;
					$message = __('member_renewal_created_succesfully');
				} else {
					$dbo->rollback();
					$status = false;
					$message = __('creating_inv_number_failed');
		
					goto return_result;
				}

			} else {
				$dbo->rollback();
				$status = false;
				$message = __('create_member_renewal_failed');
	
				goto return_result;				
			}



		} catch (Exception $e) {
			$dbo->rollback();
			$status = false;
			$message = __('data_is_not_saved') . ' ' . $e->getMessage();

			goto return_result;
		}

		return_result :

		return array('status' => $status, 'message' => $message, 'params' => $params);
	}

	public function cancel_member_renewal_trans($data, $lang) {
		$status = false;
		$message = "";
		$params = (object)array();

		/*
			1. check the validity of token (if exists) member and check if he has active membership
			2. check the validity of transaction
				-) trans must exists
				-) trans must not be having status paid
			2. update the transaction
		*/

		if (isset($data['token']) && !empty($data['token'])) {
			$objMember = ClassRegistry::init('Member.Member');		
			$member_id = $objMember->get_id_by_token($data);
			if ($member_id <= 0) {
				$status = false;
				$message = __('member_not_found');

				goto return_result;	
			}

			$data_renewal = $objMember->MemberRenewal->check_renewal($member_id);
			if (isset($data_renewal['MemberRenewal']) && !empty($data_renewal['MemberRenewal'])) {
				$status = false;
				$message = __('user_already_have_active_membership');

				goto return_result;				
			}
		}

		$option = array(
			'conditions' => array(
				'inv_number' => $data['inv_number']
			)
		);

		$data_member_renewal = $this->find('first', $option);
		if (!isset($data_member_renewal['MemberRenewal']['id']) || empty($data_member_renewal['MemberRenewal']['id'])) {
			$status = false;
			$message = __('inv_number_invalid_trans_not_found');

			goto return_result;
		}

		if ($data_member_renewal['MemberRenewal']['status'] == 3) {
			$status = false;
			$message = __('this_trans_has_been_paid_unable_to_cancel');

			goto return_result;
		}

		$data_member_renewal['MemberRenewal']['status'] = 5;

		$dbo = $this->getDataSource();
		$dbo->begin();
		try {

			if ($this->saveAll($data_member_renewal)) {
				$dbo->commit();
				$status = true;
				$message = __('member_renewal_canceled_succesfully');
			} else {
				$dbo->rollback();
				$status = false;
				$message = __('member_renewal_unable_to_cancel');
	
				goto return_result;				
			}

		} catch (Exception $e) {
			$dbo->rollback();
			$status = false;
			$message = __('data_is_not_saved') . ' ' . $e->getMessage();

			goto return_result;
		}

		return_result :

		return array('status' => $status, 'message' => $message, 'params' => $params);
	}

    public function get_payment_log_by_member_id($member_id) {
        $option = array(
            'fields' => array(
                'OrderPaymentLog.*',
                'MemberRenewal.*',
            ),
            'joins' => array(
                array(
                    'alias' => 'OrderPaymentLog',
                    'table' => Environment::read('table_prefix') . 'order_payment_logs',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderPaymentLog.id = MemberRenewal.payment_log_id',
                    ),
                ),
            ),
            'conditions' => array(
                'MemberRenewal.status' => 3,
                'MemberRenewal.member_id' => $member_id
            ),
            'order' => array('OrderPaymentLog.id' => 'DESC'),
        );

        return $this->find('all', $option);
	}
	
	public function get_status_member_renewal_trans($data, $lang) {
		$status = false;
		$message = "";
		$params = (object)array();

		$objMember = ClassRegistry::init('Member.Member');		
		$member_id = $objMember->get_id_by_token($data);
		if ($member_id <= 0) {
			$status = false;
			$message = __('member_not_found');

			goto return_result;	
		}

		$option = array(
			'conditions' => array(
				'inv_number' => $data['inv_number'],
				'member_id' => $member_id
			)
		);

		$data_member_renewal = $this->find('first', $option);
		if (!isset($data_member_renewal['MemberRenewal']['id']) || empty($data_member_renewal['MemberRenewal']['id'])) {
			$status = false;
			$message = __('inv_number_invalid_trans_not_found');

			goto return_result;
		}

		$result = array();
		$result['is_notification_received'] = $data_member_renewal['MemberRenewal']['is_notification_received'];
		$result['payment_status'] = ($data_member_renewal['MemberRenewal']['status'] == 3) ? 1 : 0;
		$message = __('transaction_found');

		$params = $result;

		$status = true;

		return_result :

		return array('status' => $status, 'message' => $message, 'params' => $params);
	}
}
