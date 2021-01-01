<?php
App::uses('CinemaAppModel', 'Cinema.Model');
/**
 * BookingEnquiry Model
 *
 * @property Hall $Hall
 * @property Equipment $Equipment
 * @property Item $Item
 */
class BookingEnquiry extends CinemaAppModel {

    public $actsAs = array('Containable');
	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Hall' => array(
			'className' => 'Cinema.Hall',
			'foreignKey' => 'hall_id',
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
        )
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Equipment' => array(
			'className' => 'Cinema.Equipment',
			'joinTable' => 'booking_enquiries_equipments',
			'foreignKey' => 'booking_enquiry_id',
			'associationForeignKey' => 'equipment_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		),
		'Item' => array(
			'className' => 'Cinema.Item',
			'joinTable' => 'booking_enquiries_items',
			'foreignKey' => 'booking_enquiry_id',
			'associationForeignKey' => 'item_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);

    public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
            'fields' => array(
                'BookingEnquiry.*'
            ),
            'contain' => array(
//                'CreatedBy',
//                'UpdatedBy'
                'Equipment',
                'Item',
                'Hall' => array(
                    'fields' => 'Hall.*'
                )
            ),
            'conditions' => $conditions,
            'order' => array( 'BookingEnquiry.id' => 'desc' ),
            'limit' => $limit,
            'page' => $page,
            'recursive' => -1
        );

        return $this->find('all', $all_settings);
    }

    public function format_data_export($data, $row){
        $model = $this->alias;

        return array(
            !empty($row[$model]["id"]) ?  $row[$model]["id"] : ' ',
            !empty($row[$model]["title"]) ?  __($row[$model]["title"]) : ' ',
            !empty($row[$model]["name"]) ?  $row[$model]["name"] : ' ',
            !empty($row[$model]["email"]) ?  $row[$model]["email"] : ' ',
            !empty($row[$model]["country_code"]) ?  $row[$model]["country_code"] : ' ',
            !empty($row[$model]["phone"]) ?  $row[$model]["phone"] : ' ',
            !empty($row[$model]["date"]) ?  date ('Y-m-d', strtotime($row[$model]["date"])) : ' ',
            !empty($row[$model]["time_from"]) ?  date ('H:i', strtotime($row[$model]["time_from"])) : ' ',
            !empty($row[$model]["time_to"]) ?  date ('H:i', strtotime($row[$model]["time_to"])) : ' ',
            !empty($row[$model]["event_purpose"]) ?  $row[$model]["event_purpose"] : ' ',
            !empty($row[$model]["movie_name"]) ?  $row[$model]["movie_name"] : ' ',
            !empty($row[$model]["no_of_attendee"]) ?  $row[$model]["no_of_attendee"] : ' ',
            !empty($row['Hall']['code']) ?  $row['Hall']['code'] : ' ',
            !empty($row[$model]["special_request"]) ?  $row[$model]["special_request"] : ' ',
            !empty($row["Equipment"]) ?  implode(', ', Hash::extract( $row["Equipment"], "{n}.code" )) : ' ',
            !empty($row["Item"]) ?  implode(', ', Hash::extract( $row["Item"], "{n}.code" )) : ' ',
        );
    }

    public function create_booking_enquiry($data) {
        $status = false;
        $message = "";
        $params = (object)array();

        $booking_enquiry = array();

        $booking_enquiry['BookingEnquiry']['title'] = isset($data['title']) ? $data['title'] : null;
        $booking_enquiry['BookingEnquiry']['name'] = isset($data['name']) ? $data['name'] : null;
        $booking_enquiry['BookingEnquiry']['email'] = isset($data['email']) ? $data['email'] : null;
        $booking_enquiry['BookingEnquiry']['country_code'] = isset($data['country_code']) ? $data['country_code'] : null;
        $booking_enquiry['BookingEnquiry']['phone'] = isset($data['phone']) ? $data['phone'] : null;
        $booking_enquiry['BookingEnquiry']['date'] = isset($data['date']) ? $data['date'] : null;
        $booking_enquiry['BookingEnquiry']['time_from'] = isset($data['time_from']) ? $data['time_from'] : null;
        $booking_enquiry['BookingEnquiry']['time_to'] = isset($data['time_to']) ? $data['time_to'] : null;
        $booking_enquiry['BookingEnquiry']['event_purpose'] = isset($data['event_purpose']) ? $data['event_purpose'] : null;
        $booking_enquiry['BookingEnquiry']['movie_name'] = isset($data['movie_name']) ? $data['movie_name'] : null;
        $booking_enquiry['BookingEnquiry']['no_of_attendee'] = isset($data['no_of_attendee']) ? $data['no_of_attendee'] : null;
        $booking_enquiry['BookingEnquiry']['hall_id'] = isset($data['hall_id']) ? $data['hall_id'] : null;
        $booking_enquiry['BookingEnquiry']['special_request'] = isset($data['special_request']) ? $data['special_request'] : null;

        if (isset($data['equipment_list']) && !empty($data['equipment_list'])) {
            $booking_enquiry['Equipment'] = $data['equipment_list'];
        }

        if (isset($data['item_list']) && !empty($data['item_list'])) {
            $booking_enquiry['Item'] = $data['item_list'];
        }

        if (isset($data['is_subscribe']) && !empty($data['is_subscribe'])) {
            $booking_enquiry['BookingEnquiry']['is_subscribe'] = $data['is_subscribe'];
        }

        if (isset($data['is_accept_term']) && !empty($data['is_accept_term'])) {
            $booking_enquiry['BookingEnquiry']['is_accept_term'] = $data['is_accept_term'];
        }

        $dbo = $this->getDataSource();
        $dbo->begin();
        try {
            if ($this->saveAll($booking_enquiry)) {
                $params = $booking_enquiry;
                $dbo->commit();
                $status = true;
                $message = __('create_booking_enquiry_succesfully');
            } else {
                $dbo->rollback();
                $status = false;
                $message = __('create_booking_enquiry_failed');

                goto return_result;
            }
        } catch (Exception $e) {
            $dbo->rollback();
            $status = false;
            $message = __('data_is_not_saved');

            goto return_result;
        }

        return_result :

        return array('status' => $status, 'message' => $message, 'params' => $params);

    }
}

