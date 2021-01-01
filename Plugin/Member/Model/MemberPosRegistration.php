<?php
App::uses('MemberAppModel', 'Member.Model');
/**
 * MemberVerification Model
 *
 * @property Member $Member
 */
class MemberPosRegistration extends MemberAppModel {

	public $actsAs = array('Containable');
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
	);
}
