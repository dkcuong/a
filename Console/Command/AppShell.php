<?php
/**
 * AppShell file
 *
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 */

App::uses('Shell', 'Console');
App::uses('ComponentCollection', 'Controller');

date_default_timezone_set('Asia/Hong_Kong');

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell {
    protected $arr_langs = array('eng', 'chi', 'zho');
    protected $arr_enviroments = array('development', 'production', 'staging', 'dev-vn');
    protected $lang = 'eng';
    protected $log_module;

    public function startup() {
        $collection = new ComponentCollection();
        $this->Session = $collection->load('Session');
    }

    protected function set_enviroment_language(){
        // temporarily using the "development" DB config
        Environment::set('development'); // developmet/production
        
        if( isset($this->args[0]) && !empty($this->args[0]) ) {
            if(!in_array($this->args[0], $this->arr_enviroments)){
                CakeLog::write($this->log_module, "Parameter enviroment must be development, production or staging.\r\n");
                return false;
            }

            Environment::set( $this->args[0] );
        }

        if (isset($this->args[1]) && !empty($this->args[1])) {
            $this->lang = $this->args[1];
            if(!in_array($this->args[1], $this->arr_langs)){
                CakeLog::write($this->log_module, "Parameter language must be zho, chi or eng.\r\n");
                return false;
            }
        }
        
        $this->Session->write('Config.language', $this->lang);
        return true;
    }

    protected function reset_point(&$success_member_ids, &$error_member_ids){
        $expired_member_points = $this->MemberPoint->get_point_expired();

        if(!$expired_member_points){
            CakeLog::write($this->log_module, "There is not any member that have expiry point. \r\n");
            return;
        }

        // if the number point is more than 0 add record to ballance it.
        foreach($expired_member_points as $item){
            $db = $this->MemberPoint->getDataSource();
            $db->begin();

            // Add record to member_point
            $member_point = array(
                'member_id' => $item['member_id'],
                'point_type_id' => $this->MemberPoint->const_point_types['reduct_expiry'],
                'expiry_date' => date('Y-m-d H:i:s', strtotime($item['expiry_date'])),
                'points' => 0 - (double)$item['total'],
                'enabled' => true,
                'remark' => 'reduct expired point: ' . $item['total'],
            );

            if(!$this->MemberPoint->save($member_point)){
                array_push($error_member_ids, $item['member_id']);
                CakeLog::write($this->log_module, "Member Id [" . $item['member_id'] . "] - Expire date [" . $item['expiry_date'] . "] add minus point FAILED (please see above errors) \r\n");
                CakeLog::write($this->log_module, json_encode($this->MemberPoint->invalidFields()) . " \r\n");
                continue;
            }

            // update all member point with same expired date and total of them is 0.
            if(!$this->MemberPoint->updateAll(
                array(  
                    'MemberPoint.is_deleted' => true, 
                    'MemberPoint.updated' => "'" . date('Y-m-d h:i:s') . "'",
                    'MemberPoint.updated_by' => 0
                ),
                array(
                    'MemberPoint.member_id' => $item['member_id'],
                    'MemberPoint.expiry_date' => $item['expiry_date'],
                )
            )) {
                array_push($error_member_ids, $item['member_id']);
                CakeLog::write($this->log_module, 'Member Id [' . $item['member_id'] . '] - Expire date [' . $item['expiry_date'] . '] change status expired point to deleted is FAILED (please see above errors) \r\n');
                CakeLog::write($this->log_module, json_encode($this->MemberPoint->invalidFields()) . " \r\n");
                continue;
            }

            $member = $this->Member->get_member_by_id($item['member_id']);

            $member['points'] = (double)$member['points'] + (double)$item['total'];
            // Update to member record
            if (!$this->Member->save($member)) {
                array_push($error_member_ids, $item['member_id']);
                CakeLog::write($this->log_module, "Member Id [" . $item['member_id'] . "] - Expire date [" . $item['expiry_date'] . "] add minus point FAILED (please see above errors) \r\n");
                CakeLog::write($this->log_module, json_encode($this->Member->invalidFields()) . " \r\n");
                continue;
            }

            $db->commit();
            array_push($success_member_ids, $item['member_id']);
            $this->MemberPoint->clear();
            $this->Member->clear();
        }
    }
}
