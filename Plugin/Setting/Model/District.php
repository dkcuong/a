<?php
App::uses('SettingAppModel', 'Setting.Model');
/**
 * Setting Model
 *
 */
class District extends SettingAppModel {
    public $actsAs = array('Containable');

	public $validate = array(
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
	);
	
	public $hasMany = array(
		'DistrictLanguage' => array(
			'className' => 'Setting.DistrictLanguage',
			'foreignKey' => 'district_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);

	public function get_active_districts($lang) {
		$option = array(
			'fields' => array(
				'District.*',
				'DistrictLanguage.name'
			),
			'joins' => array(
				array(
					'alias' => 'DistrictLanguage',
					'table' => Environment::read('table_prefix') . 'district_languages',
					'type' => 'left',
					'conditions' => array(
						'DistrictLanguage.district_id = District.id',
						'DistrictLanguage.language' => $lang,
					),
				),
			),
			'conditions' => array(
				'enabled' => 1
			)
		);

		return $this->find('all', $option);
	}

	public function get_active_district_list($lang) {
		$option = array(
			'fields' => array(
				'District.id',
				'DistrictLanguage.name'
			),
			'joins' => array(
				array(
					'alias' => 'DistrictLanguage',
					'table' => Environment::read('table_prefix') . 'district_languages',
					'type' => 'left',
					'conditions' => array(
						'DistrictLanguage.district_id = District.id',
						'DistrictLanguage.language' => $lang,
					),
				),
			),
			'conditions' => array(
				'enabled' => 1
			)
		);

		return $this->find('list', $option);
	}
}
