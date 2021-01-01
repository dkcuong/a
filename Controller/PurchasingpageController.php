<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 */
class PurchasingpageController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'RequestHandler');

	public function index() {
		$data_search = $this->request->query;
		$lang = 'zho';
		$objPurchase = ClassRegistry::init('Pos.Purchase');

		$alphabet = range('A', 'Z');
		$page = 1;
		if (isset($this->request->params['named']['page']) && !empty($this->request->params['named']['page'])) {
			$page = $this->request->params['named']['page'];
		}

		$conditions = array();

		if( isset($data_search['inv_number']) && !empty($data_search['inv_number']) ) {
			$conditions['Purchase.inv_number LIKE'] = '%' . $data_search['inv_number'] . '%';
		}

		if( isset($data_search['date']) && !empty($data_search['date']) ) {
			$filter_date = DateTime::createFromFormat('d/m/Y', $data_search['date']);

			$filter_date_start = date_format($filter_date, 'Y-m-d');
			$filter_date_end = date('Y-m-d', strtotime($filter_date_start . ' +1 days'));

			$conditions['Purchase.date >='] = $filter_date_start;
			$conditions['Purchase.date <'] = $filter_date_end;
			$data_search['date'] = date_format($filter_date, 'm/d/Y');
		}

		if( isset($data_search['item']) && !empty($data_search['item']) ) {
			$item_id = $data_search['item'];

			$option_item_bought = array(
				'id' => array(
					'Purchase.id',
				),
				'joins' => array(
					array(
						'alias' => 'PurchaseDetail',
						'table' => Environment::read('table_prefix') . 'purchase_details',
						'type' => 'left',
						'conditions' => array(
							'Purchase.id = PurchaseDetail.purchase_id',
						),
					),
					array(
						'alias' => 'Item',
						'table' => Environment::read('table_prefix') . 'items',
						'type' => 'left',
						'conditions' => array(
							'PurchaseDetail.item_id = Item.id',
						),
					),
				),
				'conditions' => array(
					'Item.id' => $item_id,
				)
			);

			$data_order_items = $objPurchase->find('list', $option_item_bought);
			
			if (!isset($data_order_items) || empty($data_order_items)) {
				$data_order_items = array(0);
			}

			$conditions['Purchase.id IN'] = $data_order_items;
		}

		$prefix = Environment::read('database.prefix');

		$sqlstr = "CREATE TEMPORARY TABLE IF NOT EXISTS " . $prefix . "mytables AS (".
		"select a.id, b.code as payment_method ".
		"from " . $prefix . "purchases a ".
			"left join ( ".
				"select a.purchase_id, b.code ".
				"from " . $prefix . "purchase_detail_payments a ".
					"left join " . $prefix . "payment_methods b on b.id = a.payment_method_id ".
				"where b.type = 1 ) b on b.purchase_id = a.id ".
		")";

		$objPurchase->query($sqlstr);

		$option = array(
			'fields' => array(
				'Purchase.*',
				'Member.name',
				'Mytable.payment_method'
			),
			'joins' => array(
				array(
					'alias' => 'Member',
					'table' => Environment::read('table_prefix') . 'members',
					'type' => 'left',
					'conditions' => array(
						'Purchase.member_id = Member.id',
					),
				),
				array(
					'alias' => 'Mytable',
					'table' => Environment::read('table_prefix') . 'mytables',
					'type' => 'left',
					'conditions' => array(
						'Mytable.id = Purchase.id',
					),
				),
			),
			'conditions' => $conditions,
			'order' => array('Purchase.id' => 'desc'),
			'limit' => 20,
			'page' => $page
		);

		$data_purchase = $objPurchase->find('all', $option);

		$purchase_ids = Hash::extract($data_purchase, '{n}.Purchase.id');

		$option_detail = array(
			'fields' => array(
				'Purchase.id',
				'ItemLanguage.name',
			),
			'joins' => array(
				array(
					'alias' => 'PurchaseDetail',
					'table' => Environment::read('table_prefix') . 'purchase_details',
					'type' => 'left',
					'conditions' => array(
						'Purchase.id = PurchaseDetail.purchase_id',
					),
				),
				array(
					'alias' => 'Item',
					'table' => Environment::read('table_prefix') . 'items',
					'type' => 'left',
					'conditions' => array(
						'PurchaseDetail.item_id = Item.id',
					),
				),
				array(
					'alias' => 'ItemLanguage',
					'table' => Environment::read('table_prefix') . 'item_languages',
					'type' => 'left',
					'conditions' => array(
						'Item.id = ItemLanguage.item_id',
						'ItemLanguage.language' => $this->lang18
					),
				),
			),
			'conditions' => array(
				'Purchase.id' => $purchase_ids,
				'NOT' => array('PurchaseDetail.id' => null)
			),
			'order' => array('Item.code' => 'asc')
		);

		$data_purchase_detail = $objPurchase->find('all', $option_detail);

		$arr_items = array();
		foreach($data_purchase_detail as $detail) {
			if (isset($detail['ItemLanguage']['name'])) {
				$arr_items[$detail['Purchase']['id']][] =$detail['ItemLanguage']['name'];
			}
		}

		/*
		foreach($data_purchase as &$purchase) {
			if (isset($arr_items[$purchase['Purchase']['id']]) && !empty($arr_items[$purchase['Purchase']['id']])) {
				$purchase['Purchase']['items'] = implode(',', $arr_items[$purchase['Purchase']['id']]);
			}
		}
		*/

		// $dbdatas = $data_order;

		$this->Paginator->settings = $option;
		$dbdatas = $this->paginate($objPurchase);

		$printer_setting = Environment::read('site.printer_setting');
		$printer_address = Environment::read('site.'.$printer_setting.'.ticketing');
		$printer_port = Environment::read('site.'.$printer_setting.'.ticketing');

		$status = $objPurchase->status_zho;

		$option_items = array(
			'fields' => array(
				'Item.id',
				'ItemLanguage.name'
			),
			'joins' => array(
				array(
					'alias' => 'ItemLanguage',
					'table' => Environment::read('table_prefix') . 'item_languages',
					'type' => 'left',
					'conditions' => array(
						'Item.id = ItemLanguage.item_id',
						'ItemLanguage.language' => $this->lang18
					),
				),
			),
			'conditions' => array(
				'enabled' => 1
			)
		);
		$objItem = ClassRegistry::init('Pos.Item');
		$items = $objItem->find('list', $option_items);

		$this->set(compact('dbdatas', 'items', 'arr_items', 'printer_address', 'printer_port', 'status', 'data_search'));	
	}
}