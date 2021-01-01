<?php
App::uses('MemberAppController', 'Member.Controller');
/**
 * MemberNotifications Controller
 *
 * @property MemberNotification $MemberNotification
 * @property PaginatorComponent $Paginator
 */
class MemberNotificationsController extends MemberAppController {

	/**
	 * Components
	 *
	 * @var array
	 */
	public $components = array('Paginator');
	private $model = 'MemberNotification';
	private $member_model = 'Member';
	private $notification_model = 'Notification';
	private $model_lang = 'NotificationLanguage';
    private $language_input_fields = array(
		'id',
		'notification_id',
		'language',
        'title',
        'content'
	);

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


	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('member_notification', 'item_title'));
	}


	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;
		$member_model = $this->member_model;
		$notification_model = $this->notification_model;

		$languages_model = $this->model_lang;

		$conditions = array();
		if(isset($data_search['title']) && $data_search['title']){
			$conditions[$languages_model .'.title LIKE'] = '%' . $data_search['title'] . '%';
		}
		if(isset($data_search['pushMethod']) && $data_search['pushMethod']){
			$conditions['Notification.push_method'] = $data_search['pushMethod'];
		}
		if(isset($data_search['phone']) && $data_search['phone']){
			$conditions['Member.phone LIKE'] = '%' . $data_search['phone'] . '%';
		}
		if(isset($data_search['name']) && $data_search['name']){
			$conditions['Member.name LIKE'] = '%' . $data_search['name'] . '%';
		}

		if ($data_search){
			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'member',
                    'controller' => 'member_notifications',
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
                    'controller' => 'member_notifications',
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
                $languages_model.".title",
                'Member.phone',
				'Member.name',
				'Notification.push_method'
			),
			'joins' => array(
                array(
					'alias' => 'Notification',
					'table' => Environment::read('table_prefix') . 'notifications',
					'type' => 'left',
					'conditions' => array(
						'Notification.id = '.$model.'.notification_id',
					),
				),
				array(
					'alias' => $languages_model,
					'table' => Environment::read('table_prefix') . 'notification_languages',
					'type' => 'left',
					'conditions' => array(
						'Notification.id = '.$languages_model.'.notification_id',
						$languages_model.'.language' => $this->lang18
					),
                ),
                array(
					'alias' => 'Member',
					'table' => Environment::read('table_prefix') . 'members',
					'type' => 'left',
					'conditions' => array(
						'Member.id = '.$model.'.member_id',
					),
				)
			),
			'conditions' => array($conditions),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.created' => 'DESC'),
		);
		
		$this->MemberNotification->bindModel(array(
			'belongsTo' => array(
					'NotificationLanguage' => array(
						'foreignKey' => false,
						'conditions' => array('Notification.id = NotificationLanguage.notification_id')
				),
			)
		));

		$this->set('dbdatas', $this->paginate());

		$this->set(compact('model', 'languages_model', 'member_model', 'notification_model' , 'data_search'));		
		
		$this->load_data();
	}

	public function admin_view($id) {
		$model = $this->model;
		$member_model = "Member";
		$notification_model = "Notification";

		$languages_model = $this->model_lang;

		$options = array(
			'fields' => array(
				$model.'.*',
				$languages_model.".title",
				'Member.phone',
				'Member.name',
				'Notification.push_method'
			),
			'contain' => array(
				$languages_model,
				'UpdatedBy',
				'CreatedBy',
			),
			'joins' => array(
                array(
					'alias' => 'Notification',
					'table' => Environment::read('table_prefix') . 'notifications',
					'type' => 'left',
					'conditions' => array(
						'Notification.id = '.$model.'.notification_id',
					),
				),
				array(
					'alias' => $languages_model,
					'table' => Environment::read('table_prefix') . 'notification_languages',
					'type' => 'left',
					'conditions' => array(
						'Notification.id = '.$languages_model.'.notification_id',
						$languages_model.'.language' => $this->lang18
					),
				),
                array(
					'alias' => 'Member',
					'table' => Environment::read('table_prefix') . 'members',
					'type' => 'left',
					'conditions' => array(
						'Member.id = '.$model.'.member_id',
					),
				)
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

        $this->set(compact('model', 'language_input_fields','languages', 'languages_model', 'member_model', 'notification_model'));
	}

	public function admin_add() {
		$model = $this->model;
		$languages_model = $this->model_lang;

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

			$valid = true;

			$slug_language = $this->lang18;
			$eng_item = array_filter($data['NotificationLanguage'], function($item) use ($slug_language){
				//return $item['language'] == $slug_language;
				return $item['language'] == str_replace('<b>', '', str_replace('</b>', '', $slug_language));
			});

			if ($valid) {

				$notif_insert = array();
				$notif_insert['Notification'] = $data['Notification'];
				$notif_insert[$languages_model] = $data[$languages_model];

				$dbo = $this->$model->getDataSource();
				$dbo->begin();

				$notification_id = 0;

				$objNotification = ClassRegistry::init('Member.Notification');
				if ($objNotification->saveAll($notif_insert)) {
					$notification_id = $objNotification->id;
				} else {
					$dbo->rollback();
					$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
					$valid = false;
				}
			}

			if ($valid) {
				$data_insert = array();

				if ($data['Notification']['push_method'] == 'send-to-spesific-person') {
					foreach($data['MemberNotification']['member_id'] as $item) {
						$tmp_data = array();
						$tmp_data['member_id'] = $item;
						$tmp_data['notification_id'] = $notification_id;
						$tmp_data['pushed'] = date('Y-m-d');

						$data_insert[] = $tmp_data;
					}
				} else {
					$option_member = array(
						'fields' => array('Member.id'),
						'conditions' => array(
							// 'Member.is_register' => 1,
							// 'Member.enabled' => 1,
							// 'Member.deleted' => null
						),
						'recursive' => -1
					);

					$objMember = ClassRegistry::init('Member.Member');
					
					$data_member = $objMember->find('all', $option_member);
					foreach($data_member as $member) {
						$tmp_data = array();
						$tmp_data['member_id'] = $member['Member']['id'];
						$tmp_data['notification_id'] = $notification_id;
						$tmp_data['pushed'] = date('Y-m-d H:i:s');

						$data_insert[] = $tmp_data;
					}
				}

				if ($this->$model->saveAll($data_insert)) {
					$dbo->commit();
					
					$member_ids = array();
					if (isset($data['Notification']['push_method']) && 
						$data['Notification']['push_method'] == 'send-to-spesific-person') {
						$member_ids = Hash::filter($data['MemberNotification']['member_id']);
					} else {
						$member_ids = Hash::extract( $data_insert, "{n}.member_id" );
					}
					

					$objDevice = ClassRegistry::init('Member.MemberDevice');
					$devices = $objDevice->get_devices($member_ids);

					// message for push
					$content = array(
						"notification" => array(
							'title' =>  reset($eng_item)['title'],
							'body' =>   reset($eng_item)['content']
						)
					);
	
					$android_message = $content;
			
					$ios_message = $content;

					$push_params = array(
						'custom_data' => array(
							'type' => 'notification'
						)
					);

					$message_error = __('data_is_saved') . '. ' . __('push_notification_failed');
					if (!isset($devices['ios_data']) || ! isset($devices['android_data'])) 
					{
						$this->Session->setFlash($message_error, 'flash/error');
					} else {
						$push_result = $this->Notification->push($devices['ios_data'], $ios_message, $devices['android_data'], $android_message);
		
						if (!isset($push_result['ios_status']) || !isset($push_result['android_status']) ||
							(!$push_result['ios_status']) || (!$push_result['android_status'])) {
							$this->Session->setFlash($message_error, 'flash/error');
						} else {
							$this->Session->setFlash(__('data_is_saved'), 'flash/success');
						}
					}
					$this->redirect(array('action' => 'index'));
				} else {
					$dbo->rollback();
					$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
					$valid = false;
				}
			}
		}

		//Company languages fields
		$language_input_fields = $this->language_input_fields;

		$languages_list = (array)Environment::read('site.available_languages');
		$this->set(compact('model', 'language_input_fields', 'languages_model', 'languages_list'));

		$this->load_data();
	}

	public function admin_edit($id = null) {
		$model = $this->model;
		$languages_model = $this->model_lang;

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

		//languages fields
		$language_input_fields = $this->language_input_fields;
		$languages_list = (array)Environment::read('site.available_languages');

		$this->set(compact('model', 'language_input_fields', 'languages_model', 'languages_list'));
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
				   $file_name = 'members_'.date('Ymd');

				   // export xls
				   if ($this->request->type == "xls") {
						$excel_readable_header = array(
							array('label' => __('id')),
							array('label' => __('title')),
							array('label' => __('push_method')),
							array('label' => __('name')),							
							array('label' => __('phone'))
						);
	
						$this->Common->export_excel(
							$cvs_data,
							$file_name,
							$excel_readable_header
						);
					} else {
						$header = array(
							'label' => __('id'),
							'label' => __('title'),
							'label' => __('push_method'),
							'label' => __('name'),
							'label' => __('phone')
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

	private function load_data(){
		$model = $this->model;
		$pushMethods = $this->$model->Notification->push_method;

		$this->set(compact('pushMethods'));
    }
}
