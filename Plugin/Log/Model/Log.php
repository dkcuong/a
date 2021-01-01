<?php
App::uses('LogAppModel', 'Log.Model');
/**
 * Log Model
 *
 * @property Company $Company
 * @property User $User
 * @property Type $Type
 */
class Log extends LogAppModel {


    public $actsAs = array('Containable');	// very import when using DB join table
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'archived' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'enabled' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'CreatedBy' => array(
			'className' => 'Administration.Administrator',
			'foreignKey' => 'created_by',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
    );
    
    public $hasMany = array(
		'LogSuccess' => array(
			'className' => 'Log.LogSuccess',
			'foreignKey' => 'log_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
        ),
		'LogError' => array(
			'className' => 'Log.LogError',
			'foreignKey' => 'log_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
        ),
    );

    /**
     * params: 
     * $data is data of administrator access(browser, version, platform)
     * $params are plugin, controller, action
     * $success is success info or data
     * $error is error info or data
     * $new_data: if have any data change or add new (update, add)
     * $old_data: if have any data change => put old data to this param (update)
     */
    public function add_log_admin($data, $params, $success, $error, $new_data, $old_data, $maximum_number = null) {
		if (!($data && $params)) {
			return false;
        }

        if(!$maximum_number){
            $maximum_number = 4642; // max length of text is over 65k, max length of id is 11, double comman, one comman
        }

        $data['plugin'] = $params['plugin'];
        $data['controller'] = $params['controller'];
        $data['action'] = $params['action'];

        if($new_data){
            $data["new_data"] = json_encode($new_data);
        }

        if($old_data){
            $data["old_data"] = json_encode($old_data);
        }

        $log_saved = $this->save($data);

        if(!$log_saved){
            return false;
        }

        $log_id = $log_saved['Log']['id'];

        if($success){
            $skip = 0;
            $log_success = array();
            do{
                $arr_success = array_slice($success, $skip, $maximum_number);
                $log_success[] = array(
                    'log_id' => $log_id,
                    'content' => json_encode($arr_success)
                );

                $skip += $maximum_number;
            }while($skip < count($success));

            if(!$this->LogSuccess->saveAll($log_success)){
                return false;
            }
        }

        if($error){
            $skip = 0;
            $log_error = array();
            do{
                $arr_error = array_slice($error, $skip, $maximum_number);
                $log_error[] = array(
                    'log_id' => $log_id,
                    'content' => json_encode($arr_error)
                );

                $skip += $maximum_number;
            }while($skip < count($error));

            if(!$this->LogError->saveAll($log_error)){
                return false;
            }
        }

        return true;
    }

    public function get_distinct_field($field) {
        $data = $this->find('all', array(
            'fields' => array('DISTINCT (Log.' . $field . ') AS get_field' ),
            'conditions' => array(
                $field . ' <> ' => ''
            )
        ));
        
        $result = array();
        foreach($data as $item){
            array_push($result, $item['Log']['get_field']);
        }

        return $result;
    }

    public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
            'conditions' => $conditions,
            'contain' => array('CreatedBy'), 
            'order' => array( 'Log.id' => 'desc' ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
    }

    public function format_data_export($data, $row){
        return array(
            $row['Log']['id'],
            isset($data['list_index_companies'][$row['Log']['company_id']]) ? $data['list_index_companies'][$row['Log']['company_id']] : '',
            isset($data['list_index_brands'][$row['Log']['brand_id']]) ? $data['list_index_brands'][$row['Log']['brand_id']] : '',
            $row['Log']['plugin'],
            $row['Log']['controller'],
            $row['Log']['action'],
            !empty($row['Log']['statement']) ? $row['Log']['statement'] : '',
            !empty($row['Log']['error']) ? $row['Log']['error'] : '',
            !empty($row['Log']['old_record']) ? $row['Log']['old_record'] : '',
            !empty($row['Log']['remark']) ? $row['Log']['remark'] : '',
            !empty($row['Log']['remote_ip']) ? $row['Log']['remote_ip'] : '',
            !empty($row['Log']['agent']) ? $row['Log']['agent'] : '',
            !empty($row['Log']['version']) ? $row['Log']['version'] : '',
            !empty($row['Log']['platform']) ? $row['Log']['platform'] : '',
            !empty($row['Log']['browser']) ? $row['Log']['browser'] : '',
            ($row['Log']['enabled'] == 1 ? 'Y' : 'N'),
            ($row['Log']['archived'] == 1 ? 'Y' : 'N'),
            !empty($row['Log']["created"]) ? $row['Log']["created"] : '',
            !empty($row['CreatedBy']["email"]) ? $row['CreatedBy']["email"] : '',
        );
    }
}