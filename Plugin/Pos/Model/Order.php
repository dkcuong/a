<?php
App::uses('PosAppModel', 'Pos.Model');

class Order extends PosAppModel {

	public $status = array(
		1 => 'processing',
		2 => 'not-paid',
		3 => 'paid',
		4 => 'timeout',
		5 => 'cancel',
		6 => 'hold',
	);

	public $status_zho = array(
		1 => '處理中',
		2 => '未付款',
		3 => '已付款',
		4 => '逾時',
		5 => '已取消',
		6 => 'HOLD',
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
		'OrderDetail' => array(
			'className' => 'Pos.OrderDetail',
			'foreignKey' => 'order_id',
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
		'OrderDetailPayment' => array(
			'className' => 'Pos.OrderDetailPayment',
			'foreignKey' => 'order_id',
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

	public function create_order($data, $lang, $is_pos = false) {
		$status = false;
		$message = "";
		$params = (object)array();

		/*
			0. validate the token(from staff or member) and the staff_id => already done in controller
			1. if order_id > 0
					get old_seat 
					get new_seat
				validate new seat
			2. validate the ticket_type
			3. if order_id > 0
					restore all unselected seat's status
				update new seat status

			4. if order > 0
				update status of old_seat back to available
				delete order_detail
			5. create/update the transaction order
			6. create the transaction order detail
			7. create the transaction order
		*/

		$order_id = 0;
		$data_order = array();
		$booked_seat = Hash::extract($data['ticket_type'], '{n}.schedule_detail_layout_id'); 
		//$booked_seat => booked seat from frontend, we will use it to create Order Detail and to update status's seat to sold
		$new_seat = $booked_seat;
		$new_seat_count = count($new_seat);
		//$new_seat => new selected seat that doesnt exists in existing order trans, used for validate the new selected seat
		$old_seat = array(); 
		//$old_seat => original seat from existing order trans, we will use it to release seat's status
		if (isset($data['order_id']) && ($data['order_id'] > 0)) {
			$order_id = $data['order_id'];

			$new_seat_count = 0;
			$new_seat = array(); 
			$data_order = $this->get_order($order_id);

			$old_seat = Hash::extract($data_order['OrderDetail'], '{n}.schedule_detail_layout_id');
			$new_seat_count = $new_seat_count - count($old_seat);

			foreach($booked_seat as $seat) {
				if (!in_array($seat, $old_seat)) {
					$new_seat[] = $seat;
					
				}
			}

		}

		$objScheduleDetailLayout = ClassRegistry::init('Movie.ScheduleDetailLayout');
		$objScheduleDetail = ClassRegistry::init('Movie.ScheduleDetail');
		if (!empty($new_seat)) {
			if (!$objScheduleDetailLayout->check_seat_schedule_detail_relation($data['schedule_detail_id'], $new_seat) ||
				!$objScheduleDetailLayout->check_seat_status_availability($new_seat)) {
				$status = false;
				$message = __('seat_not_available_or_invalid');
				$params = array('error' => 'seat_not_available');

				goto return_result;
			}
		}

		$option_used_seat = array(
			'conditions' => array(
				'schedule_detail_id' => $data['schedule_detail_id'],
				'enabled' => 1,
				'status >' => 1
			)
		);

		$data_unavailable_seat = $objScheduleDetailLayout->find('count', $option_used_seat);

		$option_schedule_detail = array(
			'conditions' => array(
				'id' => $data['schedule_detail_id']
			)
		);
		$data_schedule_detail = $objScheduleDetail->find('first', $option_schedule_detail);

		$attendance_rate = (($data_unavailable_seat + $new_seat_count) / $data_schedule_detail['ScheduleDetail']['capacity']) * 100;

		$service_charge_slug = 'service-charge';
		if ($is_pos) {
			$tmp_ticket_ids = Hash::extract($data['ticket_type'], '{n}.id');
			$ticket_type_ids = array();
			foreach($tmp_ticket_ids as $tmp) {
				$ticket_type_ids[$tmp] = $tmp;
			}

			$objScheduleDetailTicketType = ClassRegistry::init('Movie.ScheduleDetailTicketType');
			$result_ticket_type = $objScheduleDetailTicketType->get_ticket_type_value_by_id($data['schedule_detail_id'], $ticket_type_ids);

			$data_ticket_type = $result_ticket_type['data_ticket_type'];
			$data_ticket_type_hkbo = $result_ticket_type['data_ticket_type_hkbo'];

			if (!isset($data_ticket_type) || empty($data_ticket_type) || !$data_ticket_type) {
				$status = false;
				$message = __('ticket_type_invalid');
				goto return_result;
			}
			$service_charge_slug = 'service-charge-pos';
		}

		$objSetting = ClassRegistry::init('Setting.Setting');
		$service_charge_percentage = $objSetting->get_value($service_charge_slug);
		$order_detail = array();
		$total = 0;
		foreach($data['ticket_type'] as $ticket_type) {
			$tmp_data = array();
			$tmp_data['schedule_detail_ticket_type_id'] = $ticket_type['id'];
			$tmp_data['schedule_detail_layout_id'] = $ticket_type['schedule_detail_layout_id'];
			$tmp_data['qty'] = 1;

			if ($ticket_type['id'] > 0) {
				$tmp_data['price'] = $data_ticket_type[$ticket_type['id']];
				$tmp_data['price_hkbo'] = $data_ticket_type_hkbo[$ticket_type['id']];
			} else {
				$tmp_data['price'] = 0;
				$tmp_data['price_hkbo'] = 0;
			}

			$qty_price = $tmp_data['qty'] * $tmp_data['price'];
			// $tmp_data['service_charge'] = $qty_price * ($service_charge_percentage/100);
			$tmp_data['service_charge'] = $service_charge_percentage;


			$tmp_data['service_charge_percentage'] = $service_charge_percentage;
			$tmp_data['discount'] = 0;

			$tmp_data['subtotal'] = ($qty_price + $tmp_data['service_charge'] - $tmp_data['discount']);
			$total += $tmp_data['subtotal'];

			$order_detail[] = $tmp_data;
		}

		$order = array();
		$order['Order']['total_amount'] = $total;
		$order['Order']['member_id'] = (isset($data['member_id']) && !empty($data['member_id'])) ? $data['member_id'] : 0;
		$order['Order']['schedule_detail_id'] = $data['schedule_detail_id'];
		$order['Order']['date'] = date('Y-m-d H:i:s');

		$order['Order']['registration_fee'] = 0;
		$order['Order']['is_member_register'] = 0;
		$order['Order']['country_code_registration'] = '';
		$order['Order']['phone_registration'] = '';

		if ($order_id == 0) {
			$order['Order']['staff_id'] = $data['staff_id'];
			$order['Order']['inv_number'] = '';
			$order['Order']['is_pos'] = ($is_pos) ? 1 : 0;
			$order['Order']['discount_amount'] = 0;
			$order['Order']['discount_percentage'] = 0;
			$order['Order']['paid_amount'] = 0;
			$order['Order']['phone'] = '';
			$order['Order']['email'] = '';
			$order['Order']['is_paid'] = 0;
			$order['Order']['void'] = 0;
			$order['Order']['status'] = 1;
			$order['Order']['token'] = $this->generateToken();

			$grand_total = $total;
		} else {
			$order['Order']['id'] = $order_id;
			$order['Order']['discount_amount'] = $total * ($data_order['Order']['discount_percentage']/100);
			$grand_total = $total - $order['Order']['discount_amount'];
		}

		$order['Order']['grand_total'] = $total;



		$order['OrderDetail'] = $order_detail;

		$conditions = array(
			"ScheduleDetailLayout.id IN" => $booked_seat
		);
		$updates = array(
			"ScheduleDetailLayout.status" => 2,
			"ScheduleDetailLayout.order_time" => "'".date('Y-m-d H:i:s')."'"
		);

		$conditions_schedule_detail = array(
			"ScheduleDetail.id" => $data['schedule_detail_id']
		);
		$updates_schedule_detail = array(
			"ScheduleDetail.attendance_rate" => $attendance_rate,
		);

		$dbo = $this->getDataSource();
		$dbo->begin();
		try {

			if ($order_id > 0) {
				$conditions_release_status = array(
					"ScheduleDetailLayout.id IN" => $old_seat
				);
				$updates_release_status = array(
					"ScheduleDetailLayout.status" => 1,
					"ScheduleDetailLayout.order_time" => null
				);

				if (!$objScheduleDetailLayout->updateAll($updates_release_status, $conditions_release_status)) {
					$dbo->rollback();
					$status = false;
					$message = __('release_seat_status_failed');
		
					goto return_result;
				}

				//delete order detail
				$condition_delete = array(
					'order_id' => $order_id
				);
				if (!$this->OrderDetail->deleteAll($condition_delete, false)) {
					$dbo->rollback();
					$status = false;
					$message = __('deleting_existing_order_detail_failed');
		
					goto return_result;
				}
			}


			if ($objScheduleDetailLayout->updateAll($updates, $conditions) &&
				$objScheduleDetail->updateAll($updates_schedule_detail, $conditions_schedule_detail)) {

				if ($this->saveAll($order)) {
					if ($order_id == 0) {
						$order_id = $this->id;
						$order['Order']['id'] = $order_id;

						$inv_number = "'".Environment::read('site.prefix.booking').str_pad($order_id, 7, '0', STR_PAD_LEFT)."'";
						$conditions = array(
							"Order.id" => $order_id
						);
						$updates = array(
							"inv_number" => $inv_number,
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
						$params = $order;
						$dbo->commit();
						$message = __('order_created_succesfully');
					}

				} else {
					$dbo->rollback();
					$status = false;
					$message = __('create_order_failed');
		
					goto return_result;				
				}

			} else {
				$dbo->rollback();
				$status = false;
				$message = __('updating_seat_status_failed');
	
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

	public function update_membership_order($data, $lang) {
		$status = false;
		$message = "";
		$params = (object)array();

		/*
			0. validate the token and the staff_id
			1. validate the order 
				-) not yet paid
				-) status still processing
				-) not void
			2. check the validity of member
			3. update the order => member_id, discount, discount_percentage, grand_total
		*/

		
		//check staff
		$objStaff = ClassRegistry::init('Cinema.Staff');
		$data_staff = $objStaff->get_staff_by_conditions(array('id' => $data['staff_id'], 'token' => $data['token']));

		if (!isset($data_staff['Staff']['id']) || empty($data_staff['Staff']['id'])) {
			$status = false;
			$message = __('staff_not_valid');
			goto return_result;
		}

		$member_id = 0;
		$is_registration = 0;
		$registration_fee = 0;
		$objMember = ClassRegistry::init('Member.Member');
		$objSetting = ClassRegistry::init('Setting.Setting');
		if (isset($data['member_id']) && !empty($data['member_id'])) {
			$member_id = $data['member_id'];
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
		} else if (isset($data['is_member_register']) && !empty($data['is_member_register'])) {
			
			//validasi phone again
			$check_result = $objMember->check_phone_for_registration($data, $lang);
			if (!$check_result['status']) {
				$status = false;
				$message = $check_result['message'];
	
				goto return_result;
			}

			$is_registration = 1;
			$registration_fee = $objSetting->get_value('member-renewal');
		}
		
		$disc_percentage = $objSetting->get_value('discount-member');

		$option_order = array(
			'conditions' => array(
				'id' => $data['order_id'],
				'void' => 0,
				'is_paid' => 0,
				'status' => 1
			)
		);

		$data_order = $this->find('first', $option_order);

		if (isset($data_order['Order']['id']) && !empty($data_order['Order']['id'])) {

			$data_order['Order']['registration_fee'] = 0;
			$data_order['Order']['is_member_register'] = 0;
			$data_order['Order']['country_code_registration'] = '';
			$data_order['Order']['phone_registration'] = '';

			if ($member_id > 0 || $is_registration > 0) {
				$data_order['Order']['member_id'] = $data['member_id'];
				$data_order['Order']['discount_percentage'] = $disc_percentage;
				$disc_amount = $data_order['Order']['total_amount'] * ($disc_percentage/100);
				$data_order['Order']['discount_amount'] = $disc_amount;
				$data_order['Order']['grand_total'] = $data_order['Order']['total_amount'] - $disc_amount;

				if ($is_registration > 0) {
					$data_order['Order']['registration_fee'] = $registration_fee;
					$data_order['Order']['grand_total'] = $data_order['Order']['grand_total'] + $registration_fee;
					$data_order['Order']['is_member_register'] = 1;
					$data_order['Order']['country_code_registration'] = $data['country_code'];
					$data_order['Order']['phone_registration'] = $data['phone'];
				}
			} else {
				$data_order['Order']['member_id'] = 0;
				$data_order['Order']['discount_percentage'] = 0;
				$disc_amount = 0;
				$data_order['Order']['discount_amount'] = 0;
				$data_order['Order']['grand_total'] = $data_order['Order']['total_amount'];
			}	
		} else {
			$status = false;
			$message = __('order_not_valid_to_be_updated');
			goto return_result;
		}

		$dbo = $this->getDataSource();
		$dbo->begin();
		try {
			if ($this->saveAll($data_order)) {
				$params = $data_order;
				$dbo->commit();
				$status = true;
				$message = __('updating_member_success');
			} else {
				$dbo->rollback();
				$status = false;
				$message = __('updating_member_failed');
	
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

	public function get_order($id) {
		$option = array(
			'contain' => array(
				'OrderDetail'
			),
            'conditions' => array(
				'id' => $id,
			),
		);

		return $this->find('first', $option);
	}

    public function get_order_by_invoice_number($inv_number) {
        $option = array(
            'contain' => array(
                'OrderDetail'
            ),
            'conditions' => array(
                'inv_number' => $inv_number,
            ),
        );

        return $this->find('first', $option);
    }

	public function get_order_by_conditions($condition) {
		$options = array(
			'fields' => array(
				'ScheduleDetail.*',
				'Schedule.*',
				'Order.*',
				'Movie.*'
			),
			'joins' => array(
				array(
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetail.id = Order.schedule_detail_id',
					),
				),
				array(
					'alias' => 'Schedule',
					'table' => Environment::read('table_prefix') . 'schedules',
					'type' => 'left',
					'conditions' => array(
						'Schedule.id = ScheduleDetail.schedule_id',
					),
				),
				array(
					'alias' => 'Movie',
					'table' => Environment::read('table_prefix') . 'movies',
					'type' => 'left',
					'conditions' => array(
						'Movie.id = Schedule.movie_id',
					),
				),
			),
			'conditions' => $condition,
		);

		return $this->find('first', $options);
	}

	public function do_payment($data, $lang, $verification_code = '') {
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

		$option_order = array(
			'contain' => array(
				'OrderDetail'
			),
			'conditions' => array(
				'id' => $data['order_id'],
				'void' => 0,
				'is_paid' => 0,
				'status IN' => array(1,2)
			)
		);

		$data_order = $this->find('first', $option_order);

		if (isset($data_order['Order']['id']) && !empty($data_order['Order']['id'])) {
			//do nothing
		} else {
			$status = false;
			$message = __('order_not_valid_to_be_paid');
			goto return_result;
		}

		$update_order_detail = array();
		foreach($data['order_detail_coupon'] as $detail) {
			$update_order_detail[$detail['order_detail_id']] = $detail;
		}

		$total_amount = 0;
		foreach($data_order['OrderDetail'] as &$data_detail) {
			$data_detail['discount'] = $update_order_detail[$data_detail['id']]['discount'];
			$data_detail['subtotal'] = $update_order_detail[$data_detail['id']]['subtotal'];
			$total_amount += ($data_detail['subtotal'] * 1);
		}

		$data_order['Order']['total_amount'] = $total_amount;

		$coupon_number = Hash::extract($data['order_detail_coupon'], '{n}.number');

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
							$tmp_payment['order_id'] = $data_order['Order']['id'];
							$tmp_payment['payment_method_id'] = $payment_detail['id'];

							$index = array_search($number, $coupon_number);

							$tmp_payment['number'] = $number;
							if ($payment_detail['type'] == 2) {
								$tmp_payment['amount'] = abs($payment_detail['value']);
								$total_discount_coupon += abs($payment_detail['value']);
							} else {
								$tmp_payment['amount'] = isset($data['order_detail_coupon'][$index]['discount']) ? $data['order_detail_coupon'][$index]['discount'] : 0;
							}
							$tmp_payment['value'] = $payment_detail['value'];
							$payment[] = $tmp_payment;
							
						}
					} else if ($payment_detail['type'] == 1) {
						$tmp_payment = array();	
						$tmp_payment['order_id'] = $data_order['Order']['id'];
						$tmp_payment['payment_method_id'] = $payment_detail['id'];
						$tmp_payment['number'] = '';
						$tmp_payment['amount'] = $payment_detail['amount'];
						$tmp_payment['value'] = $payment_detail['value'];
						$payment[] = $tmp_payment;
					}
				}
			}
		}	

		$data_order['Order']['total_discount_coupon'] = $total_discount_coupon;

		$disc_amount = $total_amount * ($data_order['Order']['discount_percentage'] / 100);
		$data_order['Order']['discount_amount'] = $disc_amount;
		$data_order['Order']['grand_total'] = $total_amount - $total_discount_coupon - $disc_amount;

		$data_order['Order']['grand_total'] = $data_order['Order']['grand_total'] + $data_order['Order']['registration_fee'];

		$data_order['Order']['is_paid'] = 1;
		$data_order['Order']['paid_amount'] = ($data_order['Order']['grand_total'] > 0) ? $data_order['Order']['grand_total'] : 0;
		$data_order['Order']['status'] = 3;		
		$data_order['Order']['print_count'] = 1;

		// pr($payment);
		// pr($data_order['Order']);
		// pr($data_order['OrderDetail']);
		// exit;

		$is_registration = 0;
		if (isset($data_order['Order']['is_member_register']) && !empty($data_order['Order']['is_member_register'])) {
			$is_registration = 1;

			$current_date = date('Y-m-d');
			$data_register = array();
			$data_register['MemberPosRegistration']['order_id'] = $data['order_id'];
			$data_register['MemberPosRegistration']['staff_id'] = $data['staff_id'];
			$data_register['MemberPosRegistration']['member_id'] = 0;
			$data_register['MemberPosRegistration']['country_code'] = $data_order['Order']['country_code_registration'];
			$data_register['MemberPosRegistration']['phone'] = $data_order['Order']['phone_registration'];
			$data_register['MemberPosRegistration']['verification_code'] = $verification_code;
			$data_register['MemberPosRegistration']['date'] = date('Y-m-d H:i');
			$data_register['MemberPosRegistration']['expiry_date'] = date('Y-m-d', strtotime($current_date . ' +1 years'));
			$data_register['MemberPosRegistration']['amount'] = $data_order['Order']['registration_fee'];
			$data_register['MemberPosRegistration']['void'] = 0;
		}

		$dbo = $this->getDataSource();
		$dbo->begin();
		try {
			
			$objOrderDetailPayment = ClassRegistry::init('Pos.OrderDetailPayment');
			if ($objOrderDetailPayment->saveAll($payment)) {
				if ($this->saveAll($data_order)) {

					if ($is_registration > 0) {
						//insert
						//if error roll back all of them and return status error

						$objMemberPosRegistration = ClassRegistry::init('Member.MemberPosRegistration');
						if ($objMemberPosRegistration->saveAll($data_register)) {
							//doing nothing
						} else {
							$dbo->rollback();
							$status = false;
							$message = __('create_temp_registration_failed');
				
							goto return_result;
						}

					}

					$data_order_print = $this->get_data_print_order($data['order_id'], $lang);
					$params = $data_order_print;
					$dbo->commit();
					$status = true;
					$message = __('payment_saved_succesfully');
				} else {
					$dbo->rollback();
					$status = false;
					$message = __('create_order_failed');
		
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

	public function get_data_print_order($id, $lang, $is_reprint = false) {
		$lang = 'zho';
		$option = array(
			'fields' => array(
				'Order.inv_number',
				'Order.grand_total',
				'MovieLanguage.name',
				'MovieType.name',
				'Movie.rating',
				'OrderDetail.price',
				'TicketTypeLanguage.name',
				'ScheduleDetail.id',
				'ScheduleDetail.date',
				'ScheduleDetail.time',
				'Hall.code',
				'Cinema.code',
				'ScheduleDetailLayout.*'
			),
			'joins' => array(
				array(
					'alias' => 'OrderDetail',
					'table' => Environment::read('table_prefix') . 'order_details',
					'type' => 'left',
					'conditions' => array(
						'OrderDetail.order_id = Order.id',
					),
				),
				array(
					'alias' => 'ScheduleDetailTicketType',
					'table' => Environment::read('table_prefix') . 'schedule_detail_ticket_types',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetailTicketType.id = OrderDetail.schedule_detail_ticket_type_id',
					),
				),
				array(
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetail.id = Order.schedule_detail_id',
					),
				),
				array(
					'alias' => 'Schedule',
					'table' => Environment::read('table_prefix') . 'schedules',
					'type' => 'left',
					'conditions' => array(
						'Schedule.id = ScheduleDetail.schedule_id',
					),
				),
				array(
					'alias' => 'Movie',
					'table' => Environment::read('table_prefix') . 'movies',
					'type' => 'left',
					'conditions' => array(
						'Movie.id = Schedule.movie_id',
					),
				),
				array(
					'alias' => 'MovieLanguage',
					'table' => Environment::read('table_prefix') . 'movie_languages',
					'type' => 'left',
					'conditions' => array(
						'MovieLanguage.movie_id = Movie.id',
						'MovieLanguage.language' => $lang,
					),
				),
				array(
					'alias' => 'MovieType',
					'table' => Environment::read('table_prefix') . 'movie_types',
					'type' => 'left',
					'conditions' => array(
						'MovieType.id = Schedule.movie_type_id',
					),
				),
				array(
					'alias' => 'TicketTypeLanguage',
					'table' => Environment::read('table_prefix') . 'ticket_type_languages',
					'type' => 'left',
					'conditions' => array(
						'TicketTypeLanguage.ticket_type_id = ScheduleDetailTicketType.ticket_type_id',
						'TicketTypeLanguage.language' => 'zho',
					),
				),
				array(
					'alias' => 'Hall',
					'table' => Environment::read('table_prefix') . 'halls',
					'type' => 'left',
					'conditions' => array(
						'Hall.id = Schedule.hall_id',
					),
				),
				array(
					'alias' => 'Cinema',
					'table' => Environment::read('table_prefix') . 'cinemas',
					'type' => 'left',
					'conditions' => array(
						'Cinema.id = Hall.cinema_id',
					),
				),
				array(
					'alias' => 'ScheduleDetailLayout',
					'table' => Environment::read('table_prefix') . 'schedule_detail_layouts',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetailLayout.id = OrderDetail.schedule_detail_layout_id',
					),
				),
			),
			'conditions' => array(
				'Order.id' => $id
			),
		);

		$data_order_db = $this->find('all', $option);

		$option_payment = array(
			'fields' => array(
				'Order.inv_number',
				'PaymentMethod.name'
			),
			'joins' => array(
				array(
					'alias' => 'OrderDetailPayment',
					'table' => Environment::read('table_prefix') . 'order_detail_payments',
					'type' => 'left',
					'conditions' => array(
						'OrderDetailPayment.order_id = Order.id',
					),
				),
				array(
					'alias' => 'PaymentMethod',
					'table' => Environment::read('table_prefix') . 'payment_methods',
					'type' => 'left',
					'conditions' => array(
						'OrderDetailPayment.payment_method_id = PaymentMethod.id',
						'PaymentMethod.type' => 1
					),
				),
			),
			'conditions' => array(
				'Order.id' => $id
			),
		);

		$payment = $this->find('first', $option_payment);

		$alphabet = range('A', 'Z');
		$data_order = array();
		foreach($data_order_db as $data) {
			$order = array();
			$order['name'] = $data['MovieLanguage']['name'];
			$order['type'] = $data['MovieType']['name'];
			$order['category'] = $data['Movie']['rating'];
			$order['price'] = number_format($data['OrderDetail']['price'], 1);
			$order['price_display'] = number_format($data['OrderDetail']['price'], 1) . '/' . $data['TicketTypeLanguage']['name'];
			$order['ticket_type_name'] = $data['TicketTypeLanguage']['name'];
			$order['schedule_date'] = date('M d', strtotime($data['ScheduleDetail']['date']));
			$order['schedule_year'] = date('Y', strtotime($data['ScheduleDetail']['date']));
			$order['schedule_time'] = date('H:i', strtotime($data['ScheduleDetail']['time']));
			$order['schedule_house'] = $data['Hall']['code'];
			$order['schedule_seat'] = $alphabet[$data['ScheduleDetailLayout']['row_number']].$data['ScheduleDetailLayout']['label'];
			$order['payment_reference'] = $data['Order']['inv_number'];
			$order['sponsor'] = '';
			$order['reprint'] = $is_reprint;

			if ($data['Hall']['code'] == 'VIP House') {
				$order['sponsor'] = 'Support by OTO';
			}

			$data_order['ticket'][] = $order;
		}

		$staff = CakeSession::read('staff');
		$staff_name = $staff['Staff']['name'];

		$number_seats = Hash::extract($data_order['ticket'], '{n}.schedule_seat'); 
		$seats = implode(",",$number_seats);

		$data_order['receipt']['receipt_number'] = $data_order_db[0]['Order']['inv_number'];
		$data_order['receipt']['cinema'] = $data_order_db[0]['Cinema']['code'];
		$data_order['receipt']['movie'] = $data_order_db[0]['MovieLanguage']['name'];
		$data_order['receipt']['showtime'] = date('Ymd', strtotime($data_order_db[0]['ScheduleDetail']['date'])) . ' ' . date('H:i', strtotime($data_order_db[0]['ScheduleDetail']['time']));
		$data_order['receipt']['house_seats'] = $data_order_db[0]['Hall']['code']."/".$seats;
		$data_order['receipt']['payment_method'] = (isset($payment['PaymentMethod']['name']) && !empty($payment['PaymentMethod']['name'])) ? $payment['PaymentMethod']['name'] : '';
		$data_order['receipt']['amount'] = number_format($data_order_db[0]['Order']['grand_total'], 1);
		$data_order['receipt']['staff'] = $staff_name;
		$data_order['receipt']['reprint'] = $is_reprint;

		return $data_order;
	}

	public function get_data_order($id, $lang, $param_condition = null) {
		$status = false;
		$message = "";
		$params = array();

		/*
			0. get the order
			1. validate the order 
				-) not yet paid
				-) not void
		*/

		$option = array(
			'fields' => array(
				'Order.id',
				'Order.inv_number',
				'Order.status',
                'Order.is_paid',
				'Order.member_id',
				'Order.total_amount',
				'Order.grand_total',
                'Order.date',
                'Order.qrcode_path',
				'Order.discount_amount',
                'Order.print_count',
				'ScheduleDetail.id',
				'ScheduleDetail.date',
				'ScheduleDetail.time',
				'Hall.code',
				'MovieLanguage.name',
				'MovieType.name',
                'MovieType.id',
				'Cinema.location',
                'Movie.*'
			),
			'joins' => array(
				array(
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetail.id = Order.schedule_detail_id',
					),
				),
				array(
					'alias' => 'Schedule',
					'table' => Environment::read('table_prefix') . 'schedules',
					'type' => 'left',
					'conditions' => array(
						'Schedule.id = ScheduleDetail.schedule_id',
					),
				),
				array(
					'alias' => 'Hall',
					'table' => Environment::read('table_prefix') . 'halls',
					'type' => 'left',
					'conditions' => array(
						'Hall.id = Schedule.hall_id',
					),
				),
				array(
					'alias' => 'Cinema',
					'table' => Environment::read('table_prefix') . 'cinemas',
					'type' => 'left',
					'conditions' => array(
						'Cinema.id = Hall.cinema_id',
					),
				),
				array(
					'alias' => 'MovieLanguage',
					'table' => Environment::read('table_prefix') . 'movie_languages',
					'type' => 'left',
					'conditions' => array(
						'MovieLanguage.movie_id = Schedule.movie_id',
						'MovieLanguage.language' => $lang,
					),
				),
				array(
					'alias' => 'MovieType',
					'table' => Environment::read('table_prefix') . 'movie_types',
					'type' => 'left',
					'conditions' => array(
						'MovieType.id = Schedule.movie_type_id',
					),
				),
                array(
                    'alias' => 'Movie',
                    'table' => Environment::read('table_prefix') . 'movies',
                    'type' => 'left',
                    'conditions' => array(
                        'Movie.id = Schedule.movie_id'
                    ),
                ),
			),
			'conditions' => array(
				'Order.id' => $id,
				'Order.is_paid' => 0,
				'Order.void' => 0
			),
		);

        if (is_array($param_condition) && count($param_condition) > 0) {
            $option['conditions'] = $param_condition;
        }

		$data_order = $this->find('first', $option);

		$option_seat = array(
			'fields' => array(
				'ScheduleDetailLayout.*'
			),
			'joins' => array(
                array(
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.id = Order.schedule_detail_id',
                    ),
                ),
				array(
					'alias' => 'OrderDetail',
					'table' => Environment::read('table_prefix') . 'order_details',
					'type' => 'left',
					'conditions' => array(
						'OrderDetail.order_id = Order.id',
					),
				),
				array(
					'alias' => 'ScheduleDetailLayout',
					'table' => Environment::read('table_prefix') . 'schedule_detail_layouts',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetailLayout.id = OrderDetail.schedule_detail_layout_id',
					),
				),
			),
			'conditions' => array(
				'Order.id' => $id,
				'Order.is_paid' => 0,
				'Order.void' => 0
			),
		);

        if (is_array($param_condition) && count($param_condition) > 0) {
            $option_seat['conditions'] = $param_condition;
        }
		$data_order_seats = $this->find('all', $option_seat);

		$option_ticket_type = array(
			'fields' => array(
				'TicketTypeLanguage.name',
				'TicketType.is_main',
				'TicketType.is_disability',
				'TicketType.is_id_required',
				'ScheduleDetailTicketType.id',
				'ScheduleDetailTicketType.price'
			),
			'joins' => array(
				array(
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetail.id = Order.schedule_detail_id',
					),
				),
				array(
					'alias' => 'ScheduleDetailTicketType',
					'table' => Environment::read('table_prefix') . 'schedule_detail_ticket_types',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetailTicketType.schedule_detail_id = ScheduleDetail.id',
					),
				),
				array(
					'alias' => 'TicketType',
					'table' => Environment::read('table_prefix') . 'ticket_types',
					'type' => 'left',
					'conditions' => array(
						'TicketType.id = ScheduleDetailTicketType.ticket_type_id',
					),
				),
				array(
					'alias' => 'TicketTypeLanguage',
					'table' => Environment::read('table_prefix') . 'ticket_type_languages',
					'type' => 'left',
					'conditions' => array(
						'TicketTypeLanguage.ticket_type_id = TicketType.id',
						'TicketTypeLanguage.language' => $lang,
					),
				),
			),
			'conditions' => array(
				'Order.id' => $id,
				'Order.is_paid' => 0,
				'Order.void' => 0
			),
		);

        if (is_array($param_condition) && count($param_condition) > 0) {
            $option_ticket_type['conditions'] = $param_condition;
        }
		$data_ticket_type = $this->find('all', $option_ticket_type);

		if (isset($data_order['Order']['id']) && !empty($data_order['Order']['id']) &&
			isset($data_order_seats[0]['ScheduleDetailLayout']['id']) && !empty($data_order_seats[0]['ScheduleDetailLayout']['id']) &&
			isset($data_ticket_type[0]['ScheduleDetailTicketType']['id']) && !empty($data_ticket_type[0]['ScheduleDetailTicketType']['id'])) {
			$data_order['ScheduleDetail']['date_display'] = date('d M Y (D)', strtotime($data_order['ScheduleDetail']['date']));
			$data_order['ScheduleDetail']['time_display'] = date('H:i a', strtotime(date('Y-m-d', strtotime($data_order['ScheduleDetail']['date'])) . ' ' . $data_order['ScheduleDetail']['time']));

			$alphabet = range('A', 'Z');
			foreach($data_order_seats as &$seats) {
				$seats['ScheduleDetailLayout']['title'] = $alphabet[$seats['ScheduleDetailLayout']['row_number']].$seats['ScheduleDetailLayout']['label'];
			}

			$seats_number = Hash::extract($data_order_seats, '{n}.ScheduleDetailLayout.title'); 
			$disability_seats = Hash::extract($data_order_seats, '{n}.ScheduleDetailLayout.is_disability_seat'); 
			$data_order['Order']['seats'] = implode(",", $seats_number);
			$data_order['Order']['disability_seats'] = array_sum($disability_seats);
			$data_order['TicketType'] = $data_ticket_type;
			$data_order['Seat'] = $data_order_seats;

			$status = true;
			$params = $data_order;
			$message = __('trans_found');
		} else {
			$message = __('trans_not_found');
		}

		return_result :

		return array('status' => $status, 'message' => $message, 'params' => $params);

	}

