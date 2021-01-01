<?php
App::uses('ContentAppController', 'Content.Controller');
/**
 * PromotionAds Controller
 *
 * @property PromotionAd $PromotionAd
 * @property PaginatorComponent $Paginator
 */
class PromotionAdsController extends ContentAppController {
    public $components = array('Paginator');
    private $model = 'PromotionAd';

    private $filter = array(
        'link',
        'description',
    );
    private $rule = array(
        1 => array('required'),
        3 => array('required','enum'),
    );
    private $rule_spec = array(
        3 => array('N', 'Y', 'y', 'n')
    );

    private $upload_path = 'content';
    private $poster_prefix = 'promotion-ad';

    public function beforeFilter(){
        parent::beforeFilter();
        $this->set('title_for_layout', __d('promotion_ad', 'item_title'));
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
                    'plugin' => 'content',
                    'controller' => 'promotion_ads',
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
                    'plugin' => 'content',
                    'controller' => 'promotion_ads',
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
            'fields' => array(),
            'joins' => array(
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

        $this->set('dbdata', $model_data);

        $this->set(compact('model'));
    }

    public function admin_add() {
        $model = $this->model;

        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data;

            $valid = true;

            $this->upload_poster($data, $valid, $extra_errors);

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

        $display_list = array(
            'web' => __('web'),
            'mobile' => __('mobile')
        );

        $this->set(compact('model', 'display_list'));
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

        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data;

            $valid = true;

            $this->upload_poster($data, $valid, $extra_errors, $old_item[$model]['image']);

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

        $display_list = array(
            'web' => __('web'),
            'mobile' => __('mobile')
        );

        $this->set(compact('model', 'display_list'));
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
        $options = array(
            'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
            'recursive' => 1
        );
        $old_item = $this->$model->find('first', $options);

        if ($this->$model->delete()) {
            $file = new File( 'img/'.$old_item[$model]['image'] );

            $file->delete();

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
                    $file_name = 'promotion_ad_'.date('Ymd');

                    // export xls
                    if ($this->request->type == "xls") {
                        $excel_readable_header = array(
                            array('label' => __('id')),
                            array('label' => __('link')),
                            array('label' => __('description')),
                            array('label' => __('enabled'))
                        );

                        $this->Common->export_excel(
                            $cvs_data,
                            $file_name,
                            $excel_readable_header
                        );
                    } else {
                        $header = array(
                            array('label' => __('id')),
                            array('label' => __('link')),
                            array('label' => __('description')),
                            'label' => __('enabled'),
                            array('label' => __('enabled'))
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

            $objresult = $this->Common->upload_and_read_excel($data['PromotionAd'], '');

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

                        $data_insert[$model]['link'] = $obj[1];
                        $data_insert[$model]['description'] = $obj[2];
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

    private function upload_poster(&$data, &$valid, &$extra_errors, $old_poster = null)
    {
        $uploaded_poster = $data[$this->model]['image'];

        if (!empty($uploaded_poster) && $uploaded_poster['tmp_name'])
        {
            if (!preg_match('/image\/*/', $uploaded_poster['type']))
            {
                $valid = false;
                $extra_errors .= 'Wrong image type. ';
            }
            else
            {
                $uploaded = $this->Common->upload_images( $uploaded_poster, $this->upload_path, $this->poster_prefix );

                if( isset($uploaded['status']) && ($uploaded['status'] == true) )
                {
                    $data[$this->model]['image'] = $uploaded['params']['path'];
                    $data[$this->model]['image'] = str_replace("\\",'/',$data[$this->model]['image']);

                    if (!empty($old_poster)) {
                        $file = new File( 'img/'. $old_poster );
                        $file->delete();
                    }
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
                unset($data[$this->model]['image']);
            }
            else
            {
                $data[$this->model]['image'] = $old_poster;
            }
        }
    }

    public function api_get_list() {
        $this->Api->init_result();
        $model = $this->model;

        if ($this->request->is('post'))
        {
            $this->disableCache();
            $data   = $this->request->data;

            $status = true;
            $message = __('retrieve_data_successfully');
            $params = (object)array();

//            if( ! isset($data['schedule_detail_id']) || empty($data['schedule_detail_id']) ){
//                $message = __('missing_parameter') .  __('schedule_detail_id');
//            } else {
                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->get_list($data);

                $status = $status;
                $message = $message;

                if ($status) {
                    $params = $result;
                    if (!$params) {
                        $params = (object)array();
                    }

                } else {
//                    if (isset($result['log_data']) && $result['log_data']) {
//                        $this->Api->set_error_log($result['log_data']);
//                    }
                }
//            }
            $this->Api->set_result(true, $message, $params);
        }

        $this->Api->output();
    }
}
