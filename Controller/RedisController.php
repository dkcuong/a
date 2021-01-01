<?php
/**
 * Static content controller.
 *
 * This file only handle ajax request to save data into redis cache
 *
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link https://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class RedisController extends AppController {

    /**
     * This controller does not use a model
     *
     * @var array
     */
	public $uses = array();

    /**
     * Displays a view
     *
     * @return void
     */
	public function admin_update_column_cache() {
        $result = array(
            "status" => false,
            "msg" => ""
        );

        if ($this->request->is('post')) {
            $data = $this->request->data;
            
            if ($this->Redis->set_cache('booster_column', $data["module_name"], $data['visible_col'], 1000)) {
                $result["status"] = true;
                $result["msg"] = "cache is saved";
            }
        }      

        echo(json_encode($result));

        $this->autoRender = false;
	}

}