    public function get_list_order_by_member($data) {
        $status = true;
        $message = __('retrieve_data_successfully');
        $params = (object)array();

        $objMember = ClassRegistry::init('Member.Member');
        $member_id = $objMember->get_id_by_token($data);

        if (empty($member_id)) {
            $status = false;
            $message = __('user_not_found');
            goto return_result;
        }

        $option = array(
            'fields' => array(
                'Order.id',
            ),
            'joins' => array(
            ),
            'conditions' => array(
                'Order.member_id' => $member_id,
                'Order.void' => 0
            ),
        );

        $data_order = $this->find('all', $option);
        $now = date('Y-m-d');

        $results = array();
        foreach ($data_order as $k => $v) {
            $condition = array(
                'Order.id' => $v['Order']['id'],
                'Order.void' => 0
            );
            $detail = $this->get_data_order($v['Order']['id'], $data['language'], $condition);
            $detail = $detail['params'];

            $all_seat = Hash::extract( $detail['Seat'], "{n}.ScheduleDetailLayout.title" );

            $detail['ScheduleDetail']['all_seat'] = implode(',', $all_seat);

            $quantity_ticket = 0;
            foreach ($detail['TicketType'] as $kTicketType => $vTicketType) {
                if(isset($vTicketType['TicketType']['qty'])) {
                    $quantity_ticket += $vTicketType['TicketType']['qty'];
                }
            }

            //$quantity_ticket = count($detail['Seat']);
            $detail['ScheduleDetail']['quantity_ticket'] = $quantity_ticket;

            $link_domain = Environment::read('web.url_img') ;
            $detail['Movie']['poster'] = $link_domain.$detail['Movie']['poster'];
            $detail['Order']['qrcode_path'] = $link_domain.$detail['Order']['qrcode_path'];

            if (isset($data['is_valid']) && $data['is_valid'] == 1) {
                if ($detail['ScheduleDetail'] > $now) {
                    $results[] = $detail;
                }
            } else if ((isset($data['is_valid']) && $data['is_valid'] == 0)) {
                if ($detail['ScheduleDetail'] < $now) {
                    $results[] = $detail;
                }
            }
        }
        $params = $results;

        return_result :

        return array('status' => $status, 'message' => $message, 'params' => $params);
	}
	
