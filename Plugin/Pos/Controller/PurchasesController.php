<?php
App::uses('PosAppController', 'Pos.Controller');

class PurchasesController extends PosAppController {

	public $components = array('Paginator');
    private $model = 'Purchase';

    public function admin_index() {
        $data_search = $this->request->query;
        $model = $this->model;
        $now = date( 'Y-m-d' );
        $time = date ('H:i');
        //$conditions = $this->Common->get_filter_conditions($data_search, $model, $languages_model, $this->filter);
        $conditions = array();

        if (isset($data_search['date_from']) && !empty($data_search['date_from'])) {
            $conditions['Purchase.date >='] = $data_search['date_from'];
        }
        if (isset($data_search['date_to']) && !empty($data_search['date_to'])) {
            $conditions['Purchase.date <='] = $data_search['date_to'];
        }
        if (isset($data_search['inv_number']) && !empty($data_search['inv_number'])) {
            $conditions['Purchase.inv_number LIKE '] = '%'.trim($data_search['inv_number']).'%';
        }
        if (isset($data_search['status']) && !empty($data_search['status'])) {
            $conditions['Purchase.status'] = $data_search['status'];
        }

		if ($data_search){
			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'pos',
                    'controller' => 'purchases',
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
                    'controller' => 'purchases',
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
                'Member.*',
                'Staff.*',
//                'MovieLanguage.*',
//                'MovieType.*',
//                'ScheduleDetail.*',
//                'Schedule.*',
//                'OrderPaymentLog.*',
                "group_concat(DISTINCT PaymentMethod.name) as payment_method_group",
                'count(Purchase.id) as amount_of_item'
            ),
            'joins' => array(
                array(
                    'alias' => 'Member',
                    'table' => Environment::read('table_prefix') . 'members',
                    'type' => 'left',
                    'conditions' => array(
                        'Purchase.member_id = Member.id',
                    ),
                ),
                array(
                    'alias' => 'Staff',
                    'table' => Environment::read('table_prefix') . 'staffs',
                    'type' => 'left',
                    'conditions' => array(
                        'Staff.id = Purchase.staff_id'
                    ),
                ),
                array(
                    'alias' => 'PurchaseDetail',
                    'table' => Environment::read('table_prefix') . 'purchase_details',
                    'type' => 'left',
                    'conditions' => array(
                        'PurchaseDetail.purchase_id = Purchase.id',
                    ),
                ),
                array(
                    'alias' => 'PurchaseDetailPayment',
                    'table' => Environment::read('table_prefix') . 'purchase_detail_payments',
                    'type' => 'left',
                    'conditions' => array(
                        'PurchaseDetailPayment.purchase_id = Purchase.id',
                    ),
                ),
                array(
                    'alias' => 'PaymentMethod',
                    'table' => Environment::read('table_prefix') . 'payment_methods',
                    'type' => 'left',
                    'conditions' => array(
                        'PaymentMethod.id = PurchaseDetailPayment.payment_method_id'
                    ),
                )
            ),
            'contain' => array(

            ),
            'conditions' => array($conditions),
            'limit' => Environment::read('web.limit_record'),
            'order' => array($model . '.created' => 'DESC'),
            'group' => array(
                'Purchase.id'
            )
        );

        $list = $this->paginate();

        $status = $this->$model->status;

