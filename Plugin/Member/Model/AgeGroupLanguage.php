<?php
App::uses('MemberAppModel', 'Member.Model');
/**
 * AgeGroupLanguage Model
 *
 * @property Age $Age
 */
class AgeGroupLanguage extends MemberAppModel {

	public $actsAs = array('Containable');
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'age_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
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
		'Age' => array(
			'className' => 'Member.Age',
			'foreignKey' => 'age_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
