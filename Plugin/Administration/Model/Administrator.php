<?php

use SebastianBergmann\RecursionContext\Exception;

App::uses('AdministrationAppModel', 'Administration.Model');
/**
 * Administrator Model
 *
 * @property BackendLog $BackendLog
 * @property Role $Role
 */
class Administrator extends AdministrationAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			),
		),
		'email' => array(
			'notBlank' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Provide an email address'
			),
			'validEmailRule' => array(
				'rule' => array('email'),
				'message' => 'Invalid email address'
			),
			'uniqueEmailRule' => array(
				'rule' => 'isUnique',
				// 'message' =>  'Email already registered'
			)
		),
		'phone' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'required' => true,
			),
		),
	);

	public $actsAs = array('Containable');

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
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
			'joinTable' => 'administrators_roles',
			'foreignKey' => 'administrator_id',
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
		'Administrator.id', 'Administrator.name', 'Administrator.enabled', 
		'Administrator.email', 'Administrator.phone', 'Administrator.last_logged_in', 'Administrator.token',
	);

	public function find_list($conditions = array()){
		$data = array();

		$data = $this->find('all', array(
            'fields' => array('id', 'email'),
			'conditions' => $conditions,
        ));

		if ($data) {
			$data = Hash::combine($data, '{n}.Administrator.id', '{n}.Administrator.email');
		}else{
			$data = array();
		}

		return $data;
	}

	public function update_administrator( $administrator ){
		$params = array();

		$updated = array( 
			'status' => false, 
			'message' => __d('administration', 'Fail to update administrator info. Please try again.'), 
			'params' => $params
		);

		if( empty($administrator['Role']['roles']) ){
			$updated = array( 
				'status' => false, 
				'message' => __d('administration', 'Not enough data.'), 
				'params' => $params
			);
		} else {
			$data['Administrator'] = $administrator['Administrator'];
			
			if ( empty($administrator['Role']['roles']) ) {
				return $updated = array( 
					'status' => false, 
					'message' => __d('administration','please_select_role'), 
					'params' => $params
				);
			}

			if (!isset($data['Administrator']['id'])) { // add function
				$this->create();
				$data = $administrator;

				unset($data['Role']['roles']);

				if( isset($administrator['Role']['roles']) && !empty($administrator['Role']['roles']) ){
					foreach ($administrator['Role']['roles'] as $key => $role) {
						if( !empty($role) ){
							$data['Role'][] = array(
								'administrator_id' => "",
								'role_id' => $role,		// vilh
							);
						}
					}
				}
			} else {
				if( isset($administrator['Role']['roles']) && !empty($administrator['Role']['roles']) ){
					foreach ($administrator['Role']['roles'] as $key => $role) {
						if( !empty($role) ){
							$administrator_id = "";
							if( isset($administrator['Administrator']['id']) && !empty($administrator['Administrator']['id']) ){
								$administrator_id = $administrator['Administrator']['id'];
							}

							$data['Role'][] = array(
								'administrator_id' => $administrator_id,
								'role_id' => $role,
							);
						}
					}
				}
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
					$params = $administrator;
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

	public function login( $email = "", $raw_password = "" ){
        $params = array();

		if( !isset($email) || empty($email) ){
			if( Environment::is('development') ){
				$params = array( 'Missing' => 'email' );
			}
			$logged_user = array( 
				'status' => false, 
				'message' => __( 'missing_parameter' ), 
				'params' => $params
			);
		} else if( !isset($raw_password) || empty($raw_password) ){
			if( Environment::is('development') ){
				// if it is the developement environment, show the debug message
				$params = array( 'Missing' => 'raw_password' );
			}
			$logged_user = array( 
				'status' => false, 
				'message' => __( 'missing_parameter' ),
				'params' => $params
			);
		} else {
            $encrypted_password = $this->hash_password( $raw_password );
            $administrator = $this->find('first', array(
                'fields' => $this->display_fields,
                'conditions' => array(
                    'Administrator.email' => $email, 
					'Administrator.password' => $encrypted_password,
					'Administrator.enabled' => true,
                ),
            ));

            
			if( !empty($administrator) ){
                $result = $administrator['Administrator'];

                $result['Role'] = array();
                $result['Permission'] = array();
                
                // update last logged of this user.
				$last_logged_user = $this->update_last_logged_user( $result['id'] );
				if( isset($last_logged_user['status']) && ($last_logged_user['status'] == true) ){
					$result['last_logged_in'] = $last_logged_user['last_logged_in'];
                }

                $objAdministratorsRole = ClassRegistry::init('Administration.AdministratorsRole');
                $roles = $objAdministratorsRole->get_administrator_roles( $result['id'] );
				if( !empty($roles) ){
                    $result['Role'] = $roles;
                    $result['is_admin'] = false;

                    $role_ids = Hash::extract( $roles, "{n}.id" );
                    $objRolesPermission = ClassRegistry::init("Administration.RolesPermission");
                    $result['Permission'] = $objRolesPermission->get_permissions_by_role( $role_ids );
                }

				$logged_user = array( 
					'status' => true, 
					'message' => __d('administration','login_success'), 
					'params' => $result
				);
			} else {
				$logged_user = array( 
					'status' => false, 
					'message' => __d('administration','login_fail'), 
				);
			}
		}
        return_result:
		return $logged_user;
    }

    public function forgot_password( $email = "" ){
        $status = false;
        $message = '';
        $params = array();
		if( !isset($email) || empty($email) ){
			$message = __( 'missing_parameter' ) . " " . __d('administration','email');
		} else {
            $administrator = $this->find('first', array(
                'fields' => $this->display_fields,
                'conditions' => array(
                    'Administrator.email' => $email, 
					'Administrator.enabled' => true,
                ),
            ));
            
			if( $administrator ){
                // generate code to reset password
                $admin['code_forgot'] = $this->generate_code();
                $admin['created_code_forgot'] = date('Y-m-d H:i:s');
                // log system old and new data
                $saved_administrator = $this->save($admin);
                if($saved_administrator){
                    // save to log data
                    $params = array(
                        'old_data' => $administrator['Administrator'],
                        'new_data' => $saved_administrator['Administrator']
                    );

                    $status = true;
                    $message = __d('administration', 'forgot_password_success');
                }else{
                    $message = __d('administration', 'forgot_password_failed');
                    $params = $this->invalidFields();
                }
			} else {
			    $message = __d('administration', 'email_not_exist');
			}
        }
        
        return_result:
		return array(
            'status' => $status,
            'message' => $message,
            'params' => $params
        );
	}

	public function update_last_logged_user( $administrator_id ){
        $current = date('Y-m-d H:i:s');

        $data = array(
            'id' => $administrator_id,
            'last_logged_in' => $current
        );

        if( $this->save( $data ) ){
            return array( 
                'status' => true, 
                'last_logged_in' => $current
            );
        } else {
            return array( 
                'status' => false, 
            );
        }
	}
    
    public function get_user_by_id($user_id){
        return $this->find('first', array(
            'fields' => $this->display_fields,
            'conditions' => array(
                'Administrator.id' => $user_id, 
            ),
            'contain' => array('Role' => array('id', 'slug', 'name'))
        ));
    }

    public function get_all_manager_by_role_ids($role_ids){
        if(empty($role_ids)){
            return array();
        }

        $objRole = ClassRegistry::init('Administration.Role');
        $manage_role_ids = $objRole->find('list', array(
            'fields' => array('manage_role_id'),
            'conditions' => array(
                'id' => $role_ids,
                'manage_role_id <>' => 0
            )
        ));

        $objAdministratorsRole = ClassRegistry::init('Administration.AdministratorsRole');
        $administrator_ids = $objAdministratorsRole->find('list', array(
            'fields' => array('administrator_id'),
            'conditions' => array('role_id' => $manage_role_ids)
        ));
        if(empty($administrator_ids)){
            return array();
        }

        $administrators = $this->find('all', array(
            'fields' => array('Administrator.email', 'Administrator.name'),
            'conditions' => array(
                'Administrator.id' => $administrator_ids,
                'Administrator.enabled' => 1
            )
        ));

        if(empty($administrators)){
            return array();
        }
        
        $result = array(
            'emails' => array(),
            'names' => array()
        );

        foreach($administrators as $item){
            $result['emails'][] = $item['Administrator']['email'];
            $result['names'][] = $item['Administrator']['name'];
        }

        return $result;
    }
	
	public function hash_password( $string ){
		return md5(md5($string));
    }
    
    public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
            'conditions' => $conditions,
            'contain' => array(
                'Role' => array( 'fields' => array('slug', 'name') ),
                'CreatedBy',
                'UpdatedBy'
            ), 
            'order' => array( 'Administrator.id' => 'desc' ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
    }

    public function format_data_export($data, $row){
        $arr_role = array();
        foreach ($row['Role'] as $item) {
            if($item['name']){
                array_push($arr_role, $item['name']);
            }
        }
            
        return array(
            $row['Administrator']["id"],
            !empty($row['Administrator']["name"]) ?  $row['Administrator']["name"] : ' ',
            $arr_role ?  implode(', ', $arr_role) : ' ',
            !empty($row['Administrator']["email"]) ?  $row['Administrator']["email"]: ' ',
            !empty($row['Administrator']["phone"]) ?  $row['Administrator']["phone"]: ' ',
            !empty($row['Administrator']["last_logged_in"]) ?  $row['Administrator']["last_logged_in"]: ' ',
            $row['Administrator']['enabled'] == 1 ? 'Y' : 'N',
            !empty($row['Administrator']["updated"]) ?  $row['Administrator']["updated"]: ' ',
            !empty($row['UpdatedBy']["email"]) ?  $row['UpdatedBy']["email"]: ' ',
            !empty($row['Administrator']["created"]) ?  $row['Administrator']["created"]: ' ',
            !empty($row['CreatedBy']["email"]) ?  $row['CreatedBy']["email"]: ' ',
        );
    }
}
