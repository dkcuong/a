<?php
App::uses('PosAppController', 'Pos.Controller');

class PaymentMethodsController extends PosAppController {

	public $components = array('Paginator');
	private $model = 'PaymentMethod';

	private $upload_path = 'payment_methods';
	private $image_prefix = 'image';
	
	private $filter = array(
		'code',
	);

	private $api_rules = array(
		// 'token' => array(
		// 	'required' => true,
		// 	'type' => 'string',
		// ),
		'language' => array(
			'required' => true,
			'type' => 'string',
			'in' => ['zho', 'eng', 'chi'],
		),
	);

	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('payment', 'method'));
	}

	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;

		$conditions = $this->Common->get_filter_conditions($data_search, $model, null, $this->filter);

		$this->Paginator->settings = array(
			'fields' => array($model.".*"),
			'conditions' => array($conditions),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.created' => 'DESC'),
		);
		
		$type = $this->$model->type;

        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'data_search', 'type'));
	}

	public function admin_add() {
		$model = $this->model;

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

			$valid = true;
			$extra_errors = '';

			$this->upload_image($data, $valid, $extra_errors);

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

				if (!empty($extra_errors))
				{
					$this->Session->setFlash($extra_errors, 'flash/error');
				}
			}
		}
		$type = $this->$model->type;
		$this->set(compact('model', 'type'));
	}

	private function upload_image(&$data, &$valid, &$extra_errors, $old_image = null)
	{
		$uploaded_image = $data[$this->model]['image_upload'];

		if (!empty($uploaded_image) && $uploaded_image['tmp_name'])
		{
			if (!preg_match('/image\/*/', $uploaded_image['type']))
			{
				$valid = false;
				$extra_errors .= 'Wrong image type. ';
			}
			else
			{
				$uploaded = $this->Common->upload_images( $uploaded_image, $this->upload_path, $this->image_prefix );

				if( isset($uploaded['status']) && ($uploaded['status'] == true) )
				{
					$data[$this->model]['image'] = $uploaded['params']['path'];
					$data[$this->model]['image'] = str_replace("\\",'/',$data[$this->model]['image']);
				}
				else
				{
					$valid = false;
				}
			}
		}
		else
		{
			if (is_null($old_image))
			{
				unset($data[$this->model]['image']);
			}
			else
			{
				$data[$this->model]['image'] = $old_image;
			}
		}
	}

	public function admin_view($id) {
		$model = $this->model;

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

		$type = $this->$model->type;

		$this->set('dbdata', $model_data);

        $this->set(compact('model', 'type'));
	}

	public function admin_edit($id) {
		$model = $this->model;

		$options = array(
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
			'recursive' => 1
		);
		$old_item = $this->$model->find('first', $options);

		if (!$old_item) 
		{
			throw new NotFoundException(__('invalid_data'));
		}

		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$data = $this->request->data;

			$valid        = true;
			$extra_errors = '';

			$this->upload_image($data, $valid, $extra_errors, $old_item[$model]['image']);


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
		$type = $this->$model->type;
		$this->set(compact('model', 'type'));
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

	public function api_get_list()
	{
		$this->Api->init_result();

        if ($this->request->is('post')) {
            $this->disableCache();

			$data = $this->request->data;

            $url_params = $this->request->params;
			$this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);

            $status = false;
            $result_data = array();
            $message = "";

			$valid = $this->Api->validate_data($data, $this->api_rules);
			if ( !$valid ) {
				goto return_api;
			}

            $result_data = $this->PaymentMethod->get_list_api($data['language']);

            $status = true;
            $message =  __('retrieve_data_successfully');

			$this->Api->set_result($status, $message, $result_data);

        }

        return_api :
		$this->Api->output();
	}
}
