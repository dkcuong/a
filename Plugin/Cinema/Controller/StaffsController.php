<?php
App::uses('CinemaAppController', 'Cinema.Controller');

class StaffsController extends CinemaAppController {

	public $components = array('Paginator');
	private $model = 'Staff';

	private $filter = array(
		'name',
	);

	private $rule = array(
		1 => array('required'),
		2 => array('required'),
		3 => array('required','enum'),
	);
	private $rule_spec = array(
		3 => array('N', 'Y', 'y', 'n')
	);

	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('staff', 'staff_title'));
	}

	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;

		$condition_search = $data_search;
		$conditions = [];
		if (isset($condition_search['username']) && !empty($condition_search['username']))
		{
			$conditions[] = 'username LIKE "%' . $condition_search['username'] . '%"';
			unset($condition_search['username']);
		}

		if (isset($condition_search['name']) && !empty($condition_search['name']))
		{
			$conditions[] = 'name LIKE "%' . $condition_search['name'] . '%"';
			unset($condition_search['name']);
		}

		if (isset($condition_search['phone']) && !empty($condition_search['phone']))
		{
			$conditions[] = 'phone LIKE "%' . $condition_search['phone'] . '%"';
			unset($condition_search['phone']);
		}

		$conditions_temp = $this->Common->get_filter_conditions($condition_search, $model, $model, $this->filter);

		$conditions = array_merge($conditions, $conditions_temp);
		
		if ($data_search){
			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'cinema',
                    'controller' => 'staffs',
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
                    'plugin' => 'cinema',
                    'controller' => 'staffs',
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
			'fields' => array($model.".*"),
			'conditions' => array($conditions),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.name' => 'ASC'),
		);
		
        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'data_search'));	
	}

	public function admin_view($id) {
		$model = $this->model;
		$languages_model = $this->model_lang;

		$options = array(
			'fields' => array($model.'.*'),
			'contain' => array(
				'UpdatedBy',
				'CreatedBy'
			),
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
		);
		$model_data = $this->$model->find('first', $options);

		if (!$model_data) {
			throw new NotFoundException(__('invalid_data'));
		}

        //languages fields
        $language_input_fields = $this->language_display_fields;

        $languages = isset($model_data[$languages_model]) ? $model_data[$languages_model] : array();

		$this->set('dbdata', $model_data);

        $this->set(compact('model', 'language_input_fields','languages'));
	}


	public function admin_add() {
		$model = $this->model;
		$languages_model = $this->model_lang;

		$objMember = ClassRegistry::init('Member.Member');
		$country_codes = $objMember->get_country_codes();

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;
			$valid = true;

			$conditions = [
				'country_code' => $data[$model]['country_code'],
				'phone' => $data[$model]['phone']
			];
			
			// Check exist phone
			$data_user = $this->$model->get_staff_by_conditions($conditions);
	
	
			if (isset($data_user[$model]) && !empty($data_user[$model])) {
				$valid = false;
				$this->Session->setFlash(__('exist_phone_number'), 'flash/error');
				goto return_api;
			}

			$conditions = [
				'username' => $data[$model]['username']
			];

			// Check exist username
			$data_user = $this->$model->get_staff_by_conditions($conditions);

			if (isset($data_user[$model]) && !empty($data_user[$model])) {
				$valid = false;
				$this->Session->setFlash(__('exist_username'), 'flash/error');
				goto return_api;
			}

			$pass = $this->Common->generate_random_pass();
			$data[$model]['password'] = md5($pass);

			if ($valid) {
				$dbo = $this->$model->getDataSource();
				$dbo->begin();
				if ($this->$model->saveAll($data)) {
					$dbo->commit();

					$receiver = array();
					$receiver[0]['phone'] = $data[$model]['country_code'].$data[$model]['phone'];
					$receiver[0]['language'] = $this->lang18;

					$str_title = 'ACX-Cinema';
					$title = array($this->lang18 => $str_title);

					$str_msg = sprintf(__('username_password_msg'), $data[$model]['username'], $pass);
					$sms_message = array($this->lang18 => $str_msg);

					$sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
					// $sent_data['status'] = true;
					if (!$sent_data['status']) {
						$message = __('send_sms_failed');
						$this->Session->setFlash(__('send_sms_failed'), 'flash/error');
					}
					
					$this->Session->setFlash(__('data_is_saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				} else {
					$dbo->rollback();
					$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
				}
			} else {
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
			}
			
		}

		return_api:
		//languages fields
		$language_input_fields = $this->language_input_fields;

		$languages_list = (array)Environment::read('site.available_languages');

		$role = $this->$model->role;

		$this->set(compact('model', 'language_input_fields', 'languages_model', 'languages_list', 'country_codes', 'role'));
	}

	public function admin_edit($id = null) {
		$model = $this->model;
		$languages_model = $this->model_lang;
		$objMember = ClassRegistry::init('Member.Member');
		$country_codes = $objMember->get_country_codes();

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

			$valid = true;

			$conditions = [
				'country_code' => $data[$model]['country_code'],
				'phone' => $data[$model]['phone'],
				'id !=' => $id
			];
			
			// Check exist phone
			$data_user = $this->$model->get_staff_by_conditions($conditions);
	
	
			if (isset($data_user[$model]) && !empty($data_user[$model])) {
				$valid = false;
				$this->Session->setFlash(__('exist_phone_number'), 'flash/error');
				goto return_api;
			}

			$conditions = [
				'username' => $data[$model]['username'],
				'id !=' => $id
			];

			// Check exist username
			$data_user = $this->$model->get_staff_by_conditions($conditions);

			if (isset($data_user[$model]) && !empty($data_user[$model])) {
				$valid = false;
				$this->Session->setFlash(__('exist_username'), 'flash/error');
				goto return_api;
			}

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
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
			}
		} else {
			$this->request->data = $old_item;
		}
	
		return_api:
		//languages fields
		$language_input_fields = $this->language_input_fields;
		$languages_list = (array)Environment::read('site.available_languages');

		$role = $this->$model->role;

		$this->set(compact('model', 'language_input_fields', 'languages_model', 'languages_list', 'country_codes', 'role'));
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
				   $file_name = 'staffs_'.date('Ymd');

				   // export xls
				   if ($this->request->type == "xls") {
						$excel_readable_header = array(
							array('label' => __('id')),
							array('label' => __('name')),
							array('label' => __('code')),
							array('label' => __('enabled')),
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
							'label' => __('code'),
							'label' => __('enabled')
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

			$objresult = $this->Common->upload_and_read_excel($data['Staff'], '');

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

						// Because hasn't multilanguage
						if (isset($data_old[$model]['id']) && !empty($data_old[$model]['id'])) {
							$data_insert[$model]['id'] = $data_old[$model]['id'];
							// $data_id = $data_old[$model]['id'];
							// $data_lang = $this->$model->$languages_model->find('all', array('conditions' => array('ticket_type_id' => $data_old[$model]['id'])));

							// foreach($data_lang as &$lang) {
							// 	switch($lang[$languages_model]['language']) {
							// 		case 'zho' :
							// 				$lang[$languages_model]['name'] = $obj[2];
							// 			break;
							// 		case 'eng' :
							// 				$lang[$languages_model]['name'] = $obj[3];
							// 			break;
							// 	}
							// 	$data_insert[$languages_model][] = $lang[$languages_model];
							// }
						} else {
							$data_insert[$model]['id'] = null;

							// $data_insert[$languages_model][0]['language'] = 'zho';
							// $data_insert[$languages_model][0]['name'] = $obj[2];

							// $data_insert[$languages_model][1]['language'] = 'eng';
							// $data_insert[$languages_model][1]['name'] = $obj[3];
						}

						$data_insert[$model]['name'] = $obj[1];
						$data_insert[$model]['code'] = $obj[1];
						$data_insert[$model]['enabled'] = (in_array($obj[2], array('Y', 'y'))) ? 1: 0;

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

	public function api_login() {
		$this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
			
			$status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
			
			if (!isset($data['username']) || empty($data['username'])) {
				$message = __('missing_parameter') . __('username');
            } else if (!isset($data['password']) || empty($data['password'])) {
				$message = __('missing_parameter') .  __('password');
			} else if (!isset($data['model_code']) || empty($data['model_code'])) {
				$message = __('missing_parameter') . __('model_code');
			} else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->login($data, $this->Api->get_language());

                $status = $result['status'];
				$message = $result['message'];
                
                if($result['status']){
					$this->Session->write('staff', $result['params']);
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

	public function admin_resetpassword($id = null) {
		$model = $this->model;
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->$model->id = $id;
		if (!$this->$model->exists()) {
			throw new NotFoundException(__('invalid_data'));
		}

		// $pass = $this->Common->generate_random_pass();
		$pass = '123456';

		$data = $this->$model->find('first', array(
			'conditions' => array(
				$model.'.' . $this->$model->primaryKey => $id
			),
		));

		$data[$model]['password'] = md5($pass);

		$dbo = $this->$model->getDataSource();
		$dbo->begin();
	
		try {
			if ($this->$model->saveAll($data)) {
				$dbo->commit();
					
				$receiver = array();
				$receiver[0]['phone'] = $data[$model]['country_code'].$data[$model]['phone'];
				$receiver[0]['language'] = $this->lang18;

				$str_title = 'ACX-Cinema';
				$title = array($this->lang18 => $str_title);

				$str_msg = sprintf(__('username_password_msg'), $data[$model]['username'], $pass);
				$sms_message = array($this->lang18 => $str_msg);

				$sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
				// $sent_data['status'] = true;
				if (!$sent_data['status']) {
					$message = __('send_sms_failed');
					$this->Session->setFlash(__('send_sms_failed'), 'flash/error');
				}

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
		$this->redirect(array('action' => 'index'));
	}

	public function api_change_password() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = true;
            $message = "";
            $params = (object)array();
            $data = $this->request->data;

            if (!isset($data['token']) || empty($data['token'])) {
				$message = __('missing_parameter') .  __('token');
			} else if (!isset($data['current_password']) || empty($data['current_password'])) {
                $message = __('missing_parameter') .  __('current_password');
            } else if (!isset($data['staff_id']) || empty($data['staff_id'])) {
                $message = __('missing_parameter') .  __('staff_id');
            } else if (!isset($data['new_password']) || empty($data['new_password'])) {
				$message = __('missing_parameter') .  __('new_password');
            } else {
                $this->Api->set_language($this->lang18);

				$data_staff = $this->$model->get_staff_by_conditions(array('id' => $data['staff_id'], 'token' => $data['token']));

				if (!isset($data_staff['Staff']['id']) || empty($data_staff['Staff']['id'])) {
					$status = false;
					$message = __('staff_not_valid');
					goto return_result;
				}

				if ($data_staff['Staff']['enabled'] != 1) {
					$status = false;
					$message = __('staff_disabled');
					goto return_result;
				}

				if (md5($data['current_password']) != $data_staff['Staff']['password']) {
					$status = false;
					$message = __('password_invalid');
					goto return_result;
				}

				$data_staff['Staff']['password'] = md5($data['new_password']);

				if ($this->$model->saveAll($data_staff)) {
					$message = __('password_had_change');
				} else {
					$status = false;
					$message = __('change_password_failed');

					goto return_result;
				}

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $status = true;

                if($status){
                    $params = $data_staff;
                    if (!$params) {
                        $params = (object)array();
                    }
                }else{
                    $log_data = array();
                    $log_data['message'] = $message;
                    $log_data['data_result'] = $result['params'];
                    $log_data['data'] = $data;

                    $this->Api->set_error_log($log_data);
                }

            }

            return_result:

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();			
	}
}