	public function is_ticket_sold($order_id) {

		$option = array(
			'fields' => array(
				'Order.id'
			),
			'joins' => array(
				array(
					'alias' => 'ScheduleDetail',
					'table' => Environment::read('table_prefix') . 'schedule_details',
					'type' => 'left',
					'conditions' => array(
						'ScheduleDetail.schedule_id = Schedule.id',
					),
				),
				array(
					'alias' => 'Order',
					'table' => Environment::read('table_prefix') . 'orders',
					'type' => 'left',
					'conditions' => array(
						'Order.schedule_detail_id = ScheduleDetail.id',
					),
				),
			),
			'conditions' => array(
				'Schedule.movie_id' => $order_id,
				'Order.status' => 3,
				'Order.is_paid' => 1,
				'Order.void' => 0
			)
		);

		$objSchedule = ClassRegistry::init('Movie.Schedule');
		$count_order = $objSchedule->find('count', $option);

		return $count_order > 0;
	}

	public function cancel_order($data, $data_order) {
        $status = true;
        $message = __('cancel_order_succesfully');
        $params = (object)array();

		$objScheduleDetailLayout = ClassRegistry::init('Movie.ScheduleDetailLayout');
		$seats = Hash::extract($data_order['OrderDetail'], '{n}.schedule_detail_layout_id'); 

        $objOrder = ClassRegistry::init('Pos.Order');
        $dbo = $this->getDataSource();
        $dbo->begin();
        try {
            $conditions_cancel_order = array(
                "Order.id" => $data['order_id']
            );
            $updates_cancel_order = array(
                "Order.status" => 5
			);
			
			$conditions_release_seat = array(
                "ScheduleDetailLayout.id" => $seats
            );
            $updates_release_seat = array(
                "ScheduleDetailLayout.status" => 1
            );

			if ($objOrder->updateAll($updates_cancel_order, $conditions_cancel_order) &&
				$objScheduleDetailLayout->updateAll($updates_release_seat, $conditions_release_seat)) {
                $dbo->commit();
            } else {
                $dbo->rollback();
                $status = false;
                $message = __('cancel_order_failed');
            }
        } catch (Exception $e) {
            $dbo->rollback();
            $status = false;
            $message = __('cancel_order_failed');
        }

        return array('status' => $status, 'message' => $message, 'params' => $params);
	}

