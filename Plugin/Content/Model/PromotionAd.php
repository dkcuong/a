<?php
App::uses('ContentAppModel', 'Content.Model');
/**
 * PromotionAd Model
 *
 */
class PromotionAd extends ContentAppModel {

/**
 * Validation rules
 *
 * @var array
 */
    public $actsAs = array('Containable');
	public $validate = array(
		'enabled' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
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
    );

    public function get_data_export($conditions, $page, $limit, $lang){
        $prefix = Environment::read('database.prefix');

        $all_settings = array(
            'fields' => array(
                'PromotionAd.*'
            ),
            'contain' => array(
                'CreatedBy',
                'UpdatedBy'
            ),
            'joins' => array(
            ),
            'conditions' => $conditions,
            'order' => array( 'PromotionAd.id' => 'desc' ),
            'limit' => $limit,
            'page' => $page
        );

        return $this->find('all', $all_settings);
    }

    public function format_data_export($data, $row){
        $model = $this->alias;
        return array(
            !empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
            !empty($row[$model]["link"]) ?  $row[$model]["link"] : ' ',
            !empty($row[$model]["description"]) ?  $row[$model]["description"] : ' ',
            $row[$model]['enabled'] == 1 ? 'Y' : 'N',
        );
    }

    public function get_list(){
        $conditions = array($this->alias.'.enabled' => true);

        $list = $this->find('all', array(
            'fields' => array(
                $this->alias.'.*'
            ),
            'conditions' => $conditions,
        ));

        $url_img = Environment::read('web.url_img');
        foreach ($list as $k => $v) {
            $list[$k][$this->alias]['image'] = $url_img . $v[$this->alias]['image'];
        }

        return $list;
    }
}
