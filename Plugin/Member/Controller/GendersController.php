<?php
App::uses('MemberAppController', 'Member.Controller');
/**
 * Genders Controller
 */
class GendersController extends MemberAppController {

/**
 * Scaffold
 *
 * @var mixed
 */
	public $scaffold;

	private $api_rules = array(
		// 'token' => array(
		// 	'required' => true,
		// 	'type' => 'string',
		// ),
		// 'language' => array(
		// 	'required' => true,
		// 	'type' => 'string',
		// 	'in' => ['zho', 'eng', 'chi'],
		// ),
	);

	public function api_get_list(){
        $this->Api->init_result();

        if ($this->request->is('post')) {
            $this->disableCache();

            $data = $this->request->data;

            $url_params = $this->request->params;
			$this->Api->set_post_params($url_params, $data);
            $this->Api->set_save_log(true);

            $status = false;
            $result_data = array();
            $message = "";

            // $valid = $this->Api->validate_data($data, $this->api_rules);
			// if ( !$valid ) {
			// 	goto return_api;
			// }

			$this->Api->set_language($this->lang18);    
			$language = $this->Api->get_language();

            $result_data = $this->Gender->get_list_api($language);

            $status = true;
            $message =  __('retrieve_data_successfully');
    
			$this->Api->set_result($status, $message, $result_data);

        }

        return_api :
		$this->Api->output();
    }
}