	public function get_HKBO_report($data) {
		$status = false;
		$filename = '';
		$params = '';

		$condition_time = array(
			'OR' => array(
				array(
					'ScheduleDetail.date =' => $data['date_engagement'],
					'ScheduleDetail.time >=' => $data['time_start'],
				),
				array(
					'ScheduleDetail.date =' => $data['date_report'],
					'ScheduleDetail.time <' => $data['time_report'],
				),
			),
		);
		$extra_condition_time = "(a.date = '".$data['date_engagement']."' and a.time >= '" . $data['time_start'] . "') or " .
								"(a.date = '".$data['date_report']."' and a.time < '" . $data['time_report'] . "')";

		$tmp_date = $data['date_time_report'];
						
		$prefix_report = 'F';
		$root_name = 'boxoffice_final';
		if ($data['type'] == 'hourly') {

			$condition_time = array(
				'ScheduleDetail.date =' => $data['date_report'],
				'ScheduleDetail.time >=' => $data['time_start'],
				'ScheduleDetail.time <' => $data['time_report'],
			);

			$extra_condition_time = "a.date = '".$data['date_report']."' and " .
									"a.time >= '" . $data['time_start'] . "' and " .
									"a.time < '" . $data['time_report'] . "'";

			$prefix_report = 'H';
			$root_name = 'boxoffice_hourly';
		}

		$report = $this->get_header_report($data['date_of_report'], $data['datetime_start'], $data['datetime_end']);

		$prefix = Environment::read('database.prefix');

		$sqlstr = "CREATE TEMPORARY TABLE IF NOT EXISTS " . $prefix . "mytable_details AS (".
		"select b.id, sum(if(e.is_using_price_hkbo = 1, c.price_hkbo, c.price) - c.discount) as total_amount, count(c.id) as total_sold_seats " .
		"from " . $prefix . "schedule_details a " .
			  "left join " . $prefix . "orders b on b.schedule_detail_id = a.id " .
			  "left join " . $prefix . "order_details c on c.order_id = b.id " .
			  "left join " . $prefix . "schedules d on d.id = a.schedule_id " .
			  "left join " . $prefix . "halls e on e.id = d.hall_id " .
		"where b.status = 3 and b.void = 0 and " . $extra_condition_time . " " .
		"group by b.id)";

		$this->query($sqlstr);

		$sqlstr = "CREATE TEMPORARY TABLE IF NOT EXISTS " . $prefix . "mytables AS (".
				  "select a.id, sum(c.total_sold_seats) as total_sold_seats, " .
				  		"sum(c.total_amount - (c.total_amount * b.discount_percentage/100) - b.total_discount_coupon) as total_grand_total " .
				  "from " . $prefix . "schedule_details a " .
						"left join " . $prefix . "orders b on b.schedule_detail_id = a.id " .
						"left join " . $prefix . "mytable_details c on c.id = b.id " .
				  "where b.status = 3 and b.void = 0 and " . $extra_condition_time . " " .
				  "group by a.id)";

		$this->query($sqlstr);

		$option = array(
			'fields' => array(
				'ScheduleDetail.id',
				'Schedule.hall_id',
				'ScheduleDetail.date',
				'ScheduleDetail.time',
				'Movie.film_master_id',
				'Movie.id',
				'Movie.language',
				'Movie.subtitle',
				'MovieType.name',
				'MoviesMovieType.film_id',
				'Mytable.total_grand_total',
				'Mytable.total_sold_seats',
				'Hall.screen_id'
			),
			'joins' => array(
				array(
					'alias' => 'Schedule',
					'table' => Environment::read('table_prefix') . 'schedules',
					'type' => 'left',
					'conditions' => array(
						'Schedule.id = ScheduleDetail.schedule_id',
					),
				),
				array(
					'alias' => 'Hall',
					'table' => Environment::read('table_prefix') . 'halls',
					'type' => 'left',
					'conditions' => array(
						'Hall.id = Schedule.hall_id',
					),
				),
				array(
					'alias' => 'Movie',
					'table' => Environment::read('table_prefix') . 'movies',
					'type' => 'left',
					'conditions' => array(
						'Movie.id = Schedule.movie_id',
					),
				),
				array(
					'alias' => 'MovieType',
					'table' => Environment::read('table_prefix') . 'movie_types',
					'type' => 'left',
					'conditions' => array(
						'MovieType.id = Schedule.movie_type_id',
					),
				),
				array(
					'alias' => 'MoviesMovieType',
					'table' => Environment::read('table_prefix') . 'movies_movie_types',
					'type' => 'left',
					'conditions' => array(
						'MoviesMovieType.movie_id = Schedule.movie_id',
						'MoviesMovieType.movie_type_id = Schedule.movie_type_id',
					),
				),
				array(
					'alias' => 'Mytable',
					'table' => Environment::read('table_prefix') . 'mytables',
					'type' => 'left',
					'conditions' => array(
						'Mytable.id = ScheduleDetail.id',
					),
				),
			),
			'conditions' => array(
				'Hall.enabled' => 1,
				$condition_time,
			),
			'order' => array(
				'Schedule.movie_id' => 'asc',
				'ScheduleDetail.date' => 'asc',
				'ScheduleDetail.time' => 'asc',
			)
		);

		$objScheduleDetail = ClassRegistry::init('Movie.ScheduleDetail');
		$data_report = $objScheduleDetail->find('all', $option);

		$objMovieLanguage = ClassRegistry::init('Movie.MovieLanguage');
		
		if (count($data_report) > 0) {
			foreach($data_report as $myreport) {
				$myarray = array();

				$myarray['date_of_engagement'] = $data['date_engagement'];
				$myarray['screen_id'] = $myreport['Hall']['screen_id'];
				$myarray['show_time'] = date('Y-m-d', strtotime($myreport['ScheduleDetail']['date'])).' '.$myreport['ScheduleDetail']['time'];
				$myarray['film_master_id'] = $myreport['Movie']['film_master_id'];
				$myarray['film_id'] = $myreport['MoviesMovieType']['film_id'];
				$myarray['circuit_film_id'] = $myreport['Movie']['id'];	

				$option_title = array(
					'conditions' => array(
						'MovieLanguage.movie_id' => $myreport['Movie']['id']
					)				
				);

				$data_title = $objMovieLanguage->find('all', $option_title);

				$counter = 0;
				foreach($data_title as $title) {
					$counter++;
					$myarray['#'.$counter.'_title_name'] = $title['MovieLanguage']['name'];
					$myarray['@#'.$counter.'_lang'] = $title['MovieLanguage']['language'];
				}

				$myarray['film_language'] = $myreport['Movie']['language'];

				$subtitles = explode(';', $myreport['Movie']['subtitle']);

				if (count($subtitles) > 0) {
					$count = 0;
					foreach($subtitles as $subtitle) {
						$count++;
						$tmp_subtitle = '';
						if (trim($subtitle) == '中文') {
							$tmp_subtitle = 'Chinese';
						} else if (trim($subtitle) == '英文') {
							$tmp_subtitle = 'English';
						} else if (trim($subtitle) == '中英文') {
							$tmp_subtitle = 'Chinese English';
						}
						$myarray['sub_title_'.$count] = trim($tmp_subtitle);

					}
				} else {
					$myarray['sub_title_1'] = '';
					$myarray['sub_title_2'] = '';
				}

				if (count($subtitles) < 2) {
					$myarray['sub_title_2'] = '';
				}

				$myarray['film_version'] = '1.0';
				$myarray['remark_1'] = $myreport['MovieType']['name'];
				$myarray['remark_2'] = 'Dolby Atmos';
				$myarray['remark_3'] = '';

				$total_gross = (isset($myreport['Mytable']['total_grand_total']) && !empty($myreport['Mytable']['total_grand_total'])) ? $myreport['Mytable']['total_grand_total'] : 0;
				$myarray['total_gross'] = $total_gross;
				$myarray['total_admissions'] = (isset($myreport['Mytable']['total_sold_seats']) && !empty($myreport['Mytable']['total_sold_seats'])) ? $myreport['Mytable']['total_sold_seats'] : 0;

				$report['theatre'][] = $myarray;
			}
		} else {
			$report['theatre'][] = array();
		}

		$xml_data = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><'.$root_name.'></'.$root_name.'>');

		// function call to convert array to xml
		$this->array_to_xml($report,$xml_data);

		$webroot = Environment::read('web.physical_path');

		//saving generated xml file; 
		$filename = $prefix_report.'-ACX-01-'.$tmp_date.'.xml';
		$status = $xml_data->asXML($webroot.'webroot/img/uploads/'.$filename);
		$xml_result = $xml_data->asXML();
	
		$params = $xml_result;

		return array('status' => $status, 'filename' => $filename, 'params' => $params);
	}

