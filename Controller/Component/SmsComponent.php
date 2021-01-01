<?php

/**
 * Custom Component for PUSH notification
 * 
 * @author Ricky Lam @ VTL
 */
App::uses('Component', 'Controller');

class SmsComponent extends Component {
    /**
     * Components
     *
     * @var array
     */
    public $components = array();

    /* Params ********************
         $members: 
            array(
                [1] => array(
                    'phone' => '',
                    'language' => '',
                ),
                [2] => array(
                    'phone' => '',
                    'language' => '',
                ),
            )
        $type: promotion or verification or system_msg.
     */
    public function send_sms_members($members, $title, $msg, $type) {
        
        $sms_key = Environment::read('sms.'.$type);

        if ($sms_key == '') {
            return array(
                "status" => false, 
                "error_message" => __('sms_key_is_empty'),
            );
        }

        $result = array();
        try {
            $url_credit = Environment::read('sms.url_credit');
            $url_send = Environment::read('sms.url_portal');
            
            $call_header = array(
                "Accept: application/json",
                "Accept-Language: en-US",
                "Content-Type: application/json",
                "x-Api-Key: " . $sms_key
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_credit);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded', 'charset=utf-8'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $call_header);
            $json_data = curl_exec($ch);

            if (curl_errno($ch)) {
                return array(
                    'status' => false,
                    'error_message' => curl_error($ch),
                );
            }
            curl_close($ch);

            $json_data = json_decode($json_data, true);

            if (!isset($json_data['code']) || $json_data['code'] != 100) {
                return array(
                    'status' => false,
                    'error_message' => __('invalid_data') . " Code parameter",
                );
            }

            if (!isset($json_data['smsCredit']) || $json_data['smsCredit'] == '') {
                return array(
                    'status' => false,
                    'error_message' => __('missing_parameter') . " smsCredit",
                );
            }

            $total_count = count($members);
            if ($json_data['smsCredit'] < $total_count)	{
                return array(
                    'status' => false,
                    'error_message' => __('credit_over_load'), 
                );
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url_send);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Cache-Control: no-cache', 'Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded', 'charset=utf-8'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $call_header);

            $succeed_case = array();
            $failed_case  = array();

            foreach ($members as $member) {
                $send_data = array(
                    'phone' => $member['phone'],
                    'message' => __("[") . $title[$member['language']] . __("]") . " " . $msg[$member['language']],
                );

                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($send_data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
                $result_data = curl_exec($ch);

                if (curl_errno($ch)) {
                    array_push($failed_case, array(
                        'member' => $member['phone'],
                        'message' => curl_error($ch)
                    ));
                }else{
                    curl_close($ch);
                    $result_data = json_decode($result_data, true);
                    if ($result_data['code'] == 100) {
                        array_push($succeed_case, $member);
                    } else {
                        array_push($failed_case, array(
                            'member' => $member['phone'],
                            'message' => 'error code: ' . $result_data['code']
                        ));
                    }
                }
            }

            $result =  array(
                'status' => true,
                'params' => array(
                    'failed' => $failed_case,
                    'succeed' => $succeed_case,
                )
            );

        } catch (\Exception $e) {
            $result = array(
                'status' => false,
                'error_message' => $e->getMessage(),
            );
        }

        return $result;
    }
}
