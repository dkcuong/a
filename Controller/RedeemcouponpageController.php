<?php
App::uses('AppController', 'Controller');
/**
 * Home Controller
 *
 */
class RedeemcouponpageController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('RequestHandler');

	public function index() {
		$display_link_signout = true;

		$this->set(compact('display_link_signout'));
	}
}