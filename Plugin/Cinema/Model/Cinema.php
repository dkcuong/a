<?php
App::uses('CinemaAppModel', 'Cinema.Model');

class Cinema extends CinemaAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
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
		'Hall' => array(
			'className' => 'Cinema.Hall',
			'foreignKey' => 'cinema_id',
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

	public function get_active_cinema_list() {
		$option_cinema = array(
			'fields' => array("Cinema.id", "Cinema.code"),
			'conditions' => array(
				'enabled' => 1
			)
		);

		return $this->find('list', $option_cinema);
	}

	public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
			'fields' => array(
				'Cinema.*'
			),
			'contain' => array(
                'CreatedBy',
                'UpdatedBy'
			),
            'conditions' => $conditions,
            'order' => array( 'Cinema.code' => 'asc' ),
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
			!empty($row[$model]["address"]) ?  $row[$model]["address"] : ' ',
			!empty($row[$model]["location"]) ?  $row[$model]["location"] : ' ',
			!empty($row[$model]["description"]) ?  $row[$model]["description"] : ' ',
			!empty($row[$model]["phone"]) ?  $row[$model]["phone"] : ' ',
			!empty($row[$model]["email"]) ?  $row[$model]["email"] : ' ',
            $row[$model]['enabled'] == 1 ? 'Y' : 'N',
        );
    }
}
