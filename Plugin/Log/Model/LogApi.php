<?php
App::uses('LogAppModel', 'Log.Model');
/**
 * Log Model
 *
 * @property Company $Company
 * @property User $User
 * @property Type $Type
 */
class LogApi extends LogAppModel {
    public $actsAs = array('Containable');	// very import when using DB join table
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'plugin' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
                'required' => true,
			),
		),
		'controller' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
                'required' => true,
			),
		),
		'action' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
                'required' => true,
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
		'CreatedMember' => array(
			'className' => 'Member.Member',
			'foreignKey' => 'member_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

    public function add_log_api($data, $params, $success, $error, $old_record) {
		if (!($data && $params)) {
			return false;
        }

        $data['plugin'] = $params['plugin'];
        $data['controller'] = $params['controller'];
        $data['action'] = $params['action'];

        if($success){
            $data["statement"] = json_encode($success);
        }

        if($success){
            $data["error"] = json_encode($error);
        }

        if($success){
            $data["old_record"] = json_encode($old_record);
        }

        return $this->save($data);
    }

    public function get_distinct_field($field) {
        $data = $this->find('all', array(
            'fields' => array('DISTINCT (LogApi.' . $field . ') AS get_field' )
        ));
        
        $result = array();
        foreach($data as $item){
            array_push($result, $item['LogApi']['get_field']);
        }

        return $result;
    }

    public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
            'fields' => array( 'LogApi.*', 'Member.email'),
            'conditions' =>  $conditions, 
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
            'limit' => $limit,
            'page' => $page,
        );

        return $this->find('all', $all_settings);
    }

    public function format_data_export($data, $row){
        return array(
            @$row['LogApi']['id'],
            isset($data['list_index_companies'][$row['LogApi']['company_id']]) ? $data['list_index_companies'][$row['LogApi']['company_id']] : '',
            $row['LogApi']['plugin'],
            $row['LogApi']['controller'],
            $row['LogApi']['action'],
            !empty($row['LogApi']['received_params']) ? $row['LogApi']['received_params'] : '',
            !empty($row['LogApi']['success']) ? $row['LogApi']['success'] : '',
            !empty($row['LogApi']['error']) ? $row['LogApi']['error'] : '',
            !empty($row['LogApi']['old_data']) ? $row['LogApi']['old_data'] : '',
            !empty($row['LogApi']['new_data']) ? $row['LogApi']['new_data'] : '',
            $row['LogApi']['enabled'] == 1 ? 'Y' : 'N',
            $row['LogApi']['archived'] == 1 ? 'Y' : 'N',
            !empty($row['LogApi']["created"]) ? $row['LogApi']["created"] : '',
            !empty($row['Member']["email"]) ? $row['Member']["email"] : '',
        );
    }
}