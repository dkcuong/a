<?php
App::uses('AdministrationAppModel', 'Administration.Model');
/**
 * Permission Model
 *
 * @property Role $Role
 */
class Permission extends AdministrationAppModel {

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
		'p_plugin' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			),
		),
		'p_controller' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			),
		),
		'p_model' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			),
		),
		'action' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

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
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Role' => array(
			'className' => 'Administration.Role',
			'joinTable' => 'roles_permissions',
			'foreignKey' => 'permission_id',
			'associationForeignKey' => 'role_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

	public $display_fields = array(
		'Permission.id', 'Permission.slug', 'Permission.name', 'Permission.p_model', 'Permission.action',
	);

	public function get_list($type){
		$permissions = $this->find('all', array(
			'conditions' => array(
				'slug LIKE' => 'perm-' . $type . '-%'
            ),
            'order' => array('p_plugin', 'p_controller', 'p_model', 'id'),
			
		));

		$list = array();
		if( $permissions ){
			$permissions = Hash::extract( $permissions, "{n}.Permission" );
			foreach ($permissions as $key => $permission) {
				$model = ucfirst( $permission['p_model'] );

				$list[ $model ][] = array(
					'id' => $permission['id'],
					'name' => $permission['name'],
					'action' => $permission['action'],
				);
			}
		}
		return $list;
	}

    public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
            'recursive' => 0,
            'conditions' => $conditions,
            'order' => array( 'Permission.id' => 'desc' ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
    }

    public function format_data_export($data, $row){
        return array(
            $row['Permission']["id"],
            $row['Permission']["slug"],
            $row['Permission']["name"],
            $row['Permission']["p_model"],
            $row['Permission']["action"],
            $row['Permission']["updated"],
            !empty($row['UpdatedBy']["email"]) ?  $row['UpdatedBy']["email"]: ' ',
            $row['Permission']["created"],
            !empty($row['CreatedBy']["email"]) ?  $row['CreatedBy']["email"]: ' ',
        );
    }
}
