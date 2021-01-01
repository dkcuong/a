<?php
App::uses('PosAppModel', 'Pos.Model');

class Report extends PosAppModel {


	public $validate = array(
	);

	public $belongsTo = array(

	);

	public $hasMany = array(

	);

	public function report_1($now, $lang) {
        $obj = ClassRegistry::init('Pos.Order');

        //$now = date('Y-m-d');
        //$now = '2020-11-30';
        $begining_of_month = date("Y-m-01", strtotime($now));

        $conditions = array(
            'DATE(Order.date) >=' => $begining_of_month,
            'DATE(Order.date) <=' => $now,
            'Order.status' => 3,
            'Order.void' => 0,
        );
        /*$all_settings = array(
            'fields' => array(
                //"Order.id",
                "DATE(Order.date)",
                "sum(OrderDetail.qty) as total_ticket",
                "DATE(Order.date) as transaction_date",
                "Hall.code",
                "CONCAT(MovieLanguage.movie_id, '-', MovieType.id) as key_movie",
                "CONCAT(MovieLanguage.name, ' (', MovieType.name, ')') as movie_name",
                "sum(OrderDetail.qty) as total_ticket"
            ),
            'conditions' => array($conditions),
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
                    'alias' => 'OrderDetail',
                    'table' => Environment::read('table_prefix') . 'order_details',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetail.order_id = Order.id'
                    ),
                )
            ),
            'contain' => array (
                // 'Staff' => array()
            ),
            //'limit' => Environment::read('web.limit_record'),
            'order' => array('Order.date' => 'DESC'),
            'group' => array(
                'DATE(Order.date)',
//                'Schedule.movie_id',
                'Schedule.id',
            )
        );*/

        $all_settings = array(
            'fields' => array(
                "Order.id",
                "Order.grand_total",
                "DATE(Order.date) as transaction_date",
                "Hall.id",
                "Hall.code",
                "Schedule.id",
                "MovieLanguage.movie_id",
                "MovieType.id",
                "CONCAT(MovieLanguage.movie_id, '_', MovieType.id) as key_movie",
                "CONCAT(MovieLanguage.name, ' (', MovieType.name, ')') as movie_name",
                "sum(OrderDetail.qty) as total_ticket"
            ),
            'conditions' => array($conditions),
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
                    'alias' => 'OrderDetail',
                    'table' => Environment::read('table_prefix') . 'order_details',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetail.order_id = Order.id'
                    ),
                )
            ),
            'contain' => array (
                // 'Staff' => array()
            ),
            //'limit' => Environment::read('web.limit_record'),
            'order' => array('Order.date' => 'DESC'),
            'group' => array(
                'Order.id',
//                'Schedule.movie_id',
//                'Schedule.id',
            )
        );
        $result = $obj->find('all', $all_settings);

        // group by by date in code because cannot sum grand total in query
        $result_format = array();
        $result_format_total = array();
        $result_format_total_final = array();

        foreach ($result as $k=>$v) {
            //$key = $v[0]['transaction_date'] . "_" . $v['Schedule']['id'];
            $key = $v[0]['transaction_date'] . "_" . $v['MovieLanguage']['movie_id']
                    . '_' . $v['MovieType']['id'] . '_' . $v['Hall']['id'];

            $key_all_hall = $v[0]['transaction_date'] . "_" . $v['MovieLanguage']['movie_id']
                . '_' . $v['MovieType']['id'];

            $key_day = $v[0]['transaction_date'];
            if (!empty($v[0]['movie_name'])) {
                if (isset($result_format[$key])) {
                    $result_format[$key]['total_sale'] += $v['Order']['grand_total'];
                    $result_format[$key]['total_ticket'] += $v[0]['total_ticket'];
                    //$result_format[$key]['list_hall_code'][] = $v['Hall']['code'];
                    $result_format[$key]['list_order_id'][] = $v['Order']['id'];
                } else {
                    $result_format[$key]['total_sale'] = $v['Order']['grand_total'];
                    $result_format[$key]['total_ticket'] = $v[0]['total_ticket'];
                    //$result_format[$key]['list_hall_code'][] = $v['Hall']['code'];
                    $result_format[$key]['date'] = $v[0]['transaction_date'];
                    $result_format[$key]['hall'] = array (
                        'id' => $v['Hall']['id'],
                        'code' => $v['Hall']['code']
                    );
                    $result_format[$key]['movie_name'] = $v[0]['movie_name'];
                    $result_format[$key]['movie_id'] = $v['MovieLanguage']['movie_id'];
                    $result_format[$key]['movie_type_id'] = $v['MovieType']['id'];
                    $result_format[$key]['key_movie'] = $v[0]['key_movie'];
                    $result_format[$key]['list_order_id'][] = $v['Order']['id'];
                    $result_format[$key]['key'] = $key;
                }

                if (isset($result_format_total[$key_all_hall])) {
                    $result_format_total[$key_all_hall]['total_sale'] += $v['Order']['grand_total'];
                    $result_format_total[$key_all_hall]['total_ticket'] += $v[0]['total_ticket'];
                } else {
                    $result_format_total[$key_all_hall]['total_sale'] = $v['Order']['grand_total'];
                    $result_format_total[$key_all_hall]['total_ticket'] = $v[0]['total_ticket'];
                }

                if (isset($result_format_total_final[$key_day])) {
                    $result_format_total_final[$key_day]['total_sale'] += $v['Order']['grand_total'];
                    $result_format_total_final[$key_day]['total_ticket'] += $v[0]['total_ticket'];
                } else {
                    $result_format_total_final[$key_day]['total_sale'] = $v['Order']['grand_total'];
                    $result_format_total_final[$key_day]['total_ticket'] = $v[0]['total_ticket'];
                }

            }
        }

        $return_result = array(
            'result_format' => $result_format,
            'result_format_total' => $result_format_total,
            'result_format_total_final' => $result_format_total_final
        );
        return
            $return_result;
	}

    public function report_2($now, $lang) {
        $obj = ClassRegistry::init('Pos.Order');

        //$now = date('Y-m-d');
        //$now = '2020-11-30';
        $begining_of_month = date("Y-m-01", strtotime($now));
        $from_day = date('Y-m-d', strtotime($now));
        $next_day = date('Y-m-d', strtotime(' +1 day', strtotime($now)));
        $time_condition = '06:00:00';

        $conditions = array(
            'Order.status' => 3,
            'Order.void' => 0,
            //'Order.id' => 239
        );
        $conditions['OR'][] = array(
            'DATE(ScheduleDetail.date)' => $from_day,
            'ScheduleDetail.time >=' => $time_condition
        );
        $conditions['OR'][] = array(
            'DATE(ScheduleDetail.date)' => $next_day,
            'ScheduleDetail.time <' => $time_condition
        );

        $all_settings = array(
            'fields' => array(
                "Order.id",
                "Order.is_pos",
                "Order.grand_total",
                "Order.total_amount",
                "DATE(Order.date) as transaction_date",
                "Staff.id",
                "Staff.name",
                "Hall.id",
                "Hall.code",
                "Schedule.id",
                "ScheduleDetail.date",
                "ScheduleDetail.time",
                "ScheduleDetail.gv_value",
                "MovieLanguage.movie_id",
                "MovieType.id",
                "CONCAT(MovieLanguage.movie_id, '_', MovieType.id) as key_movie",
                "CONCAT(MovieLanguage.name, ' (', MovieType.name, ')') as movie_name",
                "GROUP_CONCAT(PaymentMethod.id) as group_payment_id",
                "GROUP_CONCAT(PaymentMethod.name) as group_payment_name",
                "GROUP_CONCAT(PaymentMethod.type) as group_payment_type",
                "GROUP_CONCAT(PaymentMethod.code) as group_payment_code",
                "OrderPaymentLog.payType",
                //"sum(OrderDetail.qty) as total_ticket"
            ),
            'conditions' => array($conditions),
            'joins' => array(
                array(
                    'alias' => 'Staff',
                    'table' => Environment::read('table_prefix') . 'staffs',
                    'type' => 'left',
                    'conditions' => array(
                        'Staff.id = Order.staff_id',
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
                    'alias' => 'Hall',
                    'table' => Environment::read('table_prefix') . 'halls',
                    'type' => 'left',
                    'conditions' => array(
                        'Hall.id = Schedule.hall_id',
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
//                array(
//                    'alias' => 'OrderDetail',
//                    'table' => Environment::read('table_prefix') . 'order_details',
//                    'type' => 'left',
//                    'conditions' => array(
//                        'OrderDetail.order_id = Order.id'
//                    ),
//                ),
                array(
                    'alias' => 'OrderDetailPayment',
                    'table' => Environment::read('table_prefix') . 'order_detail_payments',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetailPayment.order_id = Order.id'
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
                    'alias' => 'PaymentMethod',
                    'table' => Environment::read('table_prefix') . 'payment_methods',
                    'type' => 'left',
                    'conditions' => array(
                        'PaymentMethod.id = OrderDetailPayment.payment_method_id'
                    ),
                )
            ),
            'contain' => array (
                 'OrderDetail' => array()
            ),
            //'limit' => Environment::read('web.limit_record'),
            'order' => array(
                'MovieLanguage.name' => 'ASC',
                'Hall.code' => 'ASC',
                'ScheduleDetail.date' => 'ASC',
                'ScheduleDetail.time' => 'ASC',
                'Staff.name' => 'ASC',
            ),
            'group' => array(
                'Order.id',
                //'ScheduleDetail.time',
                //'Schedule.id',
            )
        );
        $result = $obj->find('all', $all_settings);

        $objPaymentMethod = ClassRegistry::init('Pos.PaymentMethod');
        $type = $objPaymentMethod->type;
        //pr("\n" . _CLASS_ . ' :: ' . _FUNCTION_ . ' Line:' . __LINE__);pr( $result );exit;
//        "GROUP_CONCAT(PaymentMethod.name) as group_payment_name",
//                "GROUP_CONCAT(PaymentMethod.type) as group_payment_type",
//                "GROUP_CONCAT(PaymentMethod.code) as group_payment_code",
//                "OrderPaymentLog.payType",

        // group by by date in code because cannot sum grand total in query
        $result_format = array();
        $result_format_total = array();
        $result_format_total_final['total_sale'] = 0;
        $result_format_total_final['total_sale_custom'] = 0;
        $result_format_total_final['total_amount'] = 0;
        $result_format_total_final['total_ticket'] = 0;


        foreach ($result as $k=>$v) {
            $key_movie = $v['MovieLanguage']['movie_id'] . '_' . $v['MovieType']['id'];

            $key_time = $v['ScheduleDetail']['date'] . '_' . $v['ScheduleDetail']['time'];
            $time_display = date('Y-m-d', strtotime($v['ScheduleDetail']['date'])) . ' ' . date('H:i', strtotime($v['ScheduleDetail']['time']));
            $key_hall = $v['Hall']['id'];




            if ($v['Order']['is_pos'] == 1) {
                $key_staff = $v['Staff']['id'];
                $staff_name = $v['Staff']['name'];
                $key_payment_method = $v[0]['group_payment_id'];
                $payment_method_code = $v[0]['group_payment_code'];
                $group_payment_method_type = $v[0]['group_payment_type'];
            } else {
                $key_staff = $staff_name = 'Internet';
                $key_payment_method = $v['OrderPaymentLog']['payType'];
                $payment_method_code = strtoupper($v['OrderPaymentLog']['payType']);
                $group_payment_method_type = $v[0]['group_payment_type'];
            }

            $is_gv = false;
            $count_gv = 0;
            $is_exchange = false;
            if ($v['Order']['is_pos']) {
                $list_payment_method_code = explode(',', $payment_method_code);
                $list_payment_method_type = explode(',', $group_payment_method_type);
                foreach ($list_payment_method_type as $kType => $vType) {
                    if ($type[$vType] == 'Exchange Ticket') {
                        if (strpos($list_payment_method_code[$kType], 'GV') === 0) {
                            $is_gv = true;
                            $count_gv ++ ;
                        } else {
                            $is_exchange = true;
                        }
                    }
                }
            }

            if (!empty($v[0]['movie_name'])) {
                if (isset($result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method])) {
                    $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale'] += $v['Order']['grand_total'];
                    $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_amount'] = $v['Order']['total_amount'];
                    $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_ticket'] += count($v['OrderDetail']);

                    /////////////// BEGIN  : 4 calculate ///////////////


                    if ($key_staff == 'Internet') {
                        $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'] += array_sum(Hash::extract($v['OrderDetail'], "{n}.price_hkbo" ));
                    } else if (trim($v['Hall']['code']) == 'VIP'){
                        $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'] += array_sum(Hash::extract($v['OrderDetail'], "{n}.price_hkbo" ));
                    } else if ($is_gv == true) {
                        $list_price = Hash::extract($v['OrderDetail'], "{n}.price_hkbo");
                        sort($list_price);

                        $price_custom = 0;
                        foreach ($list_price as $kPrice => $vPrice) {
                            if ($kPrice < $count_gv) {
                                $price_custom += $v['ScheduleDetail']['gv_value'];
                            } else {
                                $price_custom += $vPrice;
                            }
                        }
                        $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'] += $price_custom;
                    } else if ($is_exchange == true) {
                        $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'] += array_sum(Hash::extract($v['OrderDetail'], "{n}.price_hkbo" ));
                    } else {
                        $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'] += $v['Order']['grand_total'];
                    }
                    /////////////// END  : 4 calculate ///////////////

                } else {
                    $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale'] = $v['Order']['grand_total'];
                    $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_amount'] = $v['Order']['total_amount'];
                    $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_ticket'] = count($v['OrderDetail']);
                    $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'] = 0;

                    /////////////// BEGIN  : 4 calculate ///////////////

                    if ($key_staff == 'Internet') {
                        $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'] = array_sum(Hash::extract($v['OrderDetail'], "{n}.price_hkbo" ));
                    } else if (trim($v['Hall']['code']) == 'VIP'){
                        $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'] = array_sum(Hash::extract($v['OrderDetail'], "{n}.price_hkbo" ));
                    } else if ($is_gv == true) {
                        $list_price = Hash::extract($v['OrderDetail'], "{n}.price_hkbo");
                        sort($list_price);

                        $price_custom = 0;
                        foreach ($list_price as $kPrice => $vPrice) {
                            if ($kPrice < $count_gv) {
                                $price_custom += $v['ScheduleDetail']['gv_value'];
                            } else {
                                $price_custom += $vPrice;
                            }
                        }
                        $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'] = $price_custom;
                    } else if ($is_exchange == true) {
                        $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'] = array_sum(Hash::extract($v['OrderDetail'], "{n}.price_hkbo" ));
                    } else {
                        $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'] = $v['Order']['grand_total'];
                    }

                    /////////////// END  : 4 calculate ///////////////

                    $result_format[$key_movie]['movie_name'] = $v[0]['movie_name'];
                    $result_format[$key_movie]['list_hall'][$key_hall]['hall_code'] = $v['Hall']['code'];
                    $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['time_display'] = $time_display;
                    $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['staff_name'] = $staff_name;


                    if ($v['Order']['is_pos']) {
                        $payment_method_code_format_array = explode(',', $payment_method_code);
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
                        $payment_method_code = implode(', ', $payment_method_code_format);
                    }
                    $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['combination_payment_method_display'] = $payment_method_code;

                }
                /////////////// END  : 4 calculate ///////////////

                $total_sale_custom = $result_format[$key_movie]['list_hall'][$key_hall]['list_time'][$key_time]['list_staff'][$key_staff]['list_combination_method'][$key_payment_method]['total_sale_custom'];
                if (isset($result_format_total[$key_movie])) {
                    $result_format_total[$key_movie]['total_sale'] += $v['Order']['grand_total'];
                    $result_format_total[$key_movie]['total_sale_custom'] += $total_sale_custom;
                    $result_format_total[$key_movie]['total_amount'] += $v['Order']['total_amount'];
                    $result_format_total[$key_movie]['total_ticket'] += count($v['OrderDetail']);
                } else {
                    $result_format_total[$key_movie]['total_sale'] = $v['Order']['grand_total'];
                    $result_format_total[$key_movie]['total_sale_custom'] = $total_sale_custom;
                    $result_format_total[$key_movie]['total_amount'] = $v['Order']['total_amount'];
                    $result_format_total[$key_movie]['total_ticket'] = count($v['OrderDetail']);
                }


                $result_format_total_final['total_sale'] += $v['Order']['grand_total'];
                $result_format_total_final['total_sale_custom'] += $total_sale_custom;
                $result_format_total_final['total_amount'] += $v['Order']['total_amount'];
                $result_format_total_final['total_ticket'] += count($v['OrderDetail']);

            }

        }

        $return_result = array(
            'result_format' => $result_format,
            'result_format_total' => $result_format_total,
            'result_format_total_final' => $result_format_total_final
        );
        return
            $return_result;
    }

    public function report_3($now, $lang) {
        $obj = ClassRegistry::init('Pos.Order');

        //$now = date('Y-m-d');
        //$now = '2020-11-30';
        $begining_of_month = date("Y-m-01", strtotime($now));
        $end_of_month = date("Y-m-t", strtotime($now));

        $conditions = array(
            'DATE(ScheduleDetail.date) <=' => $end_of_month,
            'DATE(ScheduleDetail.date) >=' => $begining_of_month,
            'Order.status' => 3,
            'Order.void' => 0,
        );

        /*$all_settings = array(
            'fields' => array(
                //"Order.id",
                "DATE(Order.date)",
                "sum(OrderDetail.qty) as total_ticket",
                "DATE(Order.date) as transaction_date",
                "Hall.code",
                "CONCAT(MovieLanguage.movie_id, '-', MovieType.id) as key_movie",
                "CONCAT(MovieLanguage.name, ' (', MovieType.name, ')') as movie_name",
                "sum(OrderDetail.qty) as total_ticket"
            ),
            'conditions' => array($conditions),
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
                    'alias' => 'OrderDetail',
                    'table' => Environment::read('table_prefix') . 'order_details',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetail.order_id = Order.id'
                    ),
                )
            ),
            'contain' => array (
                // 'Staff' => array()
            ),
            //'limit' => Environment::read('web.limit_record'),
            'order' => array('Order.date' => 'DESC'),
            'group' => array(
                'DATE(Order.date)',
//                'Schedule.movie_id',
                'Schedule.id',
            )
        );*/

        $all_settings = array(
            'fields' => array(
                "Order.id",
                "Order.grand_total",
                "DATE(Order.date) as transaction_date",
                "Hall.id",
                "Hall.code",
                "Schedule.id",
                "ScheduleDetail.date",
                "ScheduleDetail.time",
                "MovieLanguage.movie_id",
                "MovieType.id",
                "CONCAT(MovieLanguage.movie_id, '_', MovieType.id) as key_movie",
                "CONCAT(MovieLanguage.name, ' (', MovieType.name, ')') as movie_name",
                "sum(OrderDetail.qty) as total_ticket"
            ),
            'conditions' => array($conditions),
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
                    'alias' => 'OrderDetail',
                    'table' => Environment::read('table_prefix') . 'order_details',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetail.order_id = Order.id'
                    ),
                )
            ),
            'contain' => array (
                //'OrderDetail' => array()
            ),
            //'limit' => Environment::read('web.limit_record'),
            'order' => array(
                'MovieLanguage.name' => 'ASC',
                //'ScheduleDetail.time' => 'ASC',
                'Hall.code' => 'ASC'
            ),
            'group' => array(
                'Order.id',
                //'ScheduleDetail.time',
                //'Schedule.id',
            )
        );
        $result = $obj->find('all', $all_settings);


        // group by by date in code because cannot sum grand total in query
        $result_format = array();
        $result_format_total = array();
        $result_format_total_final['total_sale'] = 0;
        $result_format_total_final['total_ticket'] = 0;


        foreach ($result as $k=>$v) {
            $key_movie = $v['MovieLanguage']['movie_id'] . '_' . $v['MovieType']['id'];
            $key_hall = $v['Hall']['id'];

            if (!empty($v[0]['movie_name'])) {
                if (isset($result_format[$key_movie]['list_hall'][$key_hall])) {
                    $result_format[$key_movie]['list_hall'][$key_hall]['total_sale'] += $v['Order']['grand_total'];
                    $result_format[$key_movie]['list_hall'][$key_hall]['total_ticket'] += $v[0]['total_ticket'];
                } else {
                    $result_format[$key_movie]['list_hall'][$key_hall]['total_sale'] = $v['Order']['grand_total'];
                    $result_format[$key_movie]['list_hall'][$key_hall]['total_ticket'] = $v[0]['total_ticket'];
                    $result_format[$key_movie]['list_hall'][$key_hall]['hall_code'] = $v['Hall']['code'];

                    $result_format[$key_movie]['movie_name'] = $v[0]['movie_name'];
                }

                if (isset($result_format_total[$key_movie])) {
                    $result_format_total[$key_movie]['total_sale'] += $v['Order']['grand_total'];
                    $result_format_total[$key_movie]['total_ticket'] += $v[0]['total_ticket'];
                } else {
                    $result_format_total[$key_movie]['total_sale'] = $v['Order']['grand_total'];
                    $result_format_total[$key_movie]['total_ticket'] = $v[0]['total_ticket'];
                }


                $result_format_total_final['total_sale'] += $v['Order']['grand_total'];
                $result_format_total_final['total_ticket'] += $v[0]['total_ticket'];

            }
        }

        $return_result = array(
            'result_format' => $result_format,
            'result_format_total' => $result_format_total,
            'result_format_total_final' => $result_format_total_final
        );
        return
            $return_result;
    }

    public function report_4($now, $lang) {
        $obj = ClassRegistry::init('Pos.Order');

        //$now = date('Y-m-d');
        //$now = '2020-11-30';
        $begining_of_month = date("Y-m-01", strtotime($now));
        $from_day = date('Y-m-d', strtotime($now));
        $next_day = date('Y-m-d', strtotime(' +1 day', strtotime($now)));
        $time_condition = '06:00:00';

        $conditions = array(
            //'DATE(ScheduleDetail.date)' => $now,
            'DATE(Order.date)' => $now,
            'Order.status' => 3,
            'Order.void' => 0,
            'Order.is_pos' => 1,
        );
        $conditions['OR'][] = array(
            'DATE(ScheduleDetail.date)' => $from_day,
            'ScheduleDetail.time >=' => $time_condition
        );
        $conditions['OR'][] = array(
            'DATE(ScheduleDetail.date)' => $next_day,
            'ScheduleDetail.time <' => $time_condition
        );
        /*$all_settings = array(
            'fields' => array(
                //"Order.id",
                "DATE(Order.date)",
                "sum(OrderDetail.qty) as total_ticket",
                "DATE(Order.date) as transaction_date",
                "Hall.code",
                "CONCAT(MovieLanguage.movie_id, '-', MovieType.id) as key_movie",
                "CONCAT(MovieLanguage.name, ' (', MovieType.name, ')') as movie_name",
                "sum(OrderDetail.qty) as total_ticket"
            ),
            'conditions' => array($conditions),
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
                    'alias' => 'OrderDetail',
                    'table' => Environment::read('table_prefix') . 'order_details',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetail.order_id = Order.id'
                    ),
                )
            ),
            'contain' => array (
                // 'Staff' => array()
            ),
            //'limit' => Environment::read('web.limit_record'),
            'order' => array('Order.date' => 'DESC'),
            'group' => array(
                'DATE(Order.date)',
//                'Schedule.movie_id',
                'Schedule.id',
            )
        );*/

        $all_settings = array(
            'fields' => array(
                "Order.id",
                "Order.grand_total",
                "DATE(Order.date) as transaction_date",
                "Hall.id",
                "Hall.code",
                "Schedule.id",
                "ScheduleDetail.date",
                "ScheduleDetail.time",
                "MovieLanguage.movie_id",
                "MovieType.id",
                "CONCAT(MovieLanguage.movie_id, '_', MovieType.id) as key_movie",
                "CONCAT(MovieLanguage.name, ' (', MovieType.name, ')') as movie_name",
                "sum(OrderDetail.qty) as total_ticket"
            ),
            'conditions' => array($conditions),
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
                    'alias' => 'OrderDetail',
                    'table' => Environment::read('table_prefix') . 'order_details',
                    'type' => 'left',
                    'conditions' => array(
                        'OrderDetail.order_id = Order.id'
                    ),
                )
            ),
            'contain' => array (
                //'OrderDetail' => array()
            ),
            //'limit' => Environment::read('web.limit_record'),
            'order' => array(
                'Hall.code' => 'ASC',
                'MovieLanguage.name' => 'ASC',
                'ScheduleDetail.date' => 'ASC',
                'ScheduleDetail.time' => 'ASC',
            ),
            'group' => array(
                'Order.id',
                //'ScheduleDetail.time',
                //'Schedule.id',
            )
        );
        $result = $obj->find('all', $all_settings);


        // group by by date in code because cannot sum grand total in query
        $result_format = array();
        $result_format_total_movie = array();
        $result_format_total_hall = array();

        $result_format_total_final['total_sale'] = 0;
        $result_format_total_final['total_ticket'] = 0;


        foreach ($result as $k=>$v) {
            $key_hall = $v['Hall']['id'];
            $key_movie = $v['MovieLanguage']['movie_id'] . '_' . $v['MovieType']['id'];
            $key_time = date('Y-m-d', strtotime($v['ScheduleDetail']['date'])) . '_' . date('H:i', strtotime($v['ScheduleDetail']['time']));

            if (!empty($v[0]['movie_name'])) {
                if (isset($result_format[$key_hall]['list_movie'][$key_movie]['list_time'][$key_time])) {
                    $result_format[$key_hall]['list_movie'][$key_movie]['list_time'][$key_time]['total_sale'] += $v['Order']['grand_total'];
                    $result_format[$key_hall]['list_movie'][$key_movie]['list_time'][$key_time]['total_ticket'] += $v[0]['total_ticket'];
                } else {
                    $result_format[$key_hall]['list_movie'][$key_movie]['list_time'][$key_time]['total_sale'] = $v['Order']['grand_total'];
                    $result_format[$key_hall]['list_movie'][$key_movie]['list_time'][$key_time]['total_ticket'] = $v[0]['total_ticket'];

                    $result_format[$key_hall]['list_movie'][$key_movie]['list_time'][$key_time]['schedule_date_time_display'] = date('Y-m-d', strtotime($v['ScheduleDetail']['date'])) . ' ' . date('H:i', strtotime($v['ScheduleDetail']['time']));
                    $result_format[$key_hall]['hall_code'] = $v['Hall']['code'];
                    $result_format[$key_hall]['list_movie'][$key_movie]['movie_name'] = $v[0]['movie_name'];
                }
            }

            if (isset($result_format_total_movie[$key_movie])) {
                $result_format_total_movie[$key_movie]['total_sale'] += $v['Order']['grand_total'];
                $result_format_total_movie[$key_movie]['total_ticket'] += $v[0]['total_ticket'];
            } else {
                $result_format_total_movie[$key_movie]['total_sale'] = $v['Order']['grand_total'];
                $result_format_total_movie[$key_movie]['total_ticket'] = $v[0]['total_ticket'];
            }

            if (isset($result_format_total_hall[$key_hall])) {
                $result_format_total_hall[$key_hall]['total_sale'] += $v['Order']['grand_total'];
                $result_format_total_hall[$key_hall]['total_ticket'] += $v[0]['total_ticket'];
            } else {
                $result_format_total_hall[$key_hall]['total_sale'] = $v['Order']['grand_total'];
                $result_format_total_hall[$key_hall]['total_ticket'] = $v[0]['total_ticket'];
            }

            $result_format_total_final['total_sale'] += $v['Order']['grand_total'];
            $result_format_total_final['total_ticket'] += $v[0]['total_ticket'];
        }

