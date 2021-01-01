<?php
App::uses('AdministrationAppModel', 'Administration.Model');
/**
 * RolesPermission Model
 *
 * @property Role $Role
 * @property Permission $Permission
 */
class RolesPermission extends AdministrationAppModel {

	public $actsAs = array('Containable');
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'role_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
                'required' => true,
			),
		),
		'permission_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
                'required' => true,
			),
		),
		'enabled' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Role' => array(
			'className' => 'Administration.Role',
			'foreignKey' => 'role_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Permission' => array(
			'className' => 'Administration.Permission',
			'foreignKey' => 'permission_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CreatedBy' => array(
			'className' => 'Administration.Administrator',
			'foreignKey' => 'created_by',
			'conditions' => '',
			'fields' => array('email','name'),
			'order' => ''
		),
		'UpdatedBy' => array(
			'className' => 'Administration.Administrator',
			'foreignKey' => 'updated_by',
			'conditions' => '',
			'fields' => array('email','name'),
			'order' => ''
		),
	);

	// remove row of RolePermission with $id, 
	public function delete_all($id)
	{
		return $this->RolesPermission->delete($id);
	}

	public function get_permissions_by_role( $role_ids ){
        $result = array();
        $role_permissions = $this->find('all', array(
            'fields' => " permission.p_plugin, permission.p_controller, permission.p_model" . 
                ", GROUP_CONCAT(permission.action) as actions ",
            'conditions' => array(
                'RolesPermission.role_id' => $role_ids
            ),
            'joins' => array(
                array(
                    'alias' => 'permission',
                    'table' => Environment::read('table_prefix') . 'permissions',
                    'type' => 'left',
                    'conditions' => array(
                        'RolesPermission.permission_id = permission.id',
                    ),
                ),
            ),
            'group' => array( 'permission.p_plugin', 'permission.p_controller', 'permission.p_model' )
        ));

        if( $role_permissions ){
            foreach($role_permissions as $item){
                $new_item = array(
                    'p_plugin' => $item['permission']['p_plugin'],
                    'p_controller' => $item['permission']['p_controller'],
                    'p_model' => $item['permission']['p_model'],
                );
                if(isset($item[0])){
                    $actions = explode(',', $item[0]['actions']);
                    foreach($actions as $action){
                        $new_item[$action] = true;
                    }
                }
                $result[$item['permission']['p_model']] = $new_item;
            }
        }

		return $result;
    }
    
    public function get_permission_ids_by_role( $role_id ){
        $role_permissions = $this->find('list', array(
            'fields' => array('RolesPermission.permission_id'),
            'conditions' => array(
                'RolesPermission.role_id' => $role_id
            ),
        ));

		return $role_permissions;
	}

	public function get_role_ids_by_permission($id) {
        $role_permissions = $this->find('list', array(
            'fields' => array('RolesPermission.role_id'),
            'conditions' => array(
                'RolesPermission.permission_id' => $id
            ),
        ));

		return $role_permissions;		
	}

	public function update_with_id($data) {
		$this->save($data);
	}
}