	public function array_to_xml( $data, &$xml_data ) {
		foreach( $data as $key => $value ) {
			if( is_array($value) ) {
				if( is_numeric($key) ){
					$key = 'engagement'; //dealing with <0/>..<n/> issues
				}
				$subnode = $xml_data->addChild($key);
				$this->array_to_xml($value, $subnode);
			} else if (substr($key, 0, 1) == '@') {
				$tmp_key = substr($key, 1);
				if(substr($tmp_key, 0, 1) == '#') {
					$tmp_key = substr($tmp_key, 3);
				}
				$nodes->addAttribute($tmp_key, $value);
			} else {
				$tmp_key = $key;
				if(substr($key, 0, 1) == '#') {
					$tmp_key = substr($key, 3);
				}
				$nodes = $xml_data->addChild("$tmp_key",htmlspecialchars("$value"));
			}
		 }
	}

	public function get_header_report($date_of_report, $start_date, $end_date) {
		return array(
			'version' => array(
				'pos_util_version' => 'ACX Cinemas v1.0',
				'photonlink_spec' => 'v1.0'
			),
			'currency_code' => 'HKD',
			'date_of_report' => $date_of_report,
			'report_period_start' => $start_date,
			'report_period_end' => $end_date,
			'circuit_name' => 'ACX CINEMAS',
			'circuit_id' => 'ACX',
			'theatre' => array(
				'theatre_name' => 'ACX@HarbourNorth',
				'theatre_id' => 'acxhn'
			)
		);
	}

