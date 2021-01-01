<?php
App::uses('LogAppModel', 'Log.Model');
/**
 * Log Model
 *
 * @property Company $Company
 * @property User $User
 * @property Type $Type
 */
class LogSuccess extends LogAppModel {
    public $actsAs = array('Containable');	// very import when using DB join table
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'log_id' => array(
            'notBlank' => array(
				'rule' => array('notBlank'),
                'required' => true
			),
		),
		'content' => array(
            'notBlank' => array(
				'rule' => array('notBlank'),
                'required' => true
			),
		),
	);

    // The Associations below have been created with all possible keys, those that are not needed can be removed
    public $belongsTo = array(
		'Log' => array(
			'className' => 'Log.Log',
			'foreignKey' => 'log_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
}