<?php
App::uses('DashboardAppController', 'Dashboard.Controller');
/**
 * Dashboard Controller
 *
 * @property PaginatorComponent $Paginator
 */
class DashboardController extends DashboardAppController {
/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');
    private $model = 'Dashboard';

	public function beforeFilter(){
		parent::beforeFilter();

		$this->set('title_for_layout', __d('dashboard', 'dashboard'));
	}

    /**
     * admin_index method
     *
     * @return void
     */
	public function admin_index() {
        $now = date('Y-m-d');
        //$now = '2020-11-28';
        $total_amount_ticket_by_day = 0;
        $total_amount_tuckshop_by_day = 0;
        $total_amount_member_by_day = 0;
        $total_ticket = 0;
        $payment_list = array();
        $list_map_movie_schedule = array();
        $movie_info = array();

        $data_search = $this->request->query;

        if (!isset($data_search['date_from']) || empty($data_search['date_from']) &&
            !isset($data['$data_search']) || empty($data_search['date_to'])) {
            $date_from = $data_search['date_from'] = $now;
            $date_to = $data_search['date_to'] = $now;
        } else if (!isset($data_search['date_from']) || empty($data_search['date_from']) ||
            !isset($data_search['date_to']) || empty($data_search['date_to'])) {
            $this->Session->setFlash(__('date_time_report_invalid'), 'flash/error');
            $type = '';
            goto display_initial_data;
        }


        //if (isset($data_search['date_from']) && !empty($data_search['date_from'])) {
        $date_from = $data_search['date_from'];
        //}

        //if (isset($data_search['date_to']) && !empty($data_search['date_to'])) {
        $date_to = $data_search['date_to'];
        //}

        // Daily total amount of income from the POS (total amount from ticketing, total amount from tuck shop, total amount from offline member registration)
        // Number of tickets has been issued (including using the coupons / exchange tickets

        $result_sales_ticket = $this->get_sales_ticket($date_from, $date_to);
        $total_amount_ticket_by_day = $result_sales_ticket['total_amount_ticket_by_day'];
        $total_ticket = $result_sales_ticket['total_ticket'];

        $result_sales_tuckshop = $this->get_sales_tuckshop($date_from, $date_to);
        $total_amount_tuckshop_by_day = $result_sales_tuckshop['total_amount_tuckshop_by_day'];

        $result_sales_member = $this->get_sales_member($date_from, $date_to);
        $total_amount_member_by_day = $result_sales_member['total_amount_member_by_day'];


        // For each movie, the total amount and number of tickets sold per movie
       $result_sales_movie = $this->get_sales_movie_update($date_from, $date_to);
       $list_map_movie_schedule = $result_sales_movie['list_map_movie_schedule'];

        // the distribution in term of $$ per payment method
        $result_payment_statistic = $this->get_payment_statistic($date_from, $date_to);
        $payment_list = $result_payment_statistic['payment_list'];

        display_initial_data :
        $this->set(compact(
            'data_search'
        ));

        $this->set(compact(
             'total_amount_ticket_by_day'
             ,'total_amount_tuckshop_by_day'
             ,'total_amount_member_by_day'
             ,'list_map_movie_schedule'
             ,'total_ticket'
             ,'payment_list'
         ));
    }

    public function get_sales_ticket($date_from, $date_to) {
        // Daily total amount of income from the POS (total amount from ticketing, total amount from tuck shop, total amount from offline member registration)
        $objOrder = ClassRegistry::init('Pos.Order');
        $option = array(
            'fields' => array(
                'Order.*',
                'sum(OrderDetail.qty) as number_of_ticket'
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
            ),
            'conditions' => array(
                'DATE(Order.date) >=' => $date_from,
                'DATE(Order.date) <=' => $date_to,
                'Order.status' => 3,
//                'Order.is_pos' => 1
            ),
            'contain' => array(
                'OrderDetail' => array()
            ),
            'group' => array(
                'Order.id'
            ),
            'order' => array(

            )
        );
        $result_order = $objOrder->find('all', $option);
        $total_amount_ticket_by_day = array_sum(Hash::extract( $result_order, "{n}.Order.grand_total"));
        $total_ticket = array_sum(Hash::extract( $result_order, "{n}.0.number_of_ticket"));

        return array(
            'total_amount_ticket_by_day' => $total_amount_ticket_by_day,
            'total_ticket' => $total_ticket
        );
    }

