<?php
App::uses('CinemaAppController', 'Cinema.Controller');

class HallsController extends CinemaAppController {

	public $components = array('Paginator');
	private $model = 'Hall';

	private $filter = array(
		'code',
	);

	private $rule = array(
		1 => array('required'),
		2 => array('required', 'number'),
		3 => array('required', 'enum'),
	);
	private $rule_spec = array(
		3 => array('N', 'Y', 'y', 'n')
	);

	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('place', 'hall_title'));
	}

	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;

		$conditions = $this->Common->get_filter_conditions($data_search, $model, null, $this->filter);

		if ($data_search){
			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'cinema',
                    'controller' => 'halls',
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
                    'controller' => 'halls',
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
			'order' => array($model . '.created' => 'DESC'),
		);
		
        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'data_search'));
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
				   $file_name = 'cinemas_'.date('Ymd');

				   // export xls
				   if ($this->request->type == "xls") {
						$excel_readable_header = array(
							array('label' => __('id')),
							array('label' => __('code')),
							array('label' => __d('place', 'max_seat')),
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
							'label' => __d('place', 'max_seat'),
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
		$class_hidden = 'hidden';
		$message = array();
		$is_valid_all = true;

		if ($this->request->is('post')) {
			$data = $this->request->data;

			$objresult = $this->Common->upload_and_read_excel($data['Hall'], '');

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
						} else {
							$data_insert[$model]['id'] = null;
						}

						$data_insert[$model]['code'] = $obj[1];
						$data_insert[$model]['max_seat'] = $obj[2];
						$data_insert[$model]['enabled'] = (in_array($obj[3], array('Y', 'y'))) ? 1: 0;

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

	public function admin_add() {
		$model = $this->model;

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

			$data['HallDetail'] = isset($data['HallDetail'])? json_decode($data['HallDetail'], true) : array();

			if($data['HallDetail']) {
				foreach($data['HallDetail'] as &$seat) {
					$seat['is_disability_seat'] = $seat['vegetable'];
					$seat['is_blocked_seat'] = $seat['blocked'];
				}

				$valid = true;
			} else {
				$valid = false;
			}

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

		$this->set(compact('model'));


		$this->load_data();
	}

	public function admin_edit($id = null) {
		$model = $this->model;

		$options = array(
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
			'recursive' => 1
		);
		$old_item = $this->$model->find('first', $options);

		if (!$old_item) {
			throw new NotFoundException(__('invalid_data'));
		}

		$seat_layout = array();
		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

			$data['HallDetail'] = isset($data['HallDetail'])? json_decode($data['HallDetail'], true) : array();

			if($data['HallDetail']) {
				foreach($data['HallDetail'] as &$seat) {
					$seat['is_disability_seat'] = $seat['vegetable'];
					$seat['is_blocked_seat'] = $seat['blocked'];
				}

				$seat_layout = $data['HallDetail'];

				$valid = true;
			} else {
				$valid = false;
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
			$seat_layout = $old_item['HallDetail'];
			
		}

		$layout = json_encode($seat_layout);

		$this->set(compact('model', 'layout'));

		$this->load_data();
	}

	public function admin_view($id = null) {
		$model = $this->model;

		$options = array(
			'fields' => array($model.'.*'),
			'contain' => array(
				'HallDetail',
				'UpdatedBy',
				'CreatedBy'
			),
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
		);
		$model_data = $this->$model->find('first', $options);

		$seat_array = array();
		foreach($model_data['HallDetail'] as $seat_layout) 
		{
			$seat_array[$seat_layout['row_number']][$seat_layout['column_number']] = array(
				'title' => $this->Common->getCellID($seat_layout['row_number']),
				'enabled' => $seat_layout['enabled'],
				'vegetable' => $seat_layout['is_disability_seat'],
				'blocked' => $seat_layout['is_blocked_seat'],
			);
		}
		$model_data['HallDetail'] = $seat_array;

		if (!$model_data) {
			throw new NotFoundException(__('invalid_data'));
		}

		$this->set('dbdata', $model_data);
		
        $this->set(compact('model'));
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
		
		$objCinema = ClassRegistry::init('Cinema.Cinema');
		$cinema = $objCinema->get_active_cinema_list();

		$this->set(compact('cinema'));
	}

    public function api_get_list_hall() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
            $message = "";
            $params = (object)array();
            $data = $this->request->data;

            $this->Api->set_language($this->lang18);

            $url_params = $this->request->params;
            $this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);

            $data['is_api'] = true;
            $result = $this->$model->get_list_hall($data);
            $status = true;

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


            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }
}
