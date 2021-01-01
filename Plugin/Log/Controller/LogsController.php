<?php
App::uses('LogAppController', 'Log.Controller');
/**
 * Logs Controller
 *
 * @property Log $Log
 * @property PaginatorComponent $Paginator
 */
class LogsController extends LogAppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
    public $helpers = array('Html','Form','Csv','Cache');


/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
        $data_search = $this->request->query;
        $conditions = array();

        if(isset($data_search['start_period']) && $data_search['start_period'] != "" ){
            $conditions['Log.created >= '] = $data_search['start_period'];
        }

        if(isset($data_search['end_period']) && $data_search['end_period'] != "" ){
            $conditions['Log.created <= '] = $data_search['end_period'];
        }

        if(isset($data_search['user_id']) && $data_search['user_id'] != "" ){
            $conditions['Log.created_by'] = $data_search['user_id'];
        }
        
        if(isset($data_search['plugin']) && $data_search['plugin'] != "" ){
            $conditions['Log.plugin'] = $data_search['plugin'];
        }

        if(isset($data_search['controller']) && $data_search['controller'] != "" ){
            $conditions['Log.controller'] = $data_search['controller'];
        }

        if(isset($data_search['action']) && $data_search['action'] != "" ){
            $conditions['Log.action'] = $data_search['action'];
        }
        
        if( isset($data_search['button_export_csv']) && !empty($data_search['button_export_csv']) ){
            if(isset($data_search['choose_id']) && $data_search['choose_id']){
                $conditions = array('Log.id' => $data_search['choose_id']);
            }

			$sent = $this->requestAction(array(
				'plugin' => 'log',
				'controller' => 'logs',
				'action' => 'export',
				'admin' => true,
				'prefix' => 'admin',
				'ext' => 'json'
			), array(
                'conditions' => $conditions,
				'type' => 'csv',
				'language' => $this->lang18,
			));
		}

        if( isset($data_search['button_export_excel']) && !empty($data_search['button_export_excel']) ){
            if(isset($data_search['choose_id']) && $data_search['choose_id']){
                $conditions = array('Log.id' => $data_search['choose_id']);
            }

			$sent = $this->requestAction(array(
				'plugin' => 'log',
				'controller' => 'logs',
				'action' => 'export',
				'admin' => true,
				'prefix' => 'admin',
				'ext' => 'json'
			), array(
                'conditions' => $conditions,
				'type' => 'xls',
				'language' => $this->lang18,
			));
        }
  
        $this->Paginator->settings = array(
            'conditions' => $conditions,
            'contain' => array( 'CreatedBy' ),
            'order' => array( 'Log.id' => 'desc' ),
		);

        $logs = $this->paginate();

        $admin_ids = $this->Log->get_distinct_field('created_by');
        $users = $this->Log->CreatedBy->find_list(array('id' => $admin_ids));
        $plugins = $this->Log->get_distinct_field('plugin');
        $controllers = $this->Log->get_distinct_field('controller');
        $actions = $this->Log->get_distinct_field('action');
        
        $color_actions = array(
            'admin_add' => 'label-success',
            'admin_edit' => 'label-warning',
            'admin_delete' => 'label-danger',
        );

        $column_cache = json_encode($this->Redis->get_cache('booster_column', '_logs'));
        $this->set(compact('logs', 'data_search', 'users', 'color_actions', 'plugins', 'controllers', 'actions', 'column_cache'));
	}

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
	public function admin_view($id = null) {
		if (!$this->Log->exists($id)) {
			throw new NotFoundException(__('invalid_data'));
        }
        
        $contain = array(
            'CreatedBy',
            'LogSuccess',
            'LogError'
        );

        $options = array(
            'conditions' => array('Log.' . $this->Log->primaryKey => $id),
            'contain' => $contain
        );

        $log = $this->Log->find('first', $options);
        
        $success_data = array();
        $error_data = array();
        $new_data = array();
        $old_data = array();
        if($log['LogSuccess']) {
            foreach($log['LogSuccess'] as $item){
                $success_data[] = json_decode($item['content']);
            }
            unset($log['LogSuccess']);
        }

        if($log['LogError']) {
            foreach($log['LogError'] as $item){
                $error_data[] = json_decode($item['content']);
            }
            unset($log['LogError']['content']);
        }

        if($log['Log']['new_data']) {
            $new_data = json_decode($log['Log']['new_data']);
            unset($log['Log']['new_data']);
        }

        if($log['Log']['old_data']) {
            $old_data = json_decode($log['Log']['old_data']);
            unset($log['Log']['old_data']);
        }

		$this->set(compact('log', 'success_data', 'error_data', 'new_data', 'old_data'));
    }

    public function admin_exec_cronjob(){
        if(!Environment::read('physical_source')){
            echo sprintf(__('item_is_not_exist'), " physical_source");
            die;
        }
        
        if(!isset($this->request->query['shell']) || empty($this->request->query['shell'])){
            echo sprintf(__('item_is_not_exist'), " shell");
            die;
        }

        $this->autoRender = false;
        $file_path = Environment::read('physical_source') . "Console/cake";
        if(!file_exists($file_path . '.php')){
            echo 'file not exist';
            die;
        }
        $cmd = $file_path . " " . $this->request->query['shell'];
        if(Environment::is('staging')){
            $cmd .= " staging";
        }else if(Environment::is('production')){ 
            $cmd .= " production";
        }

        $output = array();
        $return_value = 0;

        exec($cmd, $output, $return_value);

        pr($output);
        echo $return_value . "<br/>";
        echo 'execute cronjob ' . $this->request->query['shell'] . ' is success';
    }
}
