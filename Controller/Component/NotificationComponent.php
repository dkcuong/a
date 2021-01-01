<?php
/**
 * Custom Component for PUSH notification
 * 
 * @author Ricky Lam @ VTL
 */
	App::uses('Component', 'Controller');

	class NotificationComponent extends Component {
		/**
		 * Components
		 *
		 * @var array
		 */
		public $components = array(
			// Enable AndroidPushComponent
			'AndroidPush',

			// Enable ApplePushComponent
			'ApplePush'
		);

/**
 * Component callbacks
 */
		/**
		 * Is called before the controller’s beforeFilter method.
		 * 
		 */
		public function initialize(Controller $controller) {
			parent::initialize($controller);

		}

		/**
		 * Is called after the controller’s beforeFilter method 
		 * but before the controller executes the current action handler.
		 * 
		 */
		public function startup(Controller $controller) {
			parent::startup($controller);
		}

		/**
		 * Is called after the controller executes the requested action’s logic,
		 * but before the controller’s renders views and layout.
		 * 
		 */
		public function beforeRender(Controller $controller) {
			parent::beforeRender($controller);

		}

		/**
		 * Is called before output is sent to the browser.
		 * 
		 */
		public function shutdown(Controller $controller) {
			parent::shutdown($controller);

		}

		/**
		 * Is called before output is sent to the browser.
		 * 
		 */
		public function beforeRedirect(Controller $controller, $url = "", $status = null, $exit = true) {
			parent::beforeRedirect($controller, $url, $status, $exit);

		}

		public function push($ios_data, $android_data, $arr_messages, $push_params = array() ){
            $ios_status = false;
            $ios_error_messages = array();
            $ios_params = array();

            $android_status = false;
            $android_error_messages = array();
            $android_params = array();
            
            // start push apple device
            // Calling ApplePushComponent, set the PEM, passphrase and server
            $this->ApplePush->set_credential(Environment::read('push.sandbox'));

            // form the push message and push to device(s)
            $ios_pushed = $this->ApplePush->push($ios_data, $arr_messages, $push_params);

            if (isset($ios_pushed['status']) && ($ios_pushed['status'] == true)) {
                $ios_status = true;
            } else {
                $ios_status = false;
                $ios_error_messages = $ios_pushed['error_messages'];
            }
            
            $ios_params = $ios_pushed['params'];
            // end push apple device

            // start push android device
            // set the FCM server key accordingly
            $this->AndroidPush->set_credential(Environment::read('push.aos.sandbox'));

            // form the push message and push to device(s)
            $aos_pushed = $this->AndroidPush->push($android_data, $arr_messages, $push_params);

            if( isset($aos_pushed['status']) && ($aos_pushed['status'] == true) ){
                $android_status = true;
            } else {
                $android_status = false;
                $android_error_messages = $aos_pushed['error_messages'];
            }
            $android_params = $aos_pushed['params'];
            // end push android device

			return array(
                'ios_status' => $ios_status,
                'ios_error_messages' => $ios_error_messages,
                'ios_params' => $ios_params,
                'android_status' => $android_status,
                'android_error_messages' => $android_error_messages,
                'android_params' => $android_params
            );
		}
	}
