<?php
App::uses('MemberAppModel', 'Member.Model');
/**
 * AgeGroup Model
 *
 * @property Member $Member
 */
class AgeGroup extends MemberAppModel {

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
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'created_by' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Member' => array(
			'className' => 'Member',
			'foreignKey' => 'age_group_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'AgeGroupLanguage' => array(
			'className' => 'Member.AgeGroupLanguage',
			'foreignKey' => 'age_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public function get_list_api($language){
        $list_item = $this->find('all', array(
            'fields' => array('id', 'slug', 'from', 'to'),
            'conditions' => array('enabled' => true),
            'contain' => array(
                'AgeGroupLanguage' => array(
                    'fields' => array('name'),
                    'conditions' => array(
                        'language' => $language
                    )
                ),
            )
        ));

        $result = array();
		if ($list_item) {
            foreach($list_item as $item){
                $main_info = $item['AgeGroup'];
                if(reset($item['AgeGroupLanguage'])){
                    $main_info['name'] = reset($item['AgeGroupLanguage'])['name'];
                }
                array_push($result, $main_info);
            }
		}

		return $result;
	}
	
	public function get_list_active_age_group($language){
        $list_item = $this->find('list', array(
			'fields' => array('AgeGroup.id', 'AgeGroupLanguage.name'),
			'joins' => array(
                array(
                    'alias' => 'AgeGroupLanguage',
                    'table' => Environment::read('table_prefix') . 'age_group_languages',
                    'type' => 'left',
                    'conditions' => array(
						'AgeGroupLanguage.age_id = AgeGroup.id',
						'AgeGroupLanguage.language' => $language
                    ),
                ),
            ),
            'conditions' => array('enabled' => true),
        ));

		return $list_item;
    }	
}
