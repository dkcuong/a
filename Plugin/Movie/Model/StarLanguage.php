<?php
App::uses('MovieAppModel', 'Movie.Model');

class StarLanguage extends MovieAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'Star' => array(
			'className' => 'Movie.Star',
			'foreignKey' => 'star_id',
			'conditions' => '',
			'order' => ''
		),
	);
}
