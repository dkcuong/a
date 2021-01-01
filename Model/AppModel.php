<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
    public $recursive = -1;
	public $saved_ids = array(
		'insert' => array(
			'count' => 0, 'id' => array()
		),
		'update' => array(
			'count' => 0, 'id' => array()
		)
    );
    
	public $version_status = array(
        0 => 'Pending',
        1 => 'Approved',
        2 => 'Had Approved',
        3 => 'Cancel',
        4 => 'Reject',
    );
    

	public $status = array(
        0 => 'Pending',
        1 => 'Enabled',
        2 => 'Disabeld',
	);

	/**
	 * beforeSave Callback for all Models
	 */
	function beforeSave( $options = array() ){
		if( $this->hasField('updated') ){
			$this->data[$this->alias]['updated'] = date("Y-m-d H:i:s");
		}

		if( $this->hasField('updated_by') ){
			$this->data[$this->alias]['updated_by'] = CakeSession::read('Administrator.id');
		}

		// only save the 'created' time stamp when there is NO id given
		if( !isset($this->data[$this->alias]['id']) || empty($this->data[$this->alias]['id']) ){
			if( $this->hasField('created') ){
				$this->data[$this->alias]['created'] = date("Y-m-d H:i:s");
			}

			if( $this->hasField('created_by') ){
				$this->data[$this->alias]['created_by'] = CakeSession::read('Administrator.id');
			}
		}
	}

	/**
	 * afterSave Callback for all Models
	 * count the number of records that have been inserted / updated
	 * and return the affected IDs accordingly
	 */
	function afterSave( $created, $options = array() ){
		if( $created ){
			$this->saved_ids['insert']['count']++;
			$this->saved_ids['insert']['id'][] = $this->getID();
		} else {
			$this->saved_ids['update']['count']++;
			$this->saved_ids['update']['id'][] = $this->getID();
		}

		return $this->saved_ids;
	}

	/**
	 * provide a public function to retrieve the metrics
	 */
	public function getSaveMetrics(){
		return $this->saved_ids;
	}
	
	public function generateToken(){
		// Create a token
		$token = $_SERVER['HTTP_HOST'];
		$token .= $_SERVER['REQUEST_URI'];
		$token .= uniqid(rand(), true);
		
		// GUID is 128-bit hex
		$hash = strtoupper(md5($token));
		
		// Create formatted GUID
		$guid = '';
		
		// GUID format is XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX for readability    
		$guid .= substr($hash,  0,  8) . 
			'-' .
			substr($hash,  8,  4) .
			'-' .
			substr($hash, 12,  4) .
			'-' .
			substr($hash, 16,  4) .
			'-' .
			substr($hash, 20, 12);
				
		return $guid;
	}

	public function find_one($id){
		return $this->find('first', array(
			'conditions' => array(
				$this->primaryKey => $id,
			)
		));
	}

	public function find_active_one($id){
		return $this->find('first', array(
			'conditions' => array(
				$this->primaryKey => $id,
				'status' => true,
			)
		));
    }
    
    protected function format_money($value){
        return number_format($value, 2, '.', ',');
    }

    public function generate_code(){
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($permitted_chars), 0, 10);
    }

    protected function random_color_part() {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }
    
    public function get_random_color() {
        return '#' . $this->random_color_part() . $this->random_color_part() . $this->random_color_part();
	}
}
