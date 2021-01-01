<?php
App::uses('PosAppModel', 'Pos.Model');

class Coupon extends PosAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);
	
	public $type = array(
		1=>'Welcome Coupon', 
		2=>'Birthday Coupon'
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
		'MemberCoupon' => array(
			'className' => 'Member.MemberCoupon',
			'foreignKey' => 'coupon_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
        'CouponLanguage' => array(
            'className' => 'Pos.CouponLanguage',
            'foreignKey' => 'coupon_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
	);

}
