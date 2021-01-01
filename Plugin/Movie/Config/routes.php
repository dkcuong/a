<?php
	/**
	 * Cinema
	 */

	Router::connect('/:api/movie/myapi', array(
		'plugin' => 'movie', 'controller' => 'movies', 
		'action' => 'myapi', 'api' => true,
	));
?>