<?php
App::uses('MemberAppController', 'Member.Controller');

class MemberRenewalsController extends MemberAppController {

	public $components = array('Paginator');
	private $model = 'MemberRenewal';
	private $member_model = 'Member';
    private $payment_log_model = 'OrderPaymentLog';
	
	private $filter = array(
		'name',
	);
	private $rule = array(
		1 => array('required'),
		2 => array('required','enum'),
	);
	private $rule_spec = array(
		2 => array('N', 'Y', 'y', 'n')
	);

	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('member', 'renewal_item'));
	}

	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;
		$member_model = $this->member_model;

		$languages_model = $this->model_lang;

		$conditions = [];

        $conditions[$model.'.status'] = 3;

		if (isset($data_search) && !empty($data_search['name']))
		{
			$conditions['Member.name LIKE'] = '%' . $data_search['name'] . '%';
		}

		if (isset($data_search) && !empty($data_search['phone']))
		{
			$conditions['Member.phone LIKE'] = '%' . $data_search['phone'] . '%';
		}

		if (isset($data_search) && !empty($data_search['renewal_date']))
		{
			$conditions['date(MemberRenewal.renewal_date)'] = $data_search['renewal_date'];
		}

		if (isset($data_search) && !empty($data_search['expired_date']))
		{
			$conditions['date(MemberRenewal.expired_date)'] = $data_search['expired_date'];
		}

		if ($data_search){
			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'member',
                    'controller' => 'member_renewals',
                    'action' => 'export',
                    'admin' => true,
                    'prefix' => 'admin',
                    'ext' => 'json'
                ), array(
                    'conditions' => $conditions,
                    'type' => 'csv',
                ));
            }

            // button export Excel
            if( isset($data_search['button']['exportExcel']) && !empty($data_search['button']['exportExcel']) ) {
                $this->requestAction(array(
                    'plugin' => 'member',
                    'controller' => 'member_renewals',
                    'action' => 'export',
                    'admin' => true,
                    'prefix' => 'admin',
                    'ext' => 'json'
                ), array(
                    'conditions' => $conditions,
                    'type' => 'xls',
                ));
		    }			
		}
		

		$this->Paginator->settings = array(
			'contain' => [
				'Member' => [
					'id', 'name' , 'phone'
				]
			],
			'fields' => array($model.".*", 'MAX(expired_date) as lastest_expired_date'),
			'conditions' => array($conditions),
            'limit' => Environment::read('web.limit_record'),
			'group' => array($model.'.member_id'),
			'order' => array($model . '.created' => 'DESC'),
		);

        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'data_search', 'member_model'));	
	}

	public function admin_view($id) {
		$model = $this->model;
		$member_model = $this->member_model;
        $payment_log_model = $this->payment_log_model;

        // Get Member Id by Member Renewal Id
        $options = array(
            'fields' => array($model.'.*'),
            'joins' => array(
            ),
            'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
        );
        $member_renewal_date = $this->$model->find('first', $options);

        $member_id = $member_renewal_date[$model]['member_id'];

		$options = array(
			'fields' => array($model.'.*', 'MAX(expired_date) as lastest_expired_date'),
			'contain' => array(
				'Member' => [ 'id', 'name', 'phone'],
				'UpdatedBy',
				'CreatedBy'
			),
            'joins' => array(
                array(
                    'alias' => 'OrderPaymentLog',
                    'table' => Environment::read('table_prefix') . 'order_payment_logs',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderPaymentLog.id = '.$model.'.payment_log_id',
                    )
                )
            ),
			'conditions' => array($model.'.member_id' => $member_id),
            'group' => array($model.'.member_id')
        );
		$model_data = $this->$model->find('first', $options);

        $log_payment = $this->$model->get_payment_log_by_member_id($member_id);

		if (!$model_data) {
			throw new NotFoundException(__('invalid_data'));
		}

		$this->set('dbdata', $model_data);
        $this->set(compact('model', 'member_model' , 'payment_log_model', 'log_payment'));
	}


	public function admin_add() {
		$model = $this->model;
		$languages_model = $this->model_lang;

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

			$valid = true;

			if ($valid) {
				$dbo = $this->$model->getDataSource();
				$dbo->begin();
				if ($this->$model->saveAll($data)) {
					$dbo->commit();
					$this->Session->setFlash(__('data_is_saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				} else {
					$dbo->rollback();
					$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
				}
			} else {
				$this->Session->setFlash(__d('static', 'data_is_not_saved'), 'flash/error');
			}
			
		}

		//languages fields
		$language_input_fields = $this->language_input_fields;

		$languages_list = (array)Environment::read('site.available_languages');

		$this->set(compact('model', 'language_input_fields', 'languages_model', 'languages_list'));
	}

	public function admin_edit($id = null) {
	
	}

	public function admin_delete($id = null) {
	}

	public function admin_export(){
		$model = $this->model;

		$results = array(
		   'status' => false, 
		   'message' => __('missing_parameter'),
		   'params' => array(),
	   );

	   $this->disableCache();

	   if( $this->request->is('get') ) {
		   $result = $this->$model->get_data_export($this->request->conditions, 1, 2000, $this->lang18);

		   if ($result) {

				$cvs_data = array();

				foreach ($result as $row) {
					$temp = $this->$model->format_data_export(array(), $row);

					array_push($cvs_data, $temp);
				}

			   try{
				   $file_name = 'member_renewals_'.date('Ymd');

				   // export xls
				   if ($this->request->type == "xls") {
						$excel_readable_header = array(
							array('label' => __('id')),
							array('label' => __('name')),
							array('label' => __('phone')),
							array('label' => __('renewal_date')),
							array('label' => __('expired_date'))
						);
	
						$this->Common->export_excel(
							$cvs_data,
							$file_name,
							$excel_readable_header
						);
					} else {
						$header = array(
							'label' => __('id'),
							'label' => __('name'),
							'label' => __('phone'),
							'label' => __('renewal_date'),
							'label' => __('expired_date'),
						);
						$this->Common->export_csv(
							$cvs_data,
							$header,
							$file_name
						);
					}
			   	} catch ( Exception $e ) {
					$this->LogFile->writeLog($this->LogFile->get_system_error(), $e->getMessage());
					$results = array(
						'status' => false, 
						'message' => __('export_csv_fail'),
						'params' => array()
					);
			   	}
			}else{
				$results['message'] = __('no_record');
			}
	   }

	   $this->set(array(
		   'results' => $results,
		   '_serialize' => array('results')
	   ));
	}

	public function admin_import($id = null) {	
	}

	public function api_create_member_renewal_trans() {
		$this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
            
            if (!isset($data['token']) || empty($data['token'])) {
			// if (!isset($data['member_id']) || empty($data['member_id'])) {
				$message = __('missing_parameter') .  __('token');
				// $message = __('missing_parameter') .  __('member_id');
			} else if (!isset($data['language']) || empty($data['language'])) {
                $message = __('missing_parameter') .  __('language');
            } else {
				$this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->create_member_renewal_trans($data, $this->Api->get_language());

                $status = $result['status'];
				$message = $result['message'];

                if($result['status']){
					$params = $result['params'];
                    if (!$params) {
                        $params = (object)array();
                    }

                }else{
                    if(isset($result['log_data']) && $result['log_data']){
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
			}

            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
	}

	public function api_cancel_member_renewal_trans() {
		$this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
            
            if (!isset($data['inv_number']) || empty($data['inv_number'])) {
				$message = __('missing_parameter') .  __('inv_number');
			} else if (!isset($data['language']) || empty($data['language'])) {
				$message = __('missing_parameter') .  __('language');
            } else {
				$this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->cancel_member_renewal_trans($data, $this->Api->get_language());

                $status = $result['status'];
				$message = $result['message'];

                if($result['status']){
					$params = $result['params'];
                    if (!$params) {
                        $params = (object)array();
                    }

                }else{
                    if(isset($result['log_data']) && $result['log_data']){
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
			}

            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
	}

	public function api_update_trans_renewal() {
		$model = $this->model;
		$this->layout = null;
		$this->autoRender = false;

		$message = '';
		$verified = false;
		$return_object = array();
		$data = $this->request->data;

        if (isset($data['language']) && !empty($data['language'])) {
            $this->Api->set_language($data['language']);
        } else {
            $this->Api->set_language($this->lang18);
        }

		if ($this->request->is('post') && !empty($data)) {
			ksort($data);

			$sign = "";
			$signType = "";
			$payment_string = "";

			foreach ($data as $key => $val) {
				if( $key == "sign" ){
					$sign = trim($val);
				}else if( $key == "signType" ){
					$signType = trim($val);
				} else {
					$payment_string .= trim($key) . "=" . trim($val) . "&";
				}
			}

			// Append the Secret key to the tail of the string with separator "&"
			$payment_string .= Environment::read('site.project_key');

			$hashed_string = hash('sha256', trim($payment_string));

			// Verify the return object is correct and secure
			// 
			// If the verification fails, we can treat the payment as failed. Redirect to 
			if( !empty($sign) && ($hashed_string == $sign) ){
				$verified = true;
			}

			if( $verified ){
				/**
					* Update the Payment Status (and other relevant info) into Transaction Table
					* 
					* including :
					* - PayType : refer to P.43 Appendix > 6.2 Payment Types
					* - payRef : UNIQUE payment reference generated by Payment Gateway for every valid request.
					* - resMsg : as a remark, response (error) message from Payment Gateway

					* check the state and merRef => if its invalid set the message
					* and if its valid then get the trans and save the token into member_renewals and update the trans status
				*/

				$valid = true;
				$payment_settled = false;
				if (isset($data['state']) && ($data['state'] == 1)) {
					$payment_settled = true;
				} else {
					$valid = false;
					$message = 'payment_failed';
				}

				$data_inv_number_valid = false;
				if (isset($data['merRef']) && !empty($data['merRef'])) {
					$data_inv_number_valid = true;
				} else {
					$valid = false;
					$message = 'inv_number_doesnt_exists';
				}

				$token = '';
				$trans_id = 0;
				if ($payment_settled && $data_inv_number_valid) {
					$inv_number = $data['merRef'];

					$option = array(
						'conditions' => array(
							'inv_number' => $inv_number
						)
					);

					$data_member_renewal = $this->$model->find('first', $option);


					if (isset($data_member_renewal['MemberRenewal']['id']) && !empty($data_member_renewal['MemberRenewal']['id'])) {
						$token = $data_member_renewal['MemberRenewal']['token'];
						$trans_id = $data_member_renewal['MemberRenewal']['id'];

						$this->create_member_coupon($data_member_renewal);
						$this->create_member_coupon($data_member_renewal);
					} else {
						$message = 'inv_number_invalid_trans_not_found';
					}
				}

				$data['date'] = date('Y-m-d H:i:s');
				$data['trans_token'] = $token;
				$data['message'] = $message;

				$dbo = $this->$model->getDataSource();
				$dbo->begin();
				try {
					$objRenewalPaymentLog = ClassRegistry::init('Member.RenewalPaymentLog');
					if ($objRenewalPaymentLog->save($data)) {
						$log_id = $objRenewalPaymentLog->id;

						if($valid) {
							$data_member_renewal['MemberRenewal']['payment_log_id'] = $log_id;
							$data_member_renewal['MemberRenewal']['is_notification_received'] = 1;
							$data_member_renewal['MemberRenewal']['status'] = 3;
							$data_member_renewal['MemberRenewal']['renewal_date'] = date('Y-m-d');
							$data_member_renewal['MemberRenewal']['expired_date'] = date('Y-m-d', strtotime('+1 years'));

							if($this->$model->saveAll($data_member_renewal)) {
								$dbo->commit();
								$message = 'trans_completed_successfully';
							} else {
								$dbo->rollback();
								$message = 'failed_to_update_transaction';
								goto write_to_log;
							}

						}

					} else {
						$dbo->rollback();
						$message = 'log_failed_to_save';
						goto write_to_log;
					}
				} catch (Exception $e) {
					$dbo->rollback();
					$message = __('data_is_not_saved') . ' ' . $e->getMessage();
		
					goto write_to_log;
				}
			}
		}

		write_to_log:
		$post = json_encode($data);

		//A PHP array containing the data that we want to log.
		$dataToLog = array(
			date("Y-m-d H:i:s"), //Date and time
			$_SERVER['REMOTE_ADDR'], //IP address
			$message,
			$post, //Custom text
			$verified
		);
		$str_log = implode(" - ", $dataToLog);

		if( isset($return_object) && !empty($return_object) ){
			$str_log .= "\r\n\r\n--=== Start ===--\r\n";
	
			foreach ($return_object as $key => $value) {
				$str_log .= $key . " : " . $value . "\r\n";
			}
	
			$str_log .= "--=== End ===--\r\n";
		}

		//Add a newline onto the end.
		$str_log .= "\r\n";
		$str_log .= PHP_EOL;

		//The name of your log file.
		//Modify this and add a full path if you want to log it in 
		//a specific directory.
		$pathToFile = '../tmp/logs/payment-notify.log';
		
		try {
			//Log the data to your file using file_put_contents.
			file_put_contents($pathToFile, $str_log, FILE_APPEND);		
		} catch (Exception $e) {
			print "Fail to log to file.";
		}
	}

	/*
	public function create_member_coupon($data_member_renewal) {
        $data_member_coupon = array();

        $objCoupon = ClassRegistry::init('Pos.Coupon');

        $options = array(
            'fields' => array(
                'Coupon.*'
            ),
            'contain' => array(
                'CouponLanguage' => array(
                    'conditions' => array(
                        'language' => $this->Api->get_language()
                    )
                )
            ),
            'conditions'=> array( 'type' => 1 ),
        );
        $coupon_result = $objCoupon->find('first', $options);

        $coupon_id = $coupon_result['Coupon']['id'];

        $member_id = $data_member_renewal['MemberRenewal']['member_id'];
        $data_member_coupon['MemberCoupon']['coupon_id'] = $coupon_id;
        $data_member_coupon['MemberCoupon']['member_id'] = $member_id;

        $objMember = ClassRegistry::init('Member.Member');
        $code = $objMember->generateToken();
        $data_member_coupon['MemberCoupon']['code'] = $code;
        $data_member_coupon['MemberCoupon']['expired_date'] = date('Y-m-d', strtotime('+1 years'));
        $qr_code_path = $this->Common->generate_qrcode("coupon", $code, $code)['path'];
        $data_member_coupon['MemberCoupon']['code_path'] = $qr_code_path;
        $data_member_coupon['MemberCoupon']['status'] = 1;

        $objMemberCoupon = ClassRegistry::init('Member.MemberCoupon');
        if ( $objMemberCoupon->saveAll($data_member_coupon)) {
            $objMember = ClassRegistry::init('Member.Member');
            $member_info = $objMember->find('first', array('conditions'=> array('id' => $member_id)));
            $qr_code_link = Environment::read('web.url_img') . $qr_code_path ;

            $data_sent = array();
            $data_sent['expired_date'] = isset($data_member_coupon['MemberCoupon']['expired_date']) ? $data_member_coupon['MemberCoupon']['expired_date'] : null;
            $data_sent['expired_date'] = date('Y-m-d', strtotime($data_sent['expired_date']));
            $data_sent['name'] = isset($coupon_result['Coupon']['description']) ? $coupon_result['Coupon']['description'] : null;
            $data_sent['des'] = isset($coupon_result['CouponLanguage'][0]['des']) ? $coupon_result['CouponLanguage'][0]['des'] : null;
            $data_sent['terms'] = isset($coupon_result['CouponLanguage'][0]['terms']) ? $coupon_result['CouponLanguage'][0]['terms'] : null;
            $data_sent['welcome_coupon_code'] = $qr_code_link;

            // Send code to mobile
            $country_code = $member_info['Member']['country_code'];
            $phone = $member_info['Member']['phone'];

            $receiver = array();
            $receiver[0]['phone'] = $country_code . $phone;
            $receiver[0]['language'] = $this->lang18;

            $str_title = 'ACX-Cinemas';
            $title = array($this->lang18 => $str_title);

            $str_msg = sprintf(
                __('welcome_coupon_msg'),
                $data_sent['name'],
                $data_sent['expired_date'],
                $data_sent['welcome_coupon_code']
            );
            $sms_message = array($this->lang18 => $str_msg);

            $sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
            // $sent_data['status'] = true;
            if (!$sent_data['status']) {
                $result_data = $sent_data;
                $message = __('send_sms_failed');
                $valid = false;
            }

            $email = $member_info['Member']['email'];

            // Send code to email
            $template = "welcome_coupon";
            $subject = 'ACX-Cinemas - Welcome Coupon';

            $receiver = $email;

            $data_sent['email'] = $email;

            $result_email = $this->Email->send($receiver, $subject, $template, $data_sent);

            if (!$result_email['status']) {
                $result_data = $result_email;
                $message = __('send_email_failed');
                $valid = false;
            }

        } else {
            $message = 'fail_to_create_coupon';
        }
    }
	*/
	
    public function api_get_price_renewal_member() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post'))
        {
            $this->disableCache();
            $data   = $this->request->data;

            $status = true;
            $message = __('retrieve_data_successfully');
            $params = (object)array();

            /*            if( ! isset($data['language']) || empty($data['language']) ){
                            $message = __('missing_parameter') .  __('language');
                        } else if( ! isset($data['schedule_detail_id']) || empty($data['schedule_detail_id']) ){
                            $message = __('missing_parameter') .  __('schedule_detail_id');
                        }
                        else {*/
            //$this->Api->set_language($data['language']);

            $url_params = $this->request->params;
            $this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);

            $objSetting = ClassRegistry::init('Setting.Setting');

            $params = $objSetting->get_value('member-renewal');

            /*}*/

            if (!$params) {
                $params = (object)array();
            }

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
	}
	
	public function admin_send_coupon() {
		
		/*
		$order_id = 431;

		$conditions = array(
			'Order.id' => $order_id,
			'Order.void' => 0
		);

		$objOrder = ClassRegistry::init('Pos.Order');
		$result = $objOrder->get_data_order($order_id, $this->lang18, $conditions);
		$data_order = $result['params'];
		*/
		/*
		$country_code = '+852';
		$phone = '92646161';

		$title = 'ACX-Cinema';

		$sms_message = sprintf(
			__('order_qrcode_msg'),
			$data_order['MovieLanguage']['name'] . ' (' . $data_order['MovieType']['name'] . ')',
			$data_order['ScheduleDetail']['date_display'],
			$data_order['ScheduleDetail']['time_display'],
			$data_order['Hall']['code'],
			count(Hash::extract( $data_order['Seat'], "{n}.ScheduleDetailLayout.title" )),
			implode(', ', Hash::extract( $data_order['Seat'], "{n}.ScheduleDetailLayout.title" )),
			Environment::read('web.url_img') . $data_order['Order']['qrcode_path']
		);

		$result = $this->send_sms($country_code, $phone, $this->lang18, $title, $sms_message);
		*/

		/*
		$email = 'lsasus@hotmail.com';

		$template = 'order_qrcode';
		$subject = 'ACX-Cinema - Ticket QR Code';

		$data_email['movie_display'] = $data_order['MovieLanguage']['name'] . ' (' . $data_order['MovieType']['name'] . ')';
		$data_email['date_display'] = $data_order['ScheduleDetail']['date_display'];
		$data_email['time_display'] = $data_order['ScheduleDetail']['time_display'];
		$data_email['hall_display'] = $data_order['Hall']['code'];
		$data_email['number_of_seats'] = count(Hash::extract( $data_order['Seat'], "{n}.ScheduleDetailLayout.title" ));
		$data_email['seats'] = implode(', ', Hash::extract( $data_order['Seat'], "{n}.ScheduleDetailLayout.title" ));
		$data_email['qrcode_path'] = Environment::read('web.url_img') . $data_order['Order']['qrcode_path'];

		$result = $this->send_email($email, $template, $subject, $data_email);

		if (!$result['status']) {
			$status = false;
			$message .= ' - '.$result['message'];
		}
		*/
	}

	public function api_get_trans_status() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = true;
            $message = "";
            $params = (object)array();
            $data = $this->request->data;

			if (!isset($data['token']) || empty($data['token'])) {
				$message = __('missing_parameter') . __('token');
            } else if (!isset($data['inv_number']) || empty($data['inv_number'])) {
				$message = __('missing_parameter') . __('inv_number');
			} else if (!isset($data['language']) || empty($data['language'])) {
				$message = __('missing_parameter') . __('language');
            } else {
                $this->Api->set_language($data['language']);

				$url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->get_status_member_renewal_trans($data, $this->Api->get_language());

                $status = $result['status'];
				$message = $result['message'];

                if($result['status']){
					$params = $result['params'];
                    if (!$params) {
                        $params = (object)array();
                    }

                }else{
                    if(isset($result['log_data']) && $result['log_data']){
                        $this->Api->set_error_log($result['log_data']);
                    }
                }

            }

            return_result:

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();		
	}
}
