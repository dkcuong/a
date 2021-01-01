<?php
	Router::connect('/admin/login', array(
		'plugin' => 'administration', 'controller' => 'administrators', 
		'action' => 'login', 'admin' => true
	));
?>