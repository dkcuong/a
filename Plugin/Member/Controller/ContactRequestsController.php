<?php
App::uses('MemberAppController', 'Member.Controller');
/**
 * ContactRequests Controller
 *
 * @property ContactRequest $ContactRequest
 * @property PaginatorComponent $Paginator
 */
class ContactRequestsController extends MemberAppController {
    public $components = array('Paginator');
    private $model = 'ContactRequest';
/**
 * Components
 *
 * @var array
 */
    public function beforeFilter(){
        parent::beforeFilter();
        $this->set('title_for_layout', __d('member', 'contact_request_item'));
    }
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
        $data_search = $this->request->query;
        $model = $this->model;

        $condition_search = $data_search;
        $conditions = array();
        if (isset($condition_search['name']) && !empty($condition_search['name']))
        {
            $conditions[] = 'ContactRequest.name LIKE "%' . $condition_search['name'] . '%"';
        }

        if (isset($condition_search['email']) && !empty($condition_search['email']))
        {
            $conditions[] = 'ContactRequest.email LIKE "%' . $condition_search['email'] . '%"';
        }

        if (isset($condition_search['phone']) && !empty($condition_search['phone']))
        {
            $conditions[] = 'ContactRequest.phone LIKE "%' . $condition_search['phone'] . '%"';
        }

        if ($data_search){
            // button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'member',
                    'controller' => 'contact_requests',
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
                    'controller' => 'contact_requests',
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
            'contain' => array(
            ),
            'limit' => Environment::read('web.limit_record'),
            'order' => array($model . '.name' => 'ASC'),
        );


        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'data_search' ));
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
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

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->ContactRequest->create();
			if ($this->ContactRequest->save($this->request->data)) {
				$this->Flash->success(__('The contact request has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The contact request could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->ContactRequest->exists($id)) {
			throw new NotFoundException(__('Invalid contact request'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->ContactRequest->save($this->request->data)) {
				$this->Flash->success(__('The contact request has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The contact request could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('ContactRequest.' . $this->ContactRequest->primaryKey => $id));
			$this->request->data = $this->ContactRequest->find('first', $options);
		}
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->ContactRequest->exists($id)) {
			throw new NotFoundException(__('Invalid contact request'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->ContactRequest->delete($id)) {
			$this->Flash->success(__('The contact request has been deleted.'));
		} else {
			$this->Flash->error(__('The contact request could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
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
            $conditions = $this->request->conditions;

            $result = $this->$model->get_data_export($conditions, 1, 2000, $this->lang18);

            if ($result) {

                $cvs_data = array();

                foreach ($result as $row) {
                    $temp = $this->$model->format_data_export(array(), $row);

                    array_push($cvs_data, $temp);
                }

                try{
                    $file_name = 'contact_requests_'.date('Ymd');

                    // export xls
                    if ($this->request->type == "xls") {
                        $excel_readable_header = array(
                            array('label' => __('id')),
                            array('label' => __('title')),
                            array('label' => __('name')),
                            array('label' => __('email')),
                            array('label' => __('country_code')),
                            array('label' => __('phone')),
                            array('label' => __( 'message')),
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
                            'label' => __('name'),
                            'label' => __('email'),
                            'label' => __('country_code'),
                            'label' => __('phone'),
                            'label' => __( 'message'),

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

    public function api_create_contact_request() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
            $message = "";
            $params = (object)array();
            $data = $this->request->data;

//            if (!isset($data['seats']) || empty($data['seats'])) {
//                $message = __('missing_parameter') .  __('seats');
//            } else if (!isset($data['schedule_detail_id']) || empty($data['schedule_detail_id'])) {
//                $message = __('missing_parameter') .  __('schedule_detail_id');
//            } else
            if (!isset($data['language']) || empty($data['language'])) {
                $message = __('missing_parameter') .  __('language');
            } else {
                $this->Api->set_language($data['language']);


                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);


                $result = $this->$model->create_contact_request($data);

                $status = $result['status'];
                $message = $result['message'];

                if($status){
                    $this->Api->set_language('eng');
                    $params = $result['params'];

                    $email = Environment::read('email.client_receiver') ;

                    if (empty($email)) {
                        goto return_result;
                    }

                    $template = 'send_contact_request';
                    $subject = 'ACX-Cinema - Contact Request';
                    $result_email = $this->send_email($email, $template, $subject, $data);

                    if (! $result_email['status']) {
                        $status = false;
                        $message .= ' - '.$result_email['message'];
                    }

                    if (!$params) {
                        $params = (object)array();
                    }
                }else{
                    if (isset($result['params']['error']) && !empty($result['params']['error'])) {
                        $params = $result['params'];
                    }


                    if(isset($result['log_data']) && $result['log_data']){
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
            }

            return_result :
            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }
}
