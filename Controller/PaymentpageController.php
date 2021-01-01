<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 */
class PaymentpageController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

	public function index($module = 'order', $id = 0) {
		
		$model = '';
		$printer_setting = Environment::read('site.printer_setting');
		if ($module == 'order') {
			$objOrder = ClassRegistry::init('Pos.Order');
			$data_trans = $objOrder->get_order($id);
			$model = 'Order';
			$controller_home = 'ticketingpage';
			$controller_payment = 'orders';
			$printer_address = Environment::read('site.'.$printer_setting.'.ticketing');
			$printer_port = Environment::read('site.'.$printer_setting.'.ticketing');
		} else if ($module == 'purchase') {
			$objPurchase = ClassRegistry::init('Pos.Purchase');
			$data_trans = $objPurchase->get_purchase($id);
			$model = 'Purchase';
			$controller_home = 'snackpage';
			$controller_payment = 'purchases';
			$printer_address = Environment::read('site.'.$printer_setting.'.tuckshop');
			$printer_port = Environment::read('site.'.$printer_setting.'.tuckshop');
		}

		if (!isset($data_trans[$model]['id']) || empty($data_trans[$model]['id'])) {
			$this->redirect(array('controller' => $controller_home,'action' => 'index'));
		}

		$order_detail = array();
		$extra_conditions = array();
		if ($module == 'order') {
			$option_order_detail = array(
				'conditions' => array(
					'order_id' => $id
				),
				'order' => array(
					'price' => 'DESC'
				)
			);
	
			$objOrderDetail = ClassRegistry::init('Pos.OrderDetail');
			$order_detail = $objOrderDetail->find('all', $option_order_detail);
		} else if ($module == 'purchase') {
			$extra_conditions = array('type <' => 3);
		}

		$objPaymentMethod = ClassRegistry::init('Pos.PaymentMethod');
		$payment_methods = $objPaymentMethod->get_active_payment_method($extra_conditions);

		/*
			check if status = paid, then get order print
		*/
		$data_print = array();
		if (($data_trans[$model]['status'] == 3) && ($module == 'order')) {
			$reprint = false;
			//$reprint = ($data_trans[$model]['printed'] > 1) ? true : false;
			$data_print = $objOrder->get_data_print_order($data_trans[$model]['id'], $this->lang18, $reprint);
		}


		$this->set(compact('payment_methods', 'data_trans', 'model', 'order_detail', 'controller_home', 'controller_payment', 'data_print', 'printer_address'));
	}

	public function payment($order_id) {
		$this->layout = 'special';
		$payment_recon = array();
		$order = array();
		$params = array(
			"order_id" => $order_id,
			"language" => $this->lang18
		);
		$url = Environment::read('web.url_host');
		$resp = $this->Common->curl_post($url.'api/pos/orders/get_data_created_order.json', $params);
        if($resp!=false){
			$resp = json_decode($resp, true);

            if($resp['status']==true){
                if(isset($resp['params']) && !empty($resp['params'])){
                    $order = $resp['params'];
                    $lang = 'en';
					switch ($this->lang18) {
						case 'eng':
							$lang = 'en';
							break;
						case 'zho':
							$lang = 'tc';
							break;
						case 'chi':
							$lang = 'sc';
							break;
					}
                    $payment_recon = array(
						'amt' 		=> strval($order['Order']['grand_total'] * 100), // HKD 123.45
						'curr' 		=> Environment::read('site.recon.curr'),
						'desc'		=> $order['Order']['inv_number'],
						'lang' 		=> $lang,
						'merCode' 	=> Environment::read('site.recon.merCode'),
						'merRef' 	=> $order['Order']['inv_number'],
						'notifyUrl' => Environment::read('web.url_host').Environment::read('site.recon.notifyUrl_Order'),
						'returnUrl' => Environment::read('web.url_host').'paymentpage/payment_return_url/1',
						'timeout' 	=> Environment::read('site.recon.timeout'),
						'ver' 		=> Environment::read('site.recon.ver'),
					);
					// sort the payment array by the KEY in ASEC order
					ksort($payment_recon);
					/**
					 * it may be possible to use `http_build_query()` here but it takes time to check
					 */
					$payment_string = "";
					foreach ($payment_recon as $key => $val) {
						$payment_string .= trim($key) . "=" . trim($val) . "&";
					}
					$payment_string 			.= Environment::read('site.recon.merchant_id');
					$hashed_string 				= hash('sha256', trim($payment_string));
					$payment_recon['signType'] 	= "SHA-256";
					$payment_recon['sign'] 		= trim($hashed_string);
					ksort($payment_recon);
                }
			}
		
		}
		
		$actionURL = Environment::read('site.recon.actionURL');

		$this->set(compact('order_id', 'order', 'payment_recon', 'actionURL'));
	}

	public function payment_member_renewal($token) {
		$this->layout = "special";
		$payment_array = array();

		$params = array(
			'language' => $this->lang18,
			'token' => $token
		);

		$url = Environment::read('web.url_host');
		$resp = $this->Common->curl_post($url.'api/member/member_renewals/create_member_renewal_trans.json', $params);
		if($resp!=false){
			$resp = json_decode($resp, true);
			if($resp['status']==true){
				$params = $resp['params'];

				$lang = 'en';
				switch ($this->lang18) {
					case 'eng':
						$lang = 'en';
						break;
					case 'zho':
						$lang = 'tc';
						break;
					case 'chi':
						$lang = 'sc';
						break;
				}
				$payment_array = array(
					'amt' 		=> strval($params['MemberRenewal']['amount'] * 100), // HKD 123.45
					'curr' 		=> Environment::read('site.recon.curr'),
					'desc'		=> $params['MemberRenewal']['inv_number'],
					'lang' 		=> $lang,
					'merCode' 	=> Environment::read('site.recon.merCode'),
					'merRef' 	=> $params['MemberRenewal']['inv_number'],
					'notifyUrl' => Environment::read('web.url_host').Environment::read('site.recon.notifyUrl'),
					'returnUrl' => Environment::read('web.url_host').'paymentpage/payment_return_url/2',
					'timeout' 	=> Environment::read('site.recon.timeout'),
					'ver' 		=> Environment::read('site.recon.ver'),
				);
				// sort the payment array by the KEY in ASEC order
				ksort($payment_array);
				/**
				 * it may be possible to use `http_build_query()` here but it takes time to check
				 */
				$payment_string = "";
				foreach ($payment_array as $key => $val) {
					$payment_string .= trim($key) . "=" . trim($val) . "&";
				}
				$payment_string 			.= Environment::read('site.recon.merchant_id');
				$hashed_string 				= hash('sha256', trim($payment_string));
				$payment_array['signType'] 	= "SHA-256";
				$payment_array['sign'] 		= trim($hashed_string);
				ksort($payment_array);
				
			}
		}

		$actionURL = Environment::read('site.recon.actionURL');
		$this->set(compact('payment_array', 'actionURL'));
	}

	public function payment_return_url($id) {
		$this->layout = 'dashboard';
		$is_register = ($id == 2) ? true : false;
		$payment_return_data = $this->request->data;
		if($is_register){
			if($payment_return_data['state']!=1){
				$params = array(
					'language' => $this->lang18,
					'inv_number' => $payment_return_data['merRef'],
					'message' => $payment_return_data['resMsg'],
				);
				if($this->current_user){
					$params['token'] = $this->current_user['token'];
				}
				$url = Environment::read('web.url_host');
				$resp = $this->Common->curl_post($url.'api/member/member_renewals/cancel_member_renewal_trans.json', $params);
			}
		}
		$this->set(compact('payment_return_data', 'is_register'));
	}

	public function payment_notify_url() {
		pr('this is notify url');
		exit;
	}

	public function try_again() {
		pr('Opening payment page ...');
		exit;
	}

	public function get_started() {
		pr('Opening home page ...');
		exit;
	}
}