    public function get_sales_tuckshop($date_from, $date_to) {
        $objPurchase = ClassRegistry::init('Pos.Purchase');
        $option = array(
            'fields' => array(
                'Purchase.*'
            ),
            'joins' => array(
                array(
                    'alias' => 'PurchaseDetail',
                    'table' => Environment::read('table_prefix') . 'purchase_details',
                    'type' => 'left',
                    'conditions' => array(
                        'PurchaseDetail.purchase_id = Purchase.id',
                    ),
                ),
            ),
            'conditions' => array(
                //'DATE(Purchase.date)' => $now,
                'DATE(Purchase.date) >=' => $date_from,
                'DATE(Purchase.date) <=' => $date_to,
                'Purchase.status' => 3
            ),
            'contain' => array(
                'PurchaseDetail' => array()
            ),
            'group' => array(
                'Purchase.id'
            ),
            'order' => array(

            )
        );
        $result_purchase = $objPurchase->find('all', $option);
        $total_amount_tuckshop_by_day = array_sum(Hash::extract( $result_purchase, "{n}.Purchase.grand_total"));

        return array(
            'total_amount_tuckshop_by_day' => $total_amount_tuckshop_by_day
        );
    }

    public function get_sales_member($date_from, $date_to) {
        $objMemberRenewal = ClassRegistry::init('Member.MemberRenewal');
        $option = array(
            'fields' => array(
                'MemberRenewal.*'
            ),
            'joins' => array(
            ),
            'conditions' => array(
//                'DATE(MemberRenewal.date)' => $now,
                'DATE(MemberRenewal.date) >=' => $date_from,
                'DATE(MemberRenewal.date) <=' => $date_to,
                'MemberRenewal.status' => 3
            ),
            'contain' => array(
            ),
            'group' => array(
            ),
            'order' => array(

            )
        );
        $result_member_renewal = $objMemberRenewal->find('all', $option);

        $objSetting = ClassRegistry::init('Setting.Setting');
        $value_renewal = $objSetting->get_value('member-renewal');

        $total_amount_member_by_day = (count($result_member_renewal) * $value_renewal) . ".00";
        return array(
            'total_amount_member_by_day' => $total_amount_member_by_day
        );
    }

    public function get_sales_movie_detail($data, $date_from, $date_to) {
        $result = array(
            'grand_total' => 0,
            'total_ticket' => 0
        );
        $objOrder = ClassRegistry::init('Pos.Order');
        $option = array(
            'fields' => array(
                'Order.*',
                'SUM(OrderDetail.qty) as total_ticket',
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
            ),
            'conditions' => array(
                'Order.status' => 3,
                'DATE(Order.date) >=' => $date_from,
                'DATE(Order.date) <=' => $date_to,
                'Order.schedule_detail_id' => $data['list_schedule_detail']
            ),
            'contain' => array(
                //'OrderDetail' => array()
            ),
            'group' => array(
                'Order.id'
            ),
            'order' => array(

            )
        );
        $result_order = $objOrder->find('all', $option);

        if (!empty($result_order)) {
            $grand_total = array_sum(Hash::extract($result_order, "{n}.Order.grand_total"));
            $total_ticket = array_sum(Hash::extract($result_order, "{n}.0.total_ticket"));
            $result = array(
                'grand_total' => $grand_total,
                'total_ticket' => $total_ticket
            );
        }

        return $result;
    }

    public function get_sales_movie_update($date_from, $date_to) {
        $result = array(
            'grand_total' => 0,
            'total_ticket' => 0
        );
        $objOrder = ClassRegistry::init('Pos.Order');
        $option = array(
            'fields' => array(
                'Order.*',
                'Schedule.*',
                'SUM(OrderDetail.qty) as total_ticket',
                'MovieLanguage.movie_id',
                'CONCAT(MovieLanguage.name, " (", MovieType.name,")") as movie_name'
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
                    'alias' => 'MovieLanguage',
                    'table' => Environment::read('table_prefix') . 'movie_languages',
                    'type' => 'left',
                    'conditions' => array(
                        'MovieLanguage.movie_id = Schedule.movie_id',
                        'MovieLanguage.language' => $this->lang18
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
            ),
            'conditions' => array(
                'Order.status' => 3,
                'DATE(Order.date) >=' => $date_from,
                'DATE(Order.date) <=' => $date_to,
            ),
            'contain' => array(
                //'OrderDetail' => array()
            ),
            'group' => array(
                'Schedule.movie_id',
                'Schedule.movie_type_id',
                'Order.id'
            ),
            'order' => array(

            )
        );
        $result_order = $objOrder->find('all', $option);

        $list_map_movie_schedule = array();
        if (!empty($result_order)) {
            foreach ($result_order as $k=>$v) {
                if (!empty($v['Schedule']['movie_id'])) {
                    $key = $v['Schedule']['movie_id'] . "-" . $v['Schedule']['movie_type_id'];

                    if (isset($list_map_movie_schedule[$key])) {
                        $list_map_movie_schedule[$key]['grand_total'] += $v['Order']['grand_total'];
                        $list_map_movie_schedule[$key]['total_ticket'] += $v[0]['total_ticket'];
                    } else {
                        $list_map_movie_schedule[$key]['grand_total'] = $v['Order']['grand_total'];
                        $list_map_movie_schedule[$key]['total_ticket'] = $v[0]['total_ticket'];
                    }
                    $list_map_movie_schedule[$key]['movie_name'] = $v[0]['movie_name'];
                }
            }
        }

        return array(
            'list_map_movie_schedule' => $list_map_movie_schedule
        );
    }

    public function get_sales_movie($date_from, $date_to) {
        $objSchedule = ClassRegistry::init('Movie.Schedule');

        $option = array(
            'fields' => array(
                'Movie.*',
                'MovieLanguage.*',
                'MovieType.*',
                'Schedule.*',
                'ScheduleDetail.*'
            ),
            'joins' => array(
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
                        'MovieLanguage.language' => $this->lang18
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
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.schedule_id = Schedule.id',
                    ),
                ),
            ),
            'conditions' => array(
//                'DATE(ScheduleDetail.date)' => $now
                'DATE(ScheduleDetail.date) >=' => $date_from,
                'DATE(ScheduleDetail.date) <=' => $date_to,

            ),
            'contain' => array(
            ),
            'group' => array(
                'ScheduleDetail.id'
            ),
            'order' => array(
            )
        );
        $list_movie_today = $objSchedule->find('all', $option);
        //$list_schedule_detail = Hash::extract( $list_movie_today, "{n}.ScheduleDetail.id" );

        $list_map_movie_schedule = array();
        foreach ($list_movie_today as $k=>$v) {
            $key = $v['Schedule']['movie_id']. "_" . $v['Schedule']['movie_type_id'];
            $list_map_movie_schedule[$key]['movie_id'] = $v['Schedule']['movie_id'];
            $list_map_movie_schedule[$key]['movie_type_id'] = $v['Schedule']['movie_type_id'];
            if (!empty($v['Movie']['poster'])) {
                $list_map_movie_schedule[$key]['poster'] = Environment::read('web.url_img').$v['Movie']['poster'];
            } else {
                $list_map_movie_schedule[$key]['poster'] = '';
            }
            $list_map_movie_schedule[$key]['movie_name'] = $v['MovieLanguage']['name'] . " (" . $v['MovieType']['name'] . ")";
            $list_map_movie_schedule[$key]['list_schedule_detail'][] = $v['ScheduleDetail']['id'];
        }

        foreach ($list_map_movie_schedule as $k=>$v) {
            $info_movie_temp = $this->get_sales_movie_detail($v, $date_from, $date_to);
            $list_map_movie_schedule[$k]['grand_total'] = $info_movie_temp['grand_total'];
            $list_map_movie_schedule[$k]['total_ticket'] = $info_movie_temp['total_ticket'];
        }

