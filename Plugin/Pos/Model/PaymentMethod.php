<?php
App::uses('PosAppModel', 'Pos.Model');

class PaymentMethod extends PosAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
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
		'OrderDetailPayment' => array(
			'className' => 'Pos.OrderDetailPayment',
			'foreignKey' => 'payment_method_id',
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
		'PurchaseDetailPayment' => array(
			'className' => 'Pos.PurchaseDetailPayment',
			'foreignKey' => 'payment_method_id',
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

	public $type = array(
		1 => 'Cash/Credit Card/Debit Card',
		2 => 'Coupon',
		3 => 'Exchange Ticket'
	);

	public function get_list_api($language){
        $list_item = $this->find('all', array(
            'fields' => array('*'),
            'conditions' => array('enabled' => true),
		));


        $result = array_column($list_item, $this->alias);

		return $result;
	}
	
	public function get_active_payment_method($extra_conditions = array()){
		$option = array(
            'conditions' => array(
				'enabled' => 1,
				$extra_conditions
			),
		);
        
		return $this->find('all', $option);
    }
}
