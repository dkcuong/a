<?php
App::uses('SettingAppController', 'Setting.Controller');
/**
 * Settings Controller
 *
 * @property Setting $Setting
 * @property PaginatorComponent $Paginator
 */
class DistrictsController extends SettingAppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

	private $model = 'District';

	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('setting', 'item_title'));
	}

	public function api_get_district() {
        $this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
			
			if (isset($data['language']) && !empty($data['language'])) {
				$this->Api->set_language($data['language']);
			} else {
				$this->Api->set_language($this->lang18);
			}

			$url_params = $this->request->params;
			$this->Api->set_post_params($url_params, $data);
			$this->Api->set_save_log(true);

			$result = $this->$model->get_active_districts($this->Api->get_language());
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
