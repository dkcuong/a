<?php
App::uses('PosAppModel', 'Pos.Model');

class Item extends PosAppModel {

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
		'ItemGroup' => array(
			'className' => 'Pos.ItemGroup',
			'foreignKey' => 'item_group_id',
			'conditions' => '',
			'order' => ''
		),
	);

	public $hasMany = array(
		'PurchaseDetail' => array(
			'className' => 'Pos.PurchaseDetail',
			'foreignKey' => 'item_id',
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
		'ItemLanguage' => array(
			'className' => 'Pos.ItemLanguage',
			'foreignKey' => 'item_id',
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

	public function get_data_export($conditions, $page, $limit, $lang){
		$prefix = Environment::read('database.prefix');
		$this->query('drop temporary table if exists ' . $prefix . 'tmp_names');
		$strsql = "set @sql = (
			select group_concat(distinct 
				concat(
					\"max(case when `language`='\", language, \"' then `name` end) as `\", 'name_',`language`, \"`\"
				)
			) 
			from " . $prefix . "item_languages
		)";
		$this->query($strsql);

		$strsql = "set @sql = concat('create temporary table " . $prefix . "tmp_names (select item_id, ', coalesce(@sql, '1'), ' from " . $prefix . "item_languages  group by `item_id`)')";
		$this->query($strsql);

		$strsql = "prepare stmt from @sql";
		$this->query($strsql);

		$strsql = "execute stmt";
		$this->query($strsql);

		$strsql = "deallocate prepare stmt";
		$this->query($strsql);

        $all_settings = array(
			'fields' => array(
				'Item.*',
				'TmpName.*',
                'ItemGroup.*'
			),
			'contain' => array(
                'CreatedBy',
                'UpdatedBy',
                'ItemGroup'
			),
			'joins' => array(
				array(
					'alias' => 'TmpName',
					'table' => Environment::read('table_prefix') . 'tmp_names',
					'type' => 'left',
					'conditions' => array(
						'Item.id = TmpName.item_id',
					),
				),
			),
            'conditions' => $conditions,
            'order' => array( 'Item.code' => 'asc' ),
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
			!empty($row[$model]["price"]) ?  $row[$model]["price"] : ' ',
			!empty($row[$model]["availability"]) ?  $row[$model]["availability"] : ' ',
			!empty($row['ItemGroup']["code"]) ?  $row['ItemGroup']["code"] : ' ',
			!empty($row[$model]["material"]) ?  $row[$model]["material"] : ' ',
        );
	}
	
	public function check_items($item_ids) {
		$option = array(
			'conditions' => array(
				'id IN' => $item_ids,
				'enabled' => 0
			)
		);

		$disabled_items = $this->find('count', $option);

		return (($disabled_items > 0) ? false : true);
	}

	public function get_data_item_by_id($item_ids) {
		$option = array(
			'conditions' => array(
				'id IN' => $item_ids,
				'enabled' => 1
			)
		);

		return $this->find('all', $option);
	}

    public function get_list_item_booking_enquiry($data = array()){
	    $conditions = array();
        $conditions[] = array('Item.enabled' => true);
        $conditions[] = array('Item.sold_in_house_booking' => true);
        $result = array();

        if (isset($data['is_api']) && $data['is_api'] == true) {
            $result_temp = $this->find('all', array(
                'fields' => array('id', 'code'),
                'conditions' => $conditions,
            ));

            foreach ($result_temp as $k => $v) {
                $result[$k] = $v['Item'];
            }
        } else {
            $result = $this->find('list', array(
                'fields' => array('id', 'code'),
                'conditions' => $conditions,
            ));
        }
        return $result;
    }

    public function get_list($lang) {
        $option = array(
            'conditions' => array(
                'enabled' => 1
            ),
            'contain' => array(
              'ItemLanguage' => array()
            ),
            'order' => array(
                'id' => 'ASC'
            )
        );

        return $this->find('all', $option);
    }
}
