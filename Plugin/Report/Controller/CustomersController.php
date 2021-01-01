<?php
App::uses('ReportAppController', 'Report.Controller');
/**
 * Customers Controller
 *
 * @property PaginatorComponent $Paginator
 */
class CustomersController extends ReportAppController {
/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

	public function beforeFilter(){
		parent::beforeFilter();

		$this->set('title_for_layout', __('report'));
	}

    /**
     * admin_index method
     *
     * @return void
     */
	public function admin_gender() {
        
    }

    /**
     * admin_index method
     *
     * @return void
     */
	public function admin_gender_json() {
		$this->Api->init_result();
		if ($this->request->is('get')) {
            $this->disableCache();
            $data = $this->request->data;

            $objMember = ClassRegistry::init('Member.Member');
            $result_data = $objMember->report_gender_member($this->lang18);
    
			$this->Api->set_result(true, __('retrieve_data_successfully'), $result_data);
		}
		
		$this->Api->output();
    }

    /**
     * admin_index method
     *
     * @return void
     */
	public function admin_birthday() {
        
    }

    /**
     * admin_index method
     *
     * @return void
     */
	public function admin_birthday_json() {
		$this->Api->init_result();
		if ($this->request->is('get')) {
            $this->disableCache();
            $data = $this->request->data;

            $months = $this->Common->get_list_month();

            $objMember = ClassRegistry::init('Member.Member');
            $result_data = $objMember->report_birthday_member($months, $this->lang18);
    
			$this->Api->set_result(true,  __('retrieve_data_successfully'), $result_data);
		}
		
		$this->Api->output();
    }

    public function admin_high_spending(){
        $objMember = ClassRegistry::init('Order.Order');
        $orders = $objMember->get_high_spending();

        $member_ids = Hash::extract($orders, '{n}.Order.member_id');

        $objMember = ClassRegistry::init('Member.Member');
        $members = $objMember->get_list_member_with_id_key(array('id' => $member_ids));
        
        $this->set(compact('members', 'orders'));
    }

    public function admin_high_visit(){
        $objMember = ClassRegistry::init('Member.Member');
        $members = $objMember->get_high_visit();
        
        $this->set(compact('members'));
    }
}
