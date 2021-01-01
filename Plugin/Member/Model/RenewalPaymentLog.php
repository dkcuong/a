<?php
App::uses('MemberAppModel', 'Member.Model');
/**
 * MemberRenewal Model
 *
 * @property Member $Member
 */
class RenewalPaymentLog extends MemberAppModel {

	public $actsAs = array('Containable');
/**
 * Use table
 *
 * @var mixed False or table name
 */

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
	);

}