//pr("\n" . _CLASS_ . ' :: ' . _FUNCTION_ . ' Line:' . __LINE__);pr( $result_format );exit;

        $return_result = array(
            'result_format' => $result_format,
            'result_format_total_movie' => $result_format_total_movie,
            'result_format_total_hall' => $result_format_total_hall,
            'result_format_total_final' => $result_format_total_final
        );

        return
            $return_result;
    }

    public function report_5($now, $lang) {
        $obj = ClassRegistry::init('Pos.Order');

        $begining_of_month = date("Y-m-01", strtotime($now));
        $from_day = date('Y-m-d', strtotime($now));
        $next_day = date('Y-m-d', strtotime(' +1 day', strtotime($now)));
        $time_condition = '06:00:00';

        $conditions = array(
            //'DATE(Order.date)' => $now,
            'Order.status' => 3,
            'Order.void' => 0,
            'Order.is_pos' => 1,
        );
        $conditions['OR'][] = array(
            'DATE(ScheduleDetail.date)' => $from_day,
            'ScheduleDetail.time >=' => $time_condition
        );
        $conditions['OR'][] = array(
            'DATE(ScheduleDetail.date)' => $next_day,
            'ScheduleDetail.time <' => $time_condition
        );
        
        $all_settings = array(
            'fields' => array(
                "Order.id",
                "Order.grand_total",
                "Staff.id",
                "Staff.name",
                "DATE(Order.date) as transaction_date",
                "GROUP_CONCAT(PaymentMethod.id, '') as payment_method_id_group",
                "ScheduleDetail.id",
                "GROUP_CONCAT(PaymentMethod.code, '') as payment_method_group",
                "SUM(OrderDetailPayment.amount) as sum_payment"
            ),
            'conditions' => array($conditions),
            'joins' => array(
                array(
                    'alias' => 'Staff',
                    'table' => Environment::read('table_prefix') . 'staffs',
                    'type' => 'left',
                    'conditions' => array(
                        'Staff.id = Order.staff_id'
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
                ),
                array(
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.id = Order.schedule_detail_id',
                    ),
                ),
            ),
            'contain' => array (
                'OrderDetail' => array()
            ),
            //'limit' => Environment::read('web.limit_record'),
            'order' => array(

            ),
            'group' => array(
                'Order.id',
            )
        );
        $result = $obj->find('all', $all_settings);


        // group by by date in code because cannot sum grand total in query
        $result_format = array();

        $result_format_total_final['total_sale'] = 0;
        $result_format_total_final['total_ticket'] = 0;
        $result_format_total_final['total_transaction'] = 0;
        $result_format_total_final['total_payment'] = 0;




        foreach ($result as $k => $v) {
            $key_method_id_group = $v[0]['payment_method_id_group'];

            if (!empty($v['ScheduleDetail']['id'])) {
                if (isset($result_format[$key_method_id_group])) {
                    $result_format[$key_method_id_group]['total_sale']+= $v['Order']['grand_total'];
                    $result_format[$key_method_id_group]['total_payment']+= $v[0]['sum_payment'];
                    $result_format[$key_method_id_group]['total_ticket']+= count($v['OrderDetail']);
                    $result_format[$key_method_id_group]['total_transaction'] += 1;
                    $result_format[$key_method_id_group]['staff_list'][$v['Staff']['id']] = $v['Staff']['name'];
                } else {
                    $result_format[$key_method_id_group]['total_sale'] = $v['Order']['grand_total'];
                    $result_format[$key_method_id_group]['total_payment'] = $v[0]['sum_payment'];
                    $result_format[$key_method_id_group]['total_ticket'] = count($v['OrderDetail']);
                    $result_format[$key_method_id_group]['total_transaction'] = 1;
                    $result_format[$key_method_id_group]['group_method_name'] = $v[0]['payment_method_group'];
                    $result_format[$key_method_id_group]['staff_list'][$v['Staff']['id']] = $v['Staff']['name'];

                    $payment_method_code = $v[0]['payment_method_group'];
                    $payment_method_code_format_array = explode(',', $payment_method_code);
                    $payment_method_code_format_temp = array();
                    foreach ($payment_method_code_format_array as $kFormatPaymentMethod => $vFormatPaymentMethod) {
                        if (isset($payment_method_code_format_temp[$vFormatPaymentMethod])) {
                            $payment_method_code_format_temp[$vFormatPaymentMethod] += 1;
                        } else {
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
                    $payment_method_code = implode(', ', $payment_method_code_format);
                    $result_format[$key_method_id_group]['group_method_name'] = $payment_method_code;
                }
            }

            $result_format_total_final['total_sale'] += $v['Order']['grand_total'];
            $result_format_total_final['total_payment'] += $v[0]['sum_payment'];
            $result_format_total_final['total_ticket'] += count($v['OrderDetail']);
            $result_format_total_final['total_transaction'] += 1;
        }

        $return_result = array(
            'result_format' => $result_format,
            'result_format_total_final' => $result_format_total_final
        );

        return
            $return_result;
    }

    public function report_6($now, $lang) {
        $obj = ClassRegistry::init('Pos.Order');

        $begining_of_month = date("Y-m-01", strtotime($now));
        $end_of_month = date("Y-m-t", strtotime($now));
        $time_condition = '00:00:00';

        $conditions = array(
            'DATE(ScheduleDetail.date) <=' => $end_of_month,
            'DATE(ScheduleDetail.date) >=' => $begining_of_month,
            'Order.status' => 3,
            'Order.void' => 0,
            'Order.is_pos' => 1,
        );


        $all_settings = array(
            'fields' => array(
                "Order.id",
                "Order.grand_total",
                "Staff.id",
                "Staff.name",
                "DATE(Order.date) as transaction_date",
                "GROUP_CONCAT(PaymentMethod.id, '') as payment_method_id_group",
                "ScheduleDetail.id",
                "GROUP_CONCAT(PaymentMethod.code) as group_payment_code",
                "GROUP_CONCAT(PaymentMethod.name) as group_payment_name",
                "SUM(OrderDetailPayment.amount) as sum_payment"
            ),
            'conditions' => array($conditions),
            'joins' => array(
                array(
                    'alias' => 'Staff',
                    'table' => Environment::read('table_prefix') . 'staffs',
                    'type' => 'left',
                    'conditions' => array(
                        'Staff.id = Order.staff_id'
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
                ),
                array(
                    'alias' => 'ScheduleDetail',
                    'table' => Environment::read('table_prefix') . 'schedule_details',
                    'type' => 'left',
                    'conditions' => array(
                        'ScheduleDetail.id = Order.schedule_detail_id',
                    ),
                ),
            ),
            'contain' => array (
                'OrderDetail' => array()
            ),
            //'limit' => Environment::read('web.limit_record'),
            'order' => array(

            ),
            'group' => array(
                'Order.id',
            )
        );
        $result = $obj->find('all', $all_settings);


        // group by by date in code because cannot sum grand total in query
        $result_format = array();
        $result_format_total = array();

        $result_format_total_final['total_sale'] = 0;
        $result_format_total_final['total_ticket'] = 0;
        $result_format_total_final['total_transaction'] = 0;
        $result_format_total_final['total_payment'] = 0;


        foreach ($result as $k => $v) {
            $key_method_id_group = $v[0]['payment_method_id_group'];
            $key_staff = $v['Staff']['id'];

            if (!empty($v['ScheduleDetail']['id'])) {
                if (isset($result_format[$key_staff]['list_payment'][$key_method_id_group])) {
                    $result_format[$key_staff]['list_payment'][$key_method_id_group]['total_sale']+= $v['Order']['grand_total'];
                    $result_format[$key_staff]['list_payment'][$key_method_id_group]['total_ticket']+= count($v['OrderDetail']);
                } else {
                    $result_format[$key_staff]['list_payment'][$key_method_id_group]['total_sale'] = $v['Order']['grand_total'];
                    $result_format[$key_staff]['list_payment'][$key_method_id_group]['total_ticket'] = count($v['OrderDetail']);
                    $result_format[$key_staff]['staff_name'] = $v['Staff']['name'];

                    $payment_method_code = $v[0]['group_payment_code'];
                    $payment_method_code_format_array = explode(',', $payment_method_code);
                    $payment_method_code_format_temp = array();
                    foreach ($payment_method_code_format_array as $kFormatPaymentMethod => $vFormatPaymentMethod) {
                        if (isset($payment_method_code_format_temp[$vFormatPaymentMethod])) {
                            $payment_method_code_format_temp[$vFormatPaymentMethod] += 1;
                        } else {
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
                    $payment_method_code = implode(', ', $payment_method_code_format);
                    $result_format[$key_staff]['list_payment'][$key_method_id_group]['group_method_name'] = $payment_method_code;

                    if (isset($result_format_total[$key_staff])) {
                        $result_format_total[$key_staff]['total_sale'] += $v['Order']['grand_total'];
                        $result_format_total[$key_staff]['total_ticket'] += count($v['OrderDetail']);
                    } else {
                        $result_format_total[$key_staff]['total_sale'] = $v['Order']['grand_total'];
                        $result_format_total[$key_staff]['total_ticket'] = count($v['OrderDetail']);
                    }
                }
            }


            $result_format_total_final['total_sale'] += $v['Order']['grand_total'];
            $result_format_total_final['total_payment'] += $v[0]['sum_payment'];
            $result_format_total_final['total_ticket'] += count($v['OrderDetail']);
            $result_format_total_final['total_transaction'] += 1;
        }

        $return_result = array(
            'result_format' => $result_format,
            'result_format_total' => $result_format_total,
            'result_format_total_final' => $result_format_total_final
        );

        return
            $return_result;
    }
}
