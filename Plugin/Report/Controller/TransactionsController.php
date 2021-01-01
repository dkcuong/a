<?php
App::uses('ReportAppController', 'Report.Controller');
/**
 * Transactions Controller
 *
 * @property PaginatorComponent $Paginator
 */
class TransactionsController extends ReportAppController {
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

    public function admin_report_by_mall(){
        $filter_conditions = array();
        $data_search = $this->request->query;
        if(isset($data_search['mall_ids']) && $data_search['mall_ids']){
            $filter_conditions['mall_id'] = $data_search['mall_ids'];
        }

        $objMember = ClassRegistry::init('Order.Order');
        $orders = $objMember->get_spending_by_mall($filter_conditions);

        $objMall = ClassRegistry::init('Company.Mall');
        $malls = $objMall->find_list(array(), $this->lang18);
        
        $this->set(compact('orders', 'malls', 'data_search'));
    }

    public function admin_report_by_shop(){
        $filter_conditions = array();
        $data_search = $this->request->query;
        if(isset($data_search['shop_ids']) && $data_search['shop_ids']){
            $filter_conditions['shop_id'] = $data_search['shop_ids'];
        }
        
        $objMember = ClassRegistry::init('Order.Order');
        $orders = $objMember->get_spending_by_shop($filter_conditions);

        $objShop = ClassRegistry::init('Company.Shop');
        $shops = $objShop->find_list(array(), $this->lang18);
        
        $this->set(compact('orders', 'shops', 'data_search'));
    }
}
