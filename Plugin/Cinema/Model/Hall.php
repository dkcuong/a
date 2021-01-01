<?php
App::uses('CinemaAppModel', 'Cinema.Model');

class Hall extends CinemaAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'Cinema' => array(
			'className' => 'Cinema.Cinema',
			'foreignKey' => 'cinema_id',
			'conditions' => '',
			'order' => ''
		),
		'CreatedBy' => array(
			'className' => 'Administration.Administrator',
			'foreignKey' => 'created_by',
			'conditions' => '',
			'fields' => array('email','name'),
			'order' => ''
		),
		'UpdatedBy' => array(
			'className' => 'Administration.Administrator',
			'foreignKey' => 'updated_by',
			'conditions' => '',
			'fields' => array('email','name'),
			'order' => ''
		),
	);

	public $hasMany = array(
		'HallDetail' => array(
			'className' => 'Cinema.HallDetail',
			'foreignKey' => 'hall_id',
			'dependent' => true,
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

	public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
			'fields' => array(
				'Hall.*'
			),
			'contain' => array(
                'CreatedBy',
                'UpdatedBy'
			),
            'conditions' => $conditions,
            'order' => array( 'Hall.code' => 'asc' ),
            'limit' => $limit,
            'page' => $page
        );

		return $this->find('all', $all_settings);
	}

	public function format_data_export($data, $row){
		$model = $this->alias;

        return array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row[$model]["code"]) ?  $row[$model]["code"] : ' ',
			!empty($row[$model]["max_seat"]) ?  $row[$model]["max_seat"] : ' ',
            $row[$model]['enabled'] == 1 ? 'Y' : 'N',
        );
    }

    public function get_list_hall($data = array()){
        $conditions = array('Hall.enabled' => true);
        $result = array();

        if (isset($data['is_api']) && $data['is_api'] == true) {
            $result_temp = $this->find('all', array(
                'fields' => array('id', 'code'),
                'conditions' => $conditions,
            ));

            foreach ($result_temp as $k => $v) {
                $result[$k] = $v['Hall'];
            }
        } else {
            $result = $this->find('list', array(
                'fields' => array('id', 'code'),
                'conditions' => $conditions,
            ));
        }

        return $result;
    }
}