        $this->set('dbdatas', $list);
        $this->set(compact('model', 'status', 'data_search'));
    }

    public function admin_view($id) {
        $model = $this->model;
        $now = date( 'Y-m-d' );
        $time = date ('H:i');

        /*$options = array(
            'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
        );*/

        $options = array(
            'fields' => array(
                $model.".*",
                'Member.*',
                'Staff.*',
//                'MovieLanguage.*',
//                'MovieType.*',
//                'ScheduleDetail.*',
//                'Schedule.*',
//                'OrderPaymentLog.*',
                "group_concat(DISTINCT PaymentMethod.name) as payment_method_group",
                'count(Purchase.id) as amount_of_item'
            ),
            'joins' => array(
                array(
                    'alias' => 'Member',
                    'table' => Environment::read('table_prefix') . 'members',
                    'type' => 'left',
                    'conditions' => array(
                        'Purchase.member_id = Member.id',
                    ),
                ),
                array(
                    'alias' => 'Staff',
                    'table' => Environment::read('table_prefix') . 'staffs',
                    'type' => 'left',
                    'conditions' => array(
                        'Staff.id = Purchase.staff_id'
                    ),
                ),
                array(
                    'alias' => 'PurchaseDetail',
                    'table' => Environment::read('table_prefix') . 'purchase_details',
                    'type' => 'left',
                    'conditions' => array(
                        'PurchaseDetail.purchase_id = Purchase.id',
                    ),
                ),
                array(
                    'alias' => 'PurchaseDetailPayment',
                    'table' => Environment::read('table_prefix') . 'purchase_detail_payments',
                    'type' => 'left',
                    'conditions' => array(
                        'PurchaseDetailPayment.purchase_id = Purchase.id',
                    ),
                ),
                array(
                    'alias' => 'PaymentMethod',
                    'table' => Environment::read('table_prefix') . 'payment_methods',
                    'type' => 'left',
                    'conditions' => array(
                        'PaymentMethod.id = PurchaseDetailPayment.payment_method_id'
                    ),
                )
            ),
            'contain' => array(
                'PurchaseDetail' => array(
                    'Item' => array(
                        'ItemLanguage' => array(
                            'conditions' => array(
                                'ItemLanguage.language' => $this->lang18
                            )
                        )
                    )
                ),
                'UpdatedBy',
                'CreatedBy'
            ),
            'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
            'limit' => Environment::read('web.limit_record'),
            'order' => array($model . '.created' => 'DESC'),
            'group' => array(
                'Purchase.id'
            )
        );

        $model_data = $this->$model->find('first', $options);

        if (!$model_data) {
            throw new NotFoundException(__('invalid_data'));
        }

        $is_generate_new_code = false;

        /*$qrcode_path = Environment::read('web.url_img').$model_data[$model]['qrcode_path'];
        $type = 'order';
        $qrcode_value = $this->Common->get_qrcode_value($model_data[$model],$type);
        if (!isset($model_data[$model]['qrcode_path']) || empty($model_data[$model]['qrcode_path'])) {
            $qrcode = $this->Common->generate_qrcode($type, $qrcode_value, $model_data[$model]['inv_number']);

            if (isset($qrcode['status']) && $qrcode['status']) {
                $model_data[$model]['qrcode_path'] = $qrcode['path'];

                if ($this->$model->saveAll($model_data)) {
                    $is_generate_new_code = true;
                    $qrcode_path = Environment::read('web.url_img').$qrcode['path'];
                } else {
                    throw new NotFoundException(__('save_qrcode_failed'));
                }
            } else {
                throw new NotFoundException(__('qrcode_generate_failed'));
            }
        }*/

        $data_order = json_encode($model_data[$model]);
        $this->set('dbdata', $model_data);
        $this->set(compact('data_order'));

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
            $data_binding['status'] = $this->$model->status;

            if ($result) {

                $cvs_data = array();

                foreach ($result as $row) {
                    $temp = $this->$model->format_data_export(array(), $row, $data_binding);

                    array_push($cvs_data, $temp);
                }

                try{
                    $file_name = 'purchases_'.date('Ymd');

                    // export xls
                    if ($this->request->type == "xls") {
                        $excel_readable_header = array(
                            array('label' => __('id')),
                            array('label' => __('date')),
                            array('label' => __('inv_number')),
                            array('label' => __d('member','item_title')),
                            array('label' => __d('purchase','amount_of_item')),
                            array('label' => __d('payment', 'method')),
                            array('label' => __('status'))
                        );

                        $this->Common->export_excel(
                            $cvs_data,
                            $file_name,
                            $excel_readable_header
                        );
                    } else {
                        /*$header = array(
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
                        );*/
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

    public function api_create_purchase_trans() {
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
			} else if (!isset($data['items_bought']) || empty($data['items_bought'])) {
                $message = __('missing_parameter') .  __('items_bought');
            } else if (!isset($data['staff_id']) || empty($data['staff_id'])) {
                $message = __('missing_parameter') .  __('staff_id');
            } else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->create_purchase_trans($data);

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
    
    public function api_do_payment() {
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
			} else if (!isset($data['purchase_id']) || empty($data['purchase_id'])) {
                $message = __('missing_parameter') .  __('purchase_id');
            } else if (!isset($data['payment_method']) || empty($data['payment_method'])) {
                $message = __('missing_parameter') .  __('payment_method');
            } else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->do_payment($data, $this->Api->get_language());

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

    public function api_do_get_data_print() {
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
			} else if (!isset($data['trans_id']) || empty($data['trans_id'])) {
                $message = __('missing_parameter') .  __('trans_id');
            } else {
				$this->Api->set_language($this->lang18);

                $objStaff = ClassRegistry::init('Cinema.Staff');
                $data_staff = $objStaff->get_staff_by_conditions(array('id' => $data['staff_id'], 'token' => $data['token']));
    
                if (!isset($data_staff['Staff']['id']) || empty($data_staff['Staff']['id'])) {
                    $status = false;
                    $message = __('staff_not_valid');
                    goto return_result;
                }

                $option = array(
                    'conditions' => array(
                        'Purchase.id' => $data['trans_id'],
                        'Purchase.status' => 3,
                        'Purchase.void' => 0,
                    )
                );

                $data_purchase = $this->$model->find('first', $option);
                if (!isset($data_purchase[$model]['id']) || empty($data_purchase[$model]['id'])) {
                    $status = false;
                    $message = __('trans_not_found');
                    goto return_result;
                }

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $data_purchase_print = $this->$model->get_data_print_purchase($data['trans_id'], $this->Api->get_language(), true);

                $data_purchase[$model]['print_count'] = $data_purchase[$model]['print_count'] + 1;

                $dbo = $this->$model->getDataSource();
                $dbo->begin();
                try {
                    if ($this->$model->saveAll($data_purchase)) {
                        $dbo->commit();
                        $message = __('data_is_saved');
                    } else {
                        $dbo->rollback();
                        $message = __('print_count_unable_to_increase');
                    }
        
                } catch (Exception $e) {
                    $dbo->rollback();
                    $message = __('print_count_unable_to_increase');
                }


                $status = true;

                if($status){
					$params = $data_purchase_print;
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

    public function api_do_void_trans() {
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
			} else if (!isset($data['trans_id']) || empty($data['trans_id'])) {
                $message = __('missing_parameter') .  __('trans_id');
            } else {
				$this->Api->set_language($this->lang18);

                $objStaff = ClassRegistry::init('Cinema.Staff');
                $data_staff = $objStaff->get_staff_by_conditions(array('id' => $data['staff_id'], 'token' => $data['token']));
    
                if (!isset($data_staff['Staff']['id']) || empty($data_staff['Staff']['id'])) {
                    $status = false;
                    $message = __('staff_not_valid');
                    goto return_result;
                }

                $option = array(
                    'contain' => array(
                        'PurchaseDetail',
                    ),
                    'conditions' => array(
                        'Purchase.id' => $data['trans_id'],
                    )
                );

                $data_purchase = $this->$model->find('first', $option);
                if (!isset($data_purchase[$model]['id']) || empty($data_purchase[$model]['id'])) {
                    $status = false;
                    $message = __('trans_not_found');
                    goto return_result;
                }

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $data['purchase_id'] = $data['trans_id'];
                $result = $this->$model->void_purchase($data, $data_purchase);

                $status = $result['status'];
				$message = $result['message'];

                if($result['status']){
					$params = array();
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