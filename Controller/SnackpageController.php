<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 */
class SnackpageController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

	public function index() {
		$option = array(
			'fields' => array(
				'Item.*',
				'ItemLanguage.name'
			),
			'joins' => array(
				array(
					'alias' => 'ItemLanguage',
					'table' => Environment::read('table_prefix') . 'item_languages',
					'type' => 'left',
					'conditions' => array(
						'ItemLanguage.item_id = Item.id',
						'ItemLanguage.language' => $this->lang18
					),
				),
			),
			'conditions' => array(
				'enabled' => 1
			),
			'order' => array(
				'Item.item_group_id' => 'asc',
				'Item.code' => 'asc'
			)
		);

		$objItem = ClassRegistry::init('Pos.Item');
		$data_items = $objItem->find('all', $option);

		// $objSetting = ClassRegistry::init('Setting.Setting');
		// $service_charge_percentage = $objSetting->get_value('service-charge');
		$service_charge_percentage = 0;

		/*
			get uncomplete transaction for this staff that is still new ({n} minute before)
		*/
		
		$staff = $this->Session->read('staff');
		$staff_id = $staff['Staff']['id'];

		$option_purchase = array(
			'contain' => array(
				'PurchaseDetail' => array(
					'Item'
				)
			),
			'conditions' => array(
				'staff_id' => $staff_id,
				'status' => 1,
				'is_paid' => 0,
				'void' => 0,
				'DATE_ADD(date, INTERVAL 15 MINUTE) >=' => date('Y-m-d H:i:s')
			),
			'order' => array('id' => 'desc'),
			'limit' => 1
		);

		$objPurchase = ClassRegistry::init('Pos.Purchase');
		$data_purchase = $objPurchase->find('first', $option_purchase);

		// $is_continue = (isset($data_purchase['Purchase']['id']) && !empty($data_purchase['Purchase']['id'])) ? true : false;
		
		$display_link_signout = true;

		$this->set(compact('data_items', 'service_charge_percentage', 'data_purchase', 'display_link_signout'));
	}
}