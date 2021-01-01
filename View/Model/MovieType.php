<?php
App::uses('MovieAppModel', 'Movie.Model');

class MovieType extends MovieAppModel {

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
	);

	public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
			'fields' => array(
				'MovieType.*'
			),
			'contain' => array(
                'CreatedBy',
                'UpdatedBy'
			),
            'conditions' => $conditions,
            'order' => array( 'MovieType.name' => 'asc' ),
            'limit' => $limit,
            'page' => $page
        );

		return $this->find('all', $all_settings);
	}

	public function format_data_export($data, $row){
		$model = $this->alias;

        return array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row[$model]["name"]) ?  $row[$model]["name"] : ' ',
            $row[$model]['enabled'] == 1 ? 'Y' : 'N',
        );
    }

	public function get_list_movie_types($data = array()){
        $conditions = array('MovieType.enabled' => true);
        $result = array();

        if (isset($data['is_api']) && $data['is_api'] == true) {
            $result_temp = $this->find('all', array(
                'fields' => array('id', 'name'),
                'conditions' => $conditions,
            ));

            foreach ($result_temp as $k => $v) {
                $result[$k] = $v['MovieType'];
            }
        } else {
            $result = $this->find('list', array(
                'fields' => array('id', 'name'),
                'conditions' => $conditions,
            ));
        }
		return $result;
	}
}
