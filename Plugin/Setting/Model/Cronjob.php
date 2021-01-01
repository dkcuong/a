<?php
App::uses('SettingAppModel', 'Setting.Model');

class Cronjob extends SettingAppModel {
	public $actsAs = array('Containable');
}
