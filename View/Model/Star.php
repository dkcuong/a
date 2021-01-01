<?php
App::uses('MovieAppModel', 'Movie.Model');

class Star extends MovieAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $virtualFields = array(
	    'name' => 'CONCAT(first_name, " ", surname)'
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

	public $hasAndBelongsToMany = array(
		'Movie' => array(
			'className' => 'Movie.Movie',
			'joinTable' => 'movies_stars',
			'foreignKey' => 'movie_id',
			'associationForeignKey' => 'star_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

	public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
			'fields' => array(
				'Star.*',
			),
			'contain' => array(
                'CreatedBy',
                'UpdatedBy',
			),
            'conditions' => $conditions,
            'order' => array( 'Star.first_name' => 'asc' ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
	}
	
	public function format_data_export($data, $row){
		$model = $this->alias;
        return array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row[$model]["first_name"]) ?  $row[$model]["first_name"] : ' ',
			!empty($row[$model]["surname"]) ?  $row[$model]["surname"] : ' ',
			!empty($row[$model]["image_url"]) ?  $row[$model]["image_url"] : ' ',
        );
    }

	public function get_list_stars(){
        $conditions = [];

		$stars = $this->find('list', array(
            'fields' => array('id', 'name'),
            'conditions' => $conditions,
		));
		
		return $stars;
    }

}