    public function get_data_export($conditions, $page, $limit, $lang){
        $now = date('Y-m-d');
        $model = $this->alias;

        $all_settings = array(
            'fields' => array(
                $model.".*",
                'Member.*',
                'MovieLanguage.*',
                'MovieType.*',
                'ScheduleDetail.*',
                'Schedule.*',
                'OrderPaymentLog.*',
                'count(OrderDetail.id) as amount_of_ticket',
                "group_concat(PaymentMethod.name) as payment_method_group"
            ),
            'joins' => array(
                array(
                    'alias' => 'Member',
                    'table' => Environment::read('table_prefix') . 'members',
                    'type' => 'left',
                    'conditions' => array(
                        'Order.member_id = Member.id',
                    ),
                ),
                array(
                    'alias' => 'OrderPaymentLog',
                    'table' => Environment::read('table_prefix') . 'order_payment_logs',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderPaymentLog.id = Order.payment_log_id',
                    ),
                ),
                array(
                    'alias' => 'OrderDetail',
                    'table' => Environment::read('table_prefix') . 'order_details',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetail.order_id = '.$model.'.id',
                    ),
                ),
                array(
                    'alias' => 'ScheduleDetailLayout',
                    'table' => Environment::read('table_prefix') . 'schedule_detail_layouts',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetailLayout.id = OrderDetail.schedule_detail_layout_id',
                    ),
                ),
                array(
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.id = ScheduleDetailLayout.schedule_detail_id',
                    ),
                ),
                array(
                    'alias' => 'Schedule',
                    'table' => Environment::read('table_prefix') . 'schedules',
                    'type' => 'left',
                    'conditions' => array(
                        'Schedule.id = ScheduleDetail.schedule_id',
                    ),
                ),
                array(
                    'alias' => 'MovieLanguage',
                    'table' => Environment::read('table_prefix') . 'movie_languages',
                    'type' => 'left',
                    'conditions' => array(
                        'MovieLanguage.movie_id = Schedule.movie_id',
                        'MovieLanguage.language' => $lang
                    ),
                ),
                array(
                    'alias' => 'MovieType',
                    'table' => Environment::read('table_prefix') . 'movie_types',
                    'type' => 'left',
                    'conditions' => array(
                        'MovieType.id = Schedule.movie_type_id',
                    ),
                ),
                array(
                    'alias' => 'OrderDetailPayment',
                    'table' => Environment::read('table_prefix') . 'order_detail_payments',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetailPayment.order_id = Order.id'
                    ),
                ),
                array(
                    'alias' => 'PaymentMethod',
                    'table' => Environment::read('table_prefix') . 'payment_methods',
                    'type' => 'left',
                    'conditions' => array(
                        'PaymentMethod.id = OrderDetailPayment.payment_method_id'
                    ),
                )
            ),
            'contain' => array(
//			    'OrderDetail' => array(
//			        'ScheduleDetailTicketType' => array(
//			            'ScheduleDetail' => array(
//			                'Schedule' => array(
//                            )
//                        )
//                    )
//                )
            ),
            'conditions' => array($conditions),
            'order' => array($model . '.created' => 'DESC'),
            'group' => array(
                'Order.id'
            ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
    }

    public function format_data_export($data, $row, $data_binding){
        $model = $this->alias;

        $date_row = $time_row = '';
        if (isset($row["ScheduleDetail"]["date"])) {
            $date_row = date('Y-m-d', strtotime($row["ScheduleDetail"]["date"]));
        };
        if (isset($row["ScheduleDetail"]["time"])) {
            $time_row = date('H:i', strtotime($row["ScheduleDetail"]["time"]));
        };

        $payment_method = '';
        if (isset($row[$model]['is_pos']) && $row[$model]['is_pos'] == 1) {
            $payment_method = $row[0]['payment_method_group'];
        } else {
            $payment_method = !empty($row["OrderPaymentLog"]["payType"]) ?  strtoupper($row["OrderPaymentLog"]["payType"]) : ' ';
        }

        $movie_row = !empty($row["MovieLanguage"]["name"]) ?  $row["MovieLanguage"]["name"] : ' ';
        $movie_type_row = !empty($row["MovieType"]["name"]) ?  $row["MovieType"]["name"] : ' ';

        $result = array(
            !empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
            !empty($row[$model]["date"]) ?  $row[$model]["date"] : ' ',
            !empty($row[$model]["inv_number"]) ? $row[$model]["inv_number"]  : ' ',
            !empty($row['Member']["name"]) ?  $row['Member']["name"] : ' ',
            !empty($row["MovieLanguage"]["name"]) ?  $movie_row . ' ('. $movie_type_row . ')' : ' ',
            !empty($row["ScheduleDetail"]["date"]) ?  $date_row . ' ' . $time_row  : ' ',
            !empty($row[0]['amount_of_ticket']) ?  $row[0]['amount_of_ticket'] : ' ',
            $payment_method,
            !empty($data_binding['status'][$row[$model]["status"]]) ? $data_binding['status'][$row[$model]["status"]]  : ' '
        );

        return $result;
    }

    public function get_data_today_ticket_export($data, $page, $limit, $lang){
        $now = date('Y-m-d');
        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_from'])));
        $date_to = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_to'])));
        $conditions = array(
            'DATE(Order.date) >=' => $date_from,
            'DATE(Order.date) <=' => $date_to,
            'Order.status' => 3,
            //'Order.void' => 0,
            'Order.id NOT IN' => array(568, 579),
        );
        $all_settings = array(
            'fields' => array(
                "Order.*",
                "Staff.*",
                "OrderPaymentLog.*",
                "Member.*",
                "group_concat(PaymentMethod.name) as payment_method_group"
            ),
            'conditions' => array($conditions),
            'joins' => array(
                array(
                    'alias' => 'Member',
                    'table' => Environment::read('table_prefix') . 'members',
                    'type' => 'left',
                    'conditions' => array(
                        'Member.id = Order.member_id'
                    ),
                ),
                array(
                    'alias' => 'Staff',
                    'table' => Environment::read('table_prefix') . 'staffs',
                    'type' => 'left',
                    'conditions' => array(
                        'Staff.id = Order.staff_id'
                    ),
                ),
                array(
                    'alias' => 'OrderPaymentLog',
                    'table' => Environment::read('table_prefix') . 'order_payment_logs',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderPaymentLog.id = Order.payment_log_id'
                    ),
                ),
                array(
                    'alias' => 'OrderDetailPayment',
                    'table' => Environment::read('table_prefix') . 'order_detail_payments',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetailPayment.order_id = Order.id'
                    ),
                ),
                array(
                    'alias' => 'PaymentMethod',
                    'table' => Environment::read('table_prefix') . 'payment_methods',
                    'type' => 'left',
                    'conditions' => array(
                        'PaymentMethod.id = OrderDetailPayment.payment_method_id'
                    ),
                )
            ),
            'contain' => array (
               // 'Staff' => array()
            ),
            //'limit' => Environment::read('web.limit_record'),
            'order' => array('Order.date' => 'DESC'),
            'group' => array(
                'Order.id'
            ),
