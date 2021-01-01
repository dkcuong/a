<?php
App::uses('PosAppModel', 'Pos.Model');

class OrderDetail extends PosAppModel {

	public $actsAs = array('Containable');

	public $validate = array(
	);

	public $belongsTo = array(
		'Order' => array(
			'className' => 'Pos.Order',
			'foreignKey' => 'order_id',
			'conditions' => '',
			'order' => ''
		),
		'ScheduleDetailTicketType' => array(
			'className' => 'Movie.ScheduleDetailTicketType',
			'foreignKey' => 'schedule_detail_ticket_type_id',
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

	public $hasMany = array(
	);

    public function get_order_detail_by_conditions($condition, $language = 'eng') {
        $options = array(
            'conditions' => $condition,
            'contain' => array(
                'ScheduleDetailTicketType' => array(
                    'TicketType' => array(
                        'TicketTypeLanguage' => array(
                            'conditions' => array(
                                'TicketTypeLanguage.language' => $language
                            )
                        )
                    )
                )
            ),
            'recursive' => -1
        );

        return $this->find('all', $options);
    }
}
