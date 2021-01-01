<?php
App::uses('MemberAppModel', 'Member.Model');
/**
 * Member Model
 *
 * @property LogApi $LogApi
 * @property Order $Order
 * @property Purchase $Purchase
 * @property Coupon $Coupon
 * @property Notification $Notification
 */
class Member extends MemberAppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */

	// The Associations below have been created with all possible keys, those that are not needed can be removed

	public $actsAs = array('Containable');

	public $country_codes = array(
        '+852',
        '+853',
        '+86',
        '+84',
	);

	public $title = array(
		'mr' => 'Mr',
		'ms' => 'Ms',
		'mrs' => 'Mrs',
		'miss' => 'Miss',
	);

	public $validate = array(
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
		'Age' => array(
			'className' => 'Member.AgeGroup',
			'foreignKey' => 'age_group_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		// 'LogApi' => array(
		// 	'className' => 'LogApi',
		// 	'foreignKey' => 'member_id',
		// 	'dependent' => false,
		// 	'conditions' => '',
		// 	'fields' => '',
		// 	'order' => '',
		// 	'limit' => '',
		// 	'offset' => '',
		// 	'exclusive' => '',
		// 	'finderQuery' => '',
		// 	'counterQuery' => ''
		// ),
		// 'Order' => array(
		// 	'className' => 'Order',
		// 	'foreignKey' => 'member_id',
		// 	'dependent' => false,
		// 	'conditions' => '',
		// 	'fields' => '',
		// 	'order' => '',
		// 	'limit' => '',
		// 	'offset' => '',
		// 	'exclusive' => '',
		// 	'finderQuery' => '',
		// 	'counterQuery' => ''
		// ),
		// 'Purchase' => array(
		// 	'className' => 'Purchase',
		// 	'foreignKey' => 'member_id',
		// 	'dependent' => false,
		// 	'conditions' => '',
		// 	'fields' => '',
		// 	'order' => '',
		// 	'limit' => '',
		// 	'offset' => '',
		// 	'exclusive' => '',
		// 	'finderQuery' => '',
		// 	'counterQuery' => ''
		// ),
		'MemberCoupon' => array(
			'className' => 'Member.MemberCoupon',
			'foreignKey' => 'member_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => '',
		),
		'MemberRenewal' => array(
			'className' => 'Member.MemberRenewal',
			'foreignKey' => 'member_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => '',
		),
	);


	/**
	 * hasAndBelongsToMany associations
	 *
	 * @var array
	 */
	public $hasAndBelongsToMany = array(
		'Coupon' => array(
			'className' => 'Coupon',
			'joinTable' => 'member_coupons',
			'foreignKey' => 'member_id',
			'associationForeignKey' => 'coupon_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'Notification' => array(
			'className' => 'Notification',
			'joinTable' => 'member_notifications',
			'foreignKey' => 'member_id',
			'associationForeignKey' => 'notification_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

	public function get_data_export($conditions, $page, $limit, $lang){
	    $now = date('Y-m-d');
		$all_settings = array(
            'fields' => array("Member.*"),
            'conditions' => array($conditions),
            'joins' => array(
                array(
                    'alias' => 'MemberRenewal',
                    'table' => Environment::read('table_prefix') . 'member_renewals',
                    'type' => 'left',
                    'conditions' => array(
                        'MemberRenewal.member_id = Member.id'
                    ),
                ),
            ),
            'contain' => array (
                'MemberRenewal' => array(
                    'conditions' => array(
                        //'MemberRenewal.status' => 3
                    ),
                    'order' => array('MemberRenewal.expired_date' => 'DESC')
                )
            ),
            'limit' => Environment::read('web.limit_record'),
            'order' => array('Member.created' => 'DESC'),
            'group' => array(
                'Member.id'
            ),
			'limit' => $limit,
			'page' => $page
		);

		return $this->find('all', $all_settings);
	}

	public function format_data_export($data, $row, $data_binding){
		$model = $this->alias;

		$result = array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row[$model]["name"]) ?  $row[$model]["name"] : ' ',
			!empty($row[$model]["birth_month"]) ?  $data_binding['dobMonths'][$row[$model]["birth_month"]] : ' ',
			!empty($row[$model]["country_code"]) ?  $row[$model]["country_code"] : ' ',
			!empty($row[$model]["phone"]) ?  $row[$model]["phone"] : ' ',			
			!empty($row[$model]["email"]) ?  $row[$model]["email"] : ' ',			
		);

		return $result;
	}

	public function find_list_select_field($conditions, $field, $model){
		$data = array();

		$fields = array(
			$model . '.id',
			$model . '.' .$field,
		);

		// if (isset($conditions) && !empty($conditions)) {
		// 	$conditions = array_merge($conditions, array($model.'.enabled' => true));
		// }else{
		// 	$conditions = array(
		// 		$model.'.enabled' => true,
		// 	);
		// }

		$data = $this->find('all', array(
			'fields' => $fields,
            'conditions' => $conditions,
            'recursive' => -1,
            'order' => array($model . '.' .$field => 'ASC'),
            'limit' => 20
		));

		if ($data) {
			$data = Hash::combine($data, '{n}.' . $model . '.id', '{n}.' . $model . '.' . $field);
		}else{
			$data = array();
		}

		return $data;
	}
	
    public function get_country_codes(){
        $result = array();
        foreach($this->country_codes as $value){
            $result[$value] = $value;
        }
        
        return $result;
	}
		
	public function get_item_by_phone($country_code, $phone){

		$member = $this->find('first', array(
            'fields' => $this->return_fields,
            'conditions' => array(
                'country_code' => trim($country_code),
                'phone' => trim($phone),
            )
        ));

        if($member){
            return $member['Member'];
        }else{
            return array();
        }
	}	

	public function get_item_by_email_password($email, $password){

		$member = $this->find('first', array(
            'fields' => [],
            'conditions' => array(
                'email' => trim($email),
                'password' => md5($password),
            )
        ));

        if($member){
            return $member['Member'];
        }else{
            return array();
        }
	}	
	
	public function login($data, $lang) {

		$log_data = array();
		$log_data['data'] = $data;
		$error = array();
		$warning = array();
        $status = false;
		$params = array();
		$now = date( 'Y-m-d' );

		$options = array(
			'conditions' => array(
				'OR' => array (
				    'email' => $data['email'],
                    'phone' => $data['email']
                )
			),
			'contain' => array(
				'MemberRenewal' => array(
					'conditions' => array(
						'date(MemberRenewal.renewal_date) <=' => $now,
						'date(MemberRenewal.expired_date) >=' => $now,
						'status' => 3
					)
				)
			),
			'recursive' => -1
		);

		$model = $this->alias;
		$data_result = $this->find('first', $options);


		if (isset($data_result[$model]) && !empty($data_result[$model])) {
            // Add variable check verify email, phone
            $is_verified_phone = $is_verified_email = $is_renewed = true;
            if (empty($data_result[$model]['phone_verified'])) {
                $is_verified_phone = false;
            }
            if (empty($data_result[$model]['email_verified'])) {
                $is_verified_email = false;
            }
            if (count($data_result['MemberRenewal']) == 0) {
                $is_renewed = false;
            }
            $data_result[$model]['is_verified_phone'] = $is_verified_phone;
            $data_result[$model]['is_verified_email'] = $is_verified_email;
            $data_result[$model]['is_renewed'] = $is_renewed;

            if (md5($data['password']) != $data_result[$model]['password']) {
                $message = __('wrong_password');
                $log_data['message'] = $message;
                $error['password'] = $message;
                goto return_data;
            }

            if ($data_result[$model]['enabled'] == false) {
                $message = __('this_account_was_disabled');
                $log_data['message'] = $message;
                $error['phone'] = $message;

                goto return_data;
            } else if (!empty($data_result[$model]['deleted'])) {
                $message = __('this_account_was_deleted');
                $log_data['message'] = $message;
                $error['phone'] = $message;

                goto return_data;
            // } else if ($data_result[$model]['is_register'] == 0) {
            // 	$message =  __('registration_needed');
            // 	$log_data['message'] = $message;
            // 	$warning['registration'] = $message;
            } else if (empty($data_result[$model]['phone_verified'])) {
			 	$message =  __('phone_verification_needed');
			 	$log_data['message'] = $message;
            } else if (empty($data_result[$model]['email_verified'])) {
                $message =  __('email_verification_needed');
                $log_data['message'] = $message;
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
					$error['other'] = $message;

					goto return_data;
				}
			}

			$check_commit = true;
			if (! isset($data['is_browser']) || empty($data['is_browser'])) {
				$objMemberDevice = ClassRegistry::init('Member.MemberDevice');
				$check_commit = $objMemberDevice->create_new_device($data, $data_result['Member']['id']);
			}
				
			if ($check_commit) {
				$dbo->commit();

				// add full qrcode path
                $data_result[$model]['qrcode_path'] = Environment::read('web.url_img').$data_result[$model]['qrcode_path'];
            } else {
				$dbo->rollback();
				$message =  __('save_device_failed');
				$log_data['message'] = $message;
				$error['other'] = $message;

				goto return_data;
			}

			$status = true;
			$params = $data_result;
			$message = $model.' is found';
		} else {
			$message = __('user_not_found');
			$log_data['message'] = $message;
			$error['email'] = $message;
		}
		
		return_data:
		return array(
			'status' => $status,
			'message' => $message,
            'params' => $params,
			'log_data' => $log_data,
			'error' => $error,
			'warning' => $warning,
		);
	}

	public function get_id_by_token($data) {
		$options = array(
			'conditions' => array(
				'token' => $data['token'],
				'enabled' => true,
				// 'deleted' => null,
				// 'is_register' => 1,
			),
		);

		$data_user = $this->find('first', $options);

		if($data_user){
			return $data_user['Member']['id'];
		}else{
			return 0;
		}
	}	

	public function get_member_by_field($value, $field = 'phone') {
		$options = array(
			'conditions' => array(
				'enabled' => 1,
				$field => trim($value),
			),
			'recursive' => -1
		);

		return $this->find('first', $options);
	}

	public function get_member_by_conditions($condition) {
		$options = array(
			'conditions' => $condition,
			'recursive' => -1
		);

		return $this->find('first', $options);
	}

	public function check_member_validity($member_id = 0) {
		$option = array(
			'conditions' => array(
				'id' => $member_id,
				'enabled' => 1
			)
		);

		$count_member = $this->find('count', $option);

		return (($count_member > 0) ? true: false);
	}

    public function get_data_today_export($data, $page, $limit, $lang){
        $now = date('Y-m-d');

        $prefix = Environment::read('database.prefix');
        $sqlstr = "CREATE TEMPORARY TABLE IF NOT EXISTS " . $prefix . "mytable_member_renewals AS (".
            "select mr.* " .
            "from " . $prefix . "member_renewals AS mr " .
            "where mr.expired_date >= " . $now . " " .
            "group by mr.member_id)";
        $this->query($sqlstr);


        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_from'])));
        $date_to = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_to'])));
        $conditions = array(
            'DATE(Member.created) >=' => $date_from,
            'DATE(Member.created) <=' => $date_to
        );

        $all_settings = array(
            'fields' => array(
                "Member.*",
                "AgeGroupLanguage.*",
                "MemberRenewal.*"
            ),
            'conditions' => array($conditions),
            'joins' => array(
                array(
                    'alias' => 'AgeGroupLanguage',
                    'table' => Environment::read('table_prefix') . 'age_group_languages',
                    'type' => 'left',
                    'conditions' => array(
                        'AgeGroupLanguage.age_id = Member.age_group_id',
                        'AgeGroupLanguage.language' => $lang
                    ),
                ),
                array(
                    'alias' => 'MemberRenewal',
                    'table' => $prefix . 'mytable_member_renewals',
                    'type' => 'left',
                    'conditions' => array(
                        'MemberRenewal.member_id = Member.id'
                    ),
                ),
            ),
            'contain' => array (
//                'MemberRenewal' => array(
//                    'conditions' => array(
//                        //'MemberRenewal.status' => 3
//                    ),
//                    'order' => array('MemberRenewal.expired_date' => 'DESC')
//                ),
            ),
            'order' => array('Member.created' => 'DESC'),
            'group' => array(
                'Member.id'
            ),
            //'limit' => $limit,
            //'page' => $page
        );

        $result = $this->find('all', $all_settings);
        return $result;
    }

    public function format_data_today_export($data, $row, $data_binding){
        $model = $this->alias;

        $result = array(
            !empty($row['MemberRenewal']["date"]) ?  $row['MemberRenewal']["date"] : ' ',
            !empty($row['Member']["code"]) ?  $row['Member']["code"] : ' ',
            !empty($row['Member']["name"]) ?  $row['Member']["name"] : ' ',
            !empty($row['AgeGroupLanguage']["name"]) ?  $row['AgeGroupLanguage']["name"] : ' ',
            !empty($row['Member']['phone_verified']) ? 'Yes' : 'No',
            !empty($row['Member']['email_verified']) ? 'Yes' : 'No',
            !empty($row['MemberRenewal']['inv_number']) ? $row['MemberRenewal']['inv_number'] : '',
            !empty($row['MemberRenewal']['renewal_date']) ? $row['MemberRenewal']['renewal_date'] : '',
            !empty($row['MemberRenewal']['expired_date']) ? $row['MemberRenewal']['expired_date'] : '',
        );

        return $result;
	}
	
	public function check_phone_for_registration($data, $lang) {
		$status = false;
		$message = "";
		$params = (object)array();
		
		//check the number phone in member table
		//check the number phone in member registration table
		//if the country_code + phone is valid then return back the registration fee

		$conditions = [
			'country_code' => $data['country_code'],
			'phone' => $data['phone']
		];
		
		// Check exist phone
		$data_user = $this->get_member_by_conditions($conditions);

		if (isset($data_user['Member']) && !empty($data_user['Member'])) {
			// check phone exist
			$message = __('exist_phone_number');
			
			goto return_result;
		}

		$option = array(
			'conditions' => array(
				'country_code' => $data['country_code'],
				'phone' => $data['phone'],
				'void' => 0
			)
		);

		$objMemberPosRegistration = ClassRegistry::init('Member.MemberPosRegistration');
		$data_registration = $objMemberPosRegistration->find('first', $option);
		
		if (isset($data_registration['MemberPosRegistration']) && !empty($data_registration['MemberPosRegistration'])) {
			// check phone exist in temporary registration
			$message = __('exist_phone_number_temp_registration');
			
			goto return_result;
		}

		$status = true;

		return_result :

		return array('status' => $status, 'message' => $message, 'params' => $params);
	}

	public function get_pos_registration($data, $language) {
		$status = false;
		$message = "";
		$params = (object)array();

		$options = array(
			'conditions' => array(
				'verification_code' => $data['verification_code'],
				'void' => 0,
			)
		);

		$objMemberPosRegistration = ClassRegistry::init('Member.MemberPosRegistration');
		$data_registration = $objMemberPosRegistration->find('first', $options);

		if (isset($data_registration['MemberPosRegistration']) && !empty($data_registration['MemberPosRegistration'])) {
			$status = true;
			$params = array();
			$params['registration_id'] = $data_registration['MemberPosRegistration']['id'];
			$params['order_id'] = $data_registration['MemberPosRegistration']['order_id'];
			$params['phone'] = $data_registration['MemberPosRegistration']['phone'];
			$params['country_code'] = $data_registration['MemberPosRegistration']['country_code'];
			$params['date'] = $data_registration['MemberPosRegistration']['date'];
			$params['expiry_date'] = $data_registration['MemberPosRegistration']['expiry_date'];
			$params['amount'] = $data_registration['MemberPosRegistration']['amount'];
			$message = __('data_registration_found');
		} else {
			$message = __('data_registration_not_found');
		}

		return_result :

		return array('status' => $status, 'message' => $message, 'params' => $params);		
	}

}

