<?php
App::uses('CinemaAppController', 'Cinema.Controller');

class EquipmentsController extends CinemaAppController {

	public $components = array('Paginator');
	private $model = 'Equipment';

	public function beforeFilter(){
		parent::beforeFilter();
		$this->set('title_for_layout', __d('place', 'hall_title'));
	}


    public function api_get_list_equipment() {
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
            $result = $this->$model->get_list_equipment($data);
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
