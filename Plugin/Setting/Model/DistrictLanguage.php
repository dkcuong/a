<?php
App::uses('SettingAppModel', 'Setting.Model');
/**
 * Setting Model
 *
 */
class DistrictLanguage extends SettingAppModel {
    public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'District' => array(
			'className' => 'Setting.District',
			'foreignKey' => 'district_id',
			'conditions' => '',
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
}
