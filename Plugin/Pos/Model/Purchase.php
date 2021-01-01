<?php
App::uses('PosAppModel', 'Pos.Model');

class Purchase extends PosAppModel {

	public $status = array(
		1 => 'processing',
		2 => 'not-paid',
		3 => 'paid',
		4 => 'timeout',
        5 => 'cancel'
	);

    public $status_zho = array(
        1 => '處理中',
        2 => '未付款',
        3 => '已付款',
        4 => '逾時',
        5 => '已取消',
    );

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
		'PurchaseDetail' => array(
			'className' => 'Pos.PurchaseDetail',
			'foreignKey' => 'purchase_id',
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
			'foreignKey' => 'purchase_id',
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

    public function get_data_export($conditions, $page, $limit, $lang){
        $now = date('Y-m-d');
        $model = $this->alias;

        $all_settings = array(
            'fields' => array(
                $model.".*",
                'Member.*',
                'Staff.*',
//                'MovieLanguage.*',
//                'MovieType.*',
//                'ScheduleDetail.*',
//                'Schedule.*',
//                'OrderPaymentLog.*',
                "group_concat(DISTINCT PaymentMethod.name) as payment_method_group",
                'count(Purchase.id) as amount_of_item'
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
                    'alias' => 'Staff',
                    'table' => Environment::read('table_prefix') . 'staffs',
                    'type' => 'left',
                    'conditions' => array(
                        'Staff.id = Purchase.staff_id'
                    ),
                ),
                array(
                    'alias' => 'PurchaseDetail',
                    'table' => Environment::read('table_prefix') . 'purchase_details',
                    'type' => 'left',
                    'conditions' => array(
                        'PurchaseDetail.purchase_id = Purchase.id',
                    ),
                ),
                array(
                    'alias' => 'PurchaseDetailPayment',
                    'table' => Environment::read('table_prefix') . 'purchase_detail_payments',
                    'type' => 'left',
                    'conditions' => array(
                        'PurchaseDetailPayment.purchase_id = Purchase.id',
                    ),
                ),
                array(
                    'alias' => 'PaymentMethod',
                    'table' => Environment::read('table_prefix') . 'payment_methods',
                    'type' => 'left',
                    'conditions' => array(
                        'PaymentMethod.id = PurchaseDetailPayment.payment_method_id'
                    ),
                )
            ),
            'contain' => array(

            ),
            'conditions' => array($conditions),
            'order' => array($model . '.created' => 'DESC'),
            'group' => array(
                'Purchase.id'
            ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
    }

    public function format_data_export($data, $row, $data_binding){
        $model = $this->alias;

        $result = array(
            !empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
            !empty($row[$model]["date"]) ?  $row[$model]["date"] : ' ',
            !empty($row[$model]["inv_number"]) ? $row[$model]["inv_number"]  : ' ',
            !empty($row['Member']["name"]) ?  $row['Member']["name"] : ' ',
            !empty($row[0]['amount_of_item']) ?  $row[0]['amount_of_item'] : ' ',
            !empty($row[0]['payment_method_group']) ?  $row[0]['payment_method_group'] : ' ',
            !empty($data_binding['status'][$row[$model]["status"]]) ? $data_binding['status'][$row[$model]["status"]]  : ' ',
        );

        return $result;
    }

	public function create_purchase_trans($data) {
		$status = false;
		$message = "";
		$params = (object)array();

		/*
			0. validate the token and the staff_id
			1. validate the item enabled
			2. check the validity of member if member_id > 0
			3. run this step if this is continuing from existing purchase.
				-) validate the trans : 
					-) status = 1 and is_paid = false
					-) not void
				-) delete purchase detail
			4. create/update the transaction purchase
			5. create the transaction purchase detail


			


		*/

		if ($data['staff_id'] > 0) {
			//check staff
			$objStaff = ClassRegistry::init('Cinema.Staff');
			$data_staff = $objStaff->get_staff_by_conditions(array('id' => $data['staff_id'], 'token' => $data['token']));

			if (!isset($data_staff['Staff']['id']) || empty($data_staff['Staff']['id'])) {
				$status = false;
				$message = __('staff_not_valid');
				goto return_result;
			}
		} else if ($data['member_id'] > 0) {
			//check member
		}

		$items_id_bought = Hash::extract($data['items_bought'], '{n}.id');

		$objItem = ClassRegistry::init('Pos.Item');
		if (!$objItem->check_items($items_id_bought)) {
			$status = false;
			$message = __('item_not_available_or_invalid');

			goto return_result;
		}

		$disc_percentage = 0;
		if (isset($data['member_id']) && !empty($data['member_id'])) {
			$objMember = ClassRegistry::init('Member.Member');
			if (!$objMember->check_member_validity($data['member_id'])) {
				$status = false;
				$message = __('member_not_found');
	
				goto return_result;				
			}

			$data_renewal = $objMember->MemberRenewal->check_renewal($data['member_id']);
			if (!isset($data_renewal['MemberRenewal']) || empty($data_renewal['MemberRenewal'])) {
				$status = false;
				$message = __('membership_expired');
	
				goto return_result;				
			}

			$objSetting = ClassRegistry::init('Setting.Setting');
			$disc_percentage = $objSetting->get_value('discount-member');
		}

		$purchase_id = 0;
		if (isset($data['purchase_id']) && !empty($data['purchase_id'])) {
			//validate trans purchase
			if (!$this->validate_purchase_trans($data['purchase_id'])) {
				$status = false;
				$message = __('trans_purchase_has_been_paid_or_void');
	
				goto return_result;				
			}

			$purchase_id = $data['purchase_id'];
		}



		$data_item = $objItem->get_data_item_by_id($items_id_bought);
		$data_item_id = Hash::extract($data_item, '{n}.Item.id');

		$objSetting = ClassRegistry::init('Setting.Setting');
		$service_charge_percentage = $objSetting->get_value('service-charge');
		$purchase_detail = array();
		$total = 0;
		foreach($data['items_bought'] as $item) {
			$tmp_data = array();
			$tmp_data['item_id'] = $item['id'];
			$tmp_data['qty'] = $item['qty'];

			$index = array_search($item['id'], $data_item_id);

			$tmp_data['price'] = $data_item[$index]['Item']['price'];

			$qty_price = $tmp_data['qty'] * $tmp_data['price'];
			// $tmp_data['service_charge'] = $qty_price * ($service_charge_percentage/100);
			$tmp_data['service_charge'] = 0;


			//$tmp_data['service_charge_percentage'] = $service_charge_percentage;
			$tmp_data['service_charge_percentage'] = 0;
			$tmp_data['discount'] = 0;

			$tmp_data['subtotal'] = ($qty_price + $tmp_data['service_charge'] - $tmp_data['discount']);
			$total += $tmp_data['subtotal'];

			$purchase_detail[] = $tmp_data;
		}

		$purchase = array();
		$purchase['Purchase']['member_id'] = (isset($data['member_id']) && !empty($data['member_id'])) ? $data['member_id'] : 0;
		$purchase['Purchase']['total_amount'] = $total;
		$purchase['Purchase']['total_discount_coupon'] = 0;
		$disc_amount = $total * ($disc_percentage/100);
		$purchase['Purchase']['discount_amount'] = $disc_amount;
		$purchase['Purchase']['discount_percentage'] = $disc_percentage;
		$purchase['Purchase']['grand_total'] = $total - $disc_amount;

		if ($purchase_id == 0) {
			$purchase['Purchase']['staff_id'] = $data['staff_id'];
			$purchase['Purchase']['date'] = date('Y-m-d H:i:s');
			$purchase['Purchase']['inv_number'] = '';
			$purchase['Purchase']['paid_amount'] = 0;
			$purchase['Purchase']['phone'] = '';
			$purchase['Purchase']['email'] = '';
			$purchase['Purchase']['is_paid'] = 0;
			$purchase['Purchase']['void'] = 0;
			$purchase['Purchase']['status'] = 1;
		} else {
			$purchase['Purchase']['id'] = $purchase_id;
		}

		$purchase['PurchaseDetail'] = $purchase_detail;

		$dbo = $this->getDataSource();
		$dbo->begin();
		try {
			if ($purchase_id > 0) {
				//delete purchase detail
				$condition_delete = array(
					'purchase_id' => $purchase_id
				);
				if (!$this->PurchaseDetail->deleteAll($condition_delete, false)) {
					$dbo->rollback();
					$status = false;
					$message = __('deleting_existing_purchase_detail_failed');
		
					goto return_result;				
				}
			}

			if ($this->saveAll($purchase)) {
				if ($purchase_id == 0) {
					$purchase_id = $this->id;
					$purchase['Purchase']['id'] = $purchase_id;

					$inv_number = "'".Environment::read('site.prefix.purchase').str_pad($purchase_id, 7, '0', STR_PAD_LEFT)."'";
					$conditions = array(
						"Purchase.id" => $purchase_id
					);
					$updates = array(
						"Purchase.inv_number" => $inv_number,
					);

					if ($this->updateAll($updates, $conditions)) {
						$status = true;		
					} else {
						$dbo->rollback();
						$status = false;
						$message = __('creating_inv_number_failed');
			
						goto return_result;
					}
				} else {
					$status = true;
				}

				if ($status) {
					$params = $purchase;
					$dbo->commit();
					$message = __('purchase_created_succesfully');
				}

			} else {
				$dbo->rollback();
				$status = false;
				$message = __('create_purchase_failed');
	
				goto return_result;				
			}



		} catch (Exception $e) {
			$dbo->rollback();
			$status = false;
			$message = __('data_is_not_saved') . ' ' . $e->getMessage();

			goto return_result;
		}

		return_result :

		return array('status' => $status, 'message' => $message, 'params' => $params);
	}

	public function get_purchase($id) {
		$option = array(
			/*
			'contain' => array(
				'PurchaseDetail'
			),
			*/
            'conditions' => array(
				'id' => $id,
			),
		);
        
		return $this->find('first', $option);
	}

	public function do_payment($data, $lang) {
		$status = false;
		$message = "";
		$params = (object)array();

		/*
			0. validate the token and the staff_id
			1. validate the order 
				-) not yet paid
				-) status still processing
				-) not void
			2. update the order detail payment
			3. update the order
		*/

		//check staff
		$objStaff = ClassRegistry::init('Cinema.Staff');
		$data_staff = $objStaff->get_staff_by_conditions(array('id' => $data['staff_id'], 'token' => $data['token']));

		if (!isset($data_staff['Staff']['id']) || empty($data_staff['Staff']['id'])) {
			$status = false;
			$message = __('staff_not_valid');
			goto return_result;
		}

		$option_purchase = array(
			'contain' => array(
				'PurchaseDetail'
			),
			'conditions' => array(
				'id' => $data['purchase_id'],
				'void' => 0,
				'is_paid' => 0,
				'status IN' => array(1,2)
			)
		);

		$data_purchase = $this->find('first', $option_purchase);

		if (isset($data_purchase['Purchase']['id']) && !empty($data_purchase['Purchase']['id'])) {
			//do nothing
		} else {
			$status = false;
			$message = __('purchase_not_valid_to_be_paid');
			goto return_result;
		}

		$total_amount = $data_purchase['Purchase']['total_amount'];

		// pr('order_detail_coupon');
		// pr($data['order_detail_coupon']);
		// pr('coupon_number');
		// pr($coupon_number);

		unset($data['payment_method'][0]);

		$total_discount_coupon = 0;
		$payment_method = $data['payment_method'];
		$payment = array();
		foreach($data['payment_method'] as $key => $value) {
			if (isset($value) && !empty($value)) {
				foreach($value as $payment_detail) {
					if (isset($payment_detail['number']) && !empty($payment_detail['number'])) {
						foreach($payment_detail['number'] as $number) {
							$index = 0;
							$tmp_payment = array();	
							$tmp_payment['purchase_id'] = $data_purchase['Purchase']['id'];
							$tmp_payment['payment_method_id'] = $payment_detail['id'];

							//$index = array_search($number, $coupon_number);

							$tmp_payment['number'] = $number;
							$tmp_payment['amount'] = abs($payment_detail['value']);
							$total_discount_coupon += abs($payment_detail['value']);

							$tmp_payment['value'] = $payment_detail['value'];
							$payment[] = $tmp_payment;
							
						}
					} else if ($payment_detail['type'] == 1) {
						$tmp_payment = array();	
						$tmp_payment['purchase_id'] = $data_purchase['Purchase']['id'];
						$tmp_payment['payment_method_id'] = $payment_detail['id'];
						$tmp_payment['number'] = '';
						$tmp_payment['amount'] = $payment_detail['amount'];
						$tmp_payment['value'] = $payment_detail['value'];
						$payment[] = $tmp_payment;
					}
				}
			}
		}	

		$data_purchase['Purchase']['total_discount_coupon'] = $total_discount_coupon;

		$disc_amount = $total_amount * ($data_purchase['Purchase']['discount_percentage'] / 100);
		$data_purchase['Purchase']['discount_amount'] = $disc_amount;
		$data_purchase['Purchase']['grand_total'] = $total_amount - $total_discount_coupon - $disc_amount;

		$data_purchase['Purchase']['is_paid'] = 1;
		$data_purchase['Purchase']['paid_amount'] = ($data_purchase['Purchase']['grand_total'] > 0) ? $data_purchase['Purchase']['grand_total'] : 0;
		$data_purchase['Purchase']['status'] = 3;

		// pr($payment);
		// pr($data_order['Order']);
		// pr($data_order['OrderDetail']);
		// exit;


		// $data_purchase_print = $payment;

		$dbo = $this->getDataSource();
		$dbo->begin();
		try {
			
			$objPurchaseDetailPayment = ClassRegistry::init('Pos.PurchaseDetailPayment');
			if ($objPurchaseDetailPayment->saveAll($payment)) {
				if ($this->saveAll($data_purchase)) {
					$data_purchase_print = $this->get_data_print_purchase($data['purchase_id'], $lang);
					$params = $data_purchase_print;
					$dbo->commit();
					$status = true;
					$message = __('payment_saved_succesfully');
				} else {
					$dbo->rollback();
					$status = false;
					$message = __('create_purchase_failed');
		
					goto return_result;				
				}
			} else {
				$dbo->rollback();
				$status = false;
				$message = __('saving_payment_failed');
	
				goto return_result;		
			}
		} catch (Exception $e) {
			$dbo->rollback();
			$status = false;
			$message = __('data_is_not_saved');

			goto return_result;
		}

		return_result :

		return array('status' => $status, 'message' => $message, 'params' => $params);
	}

	public function get_data_print_purchase($id, $lang, $is_reprint = false) {
		$option = array(
			'fields' => array(
				'Purchase.inv_number',
				'Purchase.date',
				'Purchase.grand_total',
				'PurchaseDetail.item_id',
				'PurchaseDetail.price',
				'sum(PurchaseDetail.qty) as PurchaseDetail__total_qty',
				'sum(PurchaseDetail.subtotal) as PurchaseDetail__total_subtotal',
				'ItemLanguage.name',
			),
			'joins' => array(
				array(
					'alias' => 'Purchase',
					'table' => Environment::read('table_prefix') . 'purchases',
					'type' => 'left',
					'conditions' => array(
						'PurchaseDetail.purchase_id = Purchase.id',
					),
				),
				array(
					'alias' => 'Item',
					'table' => Environment::read('table_prefix') . 'items',
					'type' => 'left',
					'conditions' => array(
						'Item.id = PurchaseDetail.item_id',
					),
				),
				array(
					'alias' => 'ItemLanguage',
					'table' => Environment::read('table_prefix') . 'item_languages',
					'type' => 'left',
					'conditions' => array(
						'Item.id = ItemLanguage.item_id',
						'ItemLanguage.language' => $lang
					),
				),
			),
			'conditions' => array(
				'PurchaseDetail.purchase_id' => $id
			),
			'group' => array(
				'PurchaseDetail.item_id'
			)
		);

		$objPurchaseDetail = ClassRegistry::init('Pos.PurchaseDetail');
		$objPurchaseDetail->virtualFields['total_qty'] = 'sum(PurchaseDetail.qty)';
		$objPurchaseDetail->virtualFields['total_subtotal'] = 'sum(PurchaseDetail.subtotal)';

		$data_purchase_db = $objPurchaseDetail->find('all', $option);

		$option_payment = array(
			'fields' => array(
				'Purchase.inv_number',
				'PaymentMethod.*'
			),
			'joins' => array(
				array(
					'alias' => 'PurchaseDetailPayment',
					'table' => Environment::read('table_prefix') . 'purchase_detail_payments',
					'type' => 'left',
					'conditions' => array(
						'PurchaseDetailPayment.purchase_id = Purchase.id',
					),
				),
				array(
					'alias' => 'PaymentMethod',
					'table' => Environment::read('table_prefix') . 'payment_methods',
					'type' => 'left',
					'conditions' => array(
						'PurchaseDetailPayment.payment_method_id = PaymentMethod.id',
					),
				),
			),
			'conditions' => array(
				'Purchase.id' => $id,
				'PaymentMethod.type' => 2
			),
		);

		$payment = $this->find('all', $option_payment);

		$option_cinema = array(
			'condiitons' => array(
				'enabled' => 1
			)
		);
		$objCinema = ClassRegistry::init('Cinema.Cinema');
		$data_cinema = $objCinema->find('first', $option_cinema);


		$data_purchase = array();
		$data_purchase['ticket'][] = array();

		$staff = CakeSession::read('staff');
		$staff_name = $staff['Staff']['name'];


		$data_purchase['receipt']['receipt_number'] = $data_purchase_db[0]['Purchase']['inv_number'];
		$data_purchase['receipt']['staff'] = $staff_name;
		
		$data_purchase['receipt']['cinema'] = $data_cinema['Cinema']['code'];
		$data_purchase['receipt']['cinema_location'] = $data_cinema['Cinema']['location'];
		$data_purchase['receipt']['cinema_tel'] = $data_cinema['Cinema']['phone'];
		
		$data_purchase['receipt']['purchase_time'] = date('Ymd H:i', strtotime($data_purchase_db[0]['Purchase']['date']));
		// $data_purchase['receipt']['payment_method'] = (isset($payment['PaymentMethod']['name']) && !empty($payment['PaymentMethod']['name'])) ? $payment['PaymentMethod']['name'] : '';		
		$data_purchase['receipt']['payment_method'] = '';		
		$data_purchase['receipt']['amount'] = $data_purchase_db[0]['Purchase']['grand_total'];
		$data_purchase['receipt']['reprint'] = $is_reprint;

		foreach($data_purchase_db as $purchase) {
			$single_item = array();
			$single_item['item'] = $purchase['ItemLanguage']['name'];
			$single_item['qty'] = $purchase['PurchaseDetail']['total_qty'];
			$single_item['unit_price'] = $purchase['PurchaseDetail']['price'];
			$single_item['subtotal'] = $purchase['PurchaseDetail']['total_subtotal'];
			$data_purchase['receipt']['items'][] = $single_item;
		}
		
		foreach($payment as $payment) {
			$single_item = array();
			$single_item['item'] = $payment['PaymentMethod']['code'];
			$single_item['qty'] = 1;
			$single_item['unit_price'] = $payment['PaymentMethod']['value'];
			$single_item['subtotal'] = $payment['PaymentMethod']['value'];
			$data_purchase['receipt']['items'][] = $single_item;
		}

		return $data_purchase;
	}

	public function validate_purchase_trans($id) {
		$option = array(
			'conditions' => array(
				'id' => $id,
				'status' => 1,
				'is_paid' => 0,
				'void' => 0,
			)
		);

		$count_purchase = $this->find('count', $option);

		return (($count_purchase > 0) ? true : false);
	}

	public function void_purchase($data, $data_purchase) {
        $status = false;
        $message = '';
        $params = (object)array();

        $dbo = $this->getDataSource();
        $dbo->begin();
        try {
            $conditions_void_purchase = array(
                "Purchase.id" => $data['purchase_id']
            );
            $updates_void_purchase = array(
                "Purchase.void" => 1
			);

			if ($this->updateAll($updates_void_purchase, $conditions_void_purchase)) {
				$dbo->commit();
				$status = true;
				$message = __('void_purchase_succesfully');
            } else {
                $dbo->rollback();
                $status = false;
				$message = __('void_purchase_failed');
            }
        } catch (Exception $e) {
            $dbo->rollback();
            $status = false;
            $message = __('void_purchase_failed') . ', err : ' . $e->getMessage();
        }

		return_result:
        return array('status' => $status, 'message' => $message, 'params' => $params);
	}

}
