<?php
App::uses('SettingAppModel', 'Setting.Model');
/**
 * Setting Model
 *
 */
class Setting extends SettingAppModel {
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

	public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
			'fields' => array(
				'Setting.*'
			),
			'contain' => array(
                'CreatedBy',
                'UpdatedBy'
			),
            'conditions' => $conditions,
            'order' => array( 'Setting.value' => 'asc' ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
	}
	
	public function format_data_export($data, $row){
		$model = $this->alias;
        return array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row[$model]["slug"]) ?  $row[$model]["slug"] : ' ',
			!empty($row[$model]["value"]) ?  $row[$model]["value"] : ' ',
			!empty($row[$model]["description"]) ?  $row[$model]["description"] : ' ',
            $row[$model]['enabled'] == 1 ? 'Y' : 'N',
        );
	}
	
	public function get_value($slug) {
		$option = array(
			'conditions' => array(
				'slug' => $slug,
				'enabled' => 1
			)
		);

		$result = $this->find('first', $option);
		return ((isset($result['Setting']['id']) && !empty($result['Setting']['id'])) ? $result['Setting']['value'] : 0);
	}
}
