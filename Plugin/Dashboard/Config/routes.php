<?php
    Router::connect('/admin/dashboard', array(
        'plugin' => 'dashboard', 'controller' => 'dashboard', 'action' => 'index', 'admin' => true
    ));
?>