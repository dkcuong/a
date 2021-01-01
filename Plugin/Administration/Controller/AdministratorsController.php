<?php
App::uses('AdministrationAppController', 'Administration.Controller');
/**
 * Administrators Controller
 *
 * @property Administrator $Administrator
 * @property PaginatorComponent $Paginator
 */
class AdministratorsController extends AdministrationAppController {

    /**
     * Components
     *
     * @var array
     */
	public $components = array('Paginator', 'Email');

	public function beforeFilter(){
		parent::beforeFilter();
		$this->set('title_for_layout', __d('administration','administrator'));
	}

    /**
     * admin_index method
     * vilh - update filter
     * @return void
     */
	public function admin_index() {
        $AdministratorsRole = ClassRegistry::init('Administration.AdministratorsRole');
		$data_search = $this->request->query;
        $conditions = array();

        $contain = array(
            'Role' => array( 'fields' => array('slug', 'name') ),
        );

		if ($data_search){
			if (isset($data_search['cmbRole']) && !empty($data_search['cmbRole'])){
				$user_ids = $AdministratorsRole->get_user_by_role($data_search['cmbRole']);			
				if($user_ids){
					$conditions["Administrator.id IN"] = $user_ids;
				}else{
					$conditions["Administrator.id"] = '-1';
				}
			}
	
			if (isset($data_search['txtName']) && !empty($data_search['txtName'])) {
				$conditions['Administrator.name LIKE'] = '%'. trim($data_search['txtName']) . '%';
			}

			if (isset($data_search['txtEmail']) && !empty($data_search['txtEmail'])) {
				$conditions['Administrator.email LIKE'] = '%'. trim($data_search['txtEmail']) . '%';
            }
            
            // button export
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'administration',
                    'controller' => 'administrators',
                    'action' => 'export',
                    'admin' => true,
                    'prefix' => 'admin',
                    'ext' => 'json'
                ), array(
                    'conditions' => $conditions,
                    'type' => 'csv',
                    'language' => "",	//$language,
                ));
            }

            // button export Excel
            if( isset($data_search['button']['exportExcel']) && !empty($data_search['button']['exportExcel']) ) {
                $sent = $this->requestAction(array(
                    'plugin' => 'administration',
                    'controller' => 'administrators',
                    'action' => 'export',
                    'admin' => true,
                    'prefix' => 'admin',
                    'ext' => 'json'
                ), array(
                    'conditions' => $conditions,
                    'type' => 'xls',
                    'language' => "",	//$language,
                ));
		    }
        }
        
		$this->Paginator->settings = array(
			'contain' => $contain,
            'conditions' => $conditions,
            'order' => array( 'Administrator.created' => 'DESC')
		);
        
        $administrators = $this->paginate();
		$roles = $this->Administrator->Role->get_list_roles();	

        $column_cache = json_encode($this->Redis->get_cache('booster_column', '_administrators'));
		$this->set( compact('administrators', 'roles', 'data_search', 'column_cache') );
	}

	public function admin_export() {
        $this->disableCache();
        if( $this->request->is('get') ) {

            $limit = 2000;
            $header = array(
                __('id'),
                __d('administration','name'),
                __d('administration','role'),
                __d('administration','email'),
                __d('administration','phone'),
                __d('administration','last_logged_in'),
                __('enabled'),
                __('updated'),
                __('updated_by'),
                __('created'),
                __('created_by'),
            );
            
            try{
                $file_name = 'administrator_' . date('Ymdhis');

                // export xls
                if ($this->request->type == "xls") {
                    $this->ExcelSpout->setup_export_excel($header, 'Administration.Administrator', $data_binding, $this->request->conditions, $limit, $file_name, $this->lang18);
                } else {
                    $this->Common->setup_export_csv($header, 'Administration.Administrator', $data_binding, $this->request->conditions, $limit, $file_name, $this->lang18);
                }
                exit;
            } catch ( Exception $e ) {
                $this->LogFile->writeLog($this->LogFile->get_system_error(), $e->getMessage());
                $this->Session->setFlash(__('export_csv_fail') . ": " . $e->getMessage(), 'flash/error');
            }
        }
	}

    /**
     * admin_view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
	public function admin_view($id = null) {
		if (!$this->Administrator->exists($id)) {
			throw new NotFoundException(__('invalid_data'));
        }
        
		$options = array(
            'conditions' => array('Administrator.' . $this->Administrator->primaryKey => $id),
            'contain' => array(
                'Role',
                'CreatedBy',
                'UpdatedBy'
            )
        );

        $administrator = $this->Administrator->find('first', $options);

		$this->set('administrator', $administrator);
	}

    /**
     * admin_add method
     *
     * @return void
     */
	public function admin_add() {
        $this->Administrator->validate['email']['uniqueEmailRule']['message'] = __('duplicate_email_exists');

		if ($this->request->is('post')) {
            $data = $this->request->data;

			if( isset($data['Administrator']['password']) && !empty($data['Administrator']['password']) ){
				$data['Administrator']['password'] = $this->Administrator->hash_password( $data['Administrator']['password'] );
			}

			if( !isset($data['Administrator']['token']) || empty($data['Administrator']['token']) ){
				$data['Administrator']['token'] = $this->Administrator->generateToken(8);
			}

            $this->Administrator->create();

            $updated = $this->Administrator->update_administrator( $data );
            if( isset($updated['status']) && $updated['status'] ){
                $this->Session->setFlash(__('data_is_saved'), 'flash/success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash($updated['message'], 'flash/error');
            }
        }

        load_data:
        $this->load_data();
	}

    /**
     * admin_editPassword method
     * vilh - edit password administrator
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function admin_editPassword($id) {
        $this->Administrator->id = $id;
        if (!$this->Administrator->exists($id)) {
            throw new NotFoundException(__('invalid_data'));
        }

        // post
        if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data;
            $old_password  = $this->Administrator->hash_password( $data['Administrator']['old_password'] );
            $administrator = $this->Administrator->find('first', array(
                'conditions' => array(
                    'Administrator.id = ' => $data['Administrator']['id'], 
                    'Administrator.password = ' => $old_password, 
                ),
            ));

            if (!$administrator) {
                $this->Session->setFlash(__('username_password_not_found'), 'flash/error');
            } else {
                $administrator['Administrator']['password'] = $this->Administrator->hash_password( $data['Administrator']['new_password'] );
                if( $this->Administrator->save( $administrator['Administrator'] ) ){
                    $this->Session->setFlash(__('update_password_succeed'), 'flash/success');

                    //session == current user -> force relogin
                    if ($this->Session->read('Administrator.id') == $administrator['Administrator']['id']) {
                        $this->admin_logout();
                    } else {
                        $this->redirect(
                            array(
                            'plugin' => 'administration',
                            'controller' => 'administrators',
                            'action' => 'index',
                            'admin' => true
                        ));
                    }
                } else {
                    $this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
                }
            }
        } else {
            $this->request->data = $this->Administrator->get_user_by_id($id);
        }
    }

	public function admin_edit($id = null) {
        $this->Administrator->validate['email']['uniqueEmailRule']['message'] = __('duplicate_email_exists');
        
        $current_item = $this->Administrator->get_user_by_id($id);
		if (!$current_item) {
			throw new NotFoundException(__('invalid_data'));
        }

		if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data;
            
			if( !isset($data['Administrator']['token']) || empty($data['Administrator']['token']) ){
				$data['Administrator']['token'] = $this->Administrator->generateToken(8);
			}

            $updated = $this->Administrator->update_administrator($data);
			if( isset($updated['status']) && ($updated['status'] == true) ){
                $this->Session->setFlash(__('data_is_saved'), 'flash/success');
                
                $this->Common->force_logout_affected_user_by_user_ids(array($id));
                $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
			}
		} else {
            $current_item['Role']['roles'] = Hash::extract($current_item['Role'], '{n}.id');
            $this->request->data = $current_item;
        }
        
        load_data:
        $this->load_data();
        $current_roles = array();
        if(isset($this->request->data['Role']['roles']) && is_array($this->request->data['Role']['roles'])){
            foreach($this->request->data['Role']['roles'] as $item){
                array_push($current_roles, $item);
            }
        }

        $this->set( compact('brands', 'current_roles') );
    }

    private function load_data(){
		$roles =  $this->Administrator->Role->get_list_roles();	
		$this->set( compact('roles') );
    }

	public function admin_login(){
		$this->layout = "full";
		
		if( $this->request->is('post') ){
			$data = $this->request->data['Administrator'];

            $logged_user = $this->Administrator->login( $data['email'], $data['password'] );

			if( isset($logged_user['status']) && ($logged_user['status'] == true) ){
                $current_user = $logged_user['params'];
				$this->Session->write('Administrator.id', $current_user['id']);
				$this->Session->write('Administrator.current', $current_user);
                
                //i will read the session id and then put it into redis session
                $sess_id = $this->Session->id();
                $session_cache = $this->Redis->set_cache(Environment::read('redis.prefix'), '_sessionid', $sess_id, 10000);

                if(isset($this->request->query['last_url']) && $this->request->query['last_url'] != ''){
                    $this->redirect($this->request->query['last_url']);
                }else{
                    $this->redirect(array(
                        'plugin' => 'dashboard',
                        'controller' => 'dashboard',
                        'action' => 'index',
                        'admin' => true
                    ));
                }
			} else {
				$this->Session->setFlash($logged_user['message'], 'flash/error');
			}
        }
    }
    
    public function admin_forgot_password(){
		$this->layout = "full";
		if( $this->request->is('post') ){
			$data = $this->request->data['Administrator'];

            $params = $this->request->params;
            $log_data = $this->Common->get_log_data_admin();
            $objLog = ClassRegistry::init('Log.Log');
            $dbo = $this->Administrator->getDataSource();
            $dbo->begin();
            try{
                $get_code_forgot = $this->Administrator->forgot_password( $data['email'] );
                
                if( isset($get_code_forgot['status']) && ($get_code_forgot['status'] == true) ){
                    // save to log data
                    $administrator = $get_code_forgot['params']['new_data'];
                    $code = '$2a$10.' . time() .'/'. $administrator['id'] .'/'. $administrator['code_forgot'];
                    $code = base64_encode($code);
                    if(!$objLog->add_log_admin($log_data, $params, array(__d('administration', 'forgot_password_success'), $code), array(), $get_code_forgot['params']['new_data'], $get_code_forgot['params']['old_data'])){
                        $dbo->rollback();
                        $this->Session->setFlash('[' . __('log') . ']' . __('data_is_not_saved'), 'flash/error');
                    }

                    $dbo->commit();
                    // Start send mail to member by email of member
                    $subject = "[" . Environment::read('company.title') . "]" . __('email_subject_forgot_password');
                    $template = "forgot_password_admin";
                    $data = array(
                        'name' => $administrator['name'],
                        'link' => Router::url(array(
                                'plugin' => 'administration',
                                'controller' => 'administrators',
                                'action' => 'change_password',
                                'admin' => true,
                            ), true) . '?code=' . $code,
                    );
                    $this->Email->send($administrator['email'], $subject, $template, $data);
                    // END send mail to member by email of member
    
                    $this->Session->setFlash($get_code_forgot['message'], 'flash/success');
                } else {
                    $this->Session->setFlash($get_code_forgot['message'], 'flash/error');
                    $objLog->add_log_admin($log_data, $params, array(), array(
                        $get_code_forgot['message'],
                        json_encode($get_code_forgot['params'])
                    ), array(), array());
                }
            }catch(Exception $e){
                $objLog->add_log_admin($log_data, $params, array(), array($e->getMessage()), array(), array());
            }
            
            $this->redirect(array(
                'plugin' => 'administration',
                'controller' => 'administrators',
                'action' => 'login',
                'admin' => true
            ));
        }
    }
    
    public function admin_change_password(){
        $this->layout = "full";
        $status = true;
        $query = $this->request->query;
        if( !isset($query['code']) || $query['code'] == '' ){
            $this->Session->setFlash(__('missing_parameter') . __('code'), 'flash/error');
            $status = false;
            goto redirect_link;
        }

        // decode code
        $str_code = base64_decode($query['code']);
        $str_code = str_replace('$2a$10.', '', $str_code);
        $arr_code = explode('/', $str_code);
        if(count($arr_code) != 3){
            $this->Session->setFlash(__d('member', 'invalid_code'), 'flash/error');
            $status = false;
            goto redirect_link;
        }
        
        $administrator_change = $this->Administrator->find('first', array(
            'conditions' => array(
                'Administrator.id' => $arr_code[1],
                'Administrator.code_forgot' => $arr_code[2],
                'Administrator.created_code_forgot >=' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
            )
        ));

        if(!$administrator_change){
            $this->Session->setFlash(__d('member', 'invalid_code'), 'flash/error');
            $status = false;
            goto redirect_link;
        }

        if( $this->request->is('post') ){
            $data = $this->request->data['Administrator'];
            if($data['password'] != $data['confirm_password']){
                $this->Session->setFlash(__d('administration', 'confirm_password_is_not_match'), 'flash/error');
                goto redirect_link;
            }

            $params = $this->request->params;
            $log_data = $this->Common->get_log_data_admin();
            $objLog = ClassRegistry::init('Log.Log');
            $dbo = $this->Administrator->getDataSource();
            $dbo->begin();
            try{
                $administrator_data = $administrator_change['Administrator'];
                $administrator_data['password'] = $this->Administrator->hash_password( $data['password'] );
                $saved_admin = $this->Administrator->save( $administrator_data );
                if( $saved_admin ){
                    // save to log data
                    if(!$objLog->add_log_admin($log_data, $params, array(__d('administration', 'change_password_success')), array(), $saved_admin, $administrator_change)){
                        $dbo->rollback();
                        $this->Session->setFlash('[' . __('log') . ']' . __('data_is_not_saved'), 'flash/error');
                    }

                    $dbo->commit();

                    $this->Session->setFlash(__d('member', 'change_password_success'), 'flash/success');
                    $this->redirect(array(
                        'plugin' => 'administration',
                        'controller' => 'administrators',
                        'action' => 'login',
                        'admin' => true
                    ));
                } else {
                    $this->Session->setFlash(__d('member', 'change_password_fail'), 'flash/error');
                    $objLog->add_log_admin($log_data, $params, array(), $this->Administrator->invalidFields(), array(), array());
                }
            } catch(Exception $e){
                $objLog->add_log_admin($log_data, $params, array(), array($e->getMessage()), array(), array());
            }
        }

        redirect_link:
        if(!$status){
            $this->redirect(array(
                'plugin' => 'administration',
                'controller' => 'administrators',
                'action' => 'login',
                'admin' => true
            ));
        }
	}

	public function admin_logout(){
		$this->layout = $this->autoRender = false;

		if( $this->Session->check('Administrator.id') ){
			$this->Session->delete('Administrator.id');
			$this->Session->destroy();

            $this->Session->setFlash(__d('administration', 'user_is_logged_out'), 'flash/success');
			
			$this->redirect(array(
				'plugin' => 'administration',
				'controller' => 'administrators',
				'action' => 'login',
				'admin' => true
			));
		} else {
			$this->Session->setFlash(__d('administration', 'logout_error'), 'flash/error');
        }
        
		$this->redirect('/');
	}

}