        return array(
            'list_map_movie_schedule' => $list_map_movie_schedule
        );
    }

    public function get_payment_statistic($date_from, $date_to) {
        $objOrder = ClassRegistry::init('Pos.Order');
        $option = array(
            'fields' => array(
                'PaymentMethod.name',
                'sum(OrderDetailPayment.amount) as sum_payment'
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
                        'PaymentMethod.id = OrderDetailPayment.payment_method_id',
                    ),
                ),
            ),
            'conditions' => array(
                //'DATE(Order.date)' => $now,
                'DATE(Order.date) >=' => $date_from,
                'DATE(Order.date) <=' => $date_to,
                'Order.status' => 3,
                'Order.is_pos' => 1
            ),
            'contain' => array(
                //'OrderDetail' => array()
            ),
            'group' => array(
                'OrderDetailPayment.payment_method_id'
            ),
            'order' => array(

            )
        );
        $result_payment_pos = $objOrder->find('all', $option);

        $objOrder = ClassRegistry::init('Pos.Order');
        $option = array(
            'fields' => array(
                'UPPER(REPLACE(OrderPaymentLog.payType,"_"," ")) as name',
                //'UPPER(OrderPaymentLog.payType) as name',
                //'sum(OrderPaymentLog.amt) as sum_payment'
                'sum(Order.grand_total) as sum_payment'
            ),
            'joins' => array(
                array(
                    'alias' => 'OrderPaymentLog',
                    'table' => Environment::read('table_prefix') . 'order_payment_logs',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderPaymentLog.id = Order.payment_log_id',
                    ),
                ),
            ),
            'conditions' => array(
//                'DATE(Order.date)' => $now,
                'DATE(Order.date) >=' => $date_from,
                'DATE(Order.date) <=' => $date_to,
                'Order.status' => 3,
                'Order.is_pos' => 0
            ),
            'contain' => array(
                //'OrderDetail' => array()
            ),
            'group' => array(
                'OrderPaymentLog.payType'
            ),
            'order' => array(

            )
        );
        $result_payment_online = $objOrder->find('all', $option);

        $objPurchase = ClassRegistry::init('Pos.Purchase');
        $option = array(
            'fields' => array(
                'PaymentMethod.name',
                'sum(PurchaseDetailPayment.amount) as sum_payment'
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
                        'PaymentMethod.id = PurchaseDetailPayment.payment_method_id',
                    ),
                ),
            ),
            'conditions' => array(
                //'DATE(Order.date)' => $now,
                'DATE(Purchase.date) >=' => $date_from,
                'DATE(Purchase.date) <=' => $date_to,
                'Purchase.status' => 3
            ),
            'contain' => array(
                //'OrderDetail' => array()
            ),
            'group' => array(
                'PurchaseDetailPayment.payment_method_id'
            ),
            'order' => array(

            )
        );
        $result_purchase = $objPurchase->find('all', $option);

        $objMemberRenewal = ClassRegistry::init('Member.MemberRenewal');
        $option = array(
            'fields' => array(
                'UPPER(REPLACE(RenewalPaymentLog.payType,"_"," ")) as name',
                'sum(RenewalPaymentLog.amt/100) as sum_payment'
            ),
            'joins' => array(
                array(
                    'alias' => 'RenewalPaymentLog',
                    'table' => Environment::read('table_prefix') . 'renewal_payment_logs',
                    'type' => 'left',
                    'conditions' => array(
                        'RenewalPaymentLog.id = MemberRenewal.payment_log_id',
                    ),
                ),
            ),
            'conditions' => array(
                //'DATE(Order.date)' => $now,
                'DATE(RenewalPaymentLog.date) >=' => $date_from,
                'DATE(RenewalPaymentLog.date) <=' => $date_to,
            ),
            'contain' => array(
                //'OrderDetail' => array()
            ),
            'group' => array(
                'RenewalPaymentLog.payType'
            ),
            'order' => array(

            )
        );
        $result_member = $objMemberRenewal->find('all', $option);

        // merge 2 list
        $payment_list = array();
        foreach ($result_payment_pos as $k=>$v) {
            $payment_list[$v['PaymentMethod']['name']] = $v[0]['sum_payment'];
        }

        foreach ($result_payment_online as $i=>$j) {
            if (isset($payment_list[$j[0]["name"]])) {
                $payment_list[$j[0]["name"]] +=  $j[0]["sum_payment"];
            } else {
                $payment_list[$j[0]["name"]] =  $j[0]["sum_payment"];
            }
        }

        foreach ($result_purchase as $i=>$j) {
            if (isset($payment_list[$j['PaymentMethod']["name"]])) {
                $payment_list[$j['PaymentMethod']["name"]] +=  $j[0]["sum_payment"];
            } else {
                $payment_list[$j['PaymentMethod']["name"]] =  $j[0]["sum_payment"];
            }
        }

        foreach ($result_member as $i=>$j) {
            if (isset($payment_list[$j[0]["name"]])) {
                $payment_list[$j[0]["name"]] +=  $j[0]["sum_payment"];
            } else {
                $payment_list[$j[0]["name"]] =  $j[0]["sum_payment"];
            }
        }

        return array(
            'payment_list' => $payment_list
        );
    }
}
