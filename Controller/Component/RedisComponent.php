<?php
	App::uses('Component', 'Controller');
    include(APP . '/Vendor/cakephp/cakephp/lib/Cake/Cache/Engine/RedisEngine.php');

	class RedisComponent extends Component {
        private $redis_instance;

		/**
		 * Components
		 *
		 * @var array
		 */
		public $components = array(
			'Session',
		);


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

        public function connect() {
            // $redis_conf = Environment::read('redis');
			// $this->redis_instance = new RedisEngine();
			// $this->redis_instance->init($redis_conf);
        }

        public function get_instance() {
            // if (!$this->redis_instance) {
            //     $this->connect();
            // }            
            // return $this->redis_instance;
        }

        public function get_cache($cache_slug, $user_id, $module_name = '') {
            // $this->get_instance();
 
            // $result_cache = $this->redis_instance->read(Environment::read('redis.prefix').'_user_'.$user_id.$module_name);

            // return ($result_cache) ? $result_cache: array();
		}
		
		public function set_cache($cache_slug, $user_id, $module_name, $value, $duration = 100) {
			// $this->get_instance();
			// $key = Environment::read('redis.prefix').'_user_'.$user_id.$module_name;

            // return $this->redis_instance->write($key, $value, $duration * 60);
		}
		
        public function get_cache_global($name) {
            // $this->get_instance();
            // $result_cache = $this->redis_instance->read($name);

            // return ($result_cache) ? $result_cache: array();
		}
		
		public function set_cache_global($name, $value, $duration = 100) {
			// $this->get_instance();
			// return $this->redis_instance->write($name, $value, $duration);
        }		

	}
?>