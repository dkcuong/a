<?php
App::uses('MovieAppModel', 'Movie.Model');

class MovieLanguage extends MovieAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'Movie' => array(
			'className' => 'Movie.Movie',
			'foreignKey' => 'movie_id',
			'conditions' => '',
			'order' => ''
		),
	);
}
