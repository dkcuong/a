<?php
App::uses('PosAppModel', 'Pos.Model');

class OrderPaymentLog extends PosAppModel {

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