//            'limit' => $limit,
//            'page' => $page
        );

        $result =$this->find('all', $all_settings);
        $total_result = array();

        foreach ($result as $k=>$v) {
            $order_conditions = array(
                'Order.id' => $v['Order']['id'],
            );
            $total_result[$k]['origin'] = $v;
            $temp = $this->get_data_order($v['Order']['id'], $lang, $order_conditions);
            if ($temp['status']) {
                $total_result[$k]['data_order'] = $temp['params'];
            }

            $objOrderDetail = ClassRegistry::init('Pos.OrderDetail');
            $conditon_order_detail = array (
                'order_id' => $v['Order']['id']
            );
            $list_order_detail = $objOrderDetail->get_order_detail_by_conditions($conditon_order_detail, $this->lang18);
            $total_result[$k]['detail_order'] = $list_order_detail;
        }

        return $total_result;
    }

    public function format_data_today_ticket_export($data, $row, $data_binding){
        $model = $this->alias;

        $date_row = $time_row = '';
        if (isset($row["data_order"]["ScheduleDetail"]["date"])) {
            $date_row = date('Y-m-d', strtotime($row["data_order"]["ScheduleDetail"]["date"]));
        };
        if (isset($row["data_order"]["ScheduleDetail"]["time"])) {
            $time_row = date('H:i', strtotime($row["data_order"]["ScheduleDetail"]["time"]));
        };
        $movie_row = !empty($row["data_order"]["MovieLanguage"]["name"]) ?  $row["data_order"]["MovieLanguage"]["name"] : ' ';
        $movie_type_row = !empty($row["data_order"]["MovieType"]["name"]) ?  $row["data_order"]["MovieType"]["name"] : ' ';

        $adult_amount = $student_amount = $senior_amount = $child_amount = 0;
        $total_ticket = 0;
        foreach ($row['detail_order'] as $k=>$v) {
            $code_ticket_type = $v['ScheduleDetailTicketType']['TicketType']['code'];

            if ($code_ticket_type == 'A') {
                $adult_amount += $v['OrderDetail']['qty'];
            }
            if ($code_ticket_type == 'S') {
                $student_amount += $v['OrderDetail']['qty'];
            }
            if ($code_ticket_type == 'SE') {
                $senior_amount += $v['OrderDetail']['qty'];
            }
            if ($code_ticket_type == 'C') {
                $child_amount += $v['OrderDetail']['qty'];
            }
            $total_ticket += $v['OrderDetail']['qty'];

        }

        $payment_method = '';
        $staff_str = 'Internet';

        if (isset($row['origin']['Order']['is_pos']) && $row['origin']['Order']['is_pos'] == 1) {
            $payment_method = $row['origin'][0]['payment_method_group'];

            $payment_method_code_format_array = explode(',', $payment_method);
            $payment_method_code_format_temp = array();
            foreach ($payment_method_code_format_array as $kFormatPaymentMethod => $vFormatPaymentMethod) {
                if (isset($payment_method_code_format_temp[$vFormatPaymentMethod])) {
                    $payment_method_code_format_temp[$vFormatPaymentMethod] += 1;
                }  else {
                    $payment_method_code_format_temp[$vFormatPaymentMethod] = 1;
                }
            }

            $payment_method_code_format = array();
            foreach ($payment_method_code_format_temp as $kFormat => $vFormat) {
                if ($vFormat > 1) {
                    $payment_method_code_format[$kFormat] = $kFormat . "(" . $vFormat . ")";
                } else {
                    $payment_method_code_format[$kFormat] = $kFormat;
                }
            }
            $payment_method = implode(', ', $payment_method_code_format);

            $staff_str = !empty($row["origin"]["Staff"]["name"]) ?  $row["origin"]["Staff"]["name"] : ' ';
        } else {
            $payment_method = !empty($row["origin"]["OrderPaymentLog"]["payType"]) ?  strtoupper($row["origin"]["OrderPaymentLog"]["payType"]) : ' ';
        }

        $seats_str = '';
        if (isset($row['data_order']['Seat'])) {
            $seat_array = Hash::extract( $row['data_order']['Seat'], "{n}.ScheduleDetailLayout.title" );
            $seats_str = implode(', ', $seat_array);
        }

        $result = array(
            !empty($row["origin"]["Order"]["inv_number"]) ?  $row["origin"]["Order"]["inv_number"]." " : ' ',
            !empty($row["origin"]["Order"]["date"]) ?  date('Y-m-d', strtotime($row["origin"]["Order"]["date"])) : ' ',
            !empty($row["origin"]["Order"]["date"]) ?  $row["origin"]["Order"]["date"] : ' ',
            $staff_str,
            !empty($row["origin"]["Order"]["total_amount"]) ?  $row["origin"]["Order"]["total_amount"] : ' ',
            !empty($row["origin"]["Order"]["total_discount_coupon"]) ?  $row["origin"]["Order"]["total_discount_coupon"] : 0,
            !empty($row["origin"]["Order"]["discount_amount"]) ?  $row["origin"]["Order"]["discount_amount"] : 0,
            !empty($row["origin"]["Order"]["grand_total"]) ?  $row["origin"]["Order"]["grand_total"] : 0,
            $payment_method,
            !empty($row["origin"]["Order"]["void"]) ?  "Y" : "",
            !empty($row["origin"]["Member"]["code"]) ?  $row["origin"]["Member"]["code"] : ' ',
            !empty($row["data_order"]["MovieLanguage"]["name"]) ?  $movie_row . ' ('. $movie_type_row . ')' : ' ',
            !empty($row["data_order"]["ScheduleDetail"]["date"]) ?  date('Y-m-d', strtotime($row["data_order"]["ScheduleDetail"]["date"])) : ' ',
            !empty($row["data_order"]["ScheduleDetail"]["date"]) ?  $date_row . ' ' . $time_row  : ' ',
            $seats_str,
            !empty($row["data_order"]["Hall"]["code"]) ?  $row["data_order"]["Hall"]["code"] : ' ',
            $total_ticket,
            (string)$adult_amount,
            (string)$student_amount,
            (string)$senior_amount,
            (string)$child_amount
        );

        return $result;
    }
    public function get_data_today_tuckshop_export($data, $page, $limit, $lang){
        $now = date('Y-m-d');
        $date_from = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_from'])));
        $date_to = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date_to'])));

        $objOrderDetail = ClassRegistry::init('Pos.Purchase');
        $conditions = array(
            'DATE(Purchase.date) >= ' => $date_from,
            'DATE(Purchase.date) <= ' => $date_to,
            'Purchase.status' => 3,
            'Purchase.void' => 0,
//            'Purchase.id' => 2
        );

        $all_settings = array(
            'fields' => array(
                "Purchase.*",
                "Staff.*",
                "Member.*",
                'count(PurchaseDetail.id) as number_of_meal',
                "group_concat(PaymentMethod.name) as payment_method_group",
                "group_concat(PurchaseDetail.item_id) as item_id_list"
            ),
            'conditions' => array($conditions),
            'joins' => array(
                array(
                    'alias' => 'Member',
                    'table' => Environment::read('table_prefix') . 'members',
                    'type' => 'left',
                    'conditions' => array(
                        'Member.id = Purchase.member_id'
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
                        'PurchaseDetail.purchase_id = Purchase.id'
                    ),
                ),
                array(
                    'alias' => 'PurchaseDetailPayment',
                    'table' => Environment::read('table_prefix') . 'purchase_detail_payments',
                    'type' => 'left',
                    'conditions' => array(
                        'PurchaseDetailPayment.purchase_id = Purchase.id'
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
            'contain' => array (
                'PurchaseDetail' => array()
            ),
//            'limit' => Environment::read('web.limit_record'),
            'order' => array('Purchase.created' => 'DESC'),
            'group' => array(
                'Purchase.id'
            ),
//            'limit' => $limit,
//            'page' => $page
        );

        $result = $objOrderDetail->find('all', $all_settings);

        $total_result = array();

        foreach ($result as $k=>$v) {
            $total_result[$k]['origin'] = $v;

//            $objOrderDetail = ClassRegistry::init('Pos.OrderDetail');
//            $conditon_order_detail = array (
//                'order_id' => $v['Order']['id']
//            );
//            $list_order_detail = $objOrderDetail->get_order_detail_by_conditions($conditon_order_detail, $this->lang18);
            //$total_result[$k]['detail_order'] = $list_order_detail;
        }

        return $total_result;
    }

    public function format_data_today_tuckshop_export($data, $row, $data_binding){
        $model = $this->alias;

        $list_item = !empty($row["origin"]['PurchaseDetail']) ?  $row["origin"]['PurchaseDetail'] : null;

        $row_extra = array();

        if (!empty($list_item)) {
            foreach ($data_binding['list_item'] as $k => $v) {
                $row_extra[$k] = 0;
                foreach ($list_item as $i => $j) {
                    if ($j['item_id'] == $v['Item']['id']) {
                        $row_extra[$k] = $row_extra[$k] + $j['qty'];
                    }
                }
            }
            foreach ($row_extra as $k=>$v) {
                $row_extra[$k] = $v. " ";
            }
        }


        $result = array(
            !empty($row["origin"]["Purchase"]["inv_number"]) ?  $row["origin"]["Purchase"]["inv_number"]." " : ' ',
            !empty($row["origin"]["Purchase"]["date"]) ?  $row["origin"]["Purchase"]["date"] : ' ',
            !empty($row["origin"]["Staff"]["name"]) ?  $row["origin"]["Staff"]["name"] : ' ',
            !empty($row["origin"]["Purchase"]["total_amount"]) ?  $row["origin"]["Purchase"]["total_amount"] : ' ',
            !empty($row["origin"]["Purchase"]["total_discount_coupon"]) ?  $row["origin"]["Purchase"]["total_discount_coupon"] : 0,
            !empty($row["origin"]["Purchase"]["discount_amount"]) ?  $row["origin"]["Purchase"]["discount_amount"] : 0,
            !empty($row["origin"]["Purchase"]["paid_amount"]) ?  $row["origin"]["Purchase"]["paid_amount"] : 0,
            !empty($row["origin"][0]["payment_method_group"]) ?  $row["origin"][0]["payment_method_group"] : 0,
            !empty($row["origin"]["Member"]["code"]) ?  $row["origin"]["Member"]["code"] : ' ',
            !empty($row["origin"][0]["number_of_meal"]) ?  $row["origin"][0]["number_of_meal"] : ' ',
        );

        $result = array_merge($result, $row_extra);

        return $result;
	}
	
	public function void_order($data, $data_order) {
        $status = false;
        $message = '';
        $params = (object)array();

		$objScheduleDetailLayout = ClassRegistry::init('Movie.ScheduleDetailLayout');
		$seats = Hash::extract($data_order['OrderDetail'], '{n}.schedule_detail_layout_id'); 

		$objPaymentLog = ClassRegistry::init('Pos.OrderPaymentLog');
		$payment_array = array();
		$data_refund = array();
		$is_refund = false;
		$field_string = "";
		if (($data_order['Order']['is_pos'] != 1) && ($data_order['Order']['status'] == 3)) {
			$is_refund = true;

			$option_payment_log = array(
				'conditions' => array(
					'merRef' => $data_order['Order']['inv_number']
				)
			);

			$data_payment_log = $objPaymentLog->find('first', $option_payment_log);

			if (!isset($data_payment_log['OrderPaymentLog']['id']) || empty($data_payment_log['OrderPaymentLog']['id'])) {
				$status = false;
				$message = __('void_order_succesfully');
				goto return_result;
			}

			$grand_total = $data_order['Order']['grand_total'] * 100;
			$refund_inv_number = "REF_".$data_order['Order']['inv_number'];
			$payment_array = array(
				'amt' => $grand_total, // HKD 123.45
				'merCode' => Environment::read('site.recon.merCode'),
				'merRef' => $refund_inv_number,
				'payRef' => $data_payment_log['OrderPaymentLog']['payRef'],
				'reason' => 'cust canceled',
				'ver' => "1",
			);

			ksort($payment_array);
			$payment_string = "";
			foreach ($payment_array as $key => $val) {
				$payment_string .= trim($key) . "=" . trim($val) . "&";
			}
			$payment_string 			.= Environment::read('site.recon.merchant_id');
			$hashed_string 				= hash('sha256', trim($payment_string));
			$payment_array['signType'] 	= "SHA-256";
			$payment_array['sign'] 		= trim($hashed_string);
			ksort($payment_array);
			
			foreach($payment_array as $key=>$value) { $field_string .= $key.'='.$value.'&'; }
			rtrim($field_string, '&');

			$data_refund['OrderPaymentLog']['trans_token'] = $data_order['Order']['token'];
			$data_refund['OrderPaymentLog']['date'] = date('Y-m-d H:i');
			$data_refund['OrderPaymentLog']['amt'] = $grand_total;
			$data_refund['OrderPaymentLog']['authId'] = '';
			$data_refund['OrderPaymentLog']['chRef'] = '';
			$data_refund['OrderPaymentLog']['chResCode'] = '';
			$data_refund['OrderPaymentLog']['curr'] = 'HKD';
			$data_refund['OrderPaymentLog']['eci'] = '';
			$data_refund['OrderPaymentLog']['merData'] = '';
			$data_refund['OrderPaymentLog']['merRef'] = $refund_inv_number;
			$data_refund['OrderPaymentLog']['orig_merRef'] = $data_order['Order']['inv_number'];
			$data_refund['OrderPaymentLog']['panHash'] = '';
			$data_refund['OrderPaymentLog']['panHashType'] = '';
			$data_refund['OrderPaymentLog']['payRef'] = '';
			$data_refund['OrderPaymentLog']['orig_payRef'] = $payment_array['payRef'];
			$data_refund['OrderPaymentLog']['payType'] = '';
			$data_refund['OrderPaymentLog']['resMsg'] = '';
			$data_refund['OrderPaymentLog']['sign'] = $payment_array['sign'];
			$data_refund['OrderPaymentLog']['signType'] = $payment_array['signType'];
			$data_refund['OrderPaymentLog']['state'] = '';
		}

        $dbo = $this->getDataSource();
        $dbo->begin();
        try {
            $conditions_void_order = array(
                "Order.id" => $data['order_id']
            );
            $updates_void_order = array(
                "Order.void" => 1
			);
			
			$conditions_release_seat = array(
                "ScheduleDetailLayout.id" => $seats
            );
            $updates_release_seat = array(
                "ScheduleDetailLayout.status" => 1
            );

			if ($this->updateAll($updates_void_order, $conditions_void_order) &&
				$objScheduleDetailLayout->updateAll($updates_release_seat, $conditions_release_seat)) {

				$prefix = Environment::read('database.prefix');
				$sqlstr =  "update " . $prefix . "schedule_details a left join ( ".
									"select schedule_detail_id, count(id) as used_seats ".
									"from " . $prefix . "schedule_detail_layouts ".
									"where status > 1 and is_blocked_seat = 0 and enabled = 1 ".
										"and schedule_detail_id in (".$data_order['Order']['schedule_detail_id'].") ".
									"group by schedule_detail_id ".
								") b on b.schedule_detail_id = a.id ".
							"set a.attendance_rate = b.used_seats / a.capacity * 100 ".
							"where a.id in (".$data_order['Order']['schedule_detail_id'].")";

				$this->query($sqlstr);

				if ($is_refund) {

					if ($objPaymentLog->saveAll($data_refund)) {
						$refund_id = $objPaymentLog->id;
					} else {
						$dbo->rollback();
						$status = false;
						$message = __('unable_to_create_refund_payment');

						goto return_result;
					}

					//execute curl
					$url = Environment::read('site.recon.actionRefundURL');

					$curl = curl_init();
					curl_setopt_array($curl, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_URL => $url,
						CURLOPT_POST => count($payment_array),
						CURLOPT_SSL_VERIFYPEER => false,
						CURLOPT_POSTFIELDS => $field_string
					));
				
					$resp = curl_exec($curl);
					curl_close($curl);

					$obj = json_decode($resp);

					if ($obj->state == 1) {
						$data_refund['OrderPaymentLog']['id'] = $refund_id;
						$data_refund['OrderPaymentLog']['authId'] = $obj->authId;
						$data_refund['OrderPaymentLog']['chRef'] = $obj->chRef;
						$data_refund['OrderPaymentLog']['chResCode'] = $obj->chResCode;
						$data_refund['OrderPaymentLog']['eci'] = $obj->eci;
						$data_refund['OrderPaymentLog']['merData'] = $obj->merData;
						$data_refund['OrderPaymentLog']['panHash'] = $obj->panHash;
						$data_refund['OrderPaymentLog']['panHashType'] = $obj->panHashType;
						$data_refund['OrderPaymentLog']['payRef'] = $obj->payRef;
						$data_refund['OrderPaymentLog']['payType'] = $obj->payType;
						$data_refund['OrderPaymentLog']['resMsg'] = $obj->resMsg;
						$data_refund['OrderPaymentLog']['state'] = $obj->state;

						$conditions_update_order = array(
							"Order.id" => $data['order_id']
						);
						$updates_order = array(
							"Order.refund_log_id" => $refund_id
						);

						if ($this->updateAll($updates_order, $conditions_update_order) &&
						$objPaymentLog->saveAll($data_refund)) {

						} else {
							$dbo->rollback();
							$status = false;
							$message = __('unable_to_update_refund_payment');

							goto return_result;
						}


					} else {
						$dbo->rollback();
						$status = false;
						$message = __('unable_to_refund_payment') . ', msg : ' . $obj->resMsg;	
						
						goto return_result;
					}
				}

				$dbo->commit();
				$status = true;
				$message = __('void_order_succesfully');
            } else {
                $dbo->rollback();
                $status = false;
				$message = __('void_order_failed');
            }
        } catch (Exception $e) {
            $dbo->rollback();
            $status = false;
            $message = __('void_order_failed') . ', err : ' . $e->getMessage();
        }

		return_result:
        return array('status' => $status, 'message' => $message, 'params' => $params);
	}

	public function hold_order($data, $data_order) {
        $status = false;
        $message = '';
        $params = (object)array();

        $dbo = $this->getDataSource();
        $dbo->begin();
        try {
            $conditions_hold_order = array(
                "Order.id" => $data['order_id']
            );
            $updates_hold_order = array(
				"Order.status" => 6,
				"Order.remark" => "'".$data['remark']."'",
			);

			if ($this->updateAll($updates_hold_order, $conditions_hold_order)) {

				$prefix = Environment::read('database.prefix');
				$sqlstr =  "update " . $prefix . "schedule_details a left join ( ".
									"select schedule_detail_id, count(id) as used_seats ".
									"from " . $prefix . "schedule_detail_layouts ".
									"where status > 1 and is_blocked_seat = 0 and enabled = 1 ".
										"and schedule_detail_id in (".$data_order['Order']['schedule_detail_id'].") ".
									"group by schedule_detail_id ".
								") b on b.schedule_detail_id = a.id ".
							"set a.attendance_rate = b.used_seats / a.capacity * 100 ".
							"where a.id in (".$data_order['Order']['schedule_detail_id'].")";

				$this->query($sqlstr);

				$dbo->commit();
				
				$status = true;
				$message = __('hold_order_succesful');
            } else {
                $dbo->rollback();
                $status = false;
                $message = __('hold_order_failed');
            }
        } catch (Exception $e) {
            $dbo->rollback();
            $status = false;
            $message = __('void_order_failed');
        }

        return array('status' => $status, 'message' => $message, 'params' => $params);
	}
}
