<?php
App::uses('MemberAppController', 'Member.Controller');
/**
 * Members Controller
 *
 * @property Member $Member
 * @property PaginatorComponent $Paginator
 */
class MembersController extends MemberAppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');
	private $model = 'Member';
	private $device_model = 'MemberDevice';

	private $filter = array(
		// 'code',
		// 'name',
	);

	private $rule = array(
		1 => array('required'),
		2 => array('required'),
		3 => array('required'),
		4 => array('required'),
		5 => array('required'),
	);
	private $rule_spec = array(

	);

    private $upload_path = 'members';
    private $poster_prefix = 'image';

	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('member', 'item_title'));
	}


	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;
		$languages_model = $this->model_lang;
        $now = date('Y-m-d');

		$dobMonths = $this->Common->get_list_month();

		$condition_temp = array();
        $condition_renewal = array();
        $data_search_origin = $data_search;
        $join_array = array();

        $prefix = Environment::read('database.prefix');
        $sqlstr = "CREATE TEMPORARY TABLE IF NOT EXISTS " . $prefix . "mytable_member_renewals AS (".
            "select mr.* " .
            "from " . $prefix . "member_renewals AS mr " .
            "where mr.expired_date >= " . $now . " " .
            "group by mr.member_id)";
        $this->$model->query($sqlstr);

		if (isset($data_search['dob_months']) && !empty($data_search['dob_months'])) {
			$condition_temp['Member.birth_month'] = $data_search['dob_months'];
			//unset($data_search['dob_months']);
		}

		if (isset($data_search['phone_verified'])) {
            if ($data_search['phone_verified'] ==  '1') {
                $condition_temp['Member.phone_verified !='] = null;
            } else if ($data_search['phone_verified'] == '0') {
                $condition_temp['Member.phone_verified'] = null;
            }
        }

		if (isset($data_search['email_verified'])) {
            if ($data_search['email_verified'] == '1') {
                $condition_temp['Member.email_verified !='] = null;
            } else if ($data_search['email_verified'] == '0') {
                $condition_temp['Member.email_verified'] = null;
            }
        }


		if (isset($data_search['renewal_status'])) {
            if ($data_search['renewal_status'] == '1') {
                //$condition_temp['MemberRenewal.id !='] = null;
                $condition_temp['MemberRenewal.status'] = 3;
                $condition_temp['MemberRenewal.expired_date >'] = $now;
            } else if ($data_search['renewal_status'] == '0') {
                $condition_temp['MemberRenewal.id'] = null;
            }
        }

		if (isset($data_search['expired_date']) && !empty($data_search['expired_date'])) {
            $condition_temp['MemberRenewal.expired_date'] = $data_search['expired_date'];
        }

        $conditions = array();
		//$conditions = $this->Common->get_filter_conditions($data_search, $model, $languages_model, $this->filter);

		$conditions = array_merge($conditions, $condition_temp);
        //$data_search = $data_search_origin;

		if ($data_search){
			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'member',
                    'controller' => 'members',
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
                    'controller' => 'members',
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
			'fields' => array(
			    $model.".*",
                'MemberRenewal.*'
            ),
			'conditions' => array($conditions),
            'joins' => array(
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
//                )
            ),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.created' => 'DESC'),
            'group' => array(
                'Member.id'
            )
		);

        $str_yes = __('yes');
        $str_no = __('no');
        $str_please_select = __('please_select');
        $list_status = array(
            -1 => $str_please_select,
            1 => $str_yes,
            0 => $str_no,
        );

        $list =  $this->paginate();

        foreach ($list as $k=>$v) {
            $list[$k]['Member']['is_phone_verified'] = true;
            $list[$k]['Member']['is_email_verified'] = true;
            $list[$k]['Member']['is_renewal'] = true;
            if (empty($v['Member']['phone_verified'])) {
                $list[$k]['Member']['is_phone_verified'] = false;
            }
            if (empty($v['Member']['email_verified'])) {
                $list[$k]['Member']['is_email_verified'] = false;
            }
            if (
//                empty($v['MemberRenewal'])
//                || ( isset($v['MemberRenewal'][0]) && $v['MemberRenewal'][0]['expired_date'] < $now)
//                || ( isset($v['MemberRenewal'][0]) && empty($v['MemberRenewal'][0]['expired_date']))
                empty($v['MemberRenewal']['expired_date'])
            ) {
                $list[$k]['Member']['is_renewal'] = false;
            }
        }

        $this->set('dbdatas', $list);
        $this->set(compact('model', 'languages_model', 'data_search', 'dobMonths', 'list_status'));
	}

	public function admin_view($id) {
		$model = $this->model;
		$dobMonths = $this->Common->get_list_month();
        $now = date('Y-m-d');

        $prefix = Environment::read('database.prefix');
        $sqlstr = "CREATE TEMPORARY TABLE IF NOT EXISTS " . $prefix . "mytable_member_renewals AS (".
            "select mr.* " .
            "from " . $prefix . "member_renewals AS mr " .
            "where mr.expired_date >= " . $now . " " .
            "group by mr.member_id)";
        $this->$model->query($sqlstr);

		$options = array(
			'fields' => array(
			    $model.'.*',
                'MemberRenewal.*',
                'AgeGroupLanguage.name',
                'DistrictLanguage.name'
            ),
            'joins' => array(
                array(
                    'alias' => 'MemberRenewal',
                    'table' => $prefix . 'mytable_member_renewals',
                    'type' => 'left',
                    'conditions' => array(
                        'MemberRenewal.member_id = Member.id'
                    ),
                ),
                array(
                    'alias' => 'AgeGroupLanguage',
                    'table' => $prefix . 'age_group_languages',
                    'type' => 'left',
                    'conditions' => array(
                        'AgeGroupLanguage.age_id = Member.age_group_id',
						'AgeGroupLanguage.language' => $this->lang18
                    ),
                ),
                array(
					'alias' => 'DistrictLanguage',
					'table' => Environment::read('table_prefix') . 'district_languages',
					'type' => 'left',
					'conditions' => array(
						'DistrictLanguage.district_id = Member.district_id',
						'DistrictLanguage.language' => $this->lang18
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
                'UpdatedBy',
                'CreatedBy'
            ),
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
		);
		$model_data = $this->$model->find('first', $options);

		if (!$model_data) {
			throw new NotFoundException(__('invalid_data'));
		}

        $now = date('Y-m-d');

        $list = $model_data;

        $list['Member']['is_phone_verified'] = true;
        $list['Member']['is_email_verified'] = true;
        $list['Member']['is_renewal'] = true;
        if (empty($list['Member']['phone_verified'])) {
            $list['Member']['is_phone_verified'] = false;
        }
        if (empty($list['Member']['email_verified'])) {
            $list['Member']['is_email_verified'] = false;
        }
        if (
//            empty($list['MemberRenewal'])
//            || ( isset($list['MemberRenewal'][0]) && $list['MemberRenewal'][0]['expired_date'] < $now)
//            || ( isset($list['MemberRenewal'][0]) && empty($list['MemberRenewal'][0]['expired_date']))
        empty($list['MemberRenewal']['expired_date'])
        ) {
            $list['Member']['is_renewal'] = false;
        }

		$this->set('dbdata', $list);

        $this->set(compact('model', 'dobMonths'));
	}

    public function admin_view_qr_code($id) {
        $model = $this->model;

        $options = array(
            'fields' => array($model.'.*'),
            'contain' => array(
            ),
            'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
        );
        $model_data = $this->$model->find('first', $options);

        if (!$model_data) {
            throw new NotFoundException(__('invalid_data'));
        }
        $qrcode_path = Environment::read('web.url_img').$model_data[$model]['qrcode_path'];

        $this->set('dbdata', $model_data);

        $this->set(compact('qrcode_path'));
    }

	public function admin_add() {
		$model = $this->model;
		$languages_model = $this->model_lang;
		$dobMonths = $this->Common->get_list_month();
		$country_codes = $this->Member->get_country_codes();
		
		$objGender = ClassRegistry::init('Member.Gender');
		$genders = $objGender->get_static_list();

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

            $options = array(
                'conditions' => array(
                    'Member.country_code' => trim($data['Member']['country_code']),
                    'Member.phone' => trim($data['Member']['phone'])
                )
            );
            $data_member = $this->$model->find('first', $options);
			if (isset($data_member['Member']['id']) && !empty($data_member['Member']['id'])) {
				$this->Session->setFlash(__('duplicate_phone_exists'), 'flash/error');
				goto load_data;
			}

            $options = array(
                'conditions' => array(
                    'email' => $data['Member']['email']
                )
            );
            $data_member = $this->$model->find('first', $options);
            if (isset($data_member['Member']['id']) && !empty($data_member['Member']['id'])) {
                $this->Session->setFlash(__('duplicate_email_exists'), 'flash/error');
                goto load_data;
            }

            $valid = true;

            $pass = $this->Common->generate_random_pass();
            $data[$model]['password'] = md5($pass);

            $data[$model]['phone_verified'] = date('Y-m-d H:i:s');
            $data[$model]['email_verified'] = date('Y-m-d H:i:s');
            $data[$model]['is_receive_promotion'] = 1;
            $data[$model]['is_read'] = 1;
            $data[$model]['is_agreed'] = 1;
            $data[$model]['is_under_18'] = 0;


            $objSetting = ClassRegistry::init('Setting.Setting');
            $member_renewal_cost = $objSetting->get_value('member-renewal');
        
            $objMemberRenewal = ClassRegistry::init('Member.MemberRenewal');
            $data_member_renewal = array();
            $data_member_renewal['MemberRenewal']['member_id'] = 0;
            $data_member_renewal['MemberRenewal']['payment_log_id'] = 0;
            $data_member_renewal['MemberRenewal']['date'] = date('Y-m-d H:i:s');
            $data_member_renewal['MemberRenewal']['inv_number'] = '';
            $data_member_renewal['MemberRenewal']['amount'] = $member_renewal_cost;
            $data_member_renewal['MemberRenewal']['renewal_date'] = date('Y-m-d');
            $data_member_renewal['MemberRenewal']['expired_date'] = date('Y-m-d', strtotime($data_member_renewal['MemberRenewal']['renewal_date'] . ' +1 years'));
            $data_member_renewal['MemberRenewal']['token'] = $this->$model->generateToken();
            $data_member_renewal['MemberRenewal']['status'] = 3;
            $data_member_renewal['MemberRenewal']['is_cms'] = 1;

			if ($valid) {
				$dbo = $this->$model->getDataSource();
				$dbo->begin();
                if ($this->$model->saveAll($data) && 
                    $objMemberRenewal->saveAll($data_member_renewal)) {

                    $member_id = $this->$model->id;
                    $member_renewal_id = $objMemberRenewal->id;
                
                    $inv_number = Environment::read('site.prefix.renewal').str_pad($member_renewal_id, 7, '0', STR_PAD_LEFT);

                    $code = str_pad($member_id, 6, "0", STR_PAD_LEFT);
                    $code = substr_replace($code, "-", 3, 0);
                    $code = "C-".$code;
                    $qr_code = $this->Common->generate_qrcode("member", $code, $code)['path'];
                    // $qr_code = addslashes($qr_code);
                    
                    $data[$model]['id'] = $member_id;
                    $data[$model]['code'] = $code;
                    $data[$model]['qrcode_path'] = $qr_code;

                    $data_member_renewal['MemberRenewal']['id'] = $member_renewal_id;
                    $data_member_renewal['MemberRenewal']['member_id'] = $member_id;
                    $data_member_renewal['MemberRenewal']['inv_number'] = $inv_number;

                    $this->create_member_coupon($data_member_renewal);
                    $this->create_member_coupon($data_member_renewal);

                    if ($this->$model->saveAll($data) && 
                        $objMemberRenewal->saveAll($data_member_renewal)) {
                        $receiver = array();
                        $receiver[0]['phone'] = $data[$model]['country_code'].$data[$model]['phone'];
                        $receiver[0]['language'] = $this->lang18;
        
                        $str_title = 'ACX-Cinemas';
                        $title = array($this->lang18 => $str_title);
        
                        $str_msg = sprintf(__('username_password_msg'), $data[$model]['email'], $pass);
                        $sms_message = array($this->lang18 => $str_msg);
        
                        $sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
                        // $sent_data['status'] = true;
                        if (!$sent_data['status']) {
                            $message = __('send_sms_failed');
                            $this->Session->setFlash(__('send_sms_failed'), 'flash/error');
                        }                    
    
                        $dbo->commit();
                        $this->Session->setFlash(__('data_is_saved'), 'flash/success');
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $dbo->rollback();
                        $this->Session->setFlash(__('failed_to_update_data'), 'flash/error');
                    }
				} else {
					$dbo->rollback();
					$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
				}
			} else {
				$this->Session->setFlash(__d('static', 'data_is_not_saved'), 'flash/error');
			}
			
		}

		$country_codes = $this->Member->get_country_codes();

		load_data :
        $this->set(compact('model', 'dobMonths', 'genders', 'country_codes'));
        
        $this->load_data();
	}

	public function admin_edit($id = null) {
		$model = $this->model;
		$dobMonths = $this->Common->get_list_month();
		$country_codes = $this->Member->get_country_codes();

		$objGender = ClassRegistry::init('Member.Gender');
		$genders = $objGender->get_static_list();

		$options = array(
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
			'recursive' => 1
		);
		$old_item = $this->$model->find('first', $options);

		if (!$old_item) {
			throw new NotFoundException(__('invalid_data'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

            $options = array(
                'conditions' => array(
                    'country_code' => trim($data['Member']['country_code']),
                    'phone' => trim($data['Member']['phone']),
                    'id !=' => $id
                )
            );
            $data_member = $this->$model->find('first', $options);

            if (isset($data_member['Member']['id']) && !empty($data_member['Member']['id'])) {
                $this->Session->setFlash(__('duplicate_phone_exists'), 'flash/error');
                goto load_data;
            }

            $options = array(
                'conditions' => array(
                    'email' => $data['Member']['email'],
                    'id !=' => $id
                )
            );
            $data_member = $this->$model->find('first', $options);
            if (isset($data_member['Member']['id']) && !empty($data_member['Member']['id'])) {
                $this->Session->setFlash(__('duplicate_email_exists'), 'flash/error');
                goto load_data;
            }

            $valid = true;

			if ($valid) {
				$dbo = $this->$model->getDataSource();
				$dbo->begin();
				
				try {
					if ($this->$model->saveAll($data)) {
						$dbo->commit();
						$this->Session->setFlash(__('data_is_saved'), 'flash/success');
						$this->redirect(array('action' => 'index'));
					} else {
						$dbo->rollback();
						$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
					}
				} catch (Exception $ex) {
					$dbo->rollback();
					$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
				}
			} else {
				$this->Session->setFlash(__d('static', 'data_is_not_saved'), 'flash/error');
			}
		} else {
			$this->request->data = $old_item;
		}

        load_data :
        $this->set(compact('model', 'genders', 'dobMonths', 'country_codes'));
        $this->load_data();
	}

	public function admin_delete($id = null) {
        $model = $this->model;
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->$model->id = $id;
		if (!$this->$model->exists()) {
			throw new NotFoundException(__('invalid_data'));
		}
		if ($this->$model->delete()) {
			$this->Session->setFlash(__('data_is_deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('data_is_not_deleted'), 'flash/error');
        $this->redirect(array('action' => 'index'));
	}

    public function load_data() {
        $model = $this->model;
        $title = $this->$model->title;

        $objAgeGroup = ClassRegistry::init('Member.AgeGroup');
        $age_groups = $objAgeGroup->get_list_active_age_group($this->lang18);

        $objDistrict = ClassRegistry::init('Setting.District');
        $districts = $objDistrict->get_active_district_list($this->lang18);

        $this->set(compact('title', 'age_groups', 'districts'));
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
		   $data_binding['dobMonths'] = $this->Common->get_list_month();


		   if ($result) {

				$cvs_data = array();

				foreach ($result as $row) {
					$temp = $this->$model->format_data_export(array(), $row, $data_binding);

					array_push($cvs_data, $temp);
				}

			   try{
				   $file_name = 'members_'.date('Ymd');

				   // export xls
				   if ($this->request->type == "xls") {
						$excel_readable_header = array(
							array('label' => __('id')),
							array('label' => __('name')),
							array('label' => __d('member','month_of_birth')),
							array('label' => __d('member','country_code')),
							array('label' => __('phone')),
							array('label' => __('email'))
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
							'label' => __d('member','month_of_birth'),
							'label' => __d('member', 'country_code'),
							'label' => __('phone'),
							'label' => __('email')
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
		$model = $this->model;
		$languages_model = $this->model_lang;
		$class_hidden = 'hidden';
		$message = array();
		$is_valid_all = true;

		if ($this->request->is('post')) {
			$data = $this->request->data;

			$objresult = $this->Common->upload_and_read_excel($data['TicketType'], '');

			if (!isset($objresult['status']) || !$objresult['status']) {
				throw new NotFoundException(__('invalid_data'));
			}

			$sheet_list = array_keys($objresult['data']);

			$data_upload = $objresult['data'][$sheet_list[0]];

			$line = -1;
			foreach($data_upload as $obj) {
				$line++;
				if ($line > 0) {

					$check_result = $this->Common->check_rules($this->rule, $this->rule_spec, $data_upload[0], $obj, $line);
					$valid = $check_result['status'];
					$tmp_msg = $check_result['message'];

					if ($valid) {
						$dbo = $this->$model->getDataSource();
						$dbo->begin();

						$data_insert = array();

						$option = array(
							'conditions' => array(
								'id' => $obj[0]
							),
						);
						$data_old = $this->$model->find('first', $option);

						$data_id = 0;
						if (isset($data_old[$model]['id']) && !empty($data_old[$model]['id'])) {
							$data_insert[$model]['id'] = $data_old[$model]['id'];

						} else {
							$data_insert[$model]['id'] = null;

						}

						$data_insert[$model]['name'] = $obj[1];
						$data_insert[$model]['birthday'] = $obj[2];
						$data_insert[$model]['country_code'] = $obj[3];
						$data_insert[$model]['phone'] = $obj[4];
						$data_insert[$model]['email'] = $obj[5];

						if ($this->$model->saveAll($data_insert)) {
							$dbo->commit();
						} else {
							$valid = false;
							$dbo->rollback();
							$validationErrors = $this->$model->validationErrors;

							$tmp_msg = array();
							foreach($validationErrors as $key => $value) {
								foreach($value as $error_msg) {
									array_push($tmp_msg, 'Error at line' . $line . ', ' . $error_msg);
								}
							}
						}
					}

					if (!$valid) {
						$message = array_merge($message, $tmp_msg);
						$is_valid_all = false;
					}
				}
			}

			if ($is_valid_all) {
				$this->Session->setFlash(__('data_is_saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$class_hidden = '';
			}

		}

		display:
		$this->set(compact('model', 'class_hidden', 'message'));
		
	}

	public function admin_get_data_select(){

		$model = $this->model;
        $this->Api->init_result();
		
		if( $this->request->is('post') ) {
            $data = $this->request->data;
            
			$this->disableCache();

			$field = 'phone';
			if(isset($data['field_search']) && !empty($data['field_search'])){
				$field = $data['field_search'];
			}

			$conditions = array(
				$model . '.' . $field . ' LIKE' => $data['text'] . "%",
			);

			if(isset($data['member_ids']) && !empty($data['member_ids'])){
				$conditions["NOT"] = array('Member.id' => $data['member_ids']);
			}

			$result_data = $this->$model->find_list_select_field($conditions, $field, $model);
				
			$this->Api->set_result(true, __('retrieve_data_successfully'), $result_data);
			
		}
		
		$this->Api->output();
	}

    public function api_request_verification_code_new_member() {
        $model = $this->model;
        $this->Api->init_result();

        if( $this->request->is('post') ) {
            $this->disableCache();
            $data = $this->request->data;

            $valid = true;
            $message = __('send_code_successfully');
            $result_data = array();


            if (!isset($data['token']) || empty($data['token'])) {
                $message = __('missing_parameter') . __('token');
                $valid = false;
                goto return_api;
            } else if(!isset($data['type']) || empty($data['type'])){
                $message = __('missing_parameter') . __('type');
                $valid = false;
                goto return_api;
            } else if ( ! in_array($data['type'], ['phone', 'email'])) {
                $message = __('invalid_data') . ' ' .__('type');
                $valid = false;
                goto return_api;
            }

            $objMember = ClassRegistry::init('Member.Member');

            $conditions = [
                'token' => $data['token']
            ];
            $data_member = $objMember->get_member_by_conditions($conditions);

            if (isset($data_member[$model]['id']) && !empty($data_member[$model]['id'])) {
                $member_id = $data_member[$model]['id'];
            } else {
                $message = __('user_not_found');
                $valid = false;
                goto return_api;
            }

            $verification_code = $this->Common->generate_verification_code();
            //$verification_code = 1234;

            if ($data['type'] == 'phone') {
                $phone = $data_member[$model]['phone'];
                $country_code = $data_member[$model]['country_code'];
                $data_member[$model]['phone_verification'] = $verification_code;
            } else if ( $data['type'] == 'email' ) {
                $email = $data_member[$model]['email'];
                $data_member[$model]['email_verification'] = $verification_code;
            }

            if ($valid) {
                $dbo = $this->$model->getDataSource();
                $dbo->begin();
                if ($this->$model->saveAll($data_member)) {
                    $dbo->commit();

                    if ( $data['type'] == 'phone' ) {
                        $receiver = array();
                        $receiver[0]['phone'] = $country_code . $phone;
                        $receiver[0]['language'] = $this->lang18;

                        $str_title = 'ACX-Cinema';
                        $title = array($this->lang18 => $str_title);

                        $str_msg = sprintf(__('verification_code_msg'), $verification_code);
                        $sms_message = array($this->lang18 => $str_msg);

                        $sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
                        $sent_data['status'] = true;
                        if (!$sent_data['status']) {
                            $result_data = $sent_data;
                            $message = __('send_sms_failed');
                            $valid = false;
                        }
                    } else if ( $data['type'] == 'email' ) {
                        $template = "verification_code";
                        $subject = 'ACX-Cinema - Verification Code';

                        $receiver = $email;

                        $data_email['email'] = $email;
                        $data_email['verification_code'] = $verification_code;

                        $result_email = $this->Email->send($receiver, $subject, $template, $data_email);
                        $result_email['status'] = true;
                        if (!$result_email['status']) {
                            $result_data = $result_email;
                            $message = __('send_email_failed');
                            $valid = false;
                        }
                    }
                } else {
                    $message = __('data_is_not_saved');
                    $valid = false;
                }
            }

            return_api:
            $this->Api->set_result($valid, $message, $result_data);
        }

        $this->Api->output();
    }

    public function api_request_verification_code_forgot_password() {

        $this->Api->init_result();

        if( $this->request->is('post') ) {
            $this->disableCache();
            $data = $this->request->data;

            $url_params = $this->request->params;
            $this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);

            $valid = true;
            $message = __('send_code_successfully');
            $result_data = array();

            if(!isset($data['type']) || empty($data['type'])){
                $message = __('missing_parameter') . __('type');
                $valid = false;
                goto return_api;
            } else if ( ! in_array($data['type'], ['phone', 'email'])) {
                $message = __('invalid_data') . ' ' .__('type');
                $valid = false;
                goto return_api;
            }

            if ($data['type'] == 'phone') {
                if (!isset($data['country_code']) || empty($data['country_code'])) {
                    $message = __('missing_parameter') . __('country_code');
                    $valid = false;
                    goto return_api;
                } else if (!isset($data['phone']) || empty($data['phone'])) {
                    $message = __('missing_parameter') . __('phone');
                    $valid = false;
                    goto return_api;
                } else if (!$this->Common->phone_validation($data['country_code'], $data['phone'])) {
                    $valid = false;
                    $message = __('invalid_phone_format');
                    goto return_api;
                }
            } else if ($data['type'] == 'email') {
                if (!isset($data['email']) || empty($data['email'])) {
                    $message = __('missing_parameter') .  __('email');
                    $valid = false;
                    goto return_api;
                }
            }


            $objMember = ClassRegistry::init('Member.Member');

            if ($data['type'] == 'phone') {
                $conditions = [
                    'country_code' => $data['country_code'],
                    'phone' => $data['phone']
                ];
            } else if ($data['type'] == 'email') {
                $conditions = [
                    'email' => $data['email']
                ];
            }

            $data_member = $objMember->get_member_by_conditions($conditions);

            if (isset($data_member['Member']['id']) && !empty($data_member['Member']['id'])) {
                $member_id = $data_member['Member']['id'];
            } else {
                $message = __('user_not_found');
                $valid = false;
                goto return_api;
            }

            //send sms
            $verification_code = $this->Common->generate_verification_code();
            //$verification_code = 1234;

            $data_insert = array();
            $data_insert['member_id'] = $member_id;
            $data_insert['verification_code'] = $verification_code;
            $data_insert['generated_time'] = date('Y-m-d H:i:s');

            if ($data['type'] == 'phone') {
                $conditions = array(
                    'MemberVerification.country_code' => $data['country_code'],
                    'MemberVerification.phone' => $data['phone'],
                );
                $data_insert['country_code'] = $data['country_code'];
                $data_insert['phone'] = $data['phone'];

            } else if ($data['type'] == 'email') {
                $conditions = array(
                    'MemberVerification.email' => $data['email'],
                );
                $data_insert['email'] = $data['email'];
            }

            // Update old record enable = 0
            $updates = array(
                'MemberVerification.enabled' => 0
            );
            $objMemberVerification = ClassRegistry::init('Member.MemberVerification');
            $objMemberVerification->updateAll($updates, $conditions);

            if ($objMemberVerification->saveAll($data_insert)) {
                //send verification code to user's phone
                if ($data['type'] == 'phone') {
                    $receiver = array();
                    $receiver[0]['phone'] = $data_insert['country_code'].$data_insert['phone'];
                    $receiver[0]['language'] = $this->lang18;

                    $str_title = 'ACX-Cinema';
                    $title = array($this->lang18 => $str_title);

                    $str_msg = sprintf(__('verification_code_msg'), $verification_code);
                    $sms_message = array($this->lang18 => $str_msg);

                    $sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
                    $sent_data['status'] = true;
                    if (!$sent_data['status']) {
                        $result_data = $sent_data;
                        $message = __('send_sms_failed');
                        $valid = false;
                    }
                } else if ($data['type'] == 'email') {
                    $template = "verification_code";
                    $subject = 'ACX-Cinema - Verification Code';

                    $receiver = $data['email'];

                    $data_email['email'] = $data['email'];
                    $data_email['verification_code'] = $verification_code;

                    $result_email = $this->Email->send($receiver, $subject, $template, $data_email);
                    $result_email['status'] = true;
                    if (!$result_email['status']) {
                        $result_data = $result_email;
                        $message = __('send_email_failed');
                        $valid = false;
                    }
                }

            } else {
                $message = __('data_is_not_saved');
                $valid = false;
            }

            return_api:
            $this->Api->set_result($valid, $message, $result_data);
        }

        $this->Api->output();
    }

    public function api_request_verification_code_new_member_mobile() {

        $this->Api->init_result();

        if( $this->request->is('post') ) {
            $this->disableCache();
            $data = $this->request->data;

            $valid = true;
            $message = __('send_code_successfully');
            $result_data = array();

            if (!isset($data['country_code']) || empty($data['country_code'])) {
                $message = __('missing_parameter') . __('country_code');
                $valid = false;
                goto return_api;
            } else if (!isset($data['phone']) || empty($data['phone'])) {
                $message = __('missing_parameter') . __('phone');
                $valid = false;
                goto return_api;
            } else if (!$this->Common->phone_validation($data['country_code'], $data['phone'])) {
                $valid = false;
                $message = __('invalid_phone_format');
                goto return_api;
            }

            $objMember = ClassRegistry::init('Member.Member');

            $conditions = [];

            $conditions = [
                'country_code' => $data['country_code'],
                'phone' => $data['phone']
            ];

            $data_member = $objMember->get_member_by_conditions($conditions);

            $member_id = 0;
            if (isset($data_member['Member']['id']) && !empty($data_member['Member']['id'])) {
                $message = __('exist_phone_number');
                $valid = false;
                goto return_api;
            }

            $verification_code = $this->Common->generate_verification_code();
            //$verification_code = 1234;

            $data_insert = array();
            $data_insert['member_id'] = $member_id;
            $data_insert['verification_code'] = $verification_code;
            $data_insert['generated_time'] = date('Y-m-d H:i:s');

            $conditions = array(
                'MemberVerification.country_code' => $data['country_code'],
                'MemberVerification.phone' => $data['phone'],
            );
            $data_insert['country_code'] = $data['country_code'];
            $data_insert['phone'] = $data['phone'];

            // Update old record enable = 0
            $updates = array(
                'MemberVerification.enabled' => 0
            );
            $objMemberVerification = ClassRegistry::init('Member.MemberVerification');
            $objMemberVerification->updateAll($updates, $conditions);

            if ($objMemberVerification->saveAll($data_insert)) {
                //send verification code to user's phone

                $receiver = array();
                $receiver[0]['phone'] = $data_insert['country_code'].$data_insert['phone'];
                $receiver[0]['language'] = $this->lang18;

                $str_title = 'ACX-Cinema';
                $title = array($this->lang18 => $str_title);

                $str_msg = sprintf(__('verification_code_msg'), $verification_code);
                $sms_message = array($this->lang18 => $str_msg);

                $sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
                $sent_data['status'] = true;
                if (!$sent_data['status']) {
                    $result_data = $sent_data;
                    $message = __('send_sms_failed');
                    $valid = false;
                }

            } else {
                $message = __('data_is_not_saved');
                $valid = false;
            }

            return_api:
            $this->Api->set_result($valid, $message, $result_data);
        }

        $this->Api->output();
    }

    public function api_verification_code_new_member() {

	    // validate : not
        $this->Api->init_result();
        $model = $this->model;
        $model_lang = $this->model_lang;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
            $message = "";
            $params = (object)array();
            $data = $this->request->data;

            if(!isset($data['type']) || empty($data['type'])){
                $message = __('missing_parameter') . __('type');
            } else if ( ! in_array($data['type'], ['phone', 'email'])) {
                $message = __('invalid_data') . ' ' .__('type');
            } else if (!isset($data['token']) || empty($data['token'])) {
                $message = __('missing_parameter') . __('token');
            } else if (!isset($data['verification_code']) || empty($data['verification_code'])) {
                $message = __('missing_parameter') . __('verification_code');
            } else {
                $this->Api->set_language($this->lang18);

                //$data['phone'] = str_replace('+852', '0', $data['phone']);
                // $data['phone'] = preg_replace('/^0/', '+852', $data['phone']);

                $conditions = [
                    'token' => $data['token']
                ];

                $data_user = $this->$model->get_member_by_conditions($conditions);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $message = '';
                $valid = true;

                if ($data['type'] == 'phone') {
                    if (isset($data_user[$model]) && !empty($data_user[$model])) {
                        $verification_date = $data_user[$model]['phone_verified'];

                        if (!empty($verification_date)) {
                            $valid = false;
                            $message = __('is_verified_phone');
                        } else if ($data_user[$model]['phone_verification'] != $data['verification_code']) {
                            $valid = false;
                            $message = __('verification_code_is_wrong');
                        }

                        $data_user[$model]['phone_verified'] = date('Y-m-d H:i:s');
                    } else {
                        //user doesnt exists, please register
                        $valid = false;
                        $message = __('user_not_found');
                    }
                } else if ($data['type'] == 'email') {
                    if (isset($data_user[$model]) && !empty($data_user[$model])) {
                        $verification_date = $data_user[$model]['email_verified'];

                        if (!empty($verification_date)) {
                            $valid = false;
                            $message = __('is_verified_mail');
                        } else if ($data_user[$model]['email_verification'] != $data['verification_code']) {
                            $valid = false;
                            $message = __('verification_code_is_wrong');
                        }

                        $data_user[$model]['email_verified'] = date('Y-m-d H:i:s');
                    } else {
                        //user doesnt exists, please register
                        $valid = false;
                        $message = __('user_not_found');
                    }
                }

                if ($valid) {
                    $dbo = $this->$model->getDataSource();
                    $dbo->begin();
                    if ($this->$model->saveAll($data_user)) {
                        $dbo->commit();
                        $message = __('verified_code_successfully');
                    } else {
                        $dbo->rollback();
                        $message = __('data_is_not_saved');
                        $valid = false;
                    }
                }

                $status = $valid;
                $message = $message;

                if($valid){
                    $params = $data_user;
                    if (!$params) {
                        $params = (object)array();
                    }
                }else{
                    $log_data = array();
                    $log_data['message'] = $message;
                    $log_data['data_result'] = $data_user;
                    $log_data['data'] = $data;

                    $this->Api->set_error_log($log_data);
                }


                //return_data:
                //send verification code to user
            }

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }

    public function api_reset_forgot_password() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
            $message = "";
            $warning = array();
            $params = (object)array();
            $data = $this->request->data;
            $valid = true;
            $data_user = array();

            if (!isset($data['verification_code']) || empty($data['verification_code'])) {
                $message = __('missing_parameter') .  __('verification_code');
                $valid = false;
                goto return_api;
            } else if(!isset($data['type']) || empty($data['type'])){
                $message = __('missing_parameter') . __('type');
                $valid = false;
                goto return_api;
            } else if ( ! in_array($data['type'], ['phone', 'email'])) {
                $message = __('invalid_data') . ' ' .__('type');
                $valid = false;
                goto return_api;
            }

            if ($data['type'] == 'phone') {
                if (!isset($data['country_code']) || empty($data['country_code'])) {
                    $message = __('missing_parameter') .  __('country_code');
                    $valid = false;
                    goto return_api;
                } else if (!isset($data['phone']) || empty($data['phone'])) {
                    $message = __('missing_parameter') .  __('phone');
                    $valid = false;
                    goto return_api;
                }
            } else if ($data['type'] == 'email') {
                if (!isset($data['email']) || empty($data['email'])) {
                    $message = __('missing_parameter') .  __('email');
                    $valid = false;
                    goto return_api;
                }
            }

            $this->Api->set_language($this->lang18);

            $url_params = $this->request->params;
            $this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);
            $valid = true;

            $objLogin = ClassRegistry::init('Member.MemberVerification');

            if ( $data['type'] == 'phone' ) {
                $options = array(
                    'conditions' => array(
                        'verification_code' => $data['verification_code'],
                        'login_time' => null,
                        'phone' => $data['phone'],
                        'country_code' => $data['country_code'],
                        'enabled' => 1
                    ),
                    'recursive' => -1,
                    'order' => array('generated_time' => 'DESC')
                );
                $member_conditions = [
                    'country_code' => $data['country_code'],
                    'phone' => $data['phone']
                ];
            } else if ($data['type'] == 'email') {
                $options = array(
                    'conditions' => array(
                        'verification_code' => $data['verification_code'],
                        'login_time' => null,
                        'email' => $data['email'],
                        'enabled' => 1
                    ),
                    'recursive' => -1,
                    'order' => array('generated_time' => 'DESC')
                );
                $member_conditions = [
                    'email' => $data['email']
                ];
            }

            // Check verification code exist coresponding phone
            $data_login = $objLogin->find('first', $options);

            if(isset($data_login['MemberVerification']) && !empty($data_login['MemberVerification'])) {

                $minute = Environment::read('site.otp_lifetime');
                if (strtotime("+" . $minute . " minutes", strtotime($data_login['MemberVerification']['generated_time'])) < strtotime('now')) {
                    $valid = false;
                    $message = __('code_expired');

                    goto return_api;
                }

            } else {
                $valid = false;
                $message = __('verification_code_is_wrong');

                goto return_api;
            }

            //$data_login['MemberVerification']['login_time'] = date("Y-m-d H:i:s");
            $data_login['MemberVerification']['enabled'] = 0;

            $dbo = $this->$model->getDataSource();
            $dbo->begin();

            // create new token
            $data_member = $this->$model->get_member_by_conditions($member_conditions);
            $token = $this->$model->generateToken();
            $data_member[$model]['token'] = $token;

            if (
                $objLogin->saveAll($data_login)
                && $this->$model->saveAll($data_member)
            ) {
                $dbo->commit();
                $data_user = [
                    'Member' => [
                        'token' => $token
                    ]
                ];

                $message = __('verified_code_successfully');
            } else {
                $dbo->rollback();
                $message = __('verified_code_unsuccessfully');
                $valid = false;

                goto return_api;
            }


            return_api:

            $status = $valid;
            $message = $message;

            if($valid){
                $params = $data_user;
                $params['warning'] = $warning;
                if (!$params) {
                    $params = (object)array();
                }
            }else{
                $log_data = array();
                $log_data['message'] = $message;
                $log_data['data_result'] = $data_user;
                $log_data['data'] = $data;

                $this->Api->set_error_log($log_data);
            }


            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }

    public function api_forgot_password() {

        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
            $message = "";
            $warning = array();
            $params = (object)array();
            $data = $this->request->data;

            $this->Api->set_language($this->lang18);

            $url_params = $this->request->params;
            $this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);
            $valid = true;
            $data_user = array();

//			if (isset($data['is_phone']) && ! empty($data['is_phone'])) {
//				if (!isset($data['country_code']) || empty($data['country_code'])) {
//					$message = __('missing_parameter') .  __('country_code');
//					$valid = false;
//					goto return_api;
//				} else if (!isset($data['phone']) || empty($data['phone'])) {
//					$message = __('missing_parameter') .  __('phone');
//					$valid = false;
//					goto return_api;
//				}
//			}
//
//			if (isset($data['is_email']) && ! empty($data['is_email'])) {
//				if (!isset($data['email']) || empty($data['email'])) {
//					$message = __('missing_parameter') .  __('email');
//					$valid = false;
//					goto return_api;
//				}
//			}
//
//			if (isset($data['is_phone']) && ! empty($data['is_phone'])) {
//				$conditions = [
//					'country_code' => $data['country_code'],
//					'phone' => $data['phone']
//				];
//			} else if (isset($data['is_email']) && ! empty($data['is_email'])) {
//				$conditions = [
//					'email' => $data['email']
//				];
//			}

            if (!isset($data['token']) || empty($data['token'])) {
                $message = __('missing_parameter') .  __('token');
                $valid = false;
                goto return_api;
            } else if (!isset($data['new_password']) || empty($data['new_password'])) {
                $message = __('missing_parameter') .  __('new_password');
                $valid = false;
                goto return_api;
            }

            $conditions = [
                'token' => $data['token']
            ];

            $data_user = $this->$model->get_member_by_conditions($conditions);

            $data_save = array();
            if(!isset($data_user[$model]) || empty($data_user[$model])){
                $valid = false;
                $message = __('user_not_found');

                goto return_api;
            } else if ($data_user[$model]['enabled'] == 0) {
                $valid = false;
                $message = __('this_account_was_disabled');

                goto return_api;
            }

            if ($valid) {

                $pass = $data['new_password'];
                $data_user[$model]['password'] = md5($pass);
                $data_user[$model]['token'] = null;

                $dbo = $this->$model->getDataSource();
                $dbo->begin();

                if ($this->$model->saveAll($data_user)) {
                    $dbo->commit();
                    $message = __('data_is_saved');
                } else {
                    $dbo->rollback();
                    $message = __('data_is_not_saved');
                    $valid = false;
                }
            }

            return_api:

            $status = $valid;
            $message = $message;

            if($valid){
                $params = $data_user;
                $params['warning'] = $warning;
                if (!$params) {
                    $params = (object)array();
                }
            }else{
                $log_data = array();
                $log_data['message'] = $message;
                $log_data['data_result'] = $data_user;
                $log_data['data'] = $data;

                $this->Api->set_error_log($log_data);
            }

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }

	public function api_login() {
		$this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
			
			$status = false;
			$message = "";
			$error = array();
            $params = (object)array();
			$data = $this->request->data;

			if (!isset($data['email']) || empty($data['email'])) {
				$message = __('missing_parameter') .  __('email');
				$error['email'] = $message;
				goto return_api;
			} else if (!isset($data['password']) || empty($data['password'])) {
				$message = __('missing_parameter') .  __('password');
				$error['password'] = $message;
				goto return_api;
			} 
			
			if ( ! isset($data['is_browser']) || empty($data['is_browser'])) {
				if (!isset($data['device_type']) || empty($data['device_type'])) {
					$message = __('missing_parameter') . __('device_type');
					$error['other'] = $message;
					goto return_api;
				} else if (!isset($data['device_token']) || empty($data['device_token'])) {
					$message = __('missing_parameter') . __('device_token');
					$error['other'] = $message;
					goto return_api;
			    } else if (!isset($data['model_code']) || empty($data['model_code'])) {
					$message = __('missing_parameter') . __('model_code');
					$error['other'] = $message;
					goto return_api;
			    } else if (!isset($data['os_version']) || empty($data['os_version'])) {
					$message = __('missing_parameter') . __('os_version');
					$error['other'] = $message;
					goto return_api;
				}
			}
			// } else if (!isset($data['phone']) || empty($data['phone'])) {
			// 	$message = __('missing_parameter') . __('phone');
			// 	$error['phone'] = $message;
				// } else if (!isset($data['language']) || empty($data['language'])) {
			// 	$message = __('missing_parameter') .  __('language');
			// 	$error['language'] = $message;


			$this->Api->set_language($this->lang18);

			$url_params = $this->request->params;
			$this->Api->set_post_params($url_params, $data);
			$this->Api->set_save_log(true);

			//$data['phone'] = str_replace('+852', '0', $data['phone']);
			// $data['phone'] = preg_replace('/^0/', '+852', $data['phone']);

			$result = $this->$model->login($data, $this->Api->get_language());

			$status = $result['status'];
			$message = $result['message'];
			$error = $result['error'];
			$warning = $result['warning'];
			
			if($result['status']){

				/*
				$objSetting = ClassRegistry::init('Setting.Setting');
				$duration = $objSetting->get_timeout('family_timeout');

				$token = $result['params']['token'];
				$this->Redis->set_cache('timeout', $token, '', 'member', $duration);
				*/

				$params = $result['params'];
//				$params['warning'] = $warning;
				if (!$params) {
					$params = (object)array();
				}

			}else{
				if(isset($result['log_data']) && $result['log_data']){
					$this->Api->set_error_log($result['log_data']);
				}
			}

			if (!$status) {
				$params = array('error' => $error);
			}

			return_api:
            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
	}

//	public function api_check_new_member_and_send_verification_code() {
//		$this->Api->init_result();
//		$model = $this->model;
//
//		if ($this->request->is('post')) {
//            $this->disableCache();
//            $status = false;
//			$message = "";
//			$error = array();
//			$warning = array();
//            $params = (object)array();
//			$data = $this->request->data;
//
//			if (!isset($data['email']) || empty($data['email'])) {
//				$message = __('missing_parameter') .  __('email');
//				$error['email'] = $message;
//			} else if (!isset($data['country_code']) || empty($data['country_code'])) {
//				$message = __('missing_parameter') . __('country_code');
//				$error['phone'] = $message;
//			} else if (!isset($data['phone']) || empty($data['phone'])) {
//				$message = __('missing_parameter') . __('phone');
//				$error['phone'] = $message;
//            } else {
//				$this->Api->set_language($this->lang18);
//
//				$url_params = $this->request->params;
//                $this->Api->set_post_params($url_params, $data);
//				$this->Api->set_save_log(true);
//
//				$conditions = [
//					'country_code' => $data['country_code'],
//					'phone' => $data['phone']
//				];
//
//				// Check exist phone, email
//				$data_user = $this->$model->get_member_by_conditions($conditions);
//
//				$message = '';
//				$valid = true;
//				$data_save = array();
//
//				if (isset($data_user[$model]) && !empty($data_user[$model])) {
//					// check phone exist
//					$valid = false;
//					$message = __('exist_phone_number');
//					$error['phone'] = $message;
//					goto return_api;
//				}
//
//				if($valid) {
//					//check email exist
//					$options = array(
//						'conditions' => array(
//							'email' => $data['email'],
//						),
//						'recursive' => -1
//					);
//					$data_user_check = $this->$model->find('first', $options);
//
//					if (isset($data_user_check[$model]) && !empty($data_user_check[$model])) {
//						$valid = false;
//						$message = __('duplicate_email_exists');
//						$error['email'] = $message;
//						goto return_api;
//					}
//				}
//
//				$message = __('retrieve_data_successfully');
//
//				$objMember = ClassRegistry::init('Member.Member');
//				$conditions = [
//					'country_code' => $data['country_code'],
//					'phone' => $data['phone']
//				];
//
//				$data_member = $objMember->get_member_by_conditions($conditions);
//
//				$member_id = 0;
//				if (isset($data_member['Member']['id']) && !empty($data_member['Member']['id'])) {
//					$member_id = $data_member['Member']['id'];
//				}
//
//				//send sms
//				$verification_code = $this->Common->generate_verification_code();
//
//				$data_insert = array();
//				$data_insert['member_id'] = $member_id;
//				$data_insert['verification_code'] = $verification_code;
//				$data_insert['generated_time'] = date('Y-m-d H:i:s');
//
//
//				$conditions = array(
//					'MemberVerification.country_code' => $data['country_code'],
//					'MemberVerification.phone' => $data['phone'],
//				);
//				$data_insert['country_code'] = $data['country_code'];
//				$data_insert['phone'] = $data['phone'];
//
//				// Update old record enable = 0
//				$updates = array(
//					'MemberVerification.enabled' => 0
//				);
//
//				$dbo = $this->$model->getDataSource();
//				$dbo->begin();
//
//				$objMemberVerification = ClassRegistry::init('Member.MemberVerification');
//				$objMemberVerification->updateAll($updates, $conditions);
//
//				if ($objMemberVerification->saveAll($data_insert)) {
//					//send verification code to user's phone
//
//					$dbo->commit();
//					$receiver = array();
//					$receiver[0]['phone'] = $data_insert['country_code'].$data_insert['phone'];
//					$receiver[0]['language'] = $this->lang18;
//
//					$str_title = 'ACX-Cinema';
//					$title = array($this->lang18 => $str_title);
//
//					$str_msg = sprintf(__('verification_code_msg'), $verification_code);
//					$sms_message = array($this->lang18 => $str_msg);
//
//					$sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
//					// $sent_data['status'] = true;
//					if (!$sent_data['status']) {
//						$result_data = $sent_data;
//						$message = __('send_sms_failed');
//						$valid = false;
//					}
//
//
//				} else {
//					$dbo->rollback();
//					$message = __('data_is_not_saved');
//					$valid = false;
//				}
//
//				return_api:
//                $status = $valid;
//                $message = $message;
//                if($valid){
//					$params = $data_save;
//					$params['warning'] = $warning;
//                    if (!$params) {
//                        $params = (object)array();
//                    }
//                }else{
//					$log_data = array();
//					$log_data['message'] = $message;
//					$log_data['data_result'] = $data_save;
//					$log_data['data'] = $data;
//
//                    $this->Api->set_error_log($log_data);
//				}
//			}
//
//			if (!$status) {
//				$params = array('error' => $error);
//			}
//
//            $this->Api->set_result($status, $message, $params);
//        }
//
//		$this->Api->output();
//	}

	public function api_signup() {
		$this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();

            $valid = true;
            $data_save = array();

            $status = false;
			$message = "data_is_saved";
			$error = array();
			$warning = array();
            $params = (object)array();
			$data = $this->request->data;

            $this->Api->set_language($this->lang18);

            $url_params = $this->request->params;
            $this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);

            if (!isset($data['title']) || empty($data['title'])) {
				$message = __('missing_parameter') .  __('title');
				$error['title'] = $message;
                $valid = false;
				goto return_api;
			} else if (!isset($data['name']) || empty($data['name'])) {
				$message = __('missing_parameter') .  __('name');
				$error['name'] = $message;
                $valid = false;
                goto return_api;
			} else if (!isset($data['email']) || empty($data['email'])) {
				$message = __('missing_parameter') .  __('email');
				$error['email'] = $message;
                $valid = false;
                goto return_api;
			} else if (!isset($data['country_code']) || empty($data['country_code'])) {
				$message = __('missing_parameter') . __('country_code');
				$error['phone'] = $message;
                $valid = false;
                goto return_api;
			} else if (!isset($data['phone']) || empty($data['phone'])) {
				$message = __('missing_parameter') . __('phone');
				$error['phone'] = $message;
                $valid = false;
                goto return_api;
//            } else if (!isset($data['age_group_id'])) {
//                $message = __('missing_parameter') . __('age_group_id');
//                $error['age_group_id'] = $message;
//                $valid = false;
//                goto return_api;
//            } else if (!isset($data['birth_month'])) {
//                $message = __('missing_parameter') . __('birth_month');
//                $error['birth_month'] = $message;
//                $valid = false;
//                goto return_api;
//            } else if (!isset($data['district_id'])) {
//                $message = __('missing_parameter') . __('district_id');
//                $error['district_id'] = $message;
//                $valid = false;
//                goto return_api;
			} else if (!isset($data['password']) || empty($data['password'])) {
				$message = __('missing_parameter') .  __('password');
				$error['password'] = $message;
                $valid = false;
                goto return_api;
			}

            if (! isset($data['is_browser']) || empty($data['is_browser'])) {
                if (!isset($data['device_type']) || empty($data['device_type'])) {
                    $message = __('missing_parameter') . __('device_type');
                    $error['other'] = $message;
                    $valid = false;
                    goto return_api;
                } else if (!isset($data['device_token']) || empty($data['device_token'])) {
                    $message = __('missing_parameter') . __('device_token');
                    $error['other'] = $message;
                    $valid = false;
                    goto return_api;
                } else if (!isset($data['model_code']) || empty($data['model_code'])) {
                    $message = __('missing_parameter') . __('model_code');
                    $error['other'] = $message;
                    $valid = false;
                    goto return_api;
                } else if (!isset($data['os_version']) || empty($data['os_version'])) {
                    $message = __('missing_parameter') . __('os_version');
                    $error['other'] = $message;
                    $valid = false;
                    goto return_api;
                }
            }


				$conditions = [
					'country_code' => $data['country_code'],
					'phone' => $data['phone']
				];
				
				// Check exist phone, email
				$data_user = $this->$model->get_member_by_conditions($conditions);

				if (isset($data_user[$model]) && !empty($data_user[$model])) {
					// check phone exist
					$valid = false;
					$message = __('exist_phone_number');
					$error['phone'] = $message;
					goto return_api;
				}
					
				if($valid) {
					//check email exist
					$options = array(
						'conditions' => array(
							'email' => $data['email'],
						),
						'recursive' => -1
					);
					$data_user_check = $this->$model->find('first', $options);

					if (isset($data_user_check[$model]) && !empty($data_user_check[$model])) {
						$valid = false;
						$message = __('duplicate_email_exists');
						$error['email'] = $message;
						goto return_api;
					}
				}

				// Check Verification Code
//				$option = array(
//					'conditions' => array(
//						'member_id' => 0,
//						'country_code' => $data['country_code'],
//						'phone' => $data['phone'],
//						'login_time is null',
//						'enabled' => 1,
//					),
//					'recursive' => -1
//				);
//
//				$objMemberVerification = ClassRegistry::init('Member.MemberVerification');
//				$data_verification = $objMemberVerification->find('first', $option);
//				if (isset($data_verification['MemberVerification']) && !empty($data_verification['MemberVerification'])) {
//					if ($data_verification['MemberVerification']['verification_code'] == $data['verification_code']) {
//						$generated_time = $data_verification['MemberVerification']['generated_time'];
//
//						$minute = Environment::read('site.otp_lifetime');
//
//
//						if (strtotime("+" . $minute . " minutes", strtotime($generated_time)) < strtotime('now')) {
//							$valid = false;
//							$message = sprintf(__('item_already_expired'), __('verification_code'));
//						}
//
//					} else {
//						$message = __('verification_code_invalid');
//						$valid = false;
//						goto return_api;
//					}
//				} else {
//					$message = __('verification_code_invalid');
//					$valid = false;
//					goto return_api;
//				}

				if ($valid) {
					$data_save[$model]['title'] = $data['title'];
					$data_save[$model]['name'] = $data['name'];
					$data_save[$model]['country_code'] = $data['country_code'];
					$data_save[$model]['phone'] = $data['phone'];
					$data_save[$model]['email'] = $data['email'];
					$data_save[$model]['password'] = md5($data['password']);
                    $data_save[$model]['email_verified'] = date('Y-m-d H:i:s');
                    $data_save[$model]['is_read'] = 1;

                    if (isset($data['is_receive_promotion'])) {
                        $data_save[$model]['is_receive_promotion'] = $data['is_receive_promotion'];
                    }

                    if (isset($data['is_agreed']) && !empty($data['is_agreed'])) {
                        $data_save[$model]['is_agreed'] = $data['is_agreed'];
                    }

                    if (isset($data['is_under_18']) && !empty($data['is_under_18'])) {
                        $data_save[$model]['is_under_18'] = $data['is_under_18'];
                    }

                    if (isset($data['age_group_id'])) {
                        $data_save[$model]['age_group_id'] = $data['age_group_id'];
                    }
                    if (isset($data['birth_month'])) {
                        $data_save[$model]['birth_month'] = $data['birth_month'];
                    }
                    if (isset($data['district_id'])) {
                        $data_save[$model]['district_id'] = $data['district_id'];
                    }

                    $verification_phone_code = $this->Common->generate_verification_code();
                    $data_save[$model]['phone_verification'] = $verification_phone_code;

//                    $verification_email_code = $this->Common->generate_verification_code();
//                    $data_save[$model]['email_verification'] = $verification_email_code;

					if (!isset($data_user[$model]['token']) || ($data_user[$model]['token'] == '')) {
						$data_save[$model]['token'] = $this->$model->generateToken();
					} else {
						$data_save[$model]['token'] = $data_user[$model]['token'];
					}

					$dbo = $this->$model->getDataSource();
					$dbo->begin();
					if ($this->$model->saveAll($data_save)) {

						$member_id = $this->$model->id;
						$phone = $data_save[$model]['phone'];
                        $country_code = $data_save[$model]['country_code'];
                        $email = $data_save[$model]['email'];

						// Update Code - Qr Code
						$code = str_pad($member_id, 6, "0", STR_PAD_LEFT);
						$code = substr_replace($code, "-", 3, 0);
						$code = "C-".$code;
                        $qr_code = $this->Common->generate_qrcode("member", $code, $code)['path'];
                        $data_save[$model]['qrcode_path'] = Environment::read('web.url_img').$qr_code;
                        $qr_code = addslashes($qr_code);

						$data_update = [
							'Member.code' => '"'. $code .'"',
							'Member.qrcode_path' => '"'. $qr_code .'"'
						];
						$this->$model->updateAll($data_update, ['Member.id' => $member_id]);


//						// Update old record enable = 0
//						$conditions = array(
//							'MemberVerification.country_code' => $data['country_code'],
//							'MemberVerification.phone' => $data['phone'],
//						);
//						$updates = array(
//							'MemberVerification.enabled' => 0
//						);
//						$objMemberVerification = ClassRegistry::init('Member.MemberVerification');
//						$objMemberVerification->updateAll($updates, $conditions);

						if ($valid) {
							$objMemberDevice = ClassRegistry::init('Member.MemberDevice');

                            if (! isset($data['is_browser']) || empty($data['is_browser'])) {
                                if ($objMemberDevice->create_new_device($data, $member_id)) {
                                    $dbo->commit();

                                } else {
                                    $dbo->rollback();
                                    $message = __('save_device_failed');
                                    $error['other'] = $message;
                                    $valid = false;
                                }
                            } else {
                                $dbo->commit();
                            }

                            // Send code to mobile
//                            $receiver = array();
//                            $receiver[0]['phone'] = $country_code . $phone;
//                            $receiver[0]['language'] = $this->lang18;
//
//                            $str_title = 'ACX-Cinema';
//                            $title = array($this->lang18 => $str_title);
//
//                            $str_msg = sprintf(__('verification_code_msg'), $verification_phone_code);
//                            $sms_message = array($this->lang18 => $str_msg);
//
//                            $sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
//                            // $sent_data['status'] = true;
//                            if (!$sent_data['status']) {
//                                $result_data = $sent_data;
//                                $message = __('send_sms_failed');
//                                $valid = false;
//                            }


                            // Send code to email
//                            $template = "verification_code";
//                            $subject = 'ACX-Cinema - Verification Code';
//
//                            $receiver = $email;
//
//                            $data_email['email'] = $email;
//                            $data_email['verification_code'] = $verification_email_code;
//
//                            $result_email = $this->Email->send($receiver, $subject, $template, $data_email);
//
//                            if (!$result_email['status']) {
//                                $result_data = $result_email;
//                                $message = __('send_email_failed');
//                                $valid = false;
//                            }

//                            $template = "create_account";
//                            $subject = 'ACX-Cinema - Create Account Successful';
//
//                            $receiver = $data['email'];
//                            $mail_data = [
//                                'email' => $data['email'],
//                                'password' => $data['password']
//                            ];
//                            $result_email = $this->Email->send($receiver, $subject, $template, $mail_data);
//
//                            if (!$result_email['status']) {
//                                $result_data = $result_email;
//                                $message = __('send_email_failed');
//                                $valid = false;
//                            }

                            $data_save[$model]['id'] = $member_id;
						}
					} else {
						$dbo->rollback();
						$message = __('data_is_not_saved');
						$error['other'] = $message;
						$valid = false;
					}
				}

				return_api:
                $status = $valid;
                $message = $message;
                if($valid){
					$params = $data_save;
					$params['warning'] = $warning;
                    if (!$params) {
                        $params = (object)array();
                    }
                }else{
					$log_data = array();
					$log_data['message'] = $message;
					$log_data['data_result'] = $data_save;
					$log_data['data'] = $data;

                    $this->Api->set_error_log($log_data);
				}


			if (!$status) {
				$params = array('error' => $error);
			}

            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
	}

    public function api_signup_mobile() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();

            $valid = true;
            $data_save = array();

            $status = false;
            $message = "data_is_saved";
            $error = array();
            $warning = array();
            $params = (object)array();
            $data = $this->request->data;

            $this->Api->set_language($this->lang18);

            $url_params = $this->request->params;
            $this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);

            if (!isset($data['title']) || empty($data['title'])) {
                $message = __('missing_parameter') .  __('title');
                $error['title'] = $message;
                $valid = false;
                goto return_api;
            } else if (!isset($data['name']) || empty($data['name'])) {
                $message = __('missing_parameter') .  __('name');
                $error['name'] = $message;
                $valid = false;
                goto return_api;
            } else if (!isset($data['email']) || empty($data['email'])) {
                $message = __('missing_parameter') .  __('email');
                $error['email'] = $message;
                $valid = false;
                goto return_api;
            } else if (!isset($data['country_code']) || empty($data['country_code'])) {
                $message = __('missing_parameter') . __('country_code');
                $error['phone'] = $message;
                $valid = false;
                goto return_api;
            } else if (!isset($data['phone']) || empty($data['phone'])) {
                $message = __('missing_parameter') . __('phone');
                $error['phone'] = $message;
                $valid = false;
                goto return_api;
            } else if (!isset($data['password']) || empty($data['password'])) {
                $message = __('missing_parameter') .  __('password');
                $error['password'] = $message;
                $valid = false;
                goto return_api;
//            } else if (!isset($data['age_group_id'])) {
//                $message = __('missing_parameter') . __('age_group_id');
//                $error['age_group_id'] = $message;
//                $valid = false;
//                goto return_api;
//            } else if (!isset($data['birth_month'])) {
//                $message = __('missing_parameter') . __('birth_month');
//                $error['birth_month'] = $message;
//                $valid = false;
//                goto return_api;
//            } else if (!isset($data['district_id'])) {
//                $message = __('missing_parameter') . __('district_id');
//                $error['district_id'] = $message;
//                $valid = false;
//                goto return_api;
            } else if (!isset($data['verification_code']) || empty($data['verification_code'])) {
                $message = __('missing_parameter') .  __('verification_code');
                $error['password'] = $message;
                $valid = false;
                goto return_api;
            } else if (!isset($data['is_agreed']) || empty($data['is_agreed'])) {
                $message = __('missing_parameter') .  __('is_agreed');
                $error['is_agreed'] = $message;
                $valid = false;
                goto return_api;
            }

            if (! isset($data['is_browser']) || empty($data['is_browser'])) {
                if (!isset($data['device_type']) || empty($data['device_type'])) {
                    $message = __('missing_parameter') . __('device_type');
                    $error['other'] = $message;
                    $valid = false;
                    goto return_api;
                } else if (!isset($data['device_token']) || empty($data['device_token'])) {
                    $message = __('missing_parameter') . __('device_token');
                    $error['other'] = $message;
                    $valid = false;
                    goto return_api;
                } else if (!isset($data['model_code']) || empty($data['model_code'])) {
                    $message = __('missing_parameter') . __('model_code');
                    $error['other'] = $message;
                    $valid = false;
                    goto return_api;
                } else if (!isset($data['os_version']) || empty($data['os_version'])) {
                    $message = __('missing_parameter') . __('os_version');
                    $error['other'] = $message;
                    $valid = false;
                    goto return_api;
                }
            }


            $conditions = [
                'country_code' => $data['country_code'],
                'phone' => $data['phone']
            ];

            // Check exist phone, email
            $data_user = $this->$model->get_member_by_conditions($conditions);

            if (isset($data_user[$model]) && !empty($data_user[$model])) {
                // check phone exist
                $valid = false;
                $message = __('exist_phone_number');
                $error['phone'] = $message;
                goto return_api;
            }

            if($valid) {
                //check email exist
                $options = array(
                    'conditions' => array(
                        'email' => $data['email'],
                    ),
                    'recursive' => -1
                );
                $data_user_check = $this->$model->find('first', $options);

                if (isset($data_user_check[$model]) && !empty($data_user_check[$model])) {
                    $valid = false;
                    $message = __('duplicate_email_exists');
                    $error['email'] = $message;
                    goto return_api;
                }
            }

            // Check Verification Code
            $option = array(
                'conditions' => array(
                    'member_id' => 0,
                    'country_code' => $data['country_code'],
                    'phone' => $data['phone'],
                    'login_time is null',
                    'enabled' => 1,
                ),
                'recursive' => -1
            );

            $objMemberVerification = ClassRegistry::init('Member.MemberVerification');
            $data_verification = $objMemberVerification->find('first', $option);
            if (isset($data_verification['MemberVerification']) && !empty($data_verification['MemberVerification'])) {
                if ($data_verification['MemberVerification']['verification_code'] == $data['verification_code']) {
                    $generated_time = $data_verification['MemberVerification']['generated_time'];

                    $minute = Environment::read('site.otp_lifetime');


                    if (strtotime("+" . $minute . " minutes", strtotime($generated_time)) < strtotime('now')) {
                        $valid = false;
                        $message = sprintf(__('item_already_expired'), __('verification_code'));
                    }

                } else {
                    $message = __('verification_code_invalid');
                    $valid = false;
                    goto return_api;
                }
            } else {
                $message = __('verification_code_invalid');
                $valid = false;
                goto return_api;
            }

            if ($valid) {
                $data_save[$model]['title'] = $data['title'];
                $data_save[$model]['name'] = $data['name'];
                $data_save[$model]['country_code'] = $data['country_code'];
                $data_save[$model]['phone'] = $data['phone'];
                $data_save[$model]['email'] = $data['email'];
                $data_save[$model]['password'] = md5($data['password']);
                $data_save[$model]['phone_verified'] = date('Y-m-d H:i:s');
                $data_save[$model]['email_verified'] = date('Y-m-d H:i:s');
                $data_save[$model]['is_agreed'] =  $data['is_agreed'];

                if (isset($data['age_group_id'])) {
                    $data_save[$model]['age_group_id'] = $data['age_group_id'];
                }
                if (isset($data['birth_month'])) {
                    $data_save[$model]['birth_month'] = $data['birth_month'];
                }
                if (isset($data['district_id'])) {
                    $data_save[$model]['district_id'] = $data['district_id'];
                }

                if (isset($data['is_read']) && !empty($data['is_read'])) {
                    $data_save[$model]['is_read'] = $data['is_read'];
                }

                if (isset($data['is_under_18']) && !empty($data['is_under_18'])) {
                    $data_save[$model]['is_under_18'] = $data['is_under_18'];
                }

                $verification_email_code = $this->Common->generate_verification_code();
                //$verification_email_code = 1234;

                $data_save[$model]['email_verification'] = $verification_email_code;

                if (!isset($data_user[$model]['token']) || ($data_user[$model]['token'] == '')) {
                    $data_save[$model]['token'] = $this->$model->generateToken();
                } else {
                    $data_save[$model]['token'] = $data_user[$model]['token'];
                }

                $dbo = $this->$model->getDataSource();
                $dbo->begin();
                if ($this->$model->saveAll($data_save)) {

                    $member_id = $this->$model->id;
                    $phone = $data_save[$model]['phone'];
                    $country_code = $data_save[$model]['country_code'];
                    $email = $data_save[$model]['email'];

                    // Update Code - Qr Code
                    $code = str_pad($member_id, 6, "0", STR_PAD_LEFT);
                    $code = substr_replace($code, "-", 3, 0);
                    $code = "C-".$code;
                    $qr_code = $this->Common->generate_qrcode("member", $code, $code)['path'];
                    $data_save[$model]['qrcode_path'] = Environment::read('web.url_img').$qr_code;
                    $qr_code = addslashes($qr_code);

                    $data_update = [
                        'Member.code' => '"'. $code .'"',
                        'Member.qrcode_path' => '"'. $qr_code .'"'
                    ];
                    $this->$model->updateAll($data_update, ['Member.id' => $member_id]);

						// Update old record enable = 0
						$conditions = array(
							'MemberVerification.country_code' => $data['country_code'],
							'MemberVerification.phone' => $data['phone'],
						);
						$updates = array(
							'MemberVerification.enabled' => 0
						);
						$objMemberVerification = ClassRegistry::init('Member.MemberVerification');
						$objMemberVerification->updateAll($updates, $conditions);

                    if ($valid) {
                        $objMemberDevice = ClassRegistry::init('Member.MemberDevice');

                        if ($objMemberDevice->create_new_device($data, $member_id)) {
                            $dbo->commit();

                        } else {
                            $dbo->rollback();
                            $message = __('save_device_failed');
                            $error['other'] = $message;
                            $valid = false;
                        }

                        // Send code to email
                        /*$template = "verification_code";
                        $subject = 'ACX-Cinema - Verification Code';

                        $receiver = $email;

                        $data_email['email'] = $email;
                        $data_email['verification_code'] = $verification_email_code;

                        $result_email = $this->Email->send($receiver, $subject, $template, $data_email);

                        if (!$result_email['status']) {
                            $result_data = $result_email;
                            $message = __('send_email_failed');
                            $valid = false;
                        }*/

//                            $template = "create_account";
//                            $subject = 'ACX-Cinema - Create Account Successful';
//
//                            $receiver = $data['email'];
//                            $mail_data = [
//                                'email' => $data['email'],
//                                'password' => $data['password']
//                            ];
//                            $result_email = $this->Email->send($receiver, $subject, $template, $mail_data);
//
//                            if (!$result_email['status']) {
//                                $result_data = $result_email;
//                                $message = __('send_email_failed');
//                                $valid = false;
//                            }

                        $data_save[$model]['id'] = $member_id;
                    }
                } else {
                    $dbo->rollback();
                    $message = __('data_is_not_saved');
                    $error['other'] = $message;
                    $valid = false;
                }
            }

            return_api:
            $status = $valid;
            $message = $message;
            if($valid){
                $params = $data_save;
                $params['warning'] = $warning;
                if (!$params) {
                    $params = (object)array();
                }
            }else{
                $log_data = array();
                $log_data['message'] = $message;
                $log_data['data_result'] = $data_save;
                $log_data['data'] = $data;

                $this->Api->set_error_log($log_data);
            }


            if (!$status) {
                $params = array('error' => $error);
            }

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }

    public function api_signup_backup() {
		$this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
			$error = array();
			$warning = array();
            $params = (object)array();
			$data = $this->request->data;
			
			if (!isset($data['title']) || empty($data['title'])) {
				$message = __('missing_parameter') .  __('title');
				$error['title'] = $message;
			} else if (!isset($data['name']) || empty($data['name'])) {
				$message = __('missing_parameter') .  __('name');
				$error['name'] = $message;
			} else if (!isset($data['email']) || empty($data['email'])) {
				$message = __('missing_parameter') .  __('email');
				$error['email'] = $message;
			} else if (!isset($data['country_code']) || empty($data['country_code'])) {
				$message = __('missing_parameter') . __('country_code');
				$error['phone'] = $message;
			} else if (!isset($data['phone']) || empty($data['phone'])) {
				$message = __('missing_parameter') . __('phone');
				$error['phone'] = $message;
			} else if (!isset($data['password']) || empty($data['password'])) {
				$message = __('missing_parameter') .  __('password');
				$error['password'] = $message;
			} else if (!isset($data['device_type']) || empty($data['device_type'])) {
				$message = __('missing_parameter') . __('device_type');
				$error['other'] = $message;
			} else if (!isset($data['device_token']) || empty($data['device_token'])) {
				$message = __('missing_parameter') . __('device_token');
				$error['other'] = $message;
			} else if (!isset($data['model_code']) || empty($data['model_code'])) {
				$message = __('missing_parameter') . __('model_code');
				$error['other'] = $message;
			} else if (!isset($data['os_version']) || empty($data['os_version'])) {
				$message = __('missing_parameter') . __('os_version');
				$error['other'] = $message;
            } else {
				$this->Api->set_language($this->lang18);

				$url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
				$this->Api->set_save_log(true);

				$conditions = [
					'country_code' => $data['country_code'],
					'phone' => $data['phone']
				];
				
				// Check exist phone, email
				$data_user = $this->$model->get_member_by_conditions($conditions);

				$message = '';
				$valid = true;
				$data_save = array();

				if (isset($data_user[$model]) && !empty($data_user[$model])) {
					// check phone exist
					$valid = false;
					$message = __('exist_phone_number');
					$error['phone'] = $message;
					goto return_api;
				}
					
				if($valid) {
					//check email exist
					$options = array(
						'conditions' => array(
							'email' => $data['email'],
						),
						'recursive' => -1
					);
					$data_user_check = $this->$model->find('first', $options);

					if (isset($data_user_check[$model]) && !empty($data_user_check[$model])) {
						$valid = false;
						$message = __('duplicate_email_exists');
						$error['email'] = $message;
						goto return_api;
					}
				}

				if (!isset($data['verification_code']) || empty($data['verification_code'])) {
					$message = __('retrieve_data_successfully');

					$objMember = ClassRegistry::init('Member.Member');
					$conditions = [
						'country_code' => $data['country_code'],
						'phone' => $data['phone']
					];
		
					$data_member = $objMember->get_member_by_conditions($conditions);
		
					$member_id = 0;
					if (isset($data_member['Member']['id']) && !empty($data_member['Member']['id'])) {
						$member_id = $data_member['Member']['id'];
					}
		
					//send sms 
					$verification_code = $this->Common->generate_verification_code();
		
					$data_insert = array();
					$data_insert['member_id'] = $member_id;
					$data_insert['verification_code'] = $verification_code;
					$data_insert['generated_time'] = date('Y-m-d H:i:s');
		
				
					$conditions = array(
						'MemberVerification.country_code' => $data['country_code'],
						'MemberVerification.phone' => $data['phone'],
					);	
					$data_insert['country_code'] = $data['country_code'];
					$data_insert['phone'] = $data['phone'];
		
					// Update old record enable = 0
					$updates = array(
						'MemberVerification.enabled' => 0
					);

					$dbo = $this->$model->getDataSource();
					$dbo->begin();

					$objMemberVerification = ClassRegistry::init('Member.MemberVerification');
					$objMemberVerification->updateAll($updates, $conditions);
		
					if ($objMemberVerification->saveAll($data_insert)) {
						//send verification code to user's phone
						
						$dbo->commit();
						$receiver = array();
						$receiver[0]['phone'] = $data_insert['country_code'].$data_insert['phone'];
						$receiver[0]['language'] = $this->lang18;
		
						$str_title = 'ACX-Cinema';
						$title = array($this->lang18 => $str_title);
		
						$str_msg = sprintf(__('verification_code_msg'), $verification_code);
						$sms_message = array($this->lang18 => $str_msg);
		
						$sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
						// $sent_data['status'] = true;
						if (!$sent_data['status']) {
							$result_data = $sent_data;
							$message = __('send_sms_failed');
							$valid = false;
						}
			
		
					} else {
						$dbo->rollback();
						$message = __('data_is_not_saved');
						$valid = false;
					}

					// always return because just send verification code
					goto return_api;
				} else if ( isset($data['verification_code']) && ! empty($data['verification_code'])) {					
					// Check Verification Code
					$option = array(
						'conditions' => array(
							'member_id' => 0,
							'country_code' => $data['country_code'],
							'phone' => $data['phone'],
							'login_time is null',
							'enabled' => 1,
						),
						'recursive' => -1
					);

					$objMemberVerification = ClassRegistry::init('Member.MemberVerification');
					$data_verification = $objMemberVerification->find('first', $option);
					if (isset($data_verification['MemberVerification']) && !empty($data_verification['MemberVerification'])) {
						if ($data_verification['MemberVerification']['verification_code'] == $data['verification_code']) {
							$generated_time = $data_verification['MemberVerification']['generated_time'];
	
							$minute = Environment::read('site.otp_lifetime');
	
	
							if (strtotime("+" . $minute . " minutes", strtotime($generated_time)) < strtotime('now')) {
								$valid = false;
								$message = sprintf(__('item_already_expired'), __('verification_code'));
							}
	
						} else {
							$message = __('verification_code_invalid');
							$valid = false;
						}
					} else {
						$message = __('verification_code_invalid');
						$valid = false;
					}
				}

				if (isset($data['payment_type_id']) && ! empty($data['payment_type_id']) ) {
					// later
				
				}


				if ($valid) {			
					$data_save[$model]['title'] = $data['title'];
					$data_save[$model]['name'] = $data['name'];
					$data_save[$model]['country_code'] = $data['country_code'];
					$data_save[$model]['phone'] = $data['phone'];
					$data_save[$model]['age_group_id'] = $data['age_group_id'];
					$data_save[$model]['birth_month'] = $data['birth_month'];
					$data_save[$model]['email'] = $data['email'];
					$data_save[$model]['password'] = md5($data['password']);

					if (!isset($data_user[$model]['token']) || ($data_user[$model]['token'] == '')) {
						$data_save[$model]['token'] = $this->$model->generateToken();
					} else {
						$data_save[$model]['token'] = $data_user[$model]['token'];
					}

					$dbo = $this->$model->getDataSource();
					$dbo->begin();
					if ($this->$model->saveAll($data_save)) {

						$member_id = $this->$model->id;

						// Update old record enable = 0
						$conditions = array(
							'MemberVerification.country_code' => $data['country_code'],
							'MemberVerification.phone' => $data['phone'],
						);
						$updates = array(
							'MemberVerification.enabled' => 0
						);
						$objMemberVerification = ClassRegistry::init('Member.MemberVerification');
						$objMemberVerification->updateAll($updates, $conditions);

						if ($valid) {
							$objMemberDevice = ClassRegistry::init('Member.MemberDevice');
							if ($objMemberDevice->create_new_device($data, $member_id)) {
								$dbo->commit();
								$template = "create_account";
								$subject = 'ACX-Cinema - Create Account Successful';
					
								$receiver = $data['email'];
								
								$result_email = $this->Email->send($receiver, $subject, $template, []);
								
								if (!$result_email['status']) {
									$result_data = $result_email;
									$message = __('send_email_failed');
									$valid = false;
								}

								$data_save[$model]['id'] = $member_id;
							} else {
								$dbo->rollback();
								$message = __('save_device_failed');
								$error['other'] = $message;
								$valid = false;
							}
						}
					} else {
						$dbo->rollback();
						$message = __('data_is_not_saved');
						$error['other'] = $message;
						$valid = false;
					}
				}

				return_api:
                $status = $valid;
                $message = $message;
                if($valid){
					$params = $data_save;
					$params['warning'] = $warning;
                    if (!$params) {
                        $params = (object)array();
                    }
                }else{
					$log_data = array();
					$log_data['message'] = $message;
					$log_data['data_result'] = $data_save;
					$log_data['data'] = $data;

                    $this->Api->set_error_log($log_data);
				}
			}

			if (!$status) {
				$params = array('error' => $error);
			}

            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
	}

	public function api_logout() {
		$this->Api->init_result();
		$model = $this->model;
		$device_model = $this->device_model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
			$error = array();
            $params = (object)array();
			$data = $this->request->data;
			
			if (!isset($data['token']) || empty($data['token'])) {
				$message = __('missing_parameter') .  __('token');
				$error['password'] = $message;
			} else if (!isset($data['device_token']) || empty($data['device_token'])) {
				$message = __('missing_parameter') . __('device_token');
				$error['other'] = $message;
            } else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

				$valid = true;
				$message = '';

				$user_id = $this->$model->get_id_by_token($data);

				if($user_id <= 0) {
					$valid = false;
					$message = __('user_not_found');
				}

				if ($valid) {
					$conditions = array(
						'member_id' => $user_id,
						$device_model.'.token' => $data['device_token']
					);

					// Update Token Null
					$objMember = ClassRegistry::init('Member.Member');
					$objMember->id = $user_id;
					$objMember->saveField('token', null);
			

					// Remove All Member Device
					$objMemberDevice = ClassRegistry::init('Member.MemberDevice');
					if ($objMemberDevice->deleteAll($conditions)) {
						$message = sprintf(__('item_was_deleted'), 'Token');
					} else {
						$message = __('data_is_not_saved');
						$valid = false;
					}
				}

                $status = $valid;
                
                if($status){
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

	public function api_update_profile() {
		$this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
			$error = array();
            $params = (object)array();
			$data = $this->request->data;

            $extra_errors = '';

			if (!isset($data['token']) || empty($data['token'])) {
				$message = __('missing_parameter') .  __('token');
				$error['token'] = $message;
            } else if (!isset($data['name']) || empty($data['name'])) {
				$message = __('missing_parameter') .  __('name');
				$error['name'] = $message;
            } else if (!isset($data['title']) || empty($data['title'])) {
				$message = __('missing_parameter') .  __('title');
				$error['title'] = $message;
//            } else if (!isset($data['password']) || empty($data['password'])) {
//                $message = __('missing_parameter') .  __('password');
//                $error['password'] = $message;
//            } else if (!isset($data['age_group_id'])) {
//                $message = __('missing_parameter') .  __('age_group_id');
//                $error['age_group_id'] = $message;
//            } else if (!isset($data['birth_month'])) {
//                $message = __('missing_parameter') .  __('birth_month');
//                $error['birth_month'] = $message;
//            } else if (!isset($data['district_id'])) {
//                $message = __('missing_parameter') .  __('district_id');
//                $error['district_id'] = $message;
            } else if (!isset($data['country_code']) || empty($data['country_code'])) {
                $message = __('missing_parameter') .  __('country_code');
                $error['country_code'] = $message;
            } else if (!isset($data['phone']) || empty($data['phone'])) {
                $message = __('missing_parameter') .  __('phone');
                $error['phone'] = $message;
            } else if (!isset($data['email']) || empty($data['email'])) {
                $message = __('missing_parameter') .  __('email');
                $error['email'] = $message;
            } else if (!$this->Common->phone_validation($data['country_code'], $data['phone'])) {
                $valid = false;
                $message = __('invalid_phone_format');
            } else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
				$this->Api->set_save_log(true);
				
				$user_id = $this->$model->get_id_by_token($data);
				$valid = true;

				$warning = array();
				$data_user = array();
				$data_save = array();
				if($user_id > 0){	
					$data_user = $this->$model->get_member_by_field($user_id, 'id');
				} else {
					$valid = false;
					$message = __('user_not_found');
					$error['token'] = $message;
				}

                if ($valid) {

                    $diff_mail = $diff_phone = false;
                    if (
                        ($data_user[$model]['country_code'] !=  $data['country_code'])
                        ||  ($data_user[$model]['phone'] !=  $data['phone'])
                    ) {
                        $verification_phone_code = $this->Common->generate_verification_code();
                        $data_user[$model]['phone_verification'] = $verification_phone_code;
                        $data_user[$model]['phone_verified'] = null;
                        $diff_phone = true;
                    }

                    if ($data_user[$model]['email'] !=  trim($data['email'])) {
                        $verification_email_code = $this->Common->generate_verification_code();
                        $data_user[$model]['email_verification'] = $verification_email_code;
                        $data_user[$model]['email_verified'] = null;
                        $diff_mail = true;
                    }

                    $data_user[$model]['name'] = $data['name'];
					$data_user[$model]['title'] = $data['title'];
                    $data_user[$model]['country_code'] = $data['country_code'];
                    $data_user[$model]['phone'] = $data['phone'];
                    $data_user[$model]['email'] = $data['email'];

                    if (isset($data['password']) && !empty($data['password'])) {
                        $data_user[$model]['password'] = md5($data['password']);
                    }

                    if (isset($data['age_group_id'])) {
                        $data_user[$model]['age_group_id'] = $data['age_group_id'];
                    }

                    if (isset($data['birth_month'])) {
                        $data_user[$model]['birth_month'] = $data['birth_month'];
                    }

                    if (isset($data['district_id'])) {
                        $data_user[$model]['district_id'] = $data['district_id'];
                    }

                    if (isset($data['is_delete_image']) && $data['is_delete_image'] == 1) {
                        $file = new File( 'img/'. $data_user[$model]['image'] );
                        $file->delete();
                        $data_user[$model]['image'] = null;
                    } else if (isset($_FILES['image']) && ! empty($_FILES['image'])) {
                        $data['image'] = $_FILES['image'];
                        $data_user[$model]['image'] = $this->upload_poster($data, $valid, $extra_errors, $data_user[$model]['image']);

                        if ($valid == false) {
                            $message = $extra_errors;
                            goto return_api;
                        }
                    }

					$dbo = $this->$model->getDataSource();
					$dbo->begin();
					if ($this->$model->saveAll($data_user)) {

                        $phone = $data['phone'];
                        $country_code = $data['country_code'];
                        $email = trim($data['email']);

					    //$data_user[$model]['photo'] = ($data_user[$model]['photo']) ? Environment::read('web.url_img').$data_user[$model]['photo']: '';
						$dbo->commit();
                        $message = __('data_is_saved');

                        if ($diff_phone) {

                            $receiver = array();
                            $receiver[0]['phone'] = $country_code . $phone;
                            $receiver[0]['language'] = $this->lang18;

                            $str_title = 'ACX-Cinema';
                            $title = array($this->lang18 => $str_title);

                            $str_msg = sprintf(__('verification_code_msg'), $verification_phone_code);
                            $sms_message = array($this->lang18 => $str_msg);

                            $sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
                            // $sent_data['status'] = true;
                            if (!$sent_data['status']) {
                                $result_data = $sent_data;
                                $message = __('send_sms_failed');
                                $valid = false;
                            }
                        }

                        if ($diff_mail) {
                            $template = "verification_code";
                            $subject = 'ACX-Cinema - Verification Code';

                            $receiver = $email;

                            $data_email['email'] = $email;
                            $data_email['verification_code'] = $verification_email_code;

                            $result_email = $this->Email->send($receiver, $subject, $template, $data_email);

                            if (!$result_email['status']) {
                                $result_data = $result_email;
                                $message = __('send_email_failed');
                                $valid = false;
                            }
                        }
					} else {
						$dbo->rollback();
						$message = __('data_is_not_saved');
						$error['other'] = $message;
						$valid = false;
					}
				}

				return_api:

                $status = $valid;
                $message = $message;
                
                if($valid){
					$params = $data_user;
                    if (!$params) {
                        $params = (object)array();
                    }
                }else{
					$log_data = array();
					$log_data['message'] = $message;
					$log_data['data_result'] = $data_save;
					$log_data['data'] = $data;

                    $this->Api->set_error_log($log_data);
				}

			}

			if (!$status) {
				$params = array('error' => $error);
			}

            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
	}


	public function api_get_account() {
		$this->Api->init_result();
		$model = $this->model;
		$model_lang = $this->model_lang;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = true;
            $message = "";
            $params = (object)array();
			$data = $this->request->data;
			
            if (!isset($data['token']) || empty($data['token'])) {
                $message = __('missing_parameter') . __('token');
            } else {
				$this->Api->set_language($this->lang18);
                $now = date( 'Y-m-d' );

                $options = [
                    'conditions'=> [
                        'token' => $data['token']
                    ],
                    'contain' => array(
                        'MemberRenewal' => array(
                            'conditions' => array(
                                'date(MemberRenewal.renewal_date) <=' => $now,
                                'date(MemberRenewal.expired_date) >=' => $now,
                                'status' => 3
                            )
                        )
                    ),
                ];
				$data_user = $this->$model->find('first', $options);

				if (isset($data_user[$model]) && !empty($data_user[$model])) {
                    $data_user[$model]['qrcode_path'] = Environment::read('web.url_img').$data_user[$model]['qrcode_path'];

                    if($data_user[$model]['enabled'] == false) {
						$valid = false;
						$message =  __('this_account_was_disabled');
					} else if(!empty($data_user[$model]['deleted'])) {
						$valid = false;
						$message =  __('this_account_was_deleted');
					} else{
						$objGender = ClassRegistry::init('Member.Gender');
						$genders = $objGender->get_static_list();

                        $is_verified_phone = $is_verified_email = $is_renewed =  true;
						if (empty($data_user[$model]['phone_verified'])) {
                            $is_verified_phone = false;
                        }
                        if (empty($data_user[$model]['email_verified'])) {
                            $is_verified_email = false;
                        }
                        if (count($data_user['MemberRenewal']) == 0) {
                            $is_renewed = false;
                        }
                        $data_user[$model]['is_verified_phone'] = $is_verified_phone;
                        $data_user[$model]['is_verified_email'] = $is_verified_email;
                        $data_user[$model]['is_renewed'] = $is_renewed;

                        if (! empty($data_user[$model]['image'])) {
                            $data_user[$model]['image'] = Environment::read('web.url_img') . $data_user[$model]['image'];
                        }

                        $message = __('member_found');
					}
				} else {
					$valid = false;
					$message = __('user_not_found');
				}

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
				$this->Api->set_save_log(true);

                if($status){
					$params = $data_user;
                    if (!$params) {
                        $params = (object)array();
                    }		
                }else{
					$log_data = array();
					$log_data['message'] = $message;
					$log_data['data_result'] = $data_user;
					$log_data['data'] = $data;

                    $this->Api->set_error_log($log_data);
				}
				

				//return_data:
				//send verification code to user
			}

            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
	}

	public function api_change_password() {
		$this->Api->init_result();
		$model = $this->model;
		$model_lang = $this->model_lang;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
            $message = "";
            $params = (object)array();
			$data = $this->request->data;
			$valid = true;

			if (!isset($data['old_password']) || empty($data['old_password'])) {
				$message = __('missing_parameter') . __('old_password');
			} else if (!isset($data['new_password']) || empty($data['new_password'])) {
                $message = __('missing_parameter') . __('new_password');
			} else if (!isset($data['token']) || empty($data['token'])) {
                $message = __('missing_parameter') . __('token');
            } else {
					$this->Api->set_language($this->lang18);


					$data_user = $this->$model->get_member_by_field($data['token'], 'token');

					if (isset($data_user[$model]) && !empty($data_user[$model])) {	
						if($data_user[$model]['enabled'] == false) {
							$valid = false;
							$message =  __('this_account_was_disabled');
						}
					} else {
						$valid = false;
						$message = __('user_not_found');
					}


			
					if ($valid) {
						if (md5($data['old_password']) != $data_user[$model]['password']) {
							$valid = false;
							$message = __('username_password_not_found');
						} else {
							$data_user[$model]['password'] = md5($data['new_password']);
						}

						if($valid) {
							if ($this->$model->saveAll($data_user)) {
								$message = __('data_is_saved');
							} else {
								$valid = false;
								$message = __('data_is_not_saved');
							}
						}
					}

					$url_params = $this->request->params;
					$this->Api->set_post_params($url_params, $data);
					$this->Api->set_save_log(true);

					$status = $valid;

					if($status){
						$params = $data_user;
						if (!$params) {
							$params = (object)array();
						}		
					}else{
						$log_data = array();
						$log_data['message'] = $message;
						$log_data['data_result'] = $data_user;
						$log_data['data'] = $data;

						$this->Api->set_error_log($log_data);
					}
				}

				//return_data:
				//send verification code to user
            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
	}

	public function api_get_member_by_code() {
		$this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
            $message = "";
            $params = (object)array();
			$data = $this->request->data;
			
			if (!isset($data['code']) || empty($data['code'])) {
                $message = __('missing_parameter') . __('code');
			} else if (!isset($data['token']) || empty($data['token'])) {
                $message = __('missing_parameter') . __('token');
            } else {
				$this->Api->set_language($this->lang18);

				$data_user = $this->$model->get_member_by_field($data['code'], 'code');

				if (isset($data_user[$model]) && !empty($data_user[$model])) {
					//check for renewal

					$data_renewal = $this->$model->MemberRenewal->check_renewal($data_user[$model]['id']);
					if (isset($data_renewal['MemberRenewal']) && !empty($data_renewal['MemberRenewal'])) {
						$data_user[$model]['expired_date'] = $data_renewal['MemberRenewal']['expired_date'];
						$data_user[$model]['expired_date_label'] = date('m/d/Y', strtotime($data_renewal['MemberRenewal']['expired_date']));
                        $data_user[$model]['birthday_label'] = date('m/d/Y', strtotime($data_user[$model]['birthday']));


                        $objSetting = ClassRegistry::init('Setting.Setting');
                        $disc_percentage = $objSetting->get_value('discount-member');

                        $data_user[$model]['discount_member'] = $disc_percentage;
						$status = true;
						$message = __('user_found');
					} else {
						$message = __('membership_expired');
					}

				} else {
					$message = __('user_not_found');
				}

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
				$this->Api->set_save_log(true);

                if($status){
					$params = $data_user;
                    if (!$params) {
                        $params = (object)array();
                    }		
                }else{
					$log_data = array();
					$log_data['message'] = $message;
					$log_data['data_result'] = $data_user;
					$log_data['data'] = $data;

                    $this->Api->set_error_log($log_data);
				}
				
			}

            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
	}

    private function upload_poster(&$data, &$valid, &$extra_errors, $old_poster = null)
    {
        $uploaded_poster = $data['image'];
        $image_link = '';
        if (!empty($uploaded_poster) && $uploaded_poster['tmp_name'])
        {
            if (!preg_match('/image\/*/', $uploaded_poster['type']))
            {
                $valid = false;
                $extra_errors .= 'Wrong image type. ';
            }
            else
            {
                $uploaded = $this->Common->upload_images( $uploaded_poster, $this->upload_path, $this->poster_prefix );

                if( isset($uploaded['status']) && ($uploaded['status'] == true) )
                {
                    $image_link = $uploaded['params']['path'];
                    $image_link = str_replace("\\",'/',$image_link);

                    if (!empty($old_poster)) {
                        $file = new File( 'img/'. $old_poster );
                        $file->delete();
                    }
                }
                else
                {
                    $image_link =  $old_poster;
                }
            }
        }
        return $image_link;
    }

    public function admin_generate_member_sales_report() {
        $model = $this->model;

        $report_result = array();
        if ($this->request->is('post')) {
            $data = $this->request->data;

            $type = 'hourly';

            if (!isset($data['Report']['report_date_from']) || empty($data['Report']['report_date_from']) ||
                !isset($data['Report']['report_date_to']) || empty($data['Report']['report_date_to'])) {
                $this->Session->setFlash(__('date_time_report_invalid'), 'flash/error');
                $type = '';
                goto display_initial_data;
            }

            $this->requestAction(array(
                'plugin' => 'member',
                'controller' => 'members',
                'action' => 'generate_today_report',
                'admin' => true,
                'prefix' => 'admin',
                'ext' => 'json'
            ), array(
                'data' => $data,
                'type' => $type
            ));

        }

        display_initial_data :

        //$types = array('hourly','daily');
        $this->set(compact('model', 'report_result'));
    }

    public function admin_generate_today_report(){
        $model = $this->model;

        $results = array(
            'status' => false,
            'message' => __('missing_parameter'),
            'params' => array(),
        );
        $data = $this->request->data;
        $type = $this->request->type;
        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_from'])));
        $date_to = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_to'])));


        $this->disableCache();

        //if( $this->request->is('get') ) {
            $result = $this->$model->get_data_today_export($data, 1, 2000, $this->lang18);

            $data_binding['dobMonths'] = $this->Common->get_list_month();


//            if ($result) {

                $cvs_data = array();

                foreach ($result as $row) {
                    $temp = $this->$model->format_data_today_export(array(), $row, $data_binding);

                    array_push($cvs_data, $temp);
                }

                try{
                    $file_name = $date_from. '-to-' .$date_to.'-member-sales';

                    // export xls
//                    if ($this->request->type == "xls") {
                        $excel_readable_header = array(
                            array('label' => 'Transaction Date'),
                            array('label' => 'Member Code'),
                            array('label' => 'Member Name'),
                            array('label' => 'Age Group'),
                            array('label' => 'Phone Verified'),
                            array('label' => 'Email Verified'),
                            array('label' => 'Invoice Number'),
                            array('label' => 'Membership Start Date'),
                            array('label' => 'Membership End Date'),

                        );

                        $this->Common->export_excel(
                            $cvs_data,
                            $file_name,
                            $excel_readable_header
                        );
//                    } else {
//                        $header = array(
//                            'label' => __('id'),
//                            'label' => __('name'),
//                            'label' => __d('member','month_of_birth'),
//                            'label' => __d('member', 'country_code'),
//                            'label' => __('phone'),
//                            'label' => __('email')
//                        );
//                        $this->Common->export_csv(
//                            $cvs_data,
//                            $header,
//                            $file_name
//                        );
//                    }
                } catch ( Exception $e ) {
                    $this->LogFile->writeLog($this->LogFile->get_system_error(), $e->getMessage());
                    $results = array(
                        'status' => false,
                        'message' => __('export_csv_fail'),
                        'params' => array()
                    );
                }
//            }else{
//                $results['message'] = __('no_record');
//            }
        //}

        $this->set(array(
            'results' => $results,
            '_serialize' => array('results')
        ));
    }

    public function api_check_phone_registration() {
        $this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
            
            if (!isset($data['token']) || empty($data['token'])) {
                $message = __('missing_parameter') .  __('token');
            } else if (!isset($data['staff_id']) || empty($data['staff_id'])) {
                $message = __('missing_parameter') .  __('staff_id');
			} else if (!isset($data['country_code']) || empty($data['country_code'])) {
                $message = __('missing_parameter') .  __('country_code');
            } else if (!isset($data['phone']) || empty($data['phone'])) {
                $message = __('missing_parameter') .  __('phone');
            } else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $check_result = $this->$model->check_phone_for_registration($data, $this->Api->get_language());

                $valid = $check_result['status'];
                $message = $check_result['message'];

                $result = array();
                if ($valid) {
                    $objSetting = ClassRegistry::init('Setting.Setting');
                    $disc_member_percentage = $objSetting->get_value('discount-member');
                    $result['discount_member'] = $disc_member_percentage;
                    $registration_fee = $objSetting->get_value('member-renewal');
                    $result['registration_fee'] = $registration_fee;
                }

                $status = $valid;

                if($status){
					$params = $result;
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



	public function api_signup_public() {
		$this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();

            $valid = true;
            $data_save = array();

            $status = false;
			$message = "data_is_saved";
			$error = array();
			$warning = array();
            $params = (object)array();
			$data = $this->request->data;

            $this->Api->set_language($this->lang18);

            $url_params = $this->request->params;
            $this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);

            if (!isset($data['title']) || empty($data['title'])) {
				$message = __('missing_parameter') .  __('title');
				$error['title'] = $message;
                $valid = false;
				goto return_api;
			} else if (!isset($data['name']) || empty($data['name'])) {
				$message = __('missing_parameter') .  __('name');
				$error['name'] = $message;
                $valid = false;
                goto return_api;
			} else if (!isset($data['email']) || empty($data['email'])) {
				$message = __('missing_parameter') .  __('email');
				$error['email'] = $message;
                $valid = false;
                goto return_api;
			} else if (!isset($data['verification_code']) || empty($data['verification_code'])) {
				$message = __('missing_parameter') . __('verification_code');
				$error['verification_code'] = $message;
                $valid = false;
                goto return_api;
			} else if (!isset($data['password']) || empty($data['password'])) {
				$message = __('missing_parameter') .  __('password');
				$error['password'] = $message;
                $valid = false;
                goto return_api;
			}

                //get data verification code again and also check for verification code
                $result = $this->$model->get_pos_registration($data, $this->Api->get_language());

                $valid = $result['status'];
                $message = $result['message'];
                $data_pos_registration = $result['params'];

                if (!$valid) {
                    //set everything to false, and also set the error message and go to return_api
                    goto return_api;
                }

				if($valid) {
					//check email exist
					$options = array(
						'conditions' => array(
							'email' => $data['email'],
						),
						'recursive' => -1
					);
					$data_user_check = $this->$model->find('first', $options);

					if (isset($data_user_check[$model]) && !empty($data_user_check[$model])) {
						$valid = false;
						$message = __('duplicate_email_exists');
						$error['email'] = $message;
						goto return_api;
					}
				}
                
				if ($valid) {
					$data_save[$model]['title'] = $data['title'];
					$data_save[$model]['name'] = $data['name'];
					$data_save[$model]['country_code'] = $data_pos_registration['country_code'];
					$data_save[$model]['phone'] = $data_pos_registration['phone'];
					$data_save[$model]['email'] = $data['email'];
					$data_save[$model]['password'] = md5($data['password']);
                    $data_save[$model]['email_verified'] = date('Y-m-d H:i:s');
                    $data_save[$model]['phone_verification'] = $data['verification_code'];
                    $data_save[$model]['phone_verified'] = date('Y-m-d H:i:s');
                    $data_save[$model]['is_read'] = 1;

                    if (isset($data['is_receive_promotion'])) {
                        $data_save[$model]['is_receive_promotion'] = $data['is_receive_promotion'];
                    }

                    if (isset($data['is_agreed']) && !empty($data['is_agreed'])) {
                        $data_save[$model]['is_agreed'] = $data['is_agreed'];
                    }

                    if (isset($data['is_under_18']) && !empty($data['is_under_18'])) {
                        $data_save[$model]['is_under_18'] = $data['is_under_18'];
                    }

                    if (isset($data['age_group_id'])) {
                        $data_save[$model]['age_group_id'] = $data['age_group_id'];
                    }
                    if (isset($data['birth_month'])) {
                        $data_save[$model]['birth_month'] = $data['birth_month'];
                    }
                    if (isset($data['district_id'])) {
                        $data_save[$model]['district_id'] = $data['district_id'];
                    }

					if (!isset($data_user[$model]['token']) || ($data_user[$model]['token'] == '')) {
						$data_save[$model]['token'] = $this->$model->generateToken();
					} else {
						$data_save[$model]['token'] = $data_user[$model]['token'];
					}

                    $data_renewal = array();
                    $data_renewal['MemberRenewal']['payment_log_id'] = 0;
                    $data_renewal['MemberRenewal']['order_id'] = $data_pos_registration['order_id'];
                    $data_renewal['MemberRenewal']['date'] = $data_pos_registration['date'];
                    $data_renewal['MemberRenewal']['amount'] = $data_pos_registration['amount'];
                    $data_renewal['MemberRenewal']['renewal_date'] = date('Y-m-d', strtotime($data_pos_registration['date']));
                    $data_renewal['MemberRenewal']['expired_date'] = $data_pos_registration['expiry_date'];
                    $data_renewal['MemberRenewal']['token'] = $this->$model->generateToken();
                    $data_renewal['MemberRenewal']['status'] = 3;
                    $data_renewal['MemberRenewal']['is_cms'] = 2;


                    $objMemberRenewal = ClassRegistry::init('Member.MemberRenewal');

					$dbo = $this->$model->getDataSource();
					$dbo->begin();
                    if ($this->$model->saveAll($data_save) && 
                        $objMemberRenewal->saveAll($data_renewal)) {

                        $member_renewal_id = $objMemberRenewal->id;

						$member_id = $this->$model->id;
						$phone = $data_save[$model]['phone'];
                        $country_code = $data_save[$model]['country_code'];
                        $email = $data_save[$model]['email'];

						// Update Code - Qr Code
						$code = str_pad($member_id, 6, "0", STR_PAD_LEFT);
						$code = substr_replace($code, "-", 3, 0);
						$code = "C-".$code;
                        $qr_code = $this->Common->generate_qrcode("member", $code, $code)['path'];
                        $data_save[$model]['qrcode_path'] = Environment::read('web.url_img').$qr_code;
                        $qr_code = addslashes($qr_code);

						$data_update = [
							'Member.code' => '"'. $code .'"',
							'Member.qrcode_path' => '"'. $qr_code .'"'
                        ];
                        
                        $conditions_delete = array(
                            "MemberPosRegistration.id" => $data_pos_registration['registration_id']
                        );

                        $inv_number = Environment::read('site.prefix.renewal').str_pad($member_renewal_id, 7, '0', STR_PAD_LEFT);

                        $data_renewal['MemberRenewal']['id'] = $member_renewal_id;
                        $data_renewal['MemberRenewal']['member_id'] = $member_id;
                        $data_renewal['MemberRenewal']['inv_number'] = $inv_number;

                        $this->create_member_coupon($data_renewal);
                        $this->create_member_coupon($data_renewal);

                        $conditions_update_order = array(
                            "Order.id" => $data_pos_registration['order_id']
                        );

                        $update_order = array(
                            "Order.member_id" => $member_id
                        );

                        $objOrder = ClassRegistry::init('Pos.Order');
                        $objMemberPosRegistration = ClassRegistry::init('Member.MemberPosRegistration');
                        if (($this->$model->updateAll($data_update, ['Member.id' => $member_id])) && 
                            ($objOrder->updateAll($update_order, $conditions_update_order)) &&
                            ($objMemberRenewal->saveAll($data_renewal)) &&
                            ($objMemberPosRegistration->deleteAll($conditions_delete, false))) {
                            $dbo->commit();
                            $data_save[$model]['id'] = $member_id;
                        } else {
                            $dbo->rollback();
                            $message = __('failed_to_update_member');
                            $error['other'] = $message;
                            $valid = false;
                        }

					} else {
						$dbo->rollback();
						$message = __('data_is_not_saved');
						$error['other'] = $message;
						$valid = false;
					}
				}

				return_api:
                $status = $valid;
                $message = $message;
                if($valid){
					$params = $data_save;
					$params['warning'] = $warning;
                    if (!$params) {
                        $params = (object)array();
                    }
                }else{
					$log_data = array();
					$log_data['message'] = $message;
					$log_data['data_result'] = $data_save;
					$log_data['data'] = $data;

                    $this->Api->set_error_log($log_data);
				}


			if (!$status) {
				$params = array('error' => $error);
			}

            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
    }
    
    public function api_registration_pos_verification() {
        $this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
            
            if (!isset($data['verification_code']) || empty($data['verification_code'])) {
                $message = __('missing_parameter') .  __('verification_code');
            } else if (!isset($data['language']) || empty($data['language'])) {
				$message = __('missing_parameter') .  __('language');
            } else {
				$this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->get_pos_registration($data, $this->Api->get_language());

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

}
