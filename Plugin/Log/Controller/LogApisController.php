<?php
App::uses('LogAppController', 'Log.Controller');
/**
 * Logs Controller
 *
 * @property Log $Log
 * @property PaginatorComponent $Paginator
 */
class LogApisController extends LogAppController {

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
        
        if($this->company_id){
            $conditions['Log.company_id'] = $this->company_id;
        }

        if(isset($data_search['start_period']) && $data_search['start_period'] != "" ){
            $conditions['LogApi.created >= '] = $data_search['start_period'];
        }

        if(isset($data_search['end_period']) && $data_search['end_period'] != "" ){
            $conditions['LogApi.created <= '] = $data_search['end_period'];
        }

        if(isset($data_search['email_filter']) && $data_search['email_filter'] != "" ){
            $conditions['Member.email LIKE'] = '%' . $data_search['email_filter'] . '%';
        }
        
        if(isset($data_search['plugin']) && $data_search['plugin'] != "" ){
            $conditions['LogApi.plugin'] = $data_search['plugin'];
        }

        if(isset($data_search['controller']) && $data_search['controller'] != "" ){
            $conditions['LogApi.controller'] = $data_search['controller'];
        }

        if(isset($data_search['action']) && $data_search['action'] != "" ){
            $conditions['LogApi.action'] = $data_search['action'];
        }
        
        if( isset($data_search['button_export_csv']) && !empty($data_search['button_export_csv']) ){
            if($data_search['choose_id']){
                $conditions = array('Log.id' => $data_search['choose_id']);
            }

			$sent = $this->requestAction(array(
				'plugin' => 'log',
				'controller' => 'log_apis',
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
            if($data_search['choose_id']){
                $conditions = array('Log.id' => $data_search['choose_id']);
            }

			$sent = $this->requestAction(array(
				'plugin' => 'log',
				'controller' => 'log_apis',
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
            'fields' => array( 'LogApi.*', 'Member.email'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'alias' => 'Member',
                    'table' => Environment::read('table_prefix') . 'members',
                    'type' => 'left',
                    'conditions' => array(
                        'Member.id = LogApi.member_id',
                    ),
                ),
            ),
            'order' => array( 'LogApi.id' => 'desc' ),
		);

        $logs = $this->paginate();

        // format get list company name
        $company_ids = Hash::extract($logs, '{n}.LogApi.company_id');
        $list_index_companies = array();
        if($company_ids){
            $list_index_companies = $this->LogApi->Company->get_company_list(array('Company.id IN' => $company_ids), $this->lang18);
        }

        $plugins = $this->LogApi->get_distinct_field('plugin');
        $controllers = $this->LogApi->get_distinct_field('controller');
        $actions = $this->LogApi->get_distinct_field('action');
        
        $color_actions = array(
            'admin_add' => 'label-success',
            'admin_edit' => 'label-warning',
            'admin_delete' => 'label-danger',
        );

        $column_cache = json_encode($this->Redis->get_cache('booster_column', '_log_apis'));

        $this->set(compact('logs', 'data_search', 'plugins', 'controllers', 'actions', 'color_actions', 'list_index_companies', 'column_cache'));
	}

    public function admin_export() {
        $this->disableCache();

        if( $this->request->is('get') ) {
            $limit = 2000;
            $header = array(
                __('id'),
                __('company_id'),
                __('plugin'),
                __('controller'),
                __('action'),
                __('received_params'),
                __('success'),
                __('error'),
                __d('log', 'old_record'),
                __d('log', 'new_record'),
                __('enabled'),
                __('archived'),
                __('created'),
                __('created_by'),
            );
            
            $objCompany = ClassRegistry::init('Company.Company');
            $list_index_companies = $objCompany->get_company_list(array(), $this->lang18);
            
            try{
                $file_name = 'logapi_' . date('Ymdhis');
                $data_binding = array(
                    'list_index_companies' => $list_index_companies,
                );

                // export xls
                if ($this->request->type == "xls") {
                    $this->ExcelSpout->setup_export_excel($header, 'Log.LogApi', $data_binding, $this->request->conditions, $limit, $file_name, $this->lang18);
                } else {
                    $this->Common->setup_export_csv($header, 'Log.LogApi', $data_binding, $this->request->conditions, $limit, $file_name, $this->lang18);
                }
                exit;
            } catch ( Exception $e ) {
                $this->LogFile->writeLog($this->LogFile->get_system_error(), $e->getMessage());
                $this->Session->setFlash(__('export_csv_fail') . ": " . $e->getMessage(), 'flash/error');
            }
        }
    }

    /**
     * admin_view method
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
	public function admin_view($id = null) {
		if (!$this->LogApi->exists($id)) {
			throw new NotFoundException(__('invalid_data'));
        }

        $options = array(
            'conditions' => array('LogApi.' . $this->LogApi->primaryKey => $id),
            'contain' => array( 'CreatedMember' )
        );

        $log = $this->LogApi->find('first', $options);

        // get company and brand
        $list_index_companies = array();
        if($log['LogApi']['company_id']){
            $list_index_companies = $this->LogApi->Company->get_company_list(array('Company.id' => $log['LogApi']['company_id']), $this->lang18);
        }
        
        $received_params = array();
        $success_data = array();
        $error_data = array();
        $new_data = array();
        $old_data = array();
        if($log['LogApi']['received_params']) {
            $received_params = json_decode($log['LogApi']['received_params']);
            unset($log['LogApi']['received_params']);
        }

        if($log['LogApi']['new_data']) {
            $new_data = json_decode($log['LogApi']['new_data']);
            unset($log['LogApi']['new_data']);
        }

        if($log['LogApi']['old_data']) {
            $old_data = json_decode($log['LogApi']['old_data']);
            unset($log['LogApi']['old_data']);
        }

        if($log['LogApi']['success']) {
            $success_data = json_decode($log['LogApi']['success']);
            unset($log['LogApi']['success']);
        }

        if($log['LogApi']['error']) {
            $error_data = json_decode($log['LogApi']['error']);
            unset($log['LogApi']['error']);
        }

		$this->set(compact('log', 'received_params', 'new_data', 'old_data', 'success_data', 'error_data', 'list_index_companies'));
	}
}
