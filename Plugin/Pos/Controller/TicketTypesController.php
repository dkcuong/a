<?php
App::uses('PosAppController', 'Pos.Controller');

class TicketTypesController extends PosAppController {

	public $components = array('Paginator');
	private $model = 'TicketType';
	private $model_lang = 'TicketTypeLanguage';
	private $language_input_fields = array(
		'id',
		'ticket_type_id',
		'language',
		'name'
	);
	private $language_display_fields = array('name'); //used to display name in view
	private $filter = array(
		'code',
		'name',
	);
	private $rule = array(
		1 => array('required'),
		2 => array('required'),
		3 => array('required'),
		4 => array('required','enum'),
	);
	private $rule_spec = array(
		4 => array('N', 'Y', 'y', 'n')
	);

	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('ticket_type', 'item_title'));
	}

	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;
		$languages_model = $this->model_lang;

		$conditions = $this->Common->get_filter_conditions($data_search, $model, $languages_model, $this->filter);

		if ($data_search){
			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'pos',
                    'controller' => 'ticket_types',
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
                    'plugin' => 'pos',
                    'controller' => 'ticket_types',
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
			'fields' => array($model.".*", $languages_model.".name"),
			'joins' => array(
				array(
					'alias' => $languages_model,
					'table' => Environment::read('table_prefix') . 'ticket_type_languages',
					'type' => 'left',
					'conditions' => array(
						$model.'.id = '.$languages_model.'.ticket_type_id',
						$languages_model.'.language' => $this->lang18
					),
				),
			),
			'conditions' => array($conditions),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.created' => 'DESC'),
		);
		
        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'languages_model', 'data_search'));		
	}

	public function admin_view($id) {
		$model = $this->model;
		$languages_model = $this->model_lang;

		$options = array(
			'fields' => array($model.'.*'),
			'contain' => array(
				$languages_model,
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

        $this->set(compact('model', 'language_input_fields','languages', 'languages_model'));
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
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
			}
			
		}

		//languages fields
		$language_input_fields = $this->language_input_fields;

		$languages_list = (array)Environment::read('site.available_languages');

		$this->set(compact('model', 'language_input_fields', 'languages_model', 'languages_list'));
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
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
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
				   $file_name = 'ticket_types_'.date('Ymd');

				   // export xls
				   if ($this->request->type == "xls") {
						$excel_readable_header = array(
							array('label' => __('id')),
							array('label' => __('code')),
							array('label' => __('name_zho')),
							array('label' => __('name_eng')),
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
							'label' => __('code'),
							'label' => __('name_zho'),
							'label' => __('name_eng'),
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
							$data_id = $data_old[$model]['id'];
							$data_lang = $this->$model->$languages_model->find('all', array('conditions' => array('ticket_type_id' => $data_old[$model]['id'])));

							foreach($data_lang as &$lang) {
								switch($lang[$languages_model]['language']) {
									case 'zho' :
											$lang[$languages_model]['name'] = $obj[2];
										break;
									case 'eng' :
											$lang[$languages_model]['name'] = $obj[3];
										break;
								}
								$data_insert[$languages_model][] = $lang[$languages_model];
							}
						} else {
							$data_insert[$model]['id'] = null;

							$data_insert[$languages_model][0]['language'] = 'zho';
							$data_insert[$languages_model][0]['name'] = $obj[2];

							$data_insert[$languages_model][1]['language'] = 'eng';
							$data_insert[$languages_model][1]['name'] = $obj[3];
						}

						$data_insert[$model]['code'] = $obj[1];
						$data_insert[$model]['enabled'] = (in_array($obj[4], array('Y', 'y'))) ? 1: 0;

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
}
