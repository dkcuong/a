<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('Controller', 'Controller');
App::uses('HttpSocket', 'Network/Http');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	public $components = array(
		// Enable DebugKit toolbar (when debug is set to >= 1)
		// 'DebugKit.Toolbar' => array(
		// 	'panels' => array('ClearCache.ClearCache')
		// ),

		// Enable JSON or XML view
        'RequestHandler',
        
		// Enable SessionComponent
		'Session',

		// Enable FlashComponent
		'Flash',
        
		// Enable CookieComponent
		'Cookie',
        
        'Api',

        'ExcelSpout',
        
        'Email',

        'Sms',

        'Redis',

        'Notification',

		// Enable LogFileComponent
		'LogFile',

        'ApplePush',

        'AndroidPush',

		// Enable general functions in CommonComponent
        'Common',
		// Enable ControllerListComponent
		'ControllerList' => array( 
			'includePlugins' => array(
                'Administration',
                'Dashboard',
                'Pos',
                'Movie',
                'Cinema'
			)
		)
	);

	public $helpers = array( 'Session', 'Flash' );

    public $theme = "";
    
    protected $slug_language = 'eng';

	public function beforeFilter(){

        $this->layout = "default";

		// all page we don't do anything
        if($this->request->plugin == "administration" && $this->request->controller == "administrators" &&
                ($this->request->action == "admin_login" || $this->request->action == "admin_logout"
                || $this->request->action == "admin_forgot_password" || $this->request->action == "admin_change_password")){
			$this->theme = "CakeAdminLTE";
			$this->layout = 'default';
            return;
        }

        $current_user = array();
        $permissions = array();

        $this->request->addDetector('api', array(
			'callback' => function ($request) {
				return (isset($request->params['api']) && $request->params['api']);
			})
        );

		$available_language = (array)Environment::read('site.available_languages');
		$params = $this->request->params;
		
        // set and get language  *****
        if(isset($this->request->data["set_new_language"]) && $this->request->data["set_new_language"] != "" &&
                in_array($this->request->data["set_new_language"], $available_language)){
            
            $url_params = array();
            foreach($this->request->query as $key => $value){
                array_push($url_params, $key . '=' . $value);
            }

            $is_admin = true;
            if (isset($this->request->data["origin_trigger"]) && ($this->request->data["origin_trigger"] == 'frontend')) {
                $is_admin = false;
            }
            $arr_url = array(
                'plugin' => $params['plugin'],
                'controller' => $params['controller'],
                'action' => $params['action'],
                'admin' => $is_admin,
            );
            foreach($params['pass'] as $item){
                array_push($arr_url, $item);
            }
            $current_url = Router::url($arr_url, true) . ($url_params ? '?' . implode('&', $url_params) : '');

            $this->Session->write('Config.language', $this->request->data["set_new_language"]);
            
            $this->redirect($current_url);
        }

		if(!$this->Session->check('Config.language')){
            /*
                check from cache, if exists in cache then use cache and write to session 
                else use from default
            */
            $new_lang = Environment::read('site.default_language');
            
            $this->Session->write('Config.language', $new_lang);
        }

		$this->lang18 = $this->Session->read('Config.language');
		
        if ($this->lang18 && file_exists(APP . 'View' . DS . $this->lang18 . DS . $this->viewPath . DS . $this->view . $this->ext)) {
            $this->viewPath = $this->lang18 . DS . $this->viewPath;
        }		

		if ( isset($params['prefix']) && ($params['prefix'] == "admin") ) {
			$this->theme = "CakeAdminLTE";
			$this->layout = 'default';

            /***** Start Secure on web ******/
			if( !($this->Session->check('Administrator.id') && $this->Session->check('Administrator.current')) ){
                $arr_url = array(
                    'plugin' => $params['plugin'],
                    'controller' => $params['controller'],
                    'action' => $params['action'],
                    'admin' => true,
				);
				
                if($params['pass']){
                    foreach($params['pass'] as $item){
                        array_push($arr_url, $item);
                    }
                }
                
                $arr_url['?'] = $this->request->query;

                $current_url = Router::url($arr_url, true);

                return $this->redirect( Router::url( array(
                    'plugin' => 'administration',
                    'controller' => 'administrators',
                    'action' => 'login',
                    'admin' => true,
                    '?' => array('last_url' => $current_url)
                ), true));
            }

            $current_user = $this->Session->read('Administrator.current');

            $permissions = $current_user['Permission'];
            unset($current_user['Permission']);

            // check permission in action
            $action = str_replace('admin_', '', $params['action']);

            $view_actions = ['index', 'view_detail', 'export', 'get_data_select', 'export_excel', 'get_movie_type', 'add_new_time_schedule', 'add_new_image_new', 'scan_qrcode', 'add_movie_type_new', 'generate_hkbo_report', 'export_hkbo_report'
                , 'view_qr_code', 'generate_today_report', 'generate_today_ticket_report', 'generate_today_tuckshop_report'
                ,'generate_member_sales_report', 'generate_ticket_sales_report', 'generate_tuckshop_sales_report'
                ,'past_index', 'past_view', 'report'
                ];
            $add_actions = ['copy', 'get_content_push', 'add_all', 'send_coupon'];
            $edit_actions = ['resetpassword', 'editPassword', 'import', 'import_detail', 'exec_cronjob', 'get_mall_shop', 'edit_mall_shop', 'set_layout'];
            $delete_actions = ['delete_all', 'get_delete_mall_shop', 'delete_mall_shop'];

            // pr($action);
            // pr($view_actions);
            // exit;

            if(in_array($action, $view_actions)){
                $action = 'view';
			}else if(in_array($action, $add_actions)){
                $action = 'add';
            }else if(in_array($action, $edit_actions)){
                $action = 'edit';
            }else if(in_array($action, $delete_actions)){
                $action = 'delete';
            }
            
            if(!(($this->request->plugin == "dashboard" && $this->request->controller == "dashboard") || 
                ($this->request->controller == "redis" && $this->request->action == "admin_update_column_cache"))){
				$has_permission = array_filter($permissions, function($item) use($params, $action){
					return strtolower($item['p_plugin']) == strtolower($params['plugin']) && 
						strtolower($item['p_controller']) == strtolower($params['controller']) && isset($item[$action]);
                });
                
				if(!$has_permission){
					if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
						echo 'Invalid Permission'; exit;
					}else{
						throw new NotFoundException('Invalid Permission');
					}
				}
            }
        } else if ( isset($params['prefix']) && ($params['prefix'] == "api") && 
                    isset($params['action']) && (!in_array($params['action'], array(
//                        'api_logout'
                    )))) {

            // login / logout cant go here

            /*
            $data = $this->request->data;            
            if (isset($data['token']) && !empty($data['token'])) {
                $token = $data['token'];
            } else {
                throw new NotFoundException(__('invalid_data'));
            }

            $cache_value = $this->Redis->get_cache('timeout', $token);
            $objSetting = ClassRegistry::init('Setting.Setting');

            if (empty($cache_value)) {
                throw new NotFoundException(__('login_has_expired'));
            } else if ($cache_value == 'family') {
                $duration = $objSetting->get_timeout('family_timeout');
                $this->Redis->set_cache('timeout', $token, '', 'family', $duration);
            } else if ($cache_value == 'member') {
                $duration = $objSetting->get_timeout('member_timeout');
                $this->Redis->set_cache('timeout', $token, '', 'member', $duration);
            }
            */
        } else {
            if ((($params['controller'] == 'paymentpage') && ($params['action'] == 'payment')) ||
                (($params['controller'] == 'paymentpage') && ($params['action'] == 'payment_return_url')) ||
                (($params['controller'] == 'paymentpage') && ($params['action'] == 'payment_notify_url')) ||
                (($params['controller'] == 'paymentpage') && ($params['action'] == 'payment_member_renewal')) ||
                (($params['controller'] == 'paymentpage') && ($params['action'] == 'try_again')) ||
                (($params['controller'] == 'paymentpage') && ($params['action'] == 'get_started')) ||
                ($params['controller'] == 'dashboardpage')) {
                //do nothing
            } else {
                if( !$this->Session->check('staff') &&
                    ($params['controller'] != 'frontpage')) {

                    return $this->redirect( Router::url( array(
                        'controller' => 'frontpage',
                        'action' => 'index',
                        'admin' => false
                    ), true));
                }

                $staff = $this->Session->read('staff');
                $current_user = $staff['Staff']['id'];

                $webroot = Environment::read('web.url_img');

                $staff['Staff']['name'] = strtoupper($staff['Staff']['name']);
                $staff['Staff']['image'] = $webroot . 'general/avatar.png';

                $this->set(compact('webroot', 'staff'));
            }
        }
        $this->ObjLog = ClassRegistry::init('Log.Log');

        $this->set(compact('current_user', 'permissions', 'available_language'));
    }
    
    protected function slugify($string){
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    }

    public function return_json ($result){
        $this->RequestHandler->respondAs('json');
        $this->response->type('application/json');  
        $this->autoRender = false; 
        echo json_encode($result);
    }

    public function send_email_change_pass($data) {
        if (isset($data['email']) && !empty($data['email'])) {
            $template = "change_pass_notif";
            $subject = '[NannyBus] - '.__('change_pass_notif');

            $admin_email = $data['email'];

            $result_email = $this->Email->send($admin_email, $subject, $template, $data);
        }
    }
    
    protected function send_default_mail_to_manager($item_name, $item_link, $subject){
        $current_user = $this->Session->read('Administrator.current');
        $role_ids = array();
        foreach ($current_user['Role'] as $item) {
            if ($item['manage_role_id'] == 0) {
                return array(
                    'status' => true,
                );
            }
            array_push($role_ids, $item['id']);
        }

        $administrators = array();
        if(!empty($role_ids)) {
            $objAdministrator = ClassRegistry::init('Administration.Administrator');
            $administrators = $objAdministrator->get_all_manager_by_role_ids($role_ids, Environment::read('company.id'));
        }

        if(!empty($administrators)) {
            $template = "default_approved";
            $data = array(
                'name' => implode(', ', $administrators['names']),
                'item_name' => $item_name,
                'item_link' => $item_link,
                'created_name' => $current_user['name'],
            );

            $result_email = $this->Email->send($administrators['emails'], $subject, $template, $data);
            return $result_email; 
        }
        
        return array(
            'status' => true,
        );
    }


    public function send_sms($country_code, $phone, $language, $title, $message) {

        $return_result = array(
            'status' => true,
            'result_data' => array(),
            'message' => __('send_sms_success')
        );

        $country_code = $country_code;
        $phone = $phone;

        $receiver = array();
        $receiver[0]['phone'] = $country_code . $phone;
        $receiver[0]['language'] = $language;

        $str_title = $title;
        $title = array($language => $str_title);

        $str_msg = $message;
        $sms_message = array($language => $str_msg);

        $sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');

        if ($sent_data['status']) {
            $return_result = array(
                'status' => true,
                'result_data' => $sent_data,
                'message' => __('send_sms_success')
            );
        } else {
            $return_result = array(
                'status' => false,
                'result_data' => $sent_data,
                'message' => __('send_sms_failed')
            );
        }
        return $return_result;
    }

    public function send_email($email, $template, $subject, $data_email) {
        $return_result = array(
            'status' => true,
            'result_data' => array(),
            'message' => __('send_email_success')
        );

        $email_prefix = Environment::read('web.email_subject_prefix');

        $template = $template;
        $subject = $email_prefix . $subject;

        $receiver = $email;

        $result_email = $this->Email->send($receiver, $subject, $template, $data_email);

        if ($result_email['status']) {
            $return_result = array(
                'status' => true,
                'result_data' => $data_email,
                'message' => __('send_email_success')
            );
        } else {
            $return_result = array(
                'status' => false,
                'result_data' => $data_email,
                'message' => __('send_email_failed')
            );
        }
        return $return_result;
    }

    public function create_member_coupon($data_member_renewal) {
        $data_member_coupon = array();

        $objCoupon = ClassRegistry::init('Pos.Coupon');

        $options = array(
            'fields' => array(
                'Coupon.*'
            ),
            'contain' => array(
                'CouponLanguage' => array(
                    'conditions' => array(
                        'language' => $this->Api->get_language()
                    )
                )
            ),
            'conditions'=> array( 'type' => 1 ),
        );
        $coupon_result = $objCoupon->find('first', $options);

        $coupon_id = $coupon_result['Coupon']['id'];

        $member_id = $data_member_renewal['MemberRenewal']['member_id'];
        $data_member_coupon['MemberCoupon']['coupon_id'] = $coupon_id;
        $data_member_coupon['MemberCoupon']['member_id'] = $member_id;

        $objMember = ClassRegistry::init('Member.Member');
        $code = $objMember->generateToken();
        $data_member_coupon['MemberCoupon']['code'] = $code;
        $data_member_coupon['MemberCoupon']['expired_date'] = date('Y-m-d', strtotime('+1 years'));
        $qr_code_path = $this->Common->generate_qrcode("coupon", $code, $code)['path'];
        $data_member_coupon['MemberCoupon']['code_path'] = $qr_code_path;
        $data_member_coupon['MemberCoupon']['status'] = 1;

        $objMemberCoupon = ClassRegistry::init('Member.MemberCoupon');
        if ( $objMemberCoupon->saveAll($data_member_coupon)) {
            $objMember = ClassRegistry::init('Member.Member');
            $member_info = $objMember->find('first', array('conditions'=> array('id' => $member_id)));
            $qr_code_link = Environment::read('web.url_img') . $qr_code_path ;

            $data_sent = array();
            $data_sent['expired_date'] = isset($data_member_coupon['MemberCoupon']['expired_date']) ? $data_member_coupon['MemberCoupon']['expired_date'] : null;
            $data_sent['expired_date'] = date('Y-m-d', strtotime($data_sent['expired_date']));
            $data_sent['name'] = isset($coupon_result['Coupon']['description']) ? $coupon_result['Coupon']['description'] : null;
            $data_sent['des'] = isset($coupon_result['CouponLanguage'][0]['des']) ? $coupon_result['CouponLanguage'][0]['des'] : null;
            $data_sent['terms'] = isset($coupon_result['CouponLanguage'][0]['terms']) ? $coupon_result['CouponLanguage'][0]['terms'] : null;
            $data_sent['welcome_coupon_code'] = $qr_code_link;

            // Send code to mobile
            $country_code = $member_info['Member']['country_code'];
            $phone = $member_info['Member']['phone'];

            $receiver = array();
            $receiver[0]['phone'] = $country_code . $phone;
            $receiver[0]['language'] = $this->lang18;

            $str_title = 'ACX-Cinemas';
            $title = array($this->lang18 => $str_title);

            $str_msg = sprintf(
                __('welcome_coupon_msg'),
                $data_sent['name'],
                $data_sent['expired_date'],
                $data_sent['welcome_coupon_code']
            );
            $sms_message = array($this->lang18 => $str_msg);

            $sent_data = $this->Sms->send_sms_members($receiver, $title, $sms_message, 'verification');
            // $sent_data['status'] = true;
            if (!$sent_data['status']) {
                $result_data = $sent_data;
                $message = __('send_sms_failed');
                $valid = false;
            }

            $email = $member_info['Member']['email'];

            // Send code to email
            $template = "welcome_coupon";
            $subject = 'ACX-Cinemas - Welcome Coupon';

            $receiver = $email;

            $data_sent['email'] = $email;

            $result_email = $this->Email->send($receiver, $subject, $template, $data_sent);

            if (!$result_email['status']) {
                $result_data = $result_email;
                $message = __('send_email_failed');
                $valid = false;
            }

        } else {
            $message = 'fail_to_create_coupon';
        }
    }
}
