<?php
App::uses('MemberAppModel', 'Member.Model');
/**
 * ContactRequest Model
 *
 */
class ContactRequest extends MemberAppModel {
    public $actsAs = array('Containable');
/**
 * Use table
 *
 * @var mixed False or table name
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

    public function get_data_export($conditions, $page, $limit, $lang){
        $all_settings = array(
            'fields' => array(
                'ContactRequest.*'
            ),
            'contain' => array(
//                'CreatedBy',
//                'UpdatedBy'
            ),
            'conditions' => $conditions,
            'order' => array( 'ContactRequest.id' => 'desc' ),
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
            !empty($row[$model]["message"]) ?  $row[$model]["message"] : ' ',
        );
    }

    public function create_contact_request($data) {
        $status = false;
        $message = "";
        $params = (object)array();

        $data_insert = array();

        $data_insert['ContactRequest']['title'] = isset($data['title']) ? $data['title'] : null;
        $data_insert['ContactRequest']['name'] = isset($data['name']) ? $data['name'] : null;
        $data_insert['ContactRequest']['email'] = isset($data['email']) ? $data['email'] : null;
        $data_insert['ContactRequest']['country_code'] = isset($data['country_code']) ? $data['country_code'] : null;
        $data_insert['ContactRequest']['phone'] = isset($data['phone']) ? $data['phone'] : null;
        $data_insert['ContactRequest']['message'] = isset($data['message']) ? $data['message'] : null;


        $dbo = $this->getDataSource();
        $dbo->begin();
        try {
            if ($this->saveAll($data_insert)) {
                $params = $data_insert;
                $dbo->commit();
                $status = true;
                $message = __('thank_contact_request');
            } else {
                $dbo->rollback();
                $status = false;
                $message = __('create_contact_request_failed');

                goto return_result;
            }
        } catch (Exception $e) {
            $dbo->rollback();
            $status = false;
            $message = __('create_contact_request_failed');

            goto return_result;
        }

        return_result :

        return array('status' => $status, 'message' => $message, 'params' => $params);

    }
}
