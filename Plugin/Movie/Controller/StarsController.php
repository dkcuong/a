<?php
App::uses('MovieAppController', 'Movie.Controller');

class StarsController extends MovieAppController {

	public $components = array('Paginator');
	private $model = 'Star';
    private $model_lang = 'StarLanguage';

	private $filter = array(
		'first_name',
		'surname',
	);
	private $rule = array(
		1 => array('required'),
		2 => array('required'),
	);
	private $rule_spec = [];

	private $upload_path = 'stars';
	private $photo_prefix = 'star';
    private $language_input_fields = array(
        'id',
        'language',
        'star_first_name',
        'star_surname'
    ); //used to display name in view
    private $language_display_fields = array('star_first_name', 'star_surname'); //used to display name in view


	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('movie', 'star'));
	}

	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;
        $languages_model = $this->model_lang;

		$conditions = $this->Common->get_filter_conditions($data_search, $model, $languages_model, null, $this->filter);

		if ($data_search)
		{
			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'movie',
                    'controller' => 'stars',
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
                    'plugin' => 'movie',
                    'controller' => 'stars',
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
            'fields' => array($model.".*", $languages_model.".*"),
			'conditions' => array($conditions),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.created' => 'DESC'),
            'joins' => array(
                array(
                    'alias' => $languages_model,
                    'table' => Environment::read('table_prefix') . 'star_languages',
                    'type' => 'left',
                    'conditions' => array(
                        $model.'.id = '.$languages_model.'.star_id',
                        $languages_model.'.language' => $this->lang18
                    ),
                ),
            ),
		);

        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'data_search', 'languages_model'));
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
				   $file_name = 'stars_'.date('Ymd');

				   // export xls
				   if ($this->request->type == "xls") {
						$excel_readable_header = array(
							array('label' => __('id')),
							array('label' => __d('movie', 'first_name')),
							array('label' => __d('movie', 'surname')),
							array('label' => __d('movie', 'image_url')),
						);
	
						$this->Common->export_excel(
							$cvs_data,
							$file_name,
							$excel_readable_header
						);
					} else {
						$header = array(
							'label' => __('id'),
							'label' => __d('movie', 'first_name'),
							'label' => __d('movie', 'surname'),
							'label' => __d('movie', 'image_url'),
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

			$objresult = $this->Common->upload_and_read_excel($data[$model], '');

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

						$data_insert[$model]['first_name'] = $obj[1];
						$data_insert[$model]['surname'] = $obj[2];
						$data_insert[$model]['image_url'] = $obj[3];

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

	public function admin_view($id) {
		$model = $this->model;
        $languages_model = $this->model_lang;

		$options = array(
			'contain' => array(
				'UpdatedBy',
				'CreatedBy',
                $languages_model
			),
			'fields' => array($model.'.*'),
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
        //languages fields
        $language_input_fields = $this->language_input_fields;
        $languages_list = (array)Environment::read('site.available_languages');

		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$data         = $this->request->data;
			$valid        = true;
			$extra_errors = '';

			$this->upload_photo($data, $valid, $extra_errors);

			if ($valid) 
			{
				$dbo = $this->$model->getDataSource();
				$dbo->begin();

				if ($this->$model->saveAll($data)) 
				{
					$dbo->commit();
					$this->Session->setFlash(__('data_is_saved'), 'flash/success');
					$this->redirect(array('action' => 'index'));
				} 
				else 
				{
					$dbo->rollback();
					$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
				}
			} 
			else 
			{
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');

				if (!empty($extra_errors))
				{
					$this->Session->setFlash($extra_errors, 'flash/error');
				}
			}
		}
        $language_input_fields = $this->language_input_fields;

        $languages_list = (array)Environment::read('site.available_languages');

		$this->set(compact('model', 'languages_model', 'language_input_fields', 'languages_list'));
	}

	private function upload_photo(&$data, &$valid, &$extra_errors, $old_photo = null)
	{
		$uploaded_photo = $data[$this->model]['photo'];

		if (!empty($uploaded_photo) && $uploaded_photo['tmp_name'])
		{
			if (!preg_match('/image\/*/', $uploaded_photo['type']))
			{
				$valid = false;
				$extra_errors .= 'Wrong image type. ';
			}
			else
			{
				$uploaded = $this->Common->upload_images( $uploaded_photo, $this->upload_path, $this->photo_prefix );

				if( isset($uploaded['status']) && ($uploaded['status'] == true) )
				{
					$data[$this->model]['image_url'] = $uploaded['params']['path'];
					$data[$this->model]['image_url'] = str_replace("\\",'/',$data[$this->model]['image_url']);
				}
				else
				{
					$valid = false;
				}
			}
		}
		else
		{
			if (is_null($old_poster))
			{
				unset($data[$this->model]['photo']);
			}
			else
			{
				$data[$this->model]['image_url'] = $old_photo;
			}
		}
	}

	public function admin_edit($id = null) {
		$model = $this->model;
        $languages_model = $this->model_lang;
		$options = array(
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
			'recursive' => 1,
		);
		$old_item = $this->$model->find('first', $options);

		if (!$old_item) {
			throw new NotFoundException(__('invalid_data'));
		}

		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$data         = $this->request->data;
			$valid        = true;
			$extra_errors = '';

			$this->upload_photo($data, $valid, $extra_errors, $old_item[$model]['image_url']);

			if ($valid) 
			{
				$dbo = $this->$model->getDataSource();
				$dbo->begin();
				
				try 
				{
					if ($this->$model->saveAll($data)) 
					{
						$dbo->commit();
						$this->Session->setFlash(__('data_is_saved'), 'flash/success');
						$this->redirect(array('action' => 'index'));
					} 
					else 
					{
						$dbo->rollback();
						$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
					}
				} 
				catch (Exception $ex) 
				{
					$dbo->rollback();
					$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
				}
			} 
			else 
			{
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');

				if (!empty($extra_errors))
				{
					$this->Session->setFlash($extra_errors, 'flash/error');
				}
			}
		} 
		else 
		{
			$this->request->data = $old_item;
		}
        //languages fields
        $language_input_fields = $this->language_input_fields;
        $languages_list = (array)Environment::read('site.available_languages');

		$this->set(compact('model','language_input_fields', 'languages_model', 'languages_list'));
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

}
