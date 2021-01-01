<?php
App::uses('PosAppModel', 'Pos.Model');

class ItemGroup extends PosAppModel {

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
		'ItemGroupLanguage' => array(
			'className' => 'Pos.ItemGroupLanguage',
			'foreignKey' => 'item_group_id',
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
		'Item' => array(
			'className' => 'Pos.Item',
			'foreignKey' => 'item_group_id',
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

	public function get_list_item_groups(){

		$itemGroups = $this->find('list', array(
            'fields' => array('id', 'code'),
		));
		
		return $itemGroups;
	}

	public function get_data_export($conditions, $page, $limit, $lang){
		$prefix = Environment::read('database.prefix');
		$this->query('drop temporary table if exists ' . $prefix . 'tmp_names');
		$strsql = "set @sql = (
			select group_concat(distinct 
				concat(
					\"max(case when `language`='\", language, \"' then `name` end) as `\", 'name_',`language`, \"`\"
				)
			) 
			from " . $prefix . "item_group_languages
		)";
		$this->query($strsql);

		$strsql = "set @sql = concat('create temporary table " . $prefix . "tmp_names (select item_group_id, ', coalesce(@sql, '1'), ' from " . $prefix . "item_group_languages  group by `item_group_id`)')";
		$this->query($strsql);

		$strsql = "prepare stmt from @sql";
		$this->query($strsql);

		$strsql = "execute stmt";
		$this->query($strsql);

		$strsql = "deallocate prepare stmt";
		$this->query($strsql);

        $all_settings = array(
			'fields' => array(
				'ItemGroup.*',
				'TmpName.*'
			),
			'contain' => array(
                'CreatedBy',
                'UpdatedBy'
			),
			'joins' => array(
				array(
					'alias' => 'TmpName',
					'table' => Environment::read('table_prefix') . 'tmp_names',
					'type' => 'left',
					'conditions' => array(
						'ItemGroup.id = TmpName.item_group_id',
					),
				),
			),
            'conditions' => $conditions,
            'order' => array( 'ItemGroup.code' => 'asc' ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
	}
	
	public function format_data_export($data, $row){
		$model = $this->alias;
        return array(
			!empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
			!empty($row[$model]["code"]) ?  $row[$model]["code"] : ' ',
			!empty($row['TmpName']["name_zho"]) ?  $row['TmpName']["name_zho"] : ' ',
			!empty($row['TmpName']["name_eng"]) ?  $row['TmpName']["name_eng"] : ' ',
            $row[$model]['enabled'] == 1 ? 'Y' : 'N',
        );
    }

}
