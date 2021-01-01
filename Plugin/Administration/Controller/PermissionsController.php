<?php
App::uses('AdministrationAppController', 'Administration.Controller');
/**
 * Permissions Controller
 *
 * @property Permission $Permission
 * @property PaginatorComponent $Paginator
 */
class PermissionsController extends AdministrationAppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

	public function beforeFilter(){
		parent::beforeFilter();
		$this->set('title_for_layout', __d('administration', 'administration') . ' > ' . __d('administration', 'permission'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		// get list model
		$cmbModel = $this->Permission->find('list', array(
			'fields' => array( 'p_model' ),
            'order' => array( 'p_model' => 'ASC' ),
            'group' => array( 'p_model' )
        ));
        
		$data_search = $this->request->query;
        $conditions = array();
		if ($data_search) {
            // filter
			if (isset($data_search['cmbModel']) && !empty($data_search['cmbModel'])) {
                if(isset($cmbModel[$data_search['cmbModel']])){
                    $conditions['Permission.p_model'] = trim($cmbModel[$data_search['cmbModel']]);
                }
            }

            // vilh - export information
            if( isset($data_search['button']['export']) && !empty($data_search['button']['export']) ) {
                $this->requestAction(array(
                    'plugin' => 'administration',
                    'controller' => 'permissions',
                    'action' => 'export',
                    'admin' => true,
                    'prefix' => 'admin',
                    'ext' => 'json'
                ), array(
					'conditions' => $conditions,	
					'contain' => array(
						'CreatedBy',
						'UpdatedBy'
					),
                    'type' => 'csv',
                    'language' => "",	//$language,
                ));
            }

            // vilh - export excel information
            if( isset($data_search['button']['exportExcel']) && !empty($data_search['button']['exportExcel']) ) {
                $this->requestAction(array(
                    'plugin' => 'administration',
                    'controller' => 'permissions',
                    'action' => 'export',
                    'admin' => true,
                    'prefix' => 'admin',
                    'ext' => 'json'
                ), array(
					'conditions' => $conditions,	//$Coupon_conditions,
					'contain' => array(
						'CreatedBy',
						'UpdatedBy'	// left join
					),
                    'type' => 'xls',
                    'language' => "",	//$language,
                ));
            }

			// show duplicate data
			if (isset($data_search['show_duplicate_permission'])) {
				// find duplicate items
				$model = $this->Permission->find('all', array(
					'fields' => array('Permission.name', 'Permission.slug', 'Permission.p_model'),
					'group' => array('Permission.name', 'Permission.slug', 'Permission.p_model'),
					'having' => array('Count(Permission.id) >' => 1),
				));

				if ($model)	
				{
					foreach($model as $item)
					{
						$conditions['OR'][] = array(
							'Permission.name' => $item['Permission']['name'],
							'Permission.slug' => $item['Permission']['slug'],
							'Permission.p_model' => $item['Permission']['p_model'],
						);
					}
				}
				else
				{
                    $conditions['Permission.id'] = -1;
				}
			}
        }
		$this->Paginator->settings = array(
            'limit' => 30,
			'conditions' => $conditions,
			'order' => array('Permission.p_plugin' => 'ASC', 'Permission.p_controller' => 'ASC', 'Permission.p_model' => 'ASC'),
		);

        $permissions_records = $this->paginate();
        $this->set(compact('permissions_records', 'cmbModel', 'data_search'));
	}

	public function admin_export() {
		$this->disableCache();
		if( $this->request->is('get') ) {
            $header = array(
                __('id'),
                __('slug'),
                __d('administration','name'),
                __d('administration','p_model'),
                __d('administration','action'),
                __('updated'),
                __('updated_by'),
                __('created'),
                __('created_by'),
            );
            $limit = 2000;
            
			try{
                $file_name = 'permission_' . date('Ymdhis');
                $data_binding = array();

                // export xls
                if ($this->request->type == "xls") {
                    $this->ExcelSpout->setup_export_excel($header, 'Administration.Permission', $data_binding, $this->request->conditions, $limit, $file_name, $this->lang18);
                } else {
                    $this->Common->setup_export_csv($header, 'Administration.Permission', $data_binding, $this->request->conditions, $limit, $file_name, $this->lang18);
                }
                exit;
            } catch ( Exception $e ) {
                $this->LogFile->writeLog($this->LogFile->get_system_error(), $e->getMessage());
                $this->Session->setFlash(__('export_csv_fail') . ": " . $e->getMessage(), 'flash/error');
            }
	    }
    }
    
    /**
     * admin_add method
     *
     * @return void
     */
	public function admin_add() {
		if ($this->request->is('post')) {
            $this->Permission->create();
			$permission = $this->request->data;

			$permission['Permission']['slug'] = "perm-admin-" . $permission['Permission']['slug'];
			switch ($permission['Permission']['action_id']) {
				case 0: //add
					$permission['Permission']['action'] = "add";
					break;
				case 1: //delete
					$permission['Permission']['action'] = "delete";
					break;
				case 2: //view
					$permission['Permission']['action'] = "view";
					break;
				case 3: //upgrade
					$permission['Permission']['action'] = "upgrade";
					break;
			}
			unset($permission['Permission']['action_id']);

			if ($this->Permission->save($permission)) {

				$this->Session->setFlash(__('data_is_saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
			}
        }
        
        $permissions = $this->Permission->Find('list', array(
            'fields' => array('slug', 'name'),
        ));
        
        $slugs = json_encode(array_keys($permissions));
		$names = json_encode(array_values($permissions));
		$actions = array(__("add"), __("edit"), __("delete"), __("view"), __("upgrade")); // 0,1,2,3

        $this->set(compact('slugs', 'names', 'actions'));
	}

    /**
     * fung edit 2017年09月05日11:47:54
     * add all Model permission at one time
     */
	public function admin_add_all(){
		if ($this->request->is('post')) {
			$this->Permission->create();

			$data = $this->request->data['Permission'];

			$slug = strtolower(Inflector::classify($data['model']));
			$plugin = $data['plugin'];
			$controller = $data['controller'];
			$model = ucfirst(Inflector::classify($data['model']));
            $name = Inflector::classify($data['name']);
            
            $exist_permission = $this->Permission->Find('all', array(
                'conditions' => array(
                    'OR' => array(
                        'slug LIKE ' => 'perm-admin-'. $slug . '-%',
                        'LOWER(name)' => strtolower($name)
                    )
                ),
            ));

            if($exist_permission){
                $this->Session->setFlash(__d('administration', 'permission_exist'), 'flash/error');
            }else{
                $permissions = array(
                    array(
                        'slug' => 'perm-admin-'.$slug.'-view',
                        'name' => $name . '-列表',
                        'p_plugin' => $plugin,
                        'p_controller' => $controller,
                        'p_model' => $model,
                        'action' => 'view',
                    ),
                    array(
                        'slug' => 'perm-admin-'.$slug.'-add',
                        'name' => $name . '-新增',
                        'p_plugin' => $plugin,
                        'p_controller' => $controller,
                        'p_model' => $model,
                        'action' => 'add',
                    ),
                    array(
                        'slug' => 'perm-admin-'.$slug.'-edit',
                        'name' => $name . '-編輯',
                        'p_plugin' => $plugin,
                        'p_controller' => $controller,
                        'p_model' => $model,
                        'action' => 'edit',
                    ),
                    array(
                        'slug' => 'perm-admin-'.$slug.'-delete',
                        'name' => $name . '-刪除',
                        'p_plugin' => $plugin,
                        'p_controller' => $controller,
                        'p_model' => $model,
                        'action' => 'delete',
                    ),
                );

                if ($this->Permission->saveAll($permissions)) {
                    $this->Session->setFlash(__('data_is_saved'), 'flash/success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
                }
            }
			
		}
	}

    /**
     * admin_delete method
     *
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @param string $id
     * @return void
     */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Permission->id = $id;
		if (!$this->Permission->exists()) {
			throw new NotFoundException(__('invalid_data'));
        }
        
        $objRolesPermission = ClassRegistry::init('Administration.RolesPermission');
        $administration_roles = $objRolesPermission->find('count', array(
            'conditions' => array(
                'RolesPermission.permission_id' => $id, 
            ),
        ));

        if($administration_roles){
            $this->Session->setFlash(__('data_not_delete_was_used'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }

		if ($this->Permission->delete()) {
			$this->Common->force_logout_affected_user($objRolesPermission->get_role_ids_by_permission($id));
			$this->Session->setFlash(__('data_is_deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}

		$this->Session->setFlash(__('data_is_not_deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
		
	}

	// vilh (2019/03/26)
	// - delete permission on permission table
	// - delete permission had assign to role
	public function admin_deleteall() {
		try {
			if (!$this->request->is('post')) {
				throw new MethodNotAllowedException();
			}

			$data = $this->request->data;

			if ($data)
			{
				$RolesPermission = ClassRegistry::init('Administration.RolesPermission');
				
				$roles_arr = array();
				foreach ($data['ids'] as $id)
				{
					// Delete all permission had assgin to role - with permission.id (RolesPermission table)
					$RolesPermission->deleteAll(array('permission_id' => $id) , false);

					// Delete all permission - with permission.id (Permission table)
					$this->Permission->delete($id);	

					$tmp_roles_arr = $RolesPermission->get_role_ids_by_permission($id);
					$roles_arr = array_merge($roles_arr, $tmp_roles_arr);
				}
				$this->Common->force_logout_affected_user(array_unique($roles_arr));

				$this->Session->setFlash(__('data_is_deleted'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			}
		} catch (Exception $e) {
            $this->LogFile->writeLog($this->LogFile->get_system_error(), $e->getMessage());
			$this->Session->setFlash(__('data_is_not_deleted'), 'flash/error');
			$this->redirect(array('action' => 'index'));
		}		
	}
}
