<?php
App::uses('CinemaAppController', 'Cinema.Controller');

class StaffLogsController extends CinemaAppController {

	public $components = array('Paginator');
	private $model = 'StaffLog';
	private $staff_model= 'Staff';	

	// private $filter = array(
	// 	'name',
	// );
	// private $rule = array(
	// 	1 => array('required'),
	// 	2 => array('required','enum'),
	// );
	// private $rule_spec = array(
	// 	2 => array('N', 'Y', 'y', 'n')
	// );

	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('place', 'staff_log_title'));
	}

	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;
		$staff_model = $this->staff_model;

		//$conditions = $this->Common->get_filter_conditions($data_search, $model, $model, $this->filter);

		$conditions = [];

		if (isset($data_search) && !empty($data_search['name']))
		{
			$conditions[$staff_model.'.name LIKE'] = '%' . $data_search['name'] . '%';
		}

		if (isset($data_search) && !empty($data_search['clock_in_from']))
		{
			$conditions[$model.'.clock_in >='] = $data_search['clock_in_from'];
		}

		if (isset($data_search) && !empty($data_search['clock_in_to']))
		{
			$conditions[$model.'.clock_in <='] = $data_search['clock_in_to'];
		}

		if (isset($data_search) && !empty($data_search['clock_out_from']))
		{
			$conditions[$model.'.clock_out >='] = $data_search['clock_out_from'];
		}

		if (isset($data_search) && !empty($data_search['clock_out_to']))
		{
			$conditions[$model.'.clock_out <='] = $data_search['clock_out_to'];
		}

		if ($data_search){
			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'cinema',
                    'controller' => 'staff_logs',
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
                    'controller' => 'staff_logs',
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
			'contain' => [
				$staff_model => [
					'fields' => ['name']
				]
			],
			'conditions' => array($conditions),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.id' => 'DESC'),
		);

        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'staff_model', 'data_search'));	
	}

	public function admin_view($id) {
		$model = $this->model;
		$staff_model = $this->staff_model;

		$options = array(
			'fields' => array($model.'.*'),
			'contain' => array(
				$staff_model => [
					'fields' => [
						'name'
					]
				],
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

        $this->set(compact('model', 'staff_model'));
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
		   $result = $this->$model->get_data_export($this->request->conditions, 1, 2000, $this->lang18);

		   if ($result) {

				$cvs_data = array();

				foreach ($result as $row) {
					$temp = $this->$model->format_data_export(array(), $row);

					array_push($cvs_data, $temp);
				}

			   try{
				   $file_name = 'staff_logs_'.date('Ymd');

				   // export xls
				   if ($this->request->type == "xls") {
						$excel_readable_header = array(
							array('label' => __('id')),
							array('label' => __('name')),
							array('label' => __('clock_in')),
							array('label' => __('clock_out'))
						);
	
						$this->Common->export_excel(
							$cvs_data,
							$file_name,
							$excel_readable_header
						);
					} else {
						$header = array(
							'label' => __('id'),
							'label' => __('name'),
							'label' => __('clock_in'),
							'label' => __('clock_out')
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
}
