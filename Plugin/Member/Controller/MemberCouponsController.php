<?php
App::uses('MemberAppController', 'Member.Controller');
/**
 * Members Controller
 *
 * @property Member $Member
 * @property PaginatorComponent $Paginator
 */
class MemberCouponsController extends MemberAppController {

	public $components = array('Paginator');
	private $model = 'MemberCoupon';

	private $filter = array(
		'code',
		'expired_date',
	);

	public function beforeFilter(){	
		parent::beforeFilter();
		$this->set('title_for_layout', __d('member_coupon', 'item_title'));
	}

	public function admin_index() {
		$data_search = $this->request->query;
		$model = $this->model;

		foreach ($data_search as $k=>$v) {
		    $data_search[$k] = trim($v);
        }

		if (isset($data_search) && !empty($data_search['coupon_type'])) {
			$couponType = $data_search['coupon_type'];
			unset($data_search['coupon_type']);
		} else {
			$couponType = null;
		}

		if (isset($data_search) && !empty($data_search['member'])) {
			$member = $data_search['member'];
			unset($data_search['member']);
		} else {
			$member = null;
		}

		$conditions = $this->Common->get_filter_conditions($data_search, $model, null, $this->filter);

		if (!is_null($couponType)) {
			$data_search['coupon_type'] = $couponType;
		}

		if (!is_null($member)) {
			$data_search['member'] = $member;
		}

		if ($data_search)
		{
			if( isset($data_search['coupon_type']) && !empty($data_search['coupon_type']) )
			{
				// $objCouponType = ClassRegistry::init('Member.MemberCoupon');
				$objCouponType = ClassRegistry::init('Pos.Coupon');
				$option = array(
					'fields' => array('id'),
					'conditions' => array(
						'type =' => $data_search['coupon_type']
					)
				);
				$coupon_types = $objCouponType->find('list', $option);

				if (!empty($coupon_types))
				{
					$conditions["MemberCoupon.coupon_id IN"] = $coupon_types;
				}
			}

			if( isset($data_search['member']) && !empty($data_search['member']) ) {				
				$conditions["MemberCoupon.member_id"] = $data_search['member'];
				
			}

			// button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'member',
                    'controller' => 'member_coupons',
                    'action' => 'export',
                    'admin' => true,
                    'prefix' => 'admin',
                    'ext' => 'json'
                ), array(
                    'conditions' => $conditions,
                    'type' => 'csv',
                ));
            }

            // button export Excel
            if( isset($data_search['button']['exportExcel']) && !empty($data_search['button']['exportExcel']) ) {
                $this->requestAction(array(
                    'plugin' => 'member',
                    'controller' => 'member_coupons',
                    'action' => 'export',
                    'admin' => true,
                    'prefix' => 'admin',
                    'ext' => 'json'
                ), array(
                    'conditions' => $conditions,
                    'type' => 'xls',
                ));
		    }
		}

		$this->Paginator->settings = array(
			'fields' => array($model.".*", "Member.*", "Coupon.*"),
			'conditions' => array($conditions),
			'joins' => array(
				array(
					'alias' => 'Member',
					'table' => Environment::read('table_prefix') . 'members',
					'type' => 'left',
					'conditions' => array(
						$model.'.member_id = Member.id',
					),
				),
				array(
					'alias' => 'Coupon',
					'table' => Environment::read('table_prefix') . 'coupons',
					'type' => 'left',
					'conditions' => array(
						$model.'.coupon_id = Coupon.id',
					),
				),
			),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.created' => 'DESC'),
		);

		$myoption = array(
			'fields' => array($model.".*", "Member.*", "Coupon.*"),
			'conditions' => array($conditions),
			'joins' => array(
				array(
					'alias' => 'Member',
					'table' => Environment::read('table_prefix') . 'members',
					'type' => 'left',
					'conditions' => array(
						$model.'.member_id = Member.id',
					),
				),
				array(
					'alias' => 'Coupon',
					'table' => Environment::read('table_prefix') . 'coupons',
					'type' => 'left',
					'conditions' => array(
						$model.'.coupon_id = Coupon.id',
					),
				),
			),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.created' => 'DESC'),
		);

		$mydata = $this->$model->find('all', $myoption);

		$types = $this->$model->Coupon->type;
		$statuses = $this->$model->status;

		$objMember = ClassRegistry::init('Member.Member');
		$option = array(
			'fields' => array('id', 'name'),
			'conditions' => array(

			),
		);
		$members = $objMember->find('list', $option);

        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'data_search', 'types', 'statuses', 'members'));		
	}

    public function admin_view($id) {
        $model = $this->model;

        $options = array(
            'fields' => array($model.".*", "Member.*", "Coupon.*"),
            'joins' => array(
                array(
                    'alias' => 'Member',
                    'table' => Environment::read('table_prefix') . 'members',
                    'type' => 'left',
                    'conditions' => array(
                        $model.'.member_id = Member.id',
                    ),
                ),
                array(
                    'alias' => 'Coupon',
                    'table' => Environment::read('table_prefix') . 'coupons',
                    'type' => 'left',
                    'conditions' => array(
                        $model.'.coupon_id = Coupon.id',
                    ),
                ),
            ),
            'contain' => array(
                'UpdatedBy',
                'CreatedBy'
            ),
            'limit' => Environment::read('web.limit_record'),
            'order' => array($model . '.created' => 'DESC'),
            'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
        );
        $model_data = $this->$model->find('first', $options);

        if (!$model_data) {
            throw new NotFoundException(__('invalid_data'));
        }

        $types = $this->$model->Coupon->type;
        $statuses = $this->$model->status;

        $objMember = ClassRegistry::init('Member.Member');
        $option = array(
            'fields' => array('id', 'name'),
            'conditions' => array(

            ),
        );
        $members = $objMember->find('list', $option);
        $this->set('dbdata', $model_data);

        $this->set(compact('model', 'member_model', 'types', 'statuses', 'members'));
    }

	public function admin_export(){
		$model = $this->model;

		$results = array(
		   'status' => false, 
		   'message' => __('missing_parameter'),
		   'params' => array(),
	   );

	   $this->disableCache();

	   if( $this->request->is('get') ) {
		   $result = $this->$model->get_data_export($this->request->conditions, 1, 2000, $this->lang18);

		   if ($result) {

				$cvs_data = array();

				foreach ($result as $row) {
					$temp = $this->$model->format_data_export(array(), $row);

					array_push($cvs_data, $temp);
				}

			   try{
				   $file_name = 'member_coupons_'.date('Ymd');

				   // export xls
				   if ($this->request->type == "xls") {
						$excel_readable_header = array(
							array('label' => __('id')),
							array('label' => __d('coupon', 'type')),
							array('label' => __d('member', 'item_title')),
							array('label' => __d('member', 'phone')),
							array('label' => __d('member_coupon', 'obtained_date')),
							array('label' => __('code')),
							array('label' => __d('member_coupon', 'code_path')),
							array('label' => __d('coupon', 'expiry_date')),
							array('label' => __('status')),
						);
	
						$this->Common->export_excel(
							$cvs_data,
							$file_name,
							$excel_readable_header
						);
					} else {
						$header = array(
							'label' => __('id'),
							'label' => __d('coupon', 'type'),
							'label' => __d('member', 'item_title'),
							'label' => __d('member', 'phone'),
							'label' => __d('member_coupon', 'obtained_date'),
							'label' => __('code'),
							'label' => __d('member_coupon', 'code_path'),
							'label' => __d('coupon', 'expiry_date'),
							'label' => __('status'),
						);
						$this->Common->export_csv(
							$cvs_data,
							$header,
							$file_name
						);
					}
			   	} catch ( Exception $e ) {
					$this->LogFile->writeLog($this->LogFile->get_system_error(), $e->getMessage());
					$results = array(
						'status' => false, 
						'message' => __('export_csv_fail'),
						'params' => array()
					);
			   	}
			}else{
				$results['message'] = __('no_record');
			}
	   }

	   $this->set(array(
		   'results' => $results,
		   '_serialize' => array('results')
	   ));
	}

	public function api_check_coupon() {
        $this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
            
            if (!isset($data['token']) || empty($data['token'])) {
                $message = __('missing_parameter') .  __('token');
            } else if (!isset($data['staff_id']) || empty($data['staff_id'])) {
                $message = __('missing_parameter') .  __('staff_id');
            } else if (!isset($data['coupon_code']) || empty($data['coupon_code'])) {
                $message = __('missing_parameter') .  __('coupon_code');
            } else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->check_coupon_validity($data);

                $status = $result['status'];
				$message = $result['message'];

                if($result['status']){
					$params = $result['params'];
                    if (!$params) {
                        $params = (object)array();
                    }

                }else{
                    if(isset($result['log_data']) && $result['log_data']){
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
			}

            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
	}

	public function api_redeem_ecoupon() {
        $this->Api->init_result();
		$model = $this->model;

		if ($this->request->is('post')) {
            $this->disableCache();
            $status = false;
			$message = "";
            $params = (object)array();
			$data = $this->request->data;
            
            if (!isset($data['token']) || empty($data['token'])) {
                $message = __('missing_parameter') .  __('token');
            } else if (!isset($data['staff_id']) || empty($data['staff_id'])) {
                $message = __('missing_parameter') .  __('staff_id');
            } else if (!isset($data['coupon_code']) || empty($data['coupon_code'])) {
				$message = __('missing_parameter') .  __('coupon_code');
            } else if (!isset($data['physical_coupon_code']) || empty($data['physical_coupon_code'])) {
                $message = __('missing_parameter') .  __('physical_coupon_code');
            } else {
				$this->Api->set_language($this->lang18);

                $url_params = $this->request->params;
                $this->Api->set_post_params($url_params, $data);
                $this->Api->set_save_log(true);

                $result = $this->$model->redeem_ecoupon($data);

                $status = $result['status'];
				$message = $result['message'];

                if($result['status']){
					$params = $result['params'];
                    if (!$params) {
                        $params = (object)array();
                    }

                }else{
                    if(isset($result['log_data']) && $result['log_data']){
                        $this->Api->set_error_log($result['log_data']);
                    }
                }
			}

            $this->Api->set_result($status, $message, $params);
        }
        
		$this->Api->output();
	}

}
