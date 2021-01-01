<?php
App::uses('PosAppController', 'Pos.Controller');

class OrdersController extends PosAppController {

	public $components = array('Paginator');
    private $model = 'Order';

    public function admin_index() {
        $data_search = $this->request->query;
		$model = $this->model;
        $now = date( 'Y-m-d' );
        $time = date ('H:i');
        //$conditions = $this->Common->get_filter_conditions($data_search, $model, $languages_model, $this->filter);
        $conditions = array();


        if (isset($data_search['date_from']) && !empty($data_search['date_from'])) {
            $conditions['Order.date >='] = $data_search['date_from'];
        }
        if (isset($data_search['date_to']) && !empty($data_search['date_to'])) {
            $conditions['Order.date <='] = $data_search['date_to'];
        }
        if (isset($data_search['inv_number']) && !empty($data_search['inv_number'])) {
            $conditions['Order.inv_number LIKE '] = '%'.trim($data_search['inv_number']).'%';
        }
        if (isset($data_search['status']) && !empty($data_search['status'])) {
            $conditions['Order.status'] = $data_search['status'];
        }

		if ($data_search){
			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'pos',
                    'controller' => 'orders',
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
                    'controller' => 'orders',
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
                'MovieLanguage.*',
                'MovieType.*',
                'ScheduleDetail.*',
                'Schedule.*',
                'OrderPaymentLog.*',
                'count(OrderDetail.id) as amount_of_ticket',
                "group_concat(DISTINCT PaymentMethod.name) as payment_method_group"
            ),
			'joins' => array(
                 array(
                    'alias' => 'Member',
                    'table' => Environment::read('table_prefix') . 'members',
                    'type' => 'left',
                    'conditions' => array(
                        'Order.member_id = Member.id',
                    ),
                ),
                array(
                    'alias' => 'OrderPaymentLog',
                    'table' => Environment::read('table_prefix') . 'order_payment_logs',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderPaymentLog.id = Order.payment_log_id',
                    ),
                ),
				 array(
				 	'alias' => 'OrderDetail',
				 	'table' => Environment::read('table_prefix') . 'order_details',
				 	'type' => 'left',
				 	'conditions' => array(
				 	    'OrderDetail.order_id = '.$model.'.id',
				 	),
				 ),
                array(
                    'alias' => 'ScheduleDetailLayout',
                    'table' => Environment::read('table_prefix') . 'schedule_detail_layouts',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetailLayout.id = OrderDetail.schedule_detail_layout_id',
                    ),
                ),
                array(
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.id = ScheduleDetailLayout.schedule_detail_id',
                    ),
                ),
                array(
                    'alias' => 'Schedule',
                    'table' => Environment::read('table_prefix') . 'schedules',
                    'type' => 'left',
                    'conditions' => array(
                        'Schedule.id = ScheduleDetail.schedule_id',
                    ),
                ),
                array(
                    'alias' => 'MovieLanguage',
                    'table' => Environment::read('table_prefix') . 'movie_languages',
                    'type' => 'left',
                    'conditions' => array(
                        'MovieLanguage.movie_id = Schedule.movie_id',
                        'MovieLanguage.language' => $this->lang18
                    ),
                ),
                array(
                    'alias' => 'MovieType',
                    'table' => Environment::read('table_prefix') . 'movie_types',
                    'type' => 'left',
                    'conditions' => array(
                        'MovieType.id = Schedule.movie_type_id',
                    ),
                ),
                array(
                    'alias' => 'OrderDetailPayment',
                    'table' => Environment::read('table_prefix') . 'order_detail_payments',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetailPayment.order_id = Order.id'
                    ),
                ),
                array(
                    'alias' => 'PaymentMethod',
                    'table' => Environment::read('table_prefix') . 'payment_methods',
                    'type' => 'left',
                    'conditions' => array(
                        'PaymentMethod.id = OrderDetailPayment.payment_method_id'
                    ),
                )
            ),
			'contain' => array(
//			    'OrderDetail' => array(
//			        'ScheduleDetailTicketType' => array(
//			            'ScheduleDetail' => array(
//			                'Schedule' => array(
//                            )
//                        )
//                    )
//                )
            ),
			'conditions' => array($conditions),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.created' => 'DESC'),
            'group' => array(
                'Order.id'
            )
		);

        $list = $this->paginate();

        foreach ($list as $k=>$v) {
            $list[$k]['Order']['qrcode_status'] = '';
            if ($v['Order']['status'] == 3) {
                $list[$k]['Order']['qrcode_status'] = 'paymented';
            }
            if ($v['Order']['status'] == 3 && $v['Order']['print_count'] > 0) {
                $list[$k]['Order']['qrcode_status'] = 'redeemed';
            }
            if ( ($v['ScheduleDetail']['date'] < $now)
            || ( $v['ScheduleDetail']['date'] == $now && $v['ScheduleDetail']['time'] < $time )
            ) {
                $list[$k]['Order']['qrcode_status'] = 'expired';
            }
        }

		$status = $this->$model->status;

        $this->set('dbdatas', $list);
        $this->set(compact('model', 'status', 'data_search'));
    }

    public function admin_view($id) {
        $model = $this->model;
        $now = date( 'Y-m-d' );
        $time = date ('H:i');

        $options = array(
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
		);
		$model_data = $this->$model->find('first', $options);

		if (!$model_data) {
			throw new NotFoundException(__('invalid_data'));
        }

        $is_generate_new_code = false;

        $qrcode_path = Environment::read('web.url_img').$model_data[$model]['qrcode_path'];
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
        }

        $data_order = json_encode($model_data[$model]);

        $this->set(compact('qrcode_path', 'qrcode_value', 'is_generate_new_code', 'data_order'));
        
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
                    $file_name = 'orders_'.date('Ymd');

                    // export xls
                    if ($this->request->type == "xls") {
                        $excel_readable_header = array(
                            array('label' => __('id')),
                            array('label' => __('date')),
                            array('label' => __('inv_number')),
                            array('label' => __d('member','item_title')),
                            array('label' => __d('movie','item_title')),
                            array('label' => __d('schedule','item_title')),
                            array('label' => __d('order','amount_of_ticket')),
                            array('label' => __d('payment', 'method')),
                            array('label' => __('status')),
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

    public function admin_scan_qrcode() {
        $model = $this->model;

        $data_qrcode = array();
        if ($this->request->is('post')) {
            $data = $this->request->data;

            $result_data = $this->Common->decode_qrcode_value($data[$model]['qrcode']);
            if ($result_data['status']) {
                $data_qrcode = $result_data['params'];
            }   

        }

        $this->set(compact('model', 'data_qrcode'));
    }

    public function admin_generate_hkbo_report() {
        $model = $this->model;

        $report_result = array();
        if ($this->request->is('post')) {
            $data = $this->request->data;

            if(!isset($data['Report']['type'])) {
                $this->Session->setFlash(__('please_choose_types'), 'flash/error');
                goto display_initial_data;
            }

            $type = 'hourly';
            if ($data['Report']['type'] == '0') { 
                if (!isset($data['Report']['report_date']) || empty($data['Report']['report_date']) ||
                    !isset($data['Report']['time_report']) || empty($data['Report']['time_report'])) {
                    $this->Session->setFlash(__('date_time_report_invalid'), 'flash/error');
                    $type = '';
                    goto display_initial_data;
                }
            }

            if ($data['Report']['type'] == '1') {
                if (!isset($data['Report']['report_date']) || empty($data['Report']['report_date'])) {
                    $this->Session->setFlash(__('date_report_invalid'), 'flash/error');
                    goto display_initial_data;
                }
                $type = 'daily';
            }

            $this->requestAction(array(
                'plugin' => 'pos',
                'controller' => 'orders',
                'action' => 'export_hkbo_report',
                'admin' => true,
                'prefix' => 'admin',
                'ext' => 'json'
            ), array(
                'data' => $data,
                'type' => $type
            ));

        }

        display_initial_data : 

        $types = array('hourly','daily');
        $this->set(compact('model', 'types', 'report_result'));
    }

    public function admin_export_hkbo_report() {
        $model = $this->model;
        $data = $this->request->data;
        $type = $this->request->type;

        $new_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date'])));

        if ($type == 'daily') { 
            $data['type'] = 'daily';

            $data['date_report'] = $new_date;
            $data['time_report'] = '06:00:00';
            $data['time_start'] = '06:00:00';

            $data['date_time_report'] = $new_date;

            $tmp_date_time_report = date('Y-m-d H:i', strtotime($new_date . ' ' . $data['Report']['time_report']));
            $data['date_of_report'] = $tmp_date_time_report;

            $data['datetime_end'] = date('Y-m-d 05:59:59', strtotime($new_date));
            $data['datetime_start'] = date('Y-m-d 06:00:00', strtotime($new_date . ' -1 days'));

            $data['date_engagement'] = date('Y-m-d', strtotime($data['datetime_start']));

            

        } else {
            $data['type'] = 'hourly';

            $data['date_report'] = $new_date;
            $data['time_report'] = date('H:00', strtotime($data['Report']['time_report']));
            // $data['time_start'] = date('H:00', strtotime($data['Report']['time_report'] . ' -1 hours'));
            $data['time_start'] = '06:00:00';

            $tmp_date_time_report = date('Y-m-d H:00', strtotime($new_date . ' ' . $data['Report']['time_report']));
            $data['date_of_report'] = date('Y-m-d H:i', strtotime($new_date . ' ' . $data['Report']['time_report']));
            $data['date_time_report'] = date('Y-m-d-H', strtotime($tmp_date_time_report));

            $data['datetime_end'] = date('Y-m-d H:59:59', strtotime($tmp_date_time_report . ' -1 hours'));
            $data['datetime_start'] = date('Y-m-d 06:00:00', strtotime($data['datetime_end']));

            $data['date_engagement'] = date('Y-m-d', strtotime($data['datetime_start']));
            $limit_date_engagement = date('Y-m-d 06:00', strtotime($new_date));
            if (strtotime($data['datetime_end']) < strtotime($limit_date_engagement)) {
                $data['date_engagement'] = date('Y-m-d', strtotime($data['datetime_start'] . ' -1 days'));
            }
        }

        $report_result = $this->$model->get_HKBO_report($data);

        if ($report_result['status']) {
            $xml_data = $report_result['params'];
            $filename = $report_result['filename'];

            header('Content-Encoding: UTF-8'); // vilh, change to UTF-8
           
            header("Content-type: text/xml");
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Cache-Control: max-age=0');
    
            $fp = fopen('php://output', 'a');		
            fputs ($fp, "\xEF\xBB\xBF"); // UTF-8 BOM !!!!!
            fputs ($fp, $xml_data); // UTF-8 BOM !!!!!
    
            // connect and login to FTP server
            $ftp_server = "202.181.174.236";
            $ftp_port = "21";
            $ftp_username = 'acxcinema_portal';
            $ftp_userpass = '79NnkdrvV8TKQd5s';
            $ftp_conn = ftp_connect($ftp_server, $ftp_port) or die("Could not connect to $ftp_server");
            $login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);

            // upload file
            $file = 'img/uploads/'.$filename;
            ftp_chdir($ftp_conn, "tmp");
            ftp_put($ftp_conn, $filename, $file, FTP_ASCII);

            // close connection
            ftp_close($ftp_conn);

            exit;
        }
    }

    public function api_create_order() {
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
			} else if (!isset($data['ticket_type']) || empty($data['ticket_type'])) {
                $message = __('missing_parameter') .  __('ticket_type');
            } else if (!isset($data['staff_id']) || empty($data['staff_id'])) {
                $message = __('missing_parameter') .  __('staff_id');
            } else if (!isset($data['schedule_detail_id']) || empty($data['schedule_detail_id'])) {
				$message = __('missing_parameter') .  __('schedule_detail_id');
            } else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                //check staff
                $objStaff = ClassRegistry::init('Cinema.Staff');
                $data_staff = $objStaff->get_staff_by_conditions(array('id' => $data['staff_id'], 'token' => $data['token']));
    
                if (!isset($data_staff['Staff']['id']) || empty($data_staff['Staff']['id'])) {
                    $status = false;
                    $message = __('staff_not_valid');
                    goto return_result;
                }

                $result = $this->$model->create_order($data, $this->Api->get_language(), true);

                $status = $result['status'];
				$message = $result['message'];

                if($status){
					$params = $result['params'];
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

    public function api_update_membership_order() {
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
			} else if (!isset($data['order_id']) || empty($data['order_id'])) {
                $message = __('missing_parameter') .  __('order_id');
            } else {
                if (isset($data['is_member_register']) && !empty($data['is_member_register'])) {

                    if (!isset($data['country_code']) || empty($data['country_code'])) {
                        $message = __('missing_parameter') .  __('country_code');
                        goto return_api;
                    } else if (!isset($data['country_code']) || empty($data['country_code'])) {
                        $message = __('missing_parameter') .  __('country_code');
                        goto return_api;
                    }

                }


				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->update_membership_order($data, $this->Api->get_language());

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

            return_api:
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
			} else if (!isset($data['order_id']) || empty($data['order_id'])) {
                $message = __('missing_parameter') .  __('order_id');
            } else if (!isset($data['payment_method']) || empty($data['payment_method'])) {
                $message = __('missing_parameter') .  __('payment_method');
            } else if (!isset($data['order_detail_coupon']) || empty($data['order_detail_coupon'])) {
				$message = __('missing_parameter') .  __('order_detail_coupon');
            } else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);


                $verification_code = $this->Common->generate_verification_code();
                $result = $this->$model->do_payment($data, $this->Api->get_language(), $verification_code);

                $status = $result['status'];
				$message = $result['message'];

                if ($status) {

                    $data_order = $this->$model->get_order($data['order_id']);
                    if (isset($data_order[$model]['is_member_register']) && ($data_order[$model]['is_member_register'] > 0)) {
                        $option = array(
                            'conditions' => array(
                                'MemberPosRegistration.order_id' => $data['order_id'],
                                'MemberPosRegistration.void' => 0,
                            )
                        );

                        $objMemberPosRegistration = ClassRegistry::init('Member.MemberPosRegistration');
                        $data_registration = $objMemberPosRegistration->find('first', $option);
                        $country_code = $data_registration['MemberPosRegistration']['country_code'];
                        $phone = $data_registration['MemberPosRegistration']['phone'];

                        //send sms
                        $title = 'ACX-Cinemas';
            
                        $sms_message = sprintf(
                            __('pos_registration_msg'),
                            'https://acxcinema.vtl-solutions.com/authentication/register_pos/' . $data_registration['MemberPosRegistration']['verification_code']
                        );
            
                        $result_sms = $this->send_sms($country_code, $phone, $this->lang18, $title, $sms_message);
            
                        if (!$result_sms['status']) {
                            $message .= ' - send sms failed';
                        }                        
                    }
                }


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

    public function api_check_qrcode_validity() {
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
            } else if (!isset($data['qrcode_str']) || empty($data['qrcode_str'])) {
				$message = __('missing_parameter') .  __('qrcode_str');
            } else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $objStaff = ClassRegistry::init('Cinema.Staff');
                $data_staff = $objStaff->get_staff_by_conditions(array('id' => $data['staff_id'], 'token' => $data['token']));
        
                if (!isset($data_staff['Staff']['id']) || empty($data_staff['Staff']['id'])) {
                    $status = false;
                    $message = __('staff_not_valid');
                    goto return_result;
                }

                $result = $this->Common->decode_qrcode_value($data['qrcode_str']);

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

    public function api_create_order_browser() {
        $this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
            
            /*
            if (!isset($data['token']) || empty($data['token'])) {
				$message = __('missing_parameter') .  __('token');
            } else if (!isset($data['staff_id']) || empty($data['staff_id'])) {
                $message = __('missing_parameter') .  __('staff_id');
            */

            if (!isset($data['seats']) || empty($data['seats'])) {
                $message = __('missing_parameter') .  __('seats');
            } else if (!isset($data['schedule_detail_id']) || empty($data['schedule_detail_id'])) {
                $message = __('missing_parameter') .  __('schedule_detail_id');
            } else if (!isset($data['language']) || empty($data['language'])) {
				$message = __('missing_parameter') .  __('language');
            } else {
				$this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $member_id = 0;
                if (isset($data['token']) && !empty($data['token'])) {
                    //check member
                    $objMember = ClassRegistry::init('Member.Member');
                    $member_id = $objMember->get_id_by_token($data);
        
                    if ($member_id <= 0) {
                        $status = false;
                        $message = __('member_not_found');
                        goto return_result;
                    }

                    $data_renewal = $objMember->MemberRenewal->check_renewal($member_id);
                    if (!isset($data_renewal['MemberRenewal']) || empty($data_renewal['MemberRenewal'])) {
                        $status = false;
                        $message = __('membership_expired');
            
                        goto return_result;				
                    }
                }

                $data['staff_id'] = 0;
                $data['member_id'] = $member_id;
                $seats = json_decode($data['seats'], true);

                $ticket_types = array();
                foreach($seats as $seat) {
                    $tmp = array();
                    $tmp['id'] = 0;
                    $tmp['schedule_detail_layout_id'] = $seat;
                    $ticket_types[] = $tmp;
                }
                $data['ticket_type'] = $ticket_types;

                $result = $this->$model->create_order($data, $this->Api->get_language(), false);


                $status = $result['status'];
				$message = $result['message'];

                if($status){
					$params = $result['params'];
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

    public function api_get_data_order() {
        $this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
            
            /*
            if (!isset($data['token']) || empty($data['token'])) {
				$message = __('missing_parameter') .  __('token');
            } else if (!isset($data['staff_id']) || empty($data['staff_id'])) {
                $message = __('missing_parameter') .  __('staff_id');
            */

            if (!isset($data['order_id']) || empty($data['order_id'])) {
                $message = __('missing_parameter') .  __('order_id');
            } else if (!isset($data['language']) || empty($data['language'])) {
				$message = __('missing_parameter') .  __('language');
            } else {
				$this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $objSetting = ClassRegistry::init('Setting.Setting');

                $disc_member_percentage = 0;
                $member_id = 0;
                if (isset($data['token']) && !empty($data['token'])) {
                    //check member
                    $objMember = ClassRegistry::init('Member.Member');
                    $member_id = $objMember->get_id_by_token($data);
        
                    if ($member_id <= 0) {
                        $status = false;
                        $message = __('member_not_found');
                        goto return_result;
                    }

                    $data_renewal = $objMember->MemberRenewal->check_renewal($member_id);
                    if (!isset($data_renewal['MemberRenewal']) || empty($data_renewal['MemberRenewal'])) {
                        $status = false;
                        $message = __('membership_expired');
            
                        goto return_result;				
                    }

                    $disc_member_percentage = $objSetting->get_value('discount-member');
                }

                $paid_conditions = null;
                if (isset($data['is_paid']) && $data['is_paid']) {
                    $paid_conditions = array(
                        'Order.id' => $data['order_id'],
                        'Order.void' => 0
                    );
                }

                $result = $this->$model->get_data_order($data['order_id'], $this->Api->get_language(), $paid_conditions);                

                if (!isset($result['params'][$model]['id']) || empty($result['params'][$model]['id'])) {
                    $status = false;
                    $message = __('trans_not_found');
        
                    goto return_result;			
                }

                $data_order = $result['params'];
                
		        $service_charge = $objSetting->get_value('service-charge');
                $disability_seat = $data_order['Order']['disability_seats'];
                
                $count_seat = count($data_order['Seat']);
                $seat_non_disability = $count_seat - $disability_seat;

                $total_amount = 0;
                $total_service_charge = 0;
                foreach($data_order['TicketType'] as &$ticket_type) {
                    if ($ticket_type['TicketType']['is_main'] == 1) {
                        $ticket_type['TicketType']['qty'] = $seat_non_disability;
                    } else if ($ticket_type['TicketType']['is_disability'] == 1) {
                        $ticket_type['TicketType']['qty'] = $disability_seat;
                    } else {
                        $ticket_type['TicketType']['qty'] = 0;
                    }

                    $ticket_type['TicketType']['amount'] = $ticket_type['TicketType']['qty'] * $ticket_type['ScheduleDetailTicketType']['price'];
                    $total_amount += $ticket_type['TicketType']['amount'];
                    $ticket_type['TicketType']['service_charge'] = $ticket_type['TicketType']['qty'] * $service_charge;
                    $total_service_charge += $ticket_type['TicketType']['service_charge'];
                }

                $discount_amount = ($total_amount + $total_service_charge) * ($disc_member_percentage / 100);
                $grand_total = ($total_amount + $total_service_charge) - $discount_amount;

                $data_order['Order']['total_amount'] = $total_amount;
                $data_order['Order']['total_service_charge'] = $total_service_charge;
                $data_order['Order']['discount_percentage'] = $disc_member_percentage;
                $data_order['Order']['discount_amount'] = $discount_amount;
                $data_order['Order']['grand_total'] = $grand_total;

                $seat_release_time = $objSetting->get_value('seat-release');

                $data_order['Order']['deadline'] = date('Y-m-d H:i', strtotime($data_order['Order']['date'] . ' +'.$seat_release_time.' minutes'));
                $data_order['Movie']['poster'] = (isset($data_order['Movie']['poster']) && !empty($data_order['Movie']['poster'])) ? Environment::read('web.url_img').$data_order['Movie']['poster'] : '';
                $data_order['Movie']['video'] = (isset($data_order['Movie']['video']) && !empty($data_order['Movie']['video'])) ? Environment::read('web.url_img').$data_order['Movie']['video'] : '';
                if (!empty($data_order['Order']['qrcode_path'])) {
                    $data_order['Order']['qrcode_path'] = Environment::read('web.url_img').$data_order['Order']['qrcode_path'];
                }
                // Add price ticket, quantity
                $quantity_ticket = 0;
                $price_ticket = array();

                foreach ($data_order['TicketType'] as $kTicketType => $vTicketType) {
                    if(isset($vTicketType['TicketType']['qty']) && $vTicketType['TicketType']['qty'] > 0) {
                        $quantity_ticket += $vTicketType['TicketType']['qty'];
                        $price_ticket[] = 'HKD ' . $vTicketType['ScheduleDetailTicketType']['price'] . ' - ' . $vTicketType['TicketTypeLanguage']['name'];
                    }
                }
                $price_ticket = implode(" | " , $price_ticket);
                // add price ticket info
                $data_order['Order']['price_ticket'] = $price_ticket;

                //$quantity_ticket = count($detail['Seat']);
                $data_order['Order']['quantity_ticket'] = $quantity_ticket;

                // add payment_status, redemeed_status
                $data_order['Order']['payment_status'] = false;
                $data_order['Order']['redemeed_status'] = false;
                if ($data_order['Order']['status'] == 3) {
                    $data_order['Order']['payment_status'] = true;
                }
                if ($data_order['Order']['status'] == 3 && $data_order['Order']['print_count'] > 0) {
                    $data_order['Order']['redemeed_status'] = true;
                }

                $status = $result['status'];
				$message = $result['message'];

                if($status){
					$params = $data_order;
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

    public function api_update_data_order() {
        $this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
            $data = $this->request->data;
            
            if (!isset($data['order_id']) || empty($data['order_id'])) {
                $message = __('missing_parameter') .  __('order_id');
            } else if (!isset($data['ticket_type']) || empty($data['ticket_type'])) {
                $message = __('missing_parameter') .  __('ticket_type');
            } else if (!isset($data['language']) || empty($data['language'])) {
                $message = __('missing_parameter') .  __('language');
            } else if (!isset($data['receiver']) || empty($data['receiver'])) {
                $message = __('missing_parameter') .  __('receiver');
            } else if ((!isset($data['is_email']) || empty($data['is_email'])) && 
                       (!isset($data['country_code']) || empty($data['country_code']))) {
                $message = __('missing_parameter') .  __('country_code');
            } else {

                $this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $member_id = 0;
                $disc_member_percentage = 0;
                if (isset($data['token']) && !empty($data['token'])) {
                    //check member
                    $objMember = ClassRegistry::init('Member.Member');
                    $member_id = $objMember->get_id_by_token($data);
        
                    if ($member_id <= 0) {
                        $status = false;
                        $message = __('member_not_found');
                        goto return_result;
                    }

                    $data_renewal = $objMember->MemberRenewal->check_renewal($member_id);
                    if (!isset($data_renewal['MemberRenewal']) || empty($data_renewal['MemberRenewal'])) {
                        $status = false;
                        $message = __('membership_expired');
            
                        goto return_result;				
                    }

                    $objSetting = ClassRegistry::init('Setting.Setting');
		            $disc_member_percentage = $objSetting->get_value('discount-member');
                }

                $data_update_order = $this->update_data_order_trans($data, $this->Api->get_language(), $disc_member_percentage);

                $result = $this->$model->get_data_order($data['order_id'], $this->Api->get_language());

                $status = $result['status'];
				$message = $result['message'];

                if($status){
					$params = $result['params'];
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

	public function update_data_order_trans($data, $lang, $disc_member_percentage = 0) {
        $model = $this->model;

		$status = false;
		$message = "";
		$params = (object)array();

		/*
			1. validate the order 
				-) not yet paid
				-) not void
			2. update the order detail
			3. update the order
		*/

		$option = array(
			'fields' => array(
				'Order.*',
				'OrderDetail.*',
				'ScheduleDetailLayout.*'
			),
			'joins' => array(
				array(
					'alias' => 'OrderDetail',
					'table' => Environment::read('table_prefix') . 'order_details',
					'type' => 'left',
					'conditions' => array(
						'OrderDetail.order_id = Order.id',
					),
				),
				array(
					'alias' => 'ScheduleDetailLayout',
					'table' => Environment::read('table_prefix') . 'schedule_detail_layouts',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetailLayout.id = OrderDetail.schedule_detail_layout_id',
					),
				),
			),
			'conditions' => array(
				'Order.id' => $data['order_id'],
				'Order.is_paid' => 0,
				'Order.void' => 0
			),
		);

		$data_order = $this->$model->find('all', $option);

		$option_ticket_type = array(
			'fields' => array(
				'ScheduleDetailTicketType.*'
			),
			'joins' => array(
				array(
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'Order.schedule_detail_id = ScheduleDetail.id',
					),
				),
				array(
					'alias' => 'ScheduleDetailTicketType',
					'table' => Environment::read('table_prefix') . 'schedule_detail_ticket_types',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetail.id = ScheduleDetailTicketType.schedule_detail_id',
					),
				),
			),
			'conditions' => array(
				'Order.id' => $data['order_id'],
				'Order.is_paid' => 0,
				'Order.void' => 0
			),
		);

		$data_ticket_type = $this->$model->find('all', $option_ticket_type);

		$ticket_type_ids = Hash::extract($data_ticket_type, '{n}.ScheduleDetailTicketType.id');

		$tmp_ticket_type = json_decode($data['ticket_type'], true);

		$ticket_type = array();
		foreach($tmp_ticket_type as $ticket_type_parm) {
			for ($i = 0; $i < $ticket_type_parm['qty']; $i++) {
				$tmp_row = array();
				$tmp_row['ticket_type_id'] = $ticket_type_parm['ticket_type_id'];
				$tmp_row['is_disability'] = $ticket_type_parm['is_disability'];
				$index_ticket_type = array_search($ticket_type_parm['ticket_type_id'], $ticket_type_ids);
                $tmp_row['price'] = $data_ticket_type[$index_ticket_type]['ScheduleDetailTicketType']['price'];
                $tmp_row['price_hkbo'] = $data_ticket_type[$index_ticket_type]['ScheduleDetailTicketType']['price_hkbo'];
				$ticket_type[] = $tmp_row;
			}
		}

		$parm_ticket_type = $ticket_type;
		$ticket_type_disability = Hash::extract($parm_ticket_type, '{n}.is_disability');

		$objSetting = ClassRegistry::init('Setting.Setting');
		$service_charge = $objSetting->get_value('service-charge');

        $data_update_order['Order'] = $data_order[0]['Order'];
        // $discount_percentage = $data_order[0]['Order']['discount_percentage'];
        $discount_percentage = $disc_member_percentage;
        
		$order_detail = array();
		$subtotal = 0;
		foreach($data_order as &$order) {
			$index = 0;
			if($order['ScheduleDetailLayout']['is_disability_seat'] == 1) {
				$index = array_search($order['ScheduleDetailLayout']['is_disability_seat'], $ticket_type_disability);
			}

			$order['OrderDetail']['schedule_detail_ticket_type_id'] = $parm_ticket_type[$index]['ticket_type_id'];
            $order['OrderDetail']['price'] = $parm_ticket_type[$index]['price'];
            $order['OrderDetail']['price_hkbo'] = $parm_ticket_type[$index]['price_hkbo'];
			$order['OrderDetail']['service_charge'] = $service_charge;
			$order['OrderDetail']['service_charge_percentage'] = $service_charge;
			$order['OrderDetail']['subtotal'] = $order['OrderDetail']['price'] + $service_charge;

			$subtotal += $order['OrderDetail']['subtotal'];

			//$order_detail 
            $order_detail[] = $order['OrderDetail'];
            
            array_splice($parm_ticket_type, $index, 1);
            array_splice($ticket_type_disability, $index, 1);
		}

		$data_update_order['OrderDetail'] = $order_detail;

        $data_update_order['Order']['discount_percentage'] = $discount_percentage;
		$data_update_order['Order']['total_amount'] = $subtotal;
		$data_update_order['Order']['discount_amount'] = $subtotal * ($discount_percentage/100);
		$data_update_order['Order']['grand_total'] = $subtotal - $data_update_order['Order']['discount_amount'];
		$data_update_order['Order']['status'] = 2;

		if (isset($data['is_email']) && $data['is_email'] == 'true') {
            $is_email = true;
            $receiver = $data['receiver'];
            $data_update_order['Order']['email'] = $data['receiver'];
		} else {
            $is_email = false;
            $receiver = $data['country_code'].$data['receiver'];
            $data_update_order['Order']['country_code'] = $data['country_code'];
			$data_update_order['Order']['phone'] = $data['receiver'];
		}

        /*
        $qrcode_value = $this->Common->get_qrcode_value($data_update_order['Order'],'order');
        $qrcode = $this->Common->generate_qrcode('order', $qrcode_value, $data_update_order['Order']['inv_number']);

        if (isset($qrcode['status']) && $qrcode['status']) {
            $data_update_order['Order']['qrcode_path'] = $qrcode['path'];
        } else {
            $status = false;
            $message = __('qrcode_generate_failed');

            goto return_result;				
        }
        */

		$dbo = $this->$model->getDataSource();
		$dbo->begin();
		try {
			if ($this->$model->saveAll($data_update_order)) {
				$dbo->commit();
				$status = true;
				$message = __('data_is_saved');

                /*
				// Send mail, phone
                $data['qrcode_path'] = Environment::read('web.url_img') . $data_update_order['Order']['qrcode_path'];
                $result_send = $this->send_ticket_to_customer($data);

                if (!$result_send['status']) {
                    $message .= $result_send['message'];
                }
                */
			} else {
				$dbo->rollback();
				$status = false;
				$message = __('data_update_order_is_not_saved');
	
				goto return_result;				
			}

		} catch (Exception $e) {
			$dbo->rollback();
			$status = false;
			$message = __('data_is_not_saved');

			goto return_result;
		}


		return_result :

		return array('status' => $status, 'message' => $message, 'params' => $params);
	}

    public function api_calculate_total() {
        $this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
            $data = $this->request->data;
            
            if (!isset($data['ticket_type']) || empty($data['ticket_type'])) {
                $message = __('missing_parameter') .  __('ticket_type');
            } else if (!isset($data['language']) || empty($data['language'])) {
                $message = __('missing_parameter') .  __('language');
            } else {
                $this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $objSetting = ClassRegistry::init('Setting.Setting');

                $discount_member_percentage = 0;
                if (isset($data['token']) && !empty($data['token'])) {
                    //check member
                    $objMember = ClassRegistry::init('Member.Member');
                    $member_id = $objMember->get_id_by_token($data);
        
                    if ($member_id <= 0) {
                        $status = false;
                        $message = __('member_not_found');
                        goto return_result;
                    }

                    $data_renewal = $objMember->MemberRenewal->check_renewal($member_id);
                    if (!isset($data_renewal['MemberRenewal']) || empty($data_renewal['MemberRenewal'])) {
                        $status = false;
                        $message = __('membership_expired');
            
                        goto return_result;				
                    }

                    $discount_member_percentage = $objSetting->get_value('discount-member');
                }

                $order_detail = json_decode($data['ticket_type'], true);
                $order_ticket_type_ids = Hash::extract($order_detail, '{n}.ticket_type_id'); 

                $option_ticket_type = array(
                    'conditions' => array(
                        'id IN' => $order_ticket_type_ids
                    )
                );

                $objScheduleDetailTicketType = ClassRegistry::init('Movie.ScheduleDetailTicketType');
                $data_ticket_type = $objScheduleDetailTicketType->find('all', $option_ticket_type);

                if (!isset($data_ticket_type[0]['ScheduleDetailTicketType']['id']) || empty($data_ticket_type[0]['ScheduleDetailTicketType']['id'])) {
                    $status = false;
                    $message = __('ticket_type_not_found');

                    goto return_result;
                }

                $ticket_type_ids = Hash::extract($data_ticket_type, '{n}.ScheduleDetailTicketType.id'); 
                
                $service_charge = $objSetting->get_value('service-charge');

                $total_amount = 0;
                $total_service_charge = 0;
                foreach($order_detail as &$detail) {
                    $index = array_search($detail['ticket_type_id'], $ticket_type_ids);
                    $detail['price'] = $data_ticket_type[$index]['ScheduleDetailTicketType']['price'];
                    $detail['subtotal'] = $detail['price'] * $detail['qty'];
                    $detail['service_charge'] = $service_charge * $detail['qty'];

                    $total_service_charge += $detail['service_charge'];
                    $total_amount += $detail['subtotal'];
                }

                $result = array();
                $result['OrderDetail'] = $order_detail;
                $result['Order']['total_amount'] = $total_amount;
                $result['Order']['total_service_charge'] = $total_service_charge;

                $subtotal = $total_amount + $total_service_charge;
                $discount_amount = $subtotal * ($discount_member_percentage/100);

                $result['Order']['discount_amount'] = $discount_amount;
                $result['Order']['grand_total'] = $subtotal - $discount_amount;

                $status = true;

                if($status) {
					$params = $result;
                    if (!$params) {
                        $params = (object)array();
                    }

                }else{

                }

            }
            
            return_result :
            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
    }

    public function api_settle_order_trans() {
        $model = $this->model;
		$this->layout = null;
		$this->autoRender = false;

		$message = '';
		$verified = false;
		$return_object = array();
		$data = $this->request->data;
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

					$data_order = $this->$model->find('first', $option);


					if (isset($data_order[$model]['id']) && !empty($data_order[$model]['id'])) {
						$token = $data_order[$model]['token'];
						$trans_id = $data_order[$model]['id'];
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
					$objOrderPaymentLog = ClassRegistry::init('Pos.OrderPaymentLog');
					if ($objOrderPaymentLog->save($data)) {
						$log_id = $objOrderPaymentLog->id;

						if($valid) {
							$data_order[$model]['payment_log_id'] = $log_id;
							$data_order[$model]['status'] = 3;
                            $data_order[$model]['is_paid'] = 1;
                            $data_order[$model]['paid_amount'] = $data_order[$model]['grand_total'];

                            $qrcode_value = $this->Common->get_qrcode_value($data_order['Order'],'order');
                            $qrcode = $this->Common->generate_qrcode('order', $qrcode_value, $data_order['Order']['inv_number']);
                    
                            if (isset($qrcode['status']) && $qrcode['status']) {
                                $data_order['Order']['qrcode_path'] = $qrcode['path'];
                            } else {
                                // $status = false;
                                $message = __('qrcode_generate_failed');
                            }

							if($this->$model->saveAll($data_order)) {
								$dbo->commit();
                                
                                // Send mail, phone
                                

                                $data_ticket = array();
                                $data_ticket['qrcode_path'] = Environment::read('web.url_img') . $data_order[$model]['qrcode_path'];
                                $data_ticket['order_id'] = $data_order[$model]['id'];
                                if (!empty($data_order[$model]['email'])) {
                                    $data_ticket['is_email'] = 'true';
                                    $data_ticket['receiver'] = $data_order[$model]['email'];
                                } else {
                                    $data_ticket['is_email'] = 'false';
                                    $data_ticket['country_code'] = $data_order[$model]['country_code'];
                                    $data_ticket['receiver'] = $data_order[$model]['phone'];
                                }

                                $result_send = $this->send_ticket_to_customer($data_ticket);

                                if ($result_send['status']) {
                                    $message = 'trans_completed_successfully';
                                    $message .= ' - qrcode string : ' . $qrcode_value;
                                    $message .= ' - data_order : ' . json_encode($data_order['Order']);
                                    $message .= ' - data_order : ' . json_encode($data_ticket);
                                } else {
                                    $message = 'send ticket is failed : ' . $result_send['message'];
                                    $message .= 'data_order : ' . json_encode($data_ticket);
                                }
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
		$pathToFile = '../tmp/logs/payment-order-notify.log';
		
		try {
			//Log the data to your file using file_put_contents.
			file_put_contents($pathToFile, $str_log, FILE_APPEND);		
		} catch (Exception $e) {
			print "Fail to log to file.";
		}
    }

    public function api_get_list_order_by_member() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = true;
            $message = __('retrieve_data_successfully');
            $params = (object)array();
            $data = $this->request->data;
            $results = array();

            if (!isset($data['token']) || empty($data['token'])) {
                $status = false;
                $message = __('missing_parameter') . __('token');
            } else if (!isset($data['language']) || empty($data['language'])) {
                $status = false;
                $message = __('missing_parameter') . __('language');
            } else {
                $this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                // if is mobile => must has is_valid
                if ( ! isset($data['is_browser']) || empty($data['is_browser'])) {
                    if (!isset($data['is_valid']) || !in_array($data['is_valid'], array(1,0))) {
                        $status = false;
                        $message = __('missing_parameter') . __('is_valid');
                        goto return_api;
                    }
                }

                $objSetting = ClassRegistry::init('Setting.Setting');
                $objMember = ClassRegistry::init('Member.Member');
                $member_id = $objMember->get_id_by_token($data);

                if (empty($member_id)) {
                    $status = false;
                    $message = __('user_not_found');
                    goto return_api;
                }

                $data_renewal = $objMember->MemberRenewal->check_renewal($member_id);
                if (!isset($data_renewal['MemberRenewal']) || empty($data_renewal['MemberRenewal'])) {
                    $status = false;
                    $message = __('membership_expired');

                    goto return_api;
                }

                $disc_member_percentage = $objSetting->get_value('discount-member');

                $option = array(
                    'fields' => array(
                        'Order.id',
                    ),
                    'joins' => array(
                        array(
                            'alias' => 'ScheduleDetail',
                            'table' => Environment::read('table_prefix') . 'schedule_details',
                            'type' => 'left',
                            'conditions' => array(
                                'ScheduleDetail.id = Order.schedule_detail_id',
                            ),
                        ),
                    ),
                    'conditions' => array(
                        'Order.member_id' => $member_id,
                        'Order.void' => 0
                    ),
                );

                $now = date( 'Y-m-d' );
                $time = date ('H:i');
                $condition_temp = array();

                // Parameter Browser
                if (isset($data['status']) && ! empty($data['status'])) {
                    if ($data['status'] == 1) {
                        // status : payment
                        $condition_temp[] = array(
                            'Order.status < ' => 3,
                        );
                    }
                    if ($data['status'] == 2) {
                        // status : redeem
                        $condition_temp['Order.status'] = 3;
                        $condition_temp['OR'][] = array(
                            //'Order.status' => 3,
                            'Order.print_count <=' => 0,
                            //'ScheduleDetail.date >' => $now,
                        );
                        $condition_temp['OR'][] = array(
                            //'Order.status' => 3,
                            'Order.print_count =' => null,
                            //'ScheduleDetail.date >' => $now,
                        );
                    }
                    if ($data['status'] == 3) {
                        // status : complete
                        /*$condition_temp[] = array(
                            'ScheduleDetail.date < ' => $now,
                        );*/
                        $condition_temp[] = array(
                            'Order.status' => 3,
                        );
                        $condition_temp[] = array(
                            'Order.print_count > ' => 0,
                        );
                    }
                } else {
                    $condition_temp[] = array(
                        'Order.status' => array(2,3),
                    );
                }

                // Parameter Mobile
                if (isset($data['is_valid']) && $data['is_valid'] == 1) {
                    $condition_temp['Order.status'] = 3;
                    $condition_temp['OR'][] = array(
                        'Order.print_count <=' => 0
                    );
                    $condition_temp['OR'][] = array(
                        'Order.print_count =' => null
                    );
                } else if ((isset($data['is_valid']) && $data['is_valid'] == 0)) {
                    $condition_temp = array(
                        'Order.status' => 3,
                        'Order.print_count >' => 0
                    );
                }

                $option['conditions'] = array_merge($option['conditions'], $condition_temp);

                $data_order_list = $this->$model->find('all', $option);

                $data_order = array();
                foreach ($data_order_list as $k => $v) {
                    $is_id_required = false;

                    $condition = array(
                        'Order.id' => $v['Order']['id'],
                        //'Order.is_paid' => 0,
                        'Order.void' => 0
                    );
                    $condition = array_merge($condition, $condition_temp);

                    $data_order = $this->$model->get_data_order($v['Order']['id'], $data['language'], $condition);
                    if ($data_order['status'] == false) {
                        continue;
                    }
                    $data_order = $data_order['params'];

                    $data_order['Movie']['poster'] = (isset($data_order['Movie']['poster']) && !empty($data_order['Movie']['poster'])) ? Environment::read('web.url_img').$data_order['Movie']['poster'] : '';
                    $data_order['Movie']['video'] = (isset($data_order['Movie']['video']) && !empty($data_order['Movie']['video'])) ? Environment::read('web.url_img').$data_order['Movie']['video'] : '';
                    $data_order['Order']['qrcode_path'] = (isset($data_order['Order']['qrcode_path']) && !empty($data_order['Order']['qrcode_path'])) ? Environment::read('web.url_img').$data_order['Order']['qrcode_path'] : '';

                    $objOrderDetail = ClassRegistry::init('Pos.OrderDetail');
                    $conditon_order_detail = array (
                        'order_id' => $v['Order']['id']
                    );
                    $list_order_detail = $objOrderDetail->get_order_detail_by_conditions($conditon_order_detail, $data['language']);

                    $quantity_ticket = count($list_order_detail);
                    $price_ticket = array();

                    foreach ($list_order_detail as $kTicketType => $vTicketType) {
                        $id_ticket_type = $vTicketType['ScheduleDetailTicketType']['ticket_type_id'];
                        $price_ticket[$id_ticket_type] = 'HKD ' . $vTicketType['OrderDetail']['price'] . ' - ' . $vTicketType['ScheduleDetailTicketType']['TicketType']['TicketTypeLanguage'][0]['name'];
                    }

                    $price_ticket = implode("\n" , $price_ticket);

                    // add price ticket info
                    $data_order['Order']['price_ticket'] = $price_ticket;

                    //$quantity_ticket = count($detail['Seat']);
                    $data_order['Order']['quantity_ticket'] = $quantity_ticket;

                    // add payment_status, redemeed_status
                    $data_order['Order']['payment_status'] = false;
                    $data_order['Order']['redemeed_status'] = false;
                    if ($data_order['Order']['status'] == 3) {
                        $data_order['Order']['payment_status'] = true;
                    }
                    if ($data_order['Order']['status'] == 3 && $data_order['Order']['print_count'] > 0) {
                        $data_order['Order']['redemeed_status'] = true;
                    }

                    $results[] = $data_order;
                    // END : Add more info
                }

                $result_temp = array();
                if ((isset($data['is_browser']) && $data['is_browser'] == 1)
                ) {
                    if (isset($data['status']) && !empty($data['status'])) {
                        if ($data['status'] == 1) {
                            $result_temp['payment'] = $results;
                        } else if ($data['status'] == 2) {
                            $result_temp['redemption'] = $results;
                        } else if ($data['status'] == 3) {
                            $result_temp['completed'] = $results;
                        }
                    } else {
                        foreach ($results as $k => $v) {
                            if ($v['Order']['status'] != 3) {
                                $result_temp['payment'][] = $v;
                            } else if (
                                ($v['Order']['status'] == 3 && $v['Order']['print_count'] <= 0)
                                || ($v['Order']['status'] == 3 && $v['Order']['print_count'] == null)
                            ) {
                                $result_temp['redemption'][] = $v;
                            } else {
                                $result_temp['completed'][] = $v;
                            }
                            /*if ($v['ScheduleDetail']['date'] < $now) {
                                $result_temp['completed'][] = $v;
                            }*/
                        }
                    }
                    $results = $result_temp;
                }


                return_api:
                //$status = true;
                $message = $message;

                if($status){
                    $params = $results;
                    if (!$params) {
                        $params = (object)array();
                    }
                }else{
                    $log_data = array();
                    $log_data['message'] = $message;
                    $log_data['data_result'] = $results;
                    $log_data['data'] = $data;

                    $this->Api->set_error_log($log_data);
                }

            }

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }

    public function api_get_order_status_completion() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = true;
            $message = "";
            $params = (object)array();
            $data = $this->request->data;

            if (!isset($data['inv_number']) || empty($data['inv_number'])) {
                $message = __('missing_parameter') . __('inv_number');
            } else {
                $this->Api->set_language($this->lang18);

                $result = array();

                $objSetting = ClassRegistry::init('Setting.Setting');
                $result['payment_expected_time'] = $objSetting->get_value('payment_expected_time');

                $data_order = $this->$model->get_order_by_conditions(array('inv_number' => $data['inv_number']));

                $result['receiver'] = '';
                if (isset($data_order[$model]['id']) && !empty($data_order[$model]['id'])) {
                    $result['movie_id'] = $data_order['Schedule']['movie_id'];
                    $result['movie_type_id'] = $data_order['Schedule']['movie_type_id'];
                    $result['slug'] = $data_order['Movie']['slug'];
                    $result['schedule_detail_id'] = $data_order['ScheduleDetail']['id'];
                    $result['date'] = $data_order['ScheduleDetail']['date'];
                    $result['order_id'] = $data_order[$model]['id'];
                    $result['payment_status'] = ($data_order[$model]['status'] == 3) ? 1 : 0;
                    if (isset($data_order[$model]['phone']) && !empty($data_order[$model]['phone'])) {
                        $result['receiver'] = $data_order[$model]['phone'];
                    } else {
                        $result['receiver'] = $data_order[$model]['email'];
                    }

                    $result['receiver'] = substr($result['receiver'], 0, 3) . '***********' . substr($result['receiver'], -4);
                } else {
                    $status = false;
                    $message = __('trans_not_found');
        
                    goto return_result;	
                }

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $status = true;

                if($status){
                    $params = $result;
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

    public function send_ticket_to_customer($data) {
        $model = $this->model;
        $status = true;
        $message = '';

        $send_mail = $send_sms = false;
        $country_code = $phone = $email = null;

        $conditions = array(
            'Order.id' => $data['order_id'],
            'Order.void' => 0
        );

        $result = $this->$model->get_data_order($data['order_id'], $this->lang18, $conditions);
        $data_order = $result['params'];

        if (isset($data['token']) && !empty($data['token'])) {
            // is member
            $objMember = ClassRegistry::init('Member.Member');
            $condition = array(
                'token' => $data['token']
            );

            $member = $objMember->get_member_by_conditions($condition);

            if ( ! isset($member['Member']) && empty($member['Member'])) {
                $message = __('user_not_found');
                $status = false;
                goto return_result;
            }

            /*
            $send_mail = true;
            $send_sms = false;

            $email = $member['Member']['email'];
            // $country_code = $member['Member']['country_code'];
            // $phone = $member['Member']['phone'];


            if (isset($data['is_email']) && ($data['is_email'] == 'true')) {
                $email = $data['receiver'];
            } else {
                $send_sms = true;
                $country_code = $data['country_code'];
                $phone = $data['receiver'];
            }
            */

        } else {
            // is guest
            /*
            if (
                isset($data['country_code']) && !empty($data['country_code'])
                && isset($data['phone']) && !empty($data['phone'])
            ) {
                $send_sms = true;
                $country_code = $data['country_code'];
                $phone = $data['phone'];
            }
            if (isset($data['email']) && !empty($data['email'])) {
                $send_mail = true;
                $email = $data['email'];
            }
            */

            /*
            if (isset($data['is_email']) && ($data['is_email'] == 'true')) {
                $send_mail = true;
                $email = $data['receiver'];
            } else {
                $send_sms = true;
                $country_code = $data['country_code'];
                $phone = $data['receiver'];
            }
            */
        }

        if (isset($data['is_email']) && ($data['is_email'] == 'true')) {
            $send_mail = true;
            $email = $data['receiver'];
        } else {
            $send_sms = true;
            $country_code = $data['country_code'];
            $phone = $data['receiver'];
        }

        if ($send_sms) {
            $title = 'ACX-Cinemas';
            //$email_message = sprintf(__('order_qrcode_msg'), $data['qrcode_path']);

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

            if (!$result['status']) {
                $status = false;
                $message .= ' - '.$result['message'];
            }
        }

        if ($send_mail) {
            $template = 'order_qrcode';
            $subject = 'ACX-Cinemas - Ticket QR Code';

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
        }

        return_result:

        return array(
            'status' => $status,
            'message' => $message
        );
    }

    public function get_more_info_data_order ($data_order) {
        $objSetting = ClassRegistry::init('Setting.Setting');
        $disc_member_percentage = 0;

        $disc_member_percentage = $objSetting->get_value('discount-member');


        $service_charge = $objSetting->get_value('service-charge');
        $disability_seat = $data_order['Order']['disability_seats'];

        $count_seat = count($data_order['Seat']);
        $seat_non_disability = $count_seat - $disability_seat;

        $total_amount = 0;
        $total_service_charge = 0;
        foreach($data_order['TicketType'] as &$ticket_type) {
            if ($ticket_type['TicketType']['is_main'] == 1) {
                $ticket_type['TicketType']['qty'] = $seat_non_disability;
            } else if ($ticket_type['TicketType']['is_disability'] == 1) {
                $ticket_type['TicketType']['qty'] = $disability_seat;
            } else {
                $ticket_type['TicketType']['qty'] = 0;
            }

            $ticket_type['TicketType']['amount'] = $ticket_type['TicketType']['qty'] * $ticket_type['ScheduleDetailTicketType']['price'];
            $total_amount += $ticket_type['TicketType']['amount'];
            $ticket_type['TicketType']['service_charge'] = $ticket_type['TicketType']['qty'] * $service_charge;
            $total_service_charge += $ticket_type['TicketType']['service_charge'];
        }

        $discount_amount = ($total_amount + $total_service_charge) * ($disc_member_percentage / 100);
        $grand_total = ($total_amount + $total_service_charge) - $discount_amount;

        $data_order['Order']['total_amount'] = $total_amount;
        $data_order['Order']['total_service_charge'] = $total_service_charge;
        $data_order['Order']['discount_percentage'] = $disc_member_percentage;
        $data_order['Order']['discount_amount'] = $discount_amount;
        $data_order['Order']['grand_total'] = $grand_total;

        return $data_order;
    }

    public function api_cancel_order() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = true;
            $message = "";
            $params = (object)array();
            $data = $this->request->data;

            if (!isset($data['order_id']) || empty($data['order_id'])) {
                $message = __('missing_parameter') . __('order_id');
            } else {
                $this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = array();

                $objOrder = ClassRegistry::init('Pos.Order');


                $order_detail = $objOrder->get_order($data['order_id']);

                if (empty($order_detail)) {
                    $status = false;
                    $message = __('order_not_found');
                    goto return_api;
                } else {
                    if ($order_detail['Order']['status'] == 3) {
                        $status = false;
                        $message = __('order_already_paid');
                        goto return_api;
                    }
                }

                $result = $this->$model->cancel_order($data, $order_detail);

                $status = $result['status'];
                $message = $result['message'];

                if($status){
                    $params = $result['params'];
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

            return_api:

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }

    public function api_cancel_order_by_invoice_number() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = true;
            $message = "";
            $params = (object)array();
            $data = $this->request->data;

            if (!isset($data['inv_number']) || empty($data['inv_number'])) {
                $message = __('missing_parameter') . __('inv_number');
            } else {
                $this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = array();

                $objOrder = ClassRegistry::init('Pos.Order');
                $order_detail = $objOrder->get_order_by_invoice_number($data['inv_number']);

                if (empty($order_detail)) {
                    $status = false;
                    $message = __('order_not_found');
                    goto return_api;
                } else {
                    if ($order_detail['Order']['status'] == 3) {
                        $status = false;
                        $message = __('order_already_paid');
                        goto return_api;
                    }
                }
                $data['order_id'] = $order_detail['Order']['id'];
                
                $tmp_order_detail = $objOrder->get_order($data['order_id']);

                $result = $this->$model->cancel_order($data, $tmp_order_detail);


                $status = $result['status'];
                $message = $result['message'];

                if($status){
                    $params = $result['params'];
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

            return_api:

            $this->Api->set_result($status, $message, $params);
        }

        $this->Api->output();
    }

    public function api_get_data_created_order() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
            $message = "";
            $params = (object)array();
            $data = $this->request->data;

            /*
            if (!isset($data['token']) || empty($data['token'])) {
				$message = __('missing_parameter') .  __('token');
            } else if (!isset($data['staff_id']) || empty($data['staff_id'])) {
                $message = __('missing_parameter') .  __('staff_id');
            */

            if (!isset($data['order_id']) || empty($data['order_id'])) {
                $message = __('missing_parameter') .  __('order_id');
            } else if (!isset($data['language']) || empty($data['language'])) {
                $message = __('missing_parameter') .  __('language');
            } else {
                $this->Api->set_language($data['language']);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $objSetting = ClassRegistry::init('Setting.Setting');

                $disc_member_percentage = 0;
                $member_id = 0;
                if (isset($data['token']) && !empty($data['token'])) {
                    //check member
                    $objMember = ClassRegistry::init('Member.Member');
                    $member_id = $objMember->get_id_by_token($data);

                    if ($member_id <= 0) {
                        $status = false;
                        $message = __('member_not_found');
                        goto return_result;
                    }

                    $data_renewal = $objMember->MemberRenewal->check_renewal($member_id);
                    if (!isset($data_renewal['MemberRenewal']) || empty($data_renewal['MemberRenewal'])) {
                        $status = false;
                        $message = __('membership_expired');

                        goto return_result;
                    }

                    $disc_member_percentage = $objSetting->get_value('discount-member');
                }

                $order_conditions = array(
                    'Order.id' => $data['order_id'],
                    'Order.void' => 0
                );

                $result = $this->$model->get_data_order($data['order_id'], $this->Api->get_language(), $order_conditions);

                if (!isset($result['params'][$model]['id']) || empty($result['params'][$model]['id'])) {
                    $status = false;
                    $message = __('trans_not_found');

                    goto return_result;
                }

                $data_order = $result['params'];

                $data_order['Movie']['poster'] = (isset($data_order['Movie']['poster']) && !empty($data_order['Movie']['poster'])) ? Environment::read('web.url_img').$data_order['Movie']['poster'] : '';
                $data_order['Movie']['video'] = (isset($data_order['Movie']['video']) && !empty($data_order['Movie']['video'])) ? Environment::read('web.url_img').$data_order['Movie']['video'] : '';
                $data_order['Order']['qrcode_path'] = (isset($data_order['Order']['qrcode_path']) && !empty($data_order['Order']['qrcode_path'])) ? Environment::read('web.url_img').$data_order['Order']['qrcode_path'] : '';

                $objOrderDetail = ClassRegistry::init('Pos.OrderDetail');
                $conditon_order_detail = array (
                    'order_id' => $data['order_id']
                );
                $list_order_detail = $objOrderDetail->get_order_detail_by_conditions($conditon_order_detail, $data['language']);

                $quantity_ticket = count($list_order_detail);
                $price_ticket = array();

                foreach ($list_order_detail as $kTicketType => $vTicketType) {
                    $id_ticket_type = $vTicketType['ScheduleDetailTicketType']['ticket_type_id'];
                    $price_ticket[$id_ticket_type] = 'HKD ' . $vTicketType['OrderDetail']['price'] . ' - ' . $vTicketType['ScheduleDetailTicketType']['TicketType']['TicketTypeLanguage'][0]['name'];
                }

                $price_ticket = implode(" | " , $price_ticket);
                // add price ticket info
                $data_order['Order']['price_ticket'] = $price_ticket;

                //$quantity_ticket = count($detail['Seat']);
                $data_order['Order']['quantity_ticket'] = $quantity_ticket;

                // add payment_status, redemeed_status
                $data_order['Order']['payment_status'] = false;
                $data_order['Order']['redemeed_status'] = false;
                if ($data_order['Order']['status'] == 3) {
                    $data_order['Order']['payment_status'] = true;
                }
                if ($data_order['Order']['status'] == 3 && $data_order['Order']['print_count'] > 0) {
                    $data_order['Order']['redemeed_status'] = true;
                }

                $status = $result['status'];
                $message = $result['message'];

                if($status){
                    $params = $data_order;
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

    public function admin_generate_ticket_sales_report() {
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
                'plugin' => 'pos',
                'controller' => 'orders',
                'action' => 'generate_today_ticket_report',
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

    public function admin_generate_today_ticket_report(){
        $model = $this->model;
        $data = $this->request->data;
        $type = $this->request->type;
        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_from'])));
        $date_to = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_to'])));

        $results = array(
            'status' => false,
            'message' => __('missing_parameter'),
            'params' => array(),
        );

        $this->disableCache();

//        if( $this->request->is('get') ) {
            $result = $this->$model->get_data_today_ticket_export($data, 1, 2000, $this->lang18);

            $data_binding['dobMonths'] = $this->Common->get_list_month();


//            if ($result) {

                $cvs_data = array();

                foreach ($result as $row) {
                    $temp = $this->$model->format_data_today_ticket_export(array(), $row, $data_binding);

                    array_push($cvs_data, $temp);
                }

                try{
                    $file_name = $date_from. '-to-' .$date_to.'-ticket-sales';

                    // export xls
//                    if ($this->request->type == "xls") {
                    $excel_readable_header = array(
                        array('label' => 'Invoice Number'),
                        array('label' => 'Transaction Date'),
                        array('label' => 'Transaction Time'),
                        array('label' => 'Staff'),
                        array('label' => 'Total Amount'),
                        array('label' => 'Coupon Amount'),
                        array('label' => 'Discount Amount'),
                        array('label' => 'Paid Amount'),
                        array('label' => 'Payment Method'),
                        array('label' => 'Member Code'),
                        array('label' => 'Movie'),
                        array('label' => 'Show Date'),
                        array('label' => 'Show Time'),
                        array('label' => 'Seats'),
                        array('label' => 'House'),
                        array('label' => 'Ticket Total'),
                        array('label' => 'Adult'),
                        array('label' => 'Student'),
                        array('label' => 'Senior'),
                        array('label' => 'Child'),
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
//        }

        $this->set(array(
            'results' => $results,
            '_serialize' => array('results')
        ));
    }

    public function admin_generate_tuckshop_sales_report() {
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
                'plugin' => 'pos',
                'controller' => 'orders',
                'action' => 'generate_today_tuckshop_report',
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

    public function admin_generate_today_tuckshop_report(){
        $model = $this->model;
        $data = $this->request->data;
        $type = $this->request->type;
        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_from'])));
        $date_to = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_to'])));

        $results = array(
            'status' => false,
            'message' => __('missing_parameter'),
            'params' => array(),
        );

        $this->disableCache();

        //if( $this->request->is('get') ) {
            $result = $this->$model->get_data_today_tuckshop_export($data, 1, 2000, $this->lang18);

            $data_binding['dobMonths'] = $this->Common->get_list_month();


        $objItem = ClassRegistry::init('Pos.Item');
        $list_item = $objItem->get_list($this->lang18);

        $header_extra = array();
        foreach ($list_item as $k=>$v) {
            $item_code = $v['Item']['code'];
            $header_extra[] = array('label' => $item_code);
        }
        $data_binding['list_item'] = $list_item;
            //if ($result) {

                $cvs_data = array();

                foreach ($result as $row) {
                    $temp = $this->$model->format_data_today_tuckshop_export(array(), $row, $data_binding);

                    array_push($cvs_data, $temp);
                }

                try{
                    $file_name = $date_from. '-to-' .$date_to.'-tuckshop-sales';

                    // export xls
//                    if ($this->request->type == "xls") {
                    $excel_readable_header = array(
                        array('label' => 'Invoice Number'),
                        array('label' => 'Transaction Date'),
                        array('label' => 'Staff'),
                        array('label' => 'Total Amount'),
                        array('label' => 'Coupon Amount'),
                        array('label' => 'Discount Amount'),
                        array('label' => 'Paid Amount'),
                        array('label' => 'Payment Method'),
                        array('label' => 'Member Code'),
                        array('label' => 'Number Of Sales'),
                    );
                    $excel_readable_header = array_merge($excel_readable_header, $header_extra);

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
//        }

        $this->set(array(
            'results' => $results,
            '_serialize' => array('results')
        ));
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
                        'Order.id' => $data['trans_id'],
                        'Order.status' => 3,
                        'Order.void' => 0,
                    )
                );

                $data_order = $this->Order->find('first', $option);
                if (!isset($data_order['Order']['id']) || empty($data_order['Order']['id'])) {
                    $status = false;
                    $message = __('trans_not_found');
                    goto return_result;
                }

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $data_order_print = $this->Order->get_data_print_order($data['trans_id'], $this->Api->get_language(), true);

                $data_order['Order']['print_count'] = $data_order['Order']['print_count'] + 1;

                $dbo = $this->Order->getDataSource();
                $dbo->begin();
                try {
                    if ($this->Order->saveAll($data_order)) {
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
					$params = $data_order_print;
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
                        'OrderDetail',
                    ),
                    'conditions' => array(
                        'Order.id' => $data['trans_id'],
                    )
                );

                $data_order = $this->Order->find('first', $option);
                if (!isset($data_order['Order']['id']) || empty($data_order['Order']['id'])) {
                    $status = false;
                    $message = __('trans_not_found');
                    goto return_result;
                }

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $data['order_id'] = $data['trans_id'];
                $result = $this->Order->void_order($data, $data_order);

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

    public function api_hold_order() {
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
			} else if (!isset($data['order_id']) || empty($data['order_id'])) {
                $message = __('missing_parameter') .  __('order_id');
            } else if (!isset($data['remark']) || empty($data['remark'])) {
                $message = __('missing_parameter') .  __('remark');
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
                        'Order.id' => $data['order_id'],
                        'Order.void' => 0
                    )
                );

                $data_order = $this->Order->find('first', $option);
                if (!isset($data_order['Order']['id']) || empty($data_order['Order']['id'])) {
                    $status = false;
                    $message = __('trans_not_found');
                    goto return_result;
                }

                if ($data_order['Order']['status'] == 3) {
                    $status = false;
                    $message = __('trans_has_been_paid_cant_be_updated');
                    goto return_result;
                }

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->Order->hold_order($data, $data_order);

                $status = $result['status'];
				$message = $result['message'];

                if($result['status']){
					$params = $data_order;
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

    public function admin_report() {
        $data_search = $this->request->query;
        $model = $this->model;
        $now = date( 'Y-m-d' );
        $lang = 'zho';
        //$now = '2020-11-29';
        $time = date ('H:i');
        //$conditions = $this->Common->get_filter_conditions($data_search, $model, $languages_model, $this->filter);
        $conditions = array();

        $date_from = $date_to = $now;
        if (isset($data_search['date_report_from']) && !empty($data_search['date_report_from'])) {
            $date_from = $data_search['date_report_from'];
        }
        if (isset($data_search['date_report_to']) && !empty($data_search['date_report_to'])) {
            $date_to = $data_search['date_report_to'];
        }

        if (isset($data_search['date_report']) && !empty($data_search['date_report'])) {
            $now = $data_search['date_report'];
        }

        $results = array(
            'status' => false,
            'message' => __('missing_parameter'),
            'params' => array(),
        );

        $this->disableCache();

        if (!isset($data_search['type']))  {
            goto goto_view;
        }

        if ($data_search['type'] == 1) {
            $objReport = ClassRegistry::init('Pos.Report');
            $data  = $objReport->report_1($now, $lang);

            if (empty($data['result_format'])) {
                $this->Session->setFlash(__('dont_have_data_report_1'), 'flash/error');
                goto goto_view;
            }

            $this->Common->export_excel_report_1(
                $now,
                $data,
                'Report_Daily_Sales_Report_for_Management.xls',
                array()
            );

        }

        if ($data_search['type'] == 2) {
            $objReport = ClassRegistry::init('Pos.Report');
            $data  = $objReport->report_2($now, $lang);
            if (empty($data['result_format'])) {
                $this->Session->setFlash(__('dont_have_data_report_2'), 'flash/error');
                goto goto_view;
            }

            $this->Common->export_excel_report_2(
                $now,
                $data,
                'Report_Daily_Movie_Sales_Report.xls',
                array()
            );

        }

        if ($data_search['type'] == 3) {
            $objReport = ClassRegistry::init('Pos.Report');
            $data  = $objReport->report_3($now, $lang);
            if (empty($data['result_format'])) {
                $this->Session->setFlash(__('dont_have_data_report_3'), 'flash/error');
                goto goto_view;
            }

            $this->Common->export_excel_report_3(
                $now,
                $data,
                'Report_Monthly_Sales_Report.xls',
                array()
            );

        }

        if ($data_search['type'] == 4) {
            $objReport = ClassRegistry::init('Pos.Report');
            $data  = $objReport->report_4($now, $lang);
            if (empty($data['result_format'])) {
                $this->Session->setFlash(__('dont_have_data_report_4'), 'flash/error');
                goto goto_view;
            }

            $this->Common->export_excel_report_4(
                $now,
                $data,
                'Report_Daily_Sales_Report_for_Cinema_manager.xls',
                array()
            );

        }

        if ($data_search['type'] == 5) {
            $objReport = ClassRegistry::init('Pos.Report');
            $data  = $objReport->report_5($now, $lang);
            if (empty($data['result_format'])) {
                $this->Session->setFlash(__('dont_have_data_report_5'), 'flash/error');
                goto goto_view;
            }

            $this->Common->export_excel_report_5(
                $now,
                $data,
                'Report_Daily_Collection_Report.xls',
                array()
            );

        }

        if ($data_search['type'] == 6) {
            $objReport = ClassRegistry::init('Pos.Report');
            $data  = $objReport->report_6($now, $lang);
            if (empty($data['result_format'])) {
                $this->Session->setFlash(__('dont_have_data_report'), 'flash/error');
                goto goto_view;
            }

            $this->Common->export_excel_report_6(
                $now,
                $data,
                'Day End Report.xls',
                array()
            );

        }

        if ($data_search['type'] == 0) {

            $results = array(
                'status' => false,
                'message' => __('missing_parameter'),
                'params' => array(),
            );

            $this->disableCache();
            $data['Report']['report_date_from'] = $date_from;
            $data['Report']['report_date_to'] = $date_to;


            $result = $this->$model->get_data_today_ticket_export($data, 1, 2000, $lang);

            $data_binding['dobMonths'] = $this->Common->get_list_month();

            $cvs_data = array();

            foreach ($result as $row) {
                $temp = $this->$model->format_data_today_ticket_export(array(), $row, $data_binding);
                array_push($cvs_data, $temp);
            }

            try {
                $file_name = 'Sales_Raw_Data_Report_' . $date_from . '-to-' . $date_to . '-ticket-sales';

                $excel_readable_header = array(
                    array('label' => 'Invoice Number'),
                    array('label' => 'Transaction Date'),
                    array('label' => 'Transaction Time'),
                    array('label' => 'Staff'),
                    array('label' => 'Total Amount'),
                    array('label' => 'Coupon Amount'),
                    array('label' => 'Discount Amount'),
                    array('label' => 'Paid Amount'),
                    array('label' => 'Payment Method'),
                    array('label' => 'Cancel'),
                    array('label' => 'Member Code'),
                    array('label' => 'Movie'),
                    array('label' => 'Show Date'),
                    array('label' => 'Show Time'),
                    array('label' => 'Seats'),
                    array('label' => 'House'),
                    array('label' => 'Ticket Total'),
                    array('label' => 'Adult'),
                    array('label' => 'Student'),
                    array('label' => 'Senior'),
                    array('label' => 'Child'),
                );

                $this->Common->export_excel(
                    $cvs_data,
                    $file_name,
                    $excel_readable_header
                );
            } catch (Exception $e) {

                $this->LogFile->writeLog($this->LogFile->get_system_error(), $e->getMessage());
                $results = array(
                    'status' => false,
                    'message' => __('export_csv_fail'),
                    'params' => array()
                );
            }
        }
        goto_view:
        $this->set(compact('model', 'data_search'));
    }
}