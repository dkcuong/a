<?php
App::uses('CinemaAppController', 'Cinema.Controller');
/**
 * BookingEnquiries Controller
 *
 * @property BookingEnquiry $BookingEnquiry
 * @property PaginatorComponent $Paginator
 */
class BookingEnquiriesController extends CinemaAppController {

    public $components = array('Paginator');
    private $model = 'BookingEnquiry';

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
        $this->set('title_for_layout', __d('booking_enquiry', 'item_title'));
    }

    public function admin_index() {
        $data_search = $this->request->query;
        $model = $this->model;

        $condition_search = $data_search;
        $conditions = array();
        if (isset($condition_search['name']) && !empty($condition_search['name']))
        {
            $conditions[] = 'BookingEnquiry.name LIKE "%' . $condition_search['name'] . '%"';
//            unset($condition_search['name']);
        }

        if (isset($condition_search['email']) && !empty($condition_search['email']))
        {
            $conditions[] = 'BookingEnquiry.email LIKE "%' . $condition_search['email'] . '%"';
//            unset($condition_search['email']);
        }

        if (isset($condition_search['phone']) && !empty($condition_search['phone']))
        {
            $conditions[] = 'BookingEnquiry.phone LIKE "%' . $condition_search['phone'] . '%"';
//            unset($condition_search['phone']);
        }

        if (isset($condition_search['hall_id']) && !empty($condition_search['hall_id']))
        {
            $conditions[] = 'BookingEnquiry.hall_id =' . $condition_search['hall_id'];
//            unset($condition_search['hall_id']);
        }

        if (isset($condition_search['date']) && !empty($condition_search['date']))
        {
            $conditions[] = 'date(BookingEnquiry.date) = "' . $condition_search['date'] . '"';
//            unset($condition_search['date']);
        }

//        $conditions_temp = $this->Common->get_filter_conditions($condition_search, $model, $model, $this->filter);
//        $conditions = array_merge($conditions, $conditions_temp);

        if ($data_search){
            // button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'cinema',
                    'controller' => 'booking_enquiries',
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
                    'controller' => 'booking_enquiries',
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
                'Equipment',
                'Item',
                'Hall' => array(
                    'fields' => 'Hall.*'
                )
            ),
            'limit' => Environment::read('web.limit_record'),
            'order' => array($model . '.name' => 'ASC'),
        );

        $objHall = ClassRegistry::init('Cinema.Hall');
        $hall_list =  $objHall->get_list_hall();
        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'data_search' , 'hall_list'));
    }

    public function admin_view($id) {
        $model = $this->model;
        $languages_model = $this->model_lang;

        $options = array(
            'fields' => array($model.'.*'),
            'contain' => array(
                'Equipment',
                'Item',
                'Hall' => array(
                    'fields' => 'Hall.*'
                ),
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

    }

    public function admin_edit($id = null) {

    }

    public function admin_delete($id = null) {

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
                    $file_name = 'booking_enquiries_'.date('Ymd');

                    // export xls
                    if ($this->request->type == "xls") {
                        $excel_readable_header = array(
                            array('label' => __('id')),
                            array('label' => __('title')),
                            array('label' => __('name')),
                            array('label' => __('email')),
                            array('label' => __('country_code')),
                            array('label' => __('phone')),
                            array('label' => __('date')),
                            array('label' => __d('booking_enquiry', 'time_from')),
                            array('label' => __d('booking_enquiry', 'time_to')),
                            array('label' => __d('booking_enquiry', 'event_purpose')),
                            array('label' => __d('booking_enquiry', 'movie_name')),
                            array('label' => __d('booking_enquiry', 'no_of_attendee')),
                            array('label' => __d('place', 'hall_title')),
                            array('label' => __d('booking_enquiry', 'special_request')),
                            array('label' => __d('booking_enquiry', 'equipment')),
                            array('label' => __d('booking_enquiry', 'item')),
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
                            'label' => __('date'),
                            'label' => __d('booking_enquiry', 'time_from'),
                            'label' => __d('booking_enquiry', 'time_to'),
                            'label' => __d('booking_enquiry', 'event_purpose'),
                            'label' => __d('booking_enquiry', 'movie_name'),
                            'label' => __d('booking_enquiry', 'no_of_attendee'),
                            'label' => __d('place', 'hall_title'),
                            'label' => __d('booking_enquiry', 'special_request'),
                            'label' => __d('booking_enquiry', 'equipment'),
                            'label' => __d('booking_enquiry', 'item'),
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

    }

    public function api_create_booking_enquiry() {
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
                $message = __('missing_parameter') . __('language');
                goto return_result;
            } else {
                $this->Api->set_language($data['language']);

                if (isset($data['equipment_list']) && !empty($data['equipment_list'])) {
                    $data['equipment_list'] = json_decode($data['equipment_list'], true);
                }

                if (isset($data['item_list']) && !empty($data['item_list'])) {
                    $data['item_list'] = json_decode($data['item_list'], true);
                }

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);


                $objHall = ClassRegistry::init('Cinema.Hall');
                $list_hall = $objHall->get_list_hall();

                $objItem = ClassRegistry::init('Pos.Item');
                $list_item = $objItem->get_list_item_booking_enquiry();

                $objEquipment = ClassRegistry::init('Cinema.Equipment');
                $list_equipment = $objEquipment->get_list_equipment();

                $result = $this->$model->create_booking_enquiry($data);

                $status = $result['status'];
                $message = $result['message'];

                if($status){
                    $params = $result['params'];

                    $email = Environment::read('email.booking_receiver') ;

                    if (empty($email)) {
                        goto return_result;
                    }

                    $data['hall_display'] = $data['equipment_display'] = $data['item_display'] = null;

                    if (isset($data['hall_id']) && ! empty($data['hall_id'])) {
                        $data['hall_display'] = isset($list_hall[$data['hall_id']]) ? $list_hall[$data['hall_id']] : null;
                    }
                    if (isset($data['equipment_list']) && ! empty($data['equipment_list'])) {
                        foreach ($data['equipment_list'] as $k => $v) {
                            $temp = isset($list_equipment[$v]) ? $list_equipment[$v] : null;
                            if ($temp) {
                                $data['equipment_display'][] = $temp;
                            }
                        }
                        $data['equipment_display'] = implode(', ', $data['equipment_display']);
                    }
                    if (isset($data['item_list']) && ! empty($data['item_list'])) {
                        foreach ($data['item_list'] as $k => $v) {
                            $temp = isset($list_item[$v]) ? $list_item[$v] : null;
                            if ($temp) {
                                $data['item_display'][] = $temp;
                            }
                        }
                        $data['item_display'] = implode(', ', $data['item_display']);
                    }

                    $template = 'send_booking_enquiry';
                    $subject = 'ACX-Cinema - Booking Enquiry';
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

