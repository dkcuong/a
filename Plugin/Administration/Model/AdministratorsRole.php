<?php
App::uses('AdministrationAppModel', 'Administration.Model');
/**
 * AdministratorsRole Model
 *
 * @property Administrator $Administrator
 * @property Role $Role
 */
class AdministratorsRole extends AdministrationAppModel {

	public $actsAs = array('Containable');
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'administrator_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'required' => true,
			),
		),
		'role_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'required' => true,
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
		'Administrator' => array(
			'className' => 'Administration.Administrator',
			'foreignKey' => 'administrator_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Role' => array(
			'className' => 'Administration.Role',
			'foreignKey' => 'role_id',
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

	public function get_administrator_roles( $administrator_id ){
		$result = array();

        $administrator_roles = $this->find('all', array(
            'fields' => array( 'AdministratorsRole.administrator_id', 'AdministratorsRole.role_id' ),
            'conditions' => array( 'AdministratorsRole.administrator_id' => $administrator_id ),
            'contain' => array(
                'Role' => array(
                    'fields' => array( 'Role.id', 'Role.slug', 'Role.name', 'Role.manage_role_id' ),
                )
            )
        ));
        if( $administrator_roles ) {
            foreach ($administrator_roles as $administrator_role) {
                if(!empty($administrator_role['Role'])){
                    $result[] = $administrator_role['Role'];
                }
            }
        }

		return $result;
	}

	public function get_user_by_role( $role_id ) {
		$administrator_roles = $this->find('list', array(
			'fields' => array( 'AdministratorsRole.administrator_id' ),
			'conditions' => array('AdministratorsRole.role_id' => $role_id),
		));
		
		return $administrator_roles;
	}
}
