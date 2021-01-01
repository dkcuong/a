<?php
App::uses('AdministrationAppController', 'Administration.Controller');
/**
 * Roles Controller
 *
 * @property Role $Role
 * @property PaginatorComponent $Paginator
 */
class RolesController extends AdministrationAppController {

/**
 * Components
 *
 * @var array
 */
    public $components = array('Paginator');

	public function beforeFilter(){
		parent::beforeFilter();
		$this->set('title_for_layout', __d('administration', 'administration') . ' > ' . __d('administration','role'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$data = $this->request->query;
		$conditions = array();

        $txtSlug = '';
		if ($data)	// search data
		{
			if (isset($data['txtSlug']) && !empty($data['txtSlug']))
			{
                $txtSlug = $data['txtSlug'];
				$conditions = array(
					'Role.slug LIKE' => '%' . $data['txtSlug'] . '%',
				);
			}
		}
		else // index
		{
			$conditions = array(
				'Role.slug LIKE' => 'role-%',
			);
		}

		$this->Paginator->settings = array(
            'recursive' => 0,
            'conditions' => $conditions,
            'order' => array( 'Role.created' => 'DESC')
		);
		
		$roles = $this->paginate();

		$this->set(compact('roles', 'txtSlug'));

		// vilh - export information
		if( isset($data['button']['export']) && !empty($data['button']['export']) ){
			$sent = $this->requestAction(array(
				'plugin' => 'administration',
				'controller' => 'roles',
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
				'language' => "",	
			));
		}


		// vilh - export information
		if( isset($data['button']['exportExcel']) && !empty($data['button']['exportExcel']) ){
			$sent = $this->requestAction(array(
				'plugin' => 'administration',
				'controller' => 'roles',
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
				'type' => 'xls',
				'language' => "",	
			));
		}
	}

	public function admin_export() {
		$this->disableCache();
		if( $this->request->is('get') ) {
            $limit = 1000; // limit record get one time

            $header = array(
                __('id'),
                __('slug'),
                __d('administration','name'),
                __('updated'),
                __('updated_by'),
                __('created'),
                __('created_by'),
            );

			try{
                $file_name = 'role_' . date('Ymdhis');
                $data_binding = array();

                // export xls
                if ($this->request->type == "xls") {
                    $this->ExcelSpout->setup_export_excel($header, 'Administration.Role', $data_binding, $this->request->conditions, $limit, $file_name, $this->lang18);
                } else {
                    $this->Common->setup_export_csv($header, 'Administration.Role', $data_binding, $this->request->conditions, $limit, $file_name, $this->lang18);
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
		if (!$this->Role->exists($id)) {
			throw new NotFoundException(__('invalid_data'));
        }
        
        $objRole = ClassRegistry::init("Administration.Role");
        $role = $objRole->find('first', array( 
            'contain' => array(
                'ManageRole'
            ),
            'conditions' => array('Role.id' => $id )
        ));
	
        $objRolesPermission = ClassRegistry::init("Administration.RolesPermission");
        $role_permissions = $objRolesPermission->get_permissions_by_role( $id );
        $objPermission = ClassRegistry::Init('Administration.Permission');
        $distinct_actions = $objPermission->find('list', array(
            'fields' => 'Permission.action',
            'order' => 'Permission.action',
        ));
        $distinct_actions = array_unique($distinct_actions); 

		$this->set(compact('role', 'distinct_actions', 'role_permissions'));
	}

    /**
     * admin_add method
     *
     * @return void
     */
	public function admin_add() {
		if ($this->request->is('post')) {

			$this->Role->create();
			$this->request->data["Role"]["slug"] = "role-" . $this->request->data["Role"]["slug"];
			
			$updated = $this->Role->update_roles( $this->request->data );

			if( isset($updated['status']) && ($updated['status'] == true) ){
				$this->Session->setFlash(__('data_is_saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
			}
		}

		$manageRoles = $this->Role->get_list_parent();
        $permissions_matrix = $this->Role->Permission->get_list('admin');
        
		$this->set(compact('permissions_matrix', 'manageRoles'));
	}

    /**
     * admin_edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
	public function admin_edit($id = null) {
        $this->Role->id = $id;
        
		if (!$this->Role->exists($id)) {
			throw new NotFoundException(__('Invalid role'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
            $data = $this->request->data;
            $data['Role']['manage_role_id'] = empty($data['Role']['manage_role_id']) ? 0 : $data['Role']['manage_role_id'];
			$updated = $this->Role->update_roles( $data );
            
			if( isset($updated['status']) && ($updated['status'] == true) ){
				$this->Common->force_logout_affected_user(array($id));
				$this->Session->setFlash(__('data_is_saved'), 'flash/success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('data_is_not_saved'), 'flash/error');
			}
		} else {
			$this->request->data = $this->Role->get_role( $id );
        }
        
		$manageRoles = $this->Role->get_list_parent();

        $objRolesPermission = ClassRegistry::init('Administration.RolesPermission');
        $current_permissions = $objRolesPermission->get_permission_ids_by_role( $id );
        $permissions_matrix = $this->Role->Permission->get_list('admin');
        
		$this->set(compact('permissions_matrix', 'current_permissions', 'manageRoles'));
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
		$this->Role->id = $id;
		if (!$this->Role->exists()) {
			throw new NotFoundException(__('invalid_data'));
        }

        $objAdministratorsRole = ClassRegistry::init('Administration.AdministratorsRole');
        $administration_roles = $objAdministratorsRole->find('count', array(
            'conditions' => array(
                'AdministratorsRole.role_id' => $id, 
            ),
        ));

        if($administration_roles){
            $this->Session->setFlash(__('data_not_delete_was_used'), 'flash/error');
            $this->redirect(array('action' => 'index'));
        }

		if ($this->Role->delete()) {
			$this->Common->force_logout_affected_user(array($id));
			$this->Session->setFlash(__('data_is_deleted'), 'flash/success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('data_is_not_deleted'), 'flash/error');
		$this->redirect(array('action' => 'index'));
	}
}
