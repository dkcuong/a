<?php
App::uses('PosAppController', 'Pos.Controller');

class CouponsController extends PosAppController {

	public $components = array('Paginator');
	private $model = 'Coupon';
    private $model_lang = 'CouponLanguage';

    private $language_input_fields = array(
        'id',
        'coupon_id',
        'language',
        'des',
        'terms'
    );

    public function beforeFilter(){
		parent::beforeFilter();
		$this->set('title_for_layout', __d('coupon', 'item_title'));
	}

	public function admin_index() {
		$model = $this->model;

		$conditions = [];

		$this->Paginator->settings = array(
			'fields' => array($model.".*"),
			'joins' => array(
//				array(
//					'alias' => 'MemberCoupon',
//					'table' => Environment::read('table_prefix') . 'member_coupons',
//					'type' => 'left',
//					'conditions' => array(
//						$model.'.id = MemberCoupon.coupon_id',
//					),
//				),
			),
			'conditions' => array($conditions),
            'limit' => Environment::read('web.limit_record'),
			'order' => array($model . '.created' => 'DESC'),
		);

		$types = $this->$model->type;
        $this->set('dbdatas', $this->paginate());
        $this->set(compact('model', 'types'));		
	}

	public function admin_edit($id = null) {
		$model = $this->model;
        $languages_model = $this->model_lang;

		$options = array(
			'conditions' => array($model.'.' . $this->$model->primaryKey => $id),
			'recursive' => 1
		);
		$old_item = $this->$model->find('first', $options);

		if (!$old_item) {
			throw new NotFoundException(__('invalid_data'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			$data = $this->request->data;

			$valid = true;

			if ($valid) {
				$dbo = $this->$model->getDataSource();
				$dbo->begin();
				
				try {
					if ($this->$model->saveAll($data)) {
						$dbo->commit();
						$this->Session->setFlash(__('data_is_saved'), 'flash/success');
						$this->redirect(array('action' => 'index'));
					} else {
						$dbo->rollback();
						$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
					}
				} catch (Exception $ex) {
					$dbo->rollback();
					$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
				}
			} else {
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
			}
		} else {
			$this->request->data = $old_item;
		}

        //languages fields
        $language_input_fields = $this->language_input_fields;
        $languages_list = (array)Environment::read('site.available_languages');

        $types = $this->$model->type;
		$this->set(compact('model', 'language_input_fields', 'languages_model', 'languages_list', 'types'));
	}

}
