<?php
    class DoScheduledJobsShell extends AppShell {
        public $uses = array(
            'Setting.Cronjob', 
            'Setting.Setting',
            'Movie.ScheduleDetailLayout',
            'Pos.Order',
            'Member.Member'
        );

        //ERROR & WARNING
        public $date_doesnt_exists = "";
        public $method_not_found = "ERROR !!! - job '%s' doesnt have valid method '%s'";
        public $data_is_not_saved = "";
        public $run_proc_result = "";
        public $unable_to_update_last_run_time = "";

        public function startup() {
            $collection = new ComponentCollection();
            $this->Email = $collection->load('Email');
            $this->log_module = "DoScheduledJobs";

            parent::startup();
        }
        
        public function main() {
            CakeLog::write($this->log_module, "-------- Cronjob DoScheduledJobs START in " . date('Y-m-d H:i:s') . " -------- \r\n");
            
			// temporarily using the "development" DB config
			Environment::set('development'); // developmet/production

            // set new setting if have
			if (!$this->set_enviroment_language()) {
                goto end_cronjob;
            }

            $current_time = date('Y-m-d H:i');

            $options = array(
                'conditions' => array(
                    'next_run_time <= ' => $current_time,
                    'enabled' => 1
                ),
                'recursive' => -1
            );

            $data_cronjobs = $this->Cronjob->find('all', $options);

            $all_is_valid = true;
            $message = array();
            foreach($data_cronjobs as $cronjob) {
                $id = $cronjob['Cronjob']['id'];
                $last_run_time = $cronjob['Cronjob']['last_run_time'];
                $next_run_time = $cronjob['Cronjob']['next_run_time'];
                $run_interval = $cronjob['Cronjob']['run_interval'];
                $cron_type = $cronjob['Cronjob']['cron_type'];
                $proc = $cronjob['Cronjob']['name'];
                $proc_name = $cronjob['Cronjob']['proc_name'];

                $proc_result = array();

                if ((method_exists($this, $proc_name)) && is_callable(array($this, $proc_name))) {
                    $proc_result = call_user_func(array($this, $proc_name));
                    if ($proc_result['valid']) {
                        array_push($message, sprintf($this->run_proc_result, $cronjob['Cronjob']['name']));
                    } else {
                        $all_is_valid = false;
                    }
                    array_push($message, $proc_result['message']);
                } else {
                    array_push($message, sprintf($this->method_not_found, $proc, $proc_name));
                    $all_is_valid = false;
                }

                
                switch($cron_type) {
                    case 'minutely': 
                        $interval_type = 'minutes';
                        break;
                    case 'hourly': 
                        $interval_type = 'hours';
                        break;
                    case 'daily': 
                        $interval_type = 'days';
                        break;
                    case 'monthly': 
                        $interval_type = 'months';
                        break;
                    default : 
                        $interval_type = 'minutes';
                        break;
                }

                $last_run_time = $next_run_time;
                if ($cron_type == 'minutely' && $run_interval < 4) {
                    $current_time = date('Y-m-d H:i');
                    $next_run_time = date('Y-m-d H:i', strtotime($current_time . ' +' . $run_interval . ' ' . $interval_type));
                } else {
                    $next_run_time = date('Y-m-d H:i', strtotime($last_run_time . ' +' . $run_interval . ' ' . $interval_type));
                }

                $data_save = $cronjob;
                $data_save['Cronjob']['last_run_time'] = $last_run_time;
                $data_save['Cronjob']['next_run_time'] = $next_run_time;

                if (!$this->Cronjob->saveAll($data_save)) {
                    array_push($message, 'update cronjob failed');
                    $all_is_valid = false;
                }

            }

            if (empty($message)) {
                array_push($message, 'no jobs executed / errors detected');
                $all_is_valid = false;
            }

            end_cronjob:

            $template = "cronjob_report";
            $subject = '[ACX] - Scheduled Jobs Report';

            $admin_email = Environment::read('email.receiver');

            $result_email = $this->Email->send($admin_email, $subject, $template, array('message' => $message));

            CakeLog::write($this->log_module, "-------- Cronjob END in " . date('Y-m-d H:i:s') . " -------- \r\n");
        }

        public function do_upload_file_to_ftp($filename, $counter) {
            $valid = false;
            $message = 'Upload file Attempt : '.$counter."<br/>";
            
            // connect and login to FTP server
            $ftp_server = Environment::read('site.hkbo.server');
            // $ftp_port = Environment::read('site.hkbo.port');
            $ftp_username = Environment::read('site.hkbo.username');
            $ftp_userpass = Environment::read('site.hkbo.password');
            
            // $ftp_conn = ftp_connect($ftp_server, $ftp_port) or die("Could not connect to $ftp_server");
            $ftp_conn = ftp_connect($ftp_server);

            if (!$ftp_conn) {
                $message .= 'connection to FTP failed'."<br/>";
                goto return_and_exit;
            }

            
            $login = @ftp_login($ftp_conn, $ftp_username, $ftp_userpass);
            ftp_set_option($ftp_conn, FTP_USEPASVADDRESS, false);
            ftp_pasv($ftp_conn, true);

            if (!$login) {
                $message .= 'login to FTP failed'."<br/>";

                // close connection
                ftp_close($ftp_conn);

                goto return_and_exit;
            }

            // upload file
            $webroot = Environment::read('web.physical_path');
            $file = $webroot.'webroot/img/uploads/'.$filename;
            $is_upload = @ftp_put($ftp_conn, $filename, $file, FTP_ASCII);

            // close connection
            ftp_close($ftp_conn);

            if (!$is_upload) {
                $message .= 'upload to FTP failed'."<br/>";

                goto return_and_exit;
            }

            $valid = true;
            $message .= 'report upload to server successful at attempt : '.$counter."<br/>";

            return_and_exit:
            $result = array('valid' => $valid, 'message' => $message);

            return $result;
        }

        public function do_timeout_transaction_clearing() {
            $valid = false;
            $message = 'Timeout Transaction Clearing executed'."<br/>";
            $message .= 'date : '.date('Y-m-d H:i')."<br/>";
            /*
                1. find order that exceed time limit
                2. get all the seat id
                3. set the conditions and updates for data order
                4. set the conditions and updates for data seats
                5. run updateAll
                6. save into Log database ...
                6. save into Log file ...
            */

            $limit_time = $this->Setting->get_value('seat-release');

            if ($limit_time > 0) {
                $cur_time = date('Y-m-d H:i');
                $option_order = array(
                    'fields' => array(
                        'Order.*',
                        'OrderDetail.*',
                        'ScheduleDetailLayout.*',
                        'ScheduleDetail.*',
                        'Movie.*',
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
                    "conditions" => array(
                        "TIMESTAMPDIFF(MINUTE, Order.date, '".$cur_time."') > " => $limit_time,
                        "Order.status <" => 3,
                        "OrderDetail.id >" => 0
                    ), 
                    "order" => array(
                        "Order.id" => "asc"
                    )
                );

                $data_order_result = $this->Order->find('all', $option_order);

                if (isset($data_order_result) && !empty($data_order_result)) {
                    $alphabet = range('A', 'Z');

                    $data_order = array();
                    $seat_array = array();
                    $schedule_detail_array = array();
                    $order_ids = array();
                    foreach($data_order_result as $orders) {
                        $data_order[$orders['Order']['inv_number']]['Order'] = $orders['Order'];
                        $data_order[$orders['Order']['inv_number']]['movie'] = $orders['Movie']['code'];
                        $data_order[$orders['Order']['inv_number']]['show_time'] = date('Y-m-d', strtotime($orders['ScheduleDetail']['date'])) . " " . $orders['ScheduleDetail']['time'];
                        $data_order[$orders['Order']['inv_number']]['schedule_detail_layout_id'][] = $orders['OrderDetail']['schedule_detail_layout_id'];
                        $data_order[$orders['Order']['inv_number']]['seat_number'][] = $alphabet[$orders['ScheduleDetailLayout']['row_number']].$orders['ScheduleDetailLayout']['label'];

                        $seat_array[] = $orders['OrderDetail']['schedule_detail_layout_id'];
                        $schedule_detail_array[] = $orders['ScheduleDetailLayout']['schedule_detail_id'];
                        $order_ids[] = $orders['Order']['id'];
                    }

                    $tmp_msg = "";
                    $counter = 0;
                    foreach($data_order as $order) {
                        $counter++;
                        $tmp_msg .= $counter.". id : ".$order['Order']['id'].
                                    ", inv_number : ".$order['Order']['inv_number'].
                                    ", date : ".$order['Order']['date'].
                                    ", show_time : ".$order['show_time'].
                                    ", movie : ".$order['movie'].
                                    ", status : ".$order['Order']['status'].
                                    ", layout_id : ".implode(",",$order['schedule_detail_layout_id']).
                                    ", seat_number : ".implode(",",$order['seat_number']).
                                    "<br/>";
                    }
                    $message .= $tmp_msg;

                    //update seat
                    //update attendance rate
                    //update order

                    $condition_seat = array(
                        'ScheduleDetailLayout.id' => $seat_array,
                        'ScheduleDetailLayout.status > ' => 1
                    );

                    $update_seat = array(
                        'ScheduleDetailLayout.status' => 1
                    );

                    $condition_order = array(
                        'Order.id' => $order_ids,
                        'Order.status <' => 3
                    );

                    $update_order = array(
                        'Order.status' => 4
                    );

                    $schedule_detail_ids = implode(",", $schedule_detail_array);

                    $dbo = $this->Order->getDataSource();
                    $dbo->begin();
                    try {
                        
                        if (!$this->ScheduleDetailLayout->updateAll($update_seat, $condition_seat)) {
                            $dbo->rollback();
                            $message .= 'failed to update ScheduleDetailLayout'."<br/>";
                
                            goto return_and_exit;    
                        }

                        $prefix = Environment::read('database.prefix');

                        $sqlstr =  "update " . $prefix . "schedule_details a left join ( ".
                                            "select schedule_detail_id, count(id) as used_seats ".
                                            "from " . $prefix . "schedule_detail_layouts ".
                                            "where status > 1 and is_blocked_seat = 0 and enabled = 1 ".
                                                "and schedule_detail_id in (".$schedule_detail_ids.") ".
                                            "group by schedule_detail_id ".
                                        ") b on b.schedule_detail_id = a.id ".
                                    "set a.attendance_rate = b.used_seats / a.capacity * 100 ".
                                    "where a.id in (".$schedule_detail_ids.")";

                        $this->Order->query($sqlstr);

                        if ($this->Order->updateAll($update_order, $condition_order)) {
                            $dbo->commit();
                            $valid = true;
                            $message .= 'Clearing Transaction and Release seat completed'."<br/>";
                        } else {
                            $dbo->rollback();
                            $message .= 'failed to update Order'."<br/>";
                
                            goto return_and_exit;    
                        }
                        
                    } catch (Exception $e) {
                        $dbo->rollback();
                        $message .= 'unable to clear booking transaction : '.$e->getMessage()."<br/>";
            
                        goto return_and_exit;
                    }

                } else {
                    $message .= 'no transaction exceed time limit : ' . $limit_time . "<br/>";
                }

            } else {
                $message .= 'seat-release setting not found, please set set-release in module setting'."<br/>";
                goto return_and_exit;
            }

            return_and_exit:
            $result = array('valid' => $valid, 'message' => $message);

            return $result;
        }

        public function do_transaction_exceed_payment_time_limit_clearing() {
            $valid = false;
            $message = '';

            /*
                1. find order that exceed time limit
                2. get all the seat id
                3. set the conditions and updates for data order
                4. set the conditions and updates for data seats
                5. run updateAll
                6. save into Log database ...
                6. save into Log file ...
            */

            $valid = true;
            $result = array('valid' => $valid, 'message' => $message);

            return $result;            
        }

        public function do_hourly_hkbo_report() {
            $valid = false;
            $message = '';

            /*
                1. get the report
                2. upload to FTP
                3. save into Log database ...
                4. save into Log file ...
            */

            $data['type'] = 'hourly';

            $data['Report']['report_date'] = date('Y-m-d');
            $data['Report']['time_report'] = date('H:i');

            $new_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date'])));
            $data['date_report'] = $new_date;
            $data['time_report'] = date('H:00', strtotime($data['Report']['time_report']));
            $data['time_start'] = '06:00:00';

            $tmp_date_time_report = date('Y-m-d H:00', strtotime($new_date . ' ' . $data['Report']['time_report']));
            $data['date_of_report'] = date('Y-m-d H:i', strtotime($new_date . ' ' . $data['Report']['time_report']));
            $data['date_time_report'] = date('Y-m-d-H', strtotime($tmp_date_time_report));

            $data['datetime_end'] = date('Y-m-d H:59:59', strtotime($tmp_date_time_report . ' -1 hours'));
            $data['datetime_start'] = date('Y-m-d 06:00:00', strtotime($data['datetime_end']));

            $data['date_engagement'] = date('Y-m-d', strtotime($data['datetime_start']));
            $limit_date_engagement = date('Y-m-d 06:00', strtotime($new_date));
            if (strtotime($data['datetime_end']) < strtotime($limit_date_engagement)) {
                $data['date_engagement'] = date('Y-m-d', strtotime($data['datetime_start'] . ' -1 days'));
            }

            $report_result = $this->Order->get_HKBO_report($data);

            if ($report_result['status']) {
                $filename = $report_result['filename'];

                $retry = 0;
                $limit_retry = 5;
                $can_report = false;
                while (!$can_report && ($retry < $limit_retry)) {
                    $retry++;
                    $result = $this->do_upload_file_to_ftp($filename, $retry);
                    $can_report = $result['valid'];
                    $message .= $result['message'];
                }

                $valid = $can_report;
            }

            return_and_exit:

            $result = array('valid' => $valid, 'message' => $message);

            return $result;
        }

        public function do_daily_hkbo_report() {
            $valid = false;
            $message = '';

            /*
                1. get the report
                2. upload to FTP
                3. save into Log database ...
                4. save into Log file ...
            */

            $data['type'] = 'daily';
            $data['Report']['report_date'] = date('Y-m-d');
            $data['Report']['time_report'] = date('H:i');

            $new_date = date('Y-m-d', strtotime(str_replace('/', '-', $data['Report']['report_date'])));

            $data['date_report'] = $new_date;
            $data['time_report'] = '06:00:00';
            $data['time_start'] = '06:00:00';

            $data['date_time_report'] = $new_date;

            $tmp_date_time_report = date('Y-m-d H:i', strtotime($new_date . ' ' . $data['Report']['time_report']));
            $data['date_of_report'] = $tmp_date_time_report;

            $data['datetime_end'] = date('Y-m-d 05:59:59', strtotime($new_date));
            $data['datetime_start'] = date('Y-m-d 06:00:00', strtotime($new_date . ' -1 days'));

            $data['date_engagement'] = date('Y-m-d', strtotime($data['datetime_start']));

            $report_result = $this->Order->get_HKBO_report($data);

            if ($report_result['status']) {
                $filename = $report_result['filename'];

                $retry = 0;
                $limit_retry = 5;
                $can_report = false;
                while (!$can_report && ($retry < $limit_retry)) {
                    $retry++;
                    $result = $this->do_upload_file_to_ftp($filename, $retry);
                    $can_report = $result['valid'];
                    $message .= $result['message'];
                }

                $valid = $can_report;
            }

            return_and_exit:
            
            $result = array('valid' => $valid, 'message' => $message);

            return $result;
        }

        public function do_clearing_incomplete_registration() {
            $valid = false;
            $message = 'Incomplete Registration Clearing executed'."<br/>";
            $message .= 'date : '.date('Y-m-d H:i')."<br/>";
            /*
                1. Find incomplete registration that exceed time limit
                2. gather all the data for logging
                3. Remove those record
            */

            $limit_time = $this->Setting->get_value('registration-limit-time');

            if ($limit_time > 0) {
                $cur_time = date('Y-m-d H:i');

                $prefix = Environment::read('database.prefix');

                $sqlstr_drop = "DROP TEMPORARY TABLE IF EXISTS "  . $prefix . "mytables";
                $this->Member->query($sqlstr_drop);

                $sqlstr = "CREATE TEMPORARY TABLE IF NOT EXISTS " . $prefix . "mytables AS (".
                "select a.member_id as member_id,  max(a.expired_date) as expired_date " .
                "from " . $prefix . "member_renewals a " .
                "where a.status = 3 ".
                "group by a.member_id)";

                $this->Member->query($sqlstr);

                $sqlstr_drop = "DROP TEMPORARY TABLE IF EXISTS "  . $prefix . "mytable_actives";
                $this->Member->query($sqlstr_drop);

                $sqlstr = "CREATE TEMPORARY TABLE IF NOT EXISTS " . $prefix . "mytable_actives AS (".
                "select a.member_id as member_id,  max(a.expired_date) as expired_date " .
                "from " . $prefix . "member_renewals a " .
                "where a.status = 3 ".
                "group by a.member_id)";

                $this->Member->query($sqlstr);

                $option = array(
                    'fields' => array(
                        'Member.*',
                        'Mytable.member_id',
                        'MytableActive.member_id',
                    ),
                    'joins' => array(
                        array(
                            'alias' => 'Mytable',
                            'table' => Environment::read('table_prefix') . 'mytables',
                            'type' => 'left',
                            'conditions' => array(
                                'Mytable.member_id = Member.id'
                            ),
                        ),
                        array(
                            'alias' => 'MytableActive',
                            'table' => Environment::read('table_prefix') . 'mytable_actives',
                            'type' => 'left',
                            'conditions' => array(
                                'MytableActive.member_id = Member.id',
                                'MytableActive.expired_date >' => date('Y-m-d')
                            ),
                        ),
                    ),
                    "conditions" => array(
                        "TIMESTAMPDIFF(MINUTE, Member.created, '".$cur_time."') > " => $limit_time,
                        'OR' => array(
                            "Member.phone_verification" => null,
                            "Member.email_verification" => null,
                            "Mytable.member_id" => null,
                            "MytableActive.member_id" => null,
                        )
                    ), 
                    "order" => array(
                        "Member.id" => "asc"
                    )
                );

                $data_member_result = $this->Member->find('all', $option);

                if (isset($data_member_result) && !empty($data_member_result)) {
                    $delete_member_ids = array();
                    $update_member_ids = array();

                    $tmp_msg = "";
                    $counter = 0;
                    foreach($data_member_result as $member) {
                        $counter++;

                        if (isset($member['Mytable']['member_id']) && 
                            !empty($member['Mytable']['member_id']) && 
                            empty($member['MytableActive']['member_id'])) {
                            $update_member_ids[] = $member['Member']['id'];
                            $status = 'updated';
                            $last_expired_date = $member['Mytable']['expired_date'];
                        } else {
                            $delete_member_ids[] = $member['Member']['id'];
                            $status = 'deleted';
                            $last_expired_date = '';
                        }
                        
                        $tmp_msg .= $counter.". member_id : ".$member['Member']['id'].
                                    ", name : ".$member['Member']['name'].
                                    ", created_date : ".$member['Member']['created'].
                                    ", status : ".$status.
                                    ", last_expired_date : ".$last_expired_date.
                                    "<br/>";
                    }
                    $message .= $tmp_msg;

                    //update member
                    //delete member

                    $condition = array(
                        'Member.id' => $update_member_ids,
                    );

                    $update = array(
                        'Member.country_code' => "''",
                        'Member.phone' => "''",
                        'Member.email' => "''",
                    );

                    $condition_delete = array(
                        'Member.id' => $delete_member_ids,
                    );

                    $dbo = $this->Order->getDataSource();
                    $dbo->begin();
                    try {

                        if (!$this->Member->updateAll($update, $condition)) {
                            $dbo->rollback();
                            $message .= 'failed to update member (remove phone and email)'."<br/>";
                
                            goto return_and_exit;    
                        }

                        if ($this->Member->deleteAll($condition_delete)) {
                            $dbo->commit();
                            $valid = true;
                            $message .= 'Clearing incomplete member registration completed'."<br/>";
                        } else {
                            $dbo->rollback();
                            $message .= 'failed to delete member'."<br/>";
                
                            goto return_and_exit;    
                        }

                    } catch (Exception $e) {
                        $dbo->rollback();
                        $message .= 'unable to clear incomplete member registration'.$e->getMessage()."<br/>";
            
                        goto return_and_exit;
                    }

                } else {
                    $message .= 'no incomplete registration exceed time limit : ' . $limit_time . "<br/>";
                }

            } else {
                $message .= 'registration-limit-time setting not found, please set registration-limit-time in module setting'."<br/>";
                goto return_and_exit;
            }

            return_and_exit:
            $result = array('valid' => $valid, 'message' => $message);

            return $result;            
        }

    }
?>