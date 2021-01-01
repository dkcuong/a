<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 */
class FrontpageController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

	public function index() {
		$this->layout = 'frontpage';
	}

	public function do_login() {
        $this->Api->init_result();
		
		if( $this->request->is('post') ) {
			$this->disableCache();
            $data = $this->request->data;
			
			$valid = true;
			$message = __('retrieve_data_successfully');
			$result_data = array();
			$status = 0;

			if (!isset($data['username']) || empty($data['username'])) {
				$message = __('missing_parameter') . __('username');
				$valid = false;
			} else if (!isset($data['password']) || empty($data['password'])) {
				$message = __('missing_parameter') . __('password');
				$valid = false;
            } else {
				$objStaff = ClassRegistry::init('Cinema.Staff');
				$data_member = $objStaff->get_item_by_phone($data['country_code'], $data['phone']);

				if (isset($data_member['id']) && !empty($data_member['id'])) {
					$status = 1;
				}

				return_api :
				$result_data['status'] = $status;			}

			$this->Api->set_result($valid, $message, $result_data);
		}
		
		$this->Api->output();
	}

	public function do_logout() {
		$this->Session->delete('member_id');
		$this->Session->destroy();

		$this->redirect(array(
			'controller' => 'frontpage',
			'action' => 'index'
		));
	}
}