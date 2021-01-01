<?php
App::uses('AdministrationAppModel', 'Administration.Model');
/**
 * Role Model
 *
 * @property Administrator $Administrator
 * @property Permission $Permission
 */
class Role extends AdministrationAppModel {
	public $actsAs = array('Containable');
    /**
     * Validation rules
     *
     * @var array
     */
	public $validate = array(
		'slug' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
                'required' => true,
			),
		),
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
                'required' => true,
			),
		),
	);

	public $belongsTo = array(
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
        'ManageRole' => array(
            'className' => 'Member.Role',
            'foreignKey' => 'manage_role_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

	public $display_fields = array(
		'Role.id', 'Role.slug', 'Role.name', 'Role.manage_role_id',
	);

    /**
     * hasAndBelongsToMany associations
     * @var array
     */
	public $hasAndBelongsToMany = array(
		'Administrator' => array(
			'className' => 'Administration.Administrator',
			'joinTable' => 'administrators_roles',
			'foreignKey' => 'role_id',
			'associationForeignKey' => 'administrator_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'Permission' => array(
			'className' => 'Administration.Permission',
			'joinTable' => 'roles_permissions',
			'foreignKey' => 'role_id',
			'associationForeignKey' => 'permission_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

	public function get_list_roles(){
        $conditions = array( 'Role.slug LIKE' => 'role-%' );

		$roles = $this->find('list', array(
            'fields' => array('id', 'name'),
            'conditions' => $conditions,
		));
		
		return $roles;
	}

	public function update_roles( $role = array() ){
		$params = array();

		$updated = array( 
			'status' => false, 
			'message' => __('data_is_not_saved'), 
			'params' => $params
		);

		if( empty($role) ){
			$updated = array( 
				'status' => false, 
				'message' => __d('administration','missing_parameter'), 
				'params' => $params
			);
		} else {
			$data['Role'] = $role['Role'];

			if (!isset($data['Role']['id'])) {	// add
				$this->create();
				$temp = $role;
				if( isset($temp['Permission']['rules']) && !empty($temp['Permission']['rules']) ){
					foreach ($temp['Permission']['rules'] as $key => $rule) {
						if( !empty($temp) ){
							$data['Permission'][] = array(
								'role_id' => "",
								'permission_id' => $rule,
							);
						}
					}
                }
                
				unset($data['Permission']['rules']);
			}else{	
				if( isset($role['Permission']['rules']) && !empty($role['Permission']['rules']) ){
					foreach ($role['Permission']['rules'] as $key => $rule) {
						if( !empty($role) ){
							$role_id = "";
							if( isset($role['Role']['id']) && !empty($role['Role']['id']) ){
								$role_id = $role['Role']['id'];
							}

							$data['Permission'][] = array(
								'role_id' => $role_id,
								'permission_id' => $rule,
							);
						}
					}
				}
            }
            
            if(isset($data['Role']['manage_role_id']) && $data['Role']['manage_role_id'] == ''){
                $data['Role']['manage_role_id'] = (int)$data['Role']['manage_role_id'];
            }
            
			if( $this->saveAll( $data ) ){
				$updated = array( 
					'status' => true, 
					'message' => __('data_is_saved'), 
					'params' => $params
				);
			} else {
				if( Environment::is('development') ){
					// if it is the developement environment, show the debug message
					$params = $role;
				}

				$updated = array( 
					'status' => false, 
					'message' => __('data_is_not_saved'), 
					'params' => $params
				);
			}
		}

		return $updated;
	}

	public function get_role( $role_id ){
        $role = $this->find('first', array(
            'fields' => $this->display_fields,
            'conditions' => array('Role.id' => $role_id),
        ));

        return $role;
    }
    
    public function get_list_parent(){
        $roles = $this->find('list', array(
            'fields' => 'Role.id, Role.name',
            'conditions' => array(
                'Role.manage_role_id' => 0
            ),
        ));

        return $roles;
    }

    public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
            'conditions' => $conditions,
            'contain' => array(
                'CreatedBy',
                'UpdatedBy'
            ), 
            'order' => array( 'Role.id' => 'desc' ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
    }

    public function format_data_export($data, $row){
        return array(
            !empty($row['Role']["id"]) ? $row['Role']["id"] : '',
            !empty($row['Role']["slug"]) ? $row['Role']["slug"] : '',
            !empty($row['Role']["name"]) ? $row['Role']["name"] : '',
            !empty($row['Role']["updated"]) ? $row['Role']["updated"] : '',
            !empty($row['UpdatedBy']["email"]) ?  $row['UpdatedBy']["email"]: ' ',
            !empty($row['Role']["created"]) ? $row['Role']["created"] : '',
            !empty($row['CreatedBy']["email"]) ?  $row['CreatedBy']["email"]: ' ',
        );
    }
}

