<?php
App::uses('MovieAppModel', 'Movie.Model');

class MoviesMovieType extends MovieAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
        'Movie' => array(
			'className' => 'Movie.Movie',
			'foreignKey' => 'movie_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MovieType' => array(
			'className' => 'Movie.MovieType',
			'foreignKey' => 'movie_type_id',
			'conditions' => '',
			'fields' => '',
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
	);
}
