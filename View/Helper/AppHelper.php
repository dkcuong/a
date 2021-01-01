<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {
    public function statusLabel($status)
    {
        $label_type = '';
        switch($status){
            case 1:
                $label_type = 'label-success';
                break;
            case 2:
                $label_type = 'label-info';
                break;
            case 3:
                $label_type = 'label-warning';
                break;
            case 4:
                $label_type = 'label-danger';
                break;
            case 0:
            default:
                $label_type = 'label-default';
                break;
        }
        return $label_type;
    }
}
