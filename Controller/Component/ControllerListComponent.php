<?php
	/**
	 * A simple CakePHP component that returns a list of controllers.
	 *
	 * Copyright (c) by Daniel Hofstetter (daniel.hofstetter@42dh.com, http://cakebaker.42dh.com)
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
	 */
	class ControllerListComponent extends Component {
		public $parentControllers = array('AppController');

		/**
		 * 2016-11-25
		 * 
		 * @author 				Ricky Lam @ VTL
		 * @modification 		only include specific plugins
		 * @default 			(boolean)
		 * @input 				(array)
		 */
		public $includePlugins = false;

		public function getList(Array $controllersToExclude = array('PagesController')) {
			foreach($this->parentControllers as $parentController) {
				$controllersToExclude[] = $parentController;
			}
			$results = array();
			$results += $this->getControllers($controllersToExclude);

			if ($this->includePlugins) {
				// if includePlugins is given as array from initialization
				if( is_array($this->includePlugins) ){
					// remove all duplicates
					$this->includePlugins = array_unique( $this->includePlugins );
				}

				foreach (CakePlugin::loaded() as $plugin) {
					// only include those mentioned specific plugins 
					if( in_array($plugin, $this->includePlugins) ){
						$controllersToExclude[] = $plugin . 'AppController';
						App::uses($plugin . 'AppController', $plugin . '.Controller');
						$results += $this->getControllers($controllersToExclude, $plugin);
					}
				}
			}
			return $results;
		}

		/**
		 *	Custom function
		 *  2016-11-25
		 * 
		 * @author 				Ricky Lam @ VTL
		 * @prefixes 			(array) Routing.prefixes in core.php
		 * @inlcude_actions 	(array) Only include these actions in the menu
		 * @current 			(array) Routing array of current action, to make the menu item active (CSS)
		 */
		public function composeMenu( $prefixes = array(), $inlcude_actions = array(), $current = array() ){
			$controller_actions = $this->getList();
			
			// resultant menu
			$menu = array();

			$has_prefix = false;

			// form the available actions that should display in the menu
			$available_actions = $inlcude_actions;
			if( !empty($prefixes) ){
				foreach ($prefixes as $prefix) {
					foreach ($inlcude_actions as $action) {
						$available_actions[] = $prefix . "_" . $action;
					}
				}

				$has_prefix = true;
			}

			foreach ($controller_actions as $action) {
				// Get the Plugin name and inflected name for routing
				$_plugin = $action['plugin'];
				$_plugin_inflected = Inflector::underscore( $_plugin );

				// Get the Controller name and inflected name for routing
				$_controller = $action['name'];
				$_controller_inflected = Inflector::underscore( $_controller );

				// Get the display name of Controller
				$_display_name = $action['displayName'];

				// by default, set the plugin status as "not active"
				$plugin_active = $controller_active = false;

				// compare the current plugin and controller, to mark the status for menu item
				if( (mb_strtolower( $_plugin ) == $current['plugin']) && 
					(mb_strtolower( $_controller ) == $current['controller']) ){
					$controller_active = true;
					$plugin_active = true;
				}

				// set a flag for indicating Plugin status
				if( isset($menu[ $_plugin_inflected ]['active']) && ($menu[ $_plugin_inflected ]['active'] == fasle) ){
					$menu[ $_plugin_inflected ]['active'] = $plugin_active;
				}

				// set the menu object for plugins and their controllers
				$menu[ $_plugin_inflected ][ $_controller_inflected ] = array(
					'active' => $controller_active,
					'display_name' => $_display_name,
					'routes' => array()
				);

				// form the proper routing arrays
				foreach ($available_actions as $available_action) {
					if( in_array($available_action, $action['actions']) ){
						// mark status active if it matches with current action
						$action_active = false;
						if( (mb_strtolower( $_plugin ) == $current['plugin']) && 
							(mb_strtolower( $_controller ) == $current['controller']) && 
							(mb_strtolower( $available_action ) == $current['action']) ){
							$action_active = true;
						}
						
						$menu[ $_plugin_inflected ][ $_controller_inflected ]['routes'][] = array(
							'admin' => $has_prefix,
							'plugin' => $_plugin_inflected,
							'controller' => $_controller_inflected,
							'action' => $available_action,
							'active' => $action_active,
						);
					}
				}
			}

			// return the resultant menu array
			return $menu;
		}

		private function getControllers($controllersToExclude, $plugin = '') {
			$pluginPrefix = $plugin == '' ? '' : $plugin . '.';
			$package = $pluginPrefix . 'Controller';
			$controllerClasses = array_filter(App::objects($package), function ($controller) use ($controllersToExclude) {
				return !in_array($controller, $controllersToExclude);
			});
			$result = array();
			foreach($controllerClasses as $controller) {
				$controllerName = str_replace('Controller', '', $controller);

				/**
				 * 2016-11-25
				 * 
				 * @author 				Ricky Lam @ VTL
				 * @modification 		Add plugin name in the result
				 */
				$result[$pluginPrefix . $controller]['plugin'] = $plugin;

				$result[$pluginPrefix . $controller]['name'] = $controllerName;
				$result[$pluginPrefix . $controller]['displayName'] = Inflector::humanize(Inflector::underscore($controllerName));
				$result[$pluginPrefix . $controller]['actions'] = $this->getActions($controller, $package);
			}
			return $result;
		}

		private function getActions($controller, $package) {
			App::uses($controller, $package);
			$methods = get_class_methods($controller);
			$methods = $this->removeParentMethods($controller, $methods);
			$methods = $this->removePrivateActions($methods);
			return $methods;
		}

		private function removeParentMethods($controller, Array $methods) {
			foreach($this->parentControllers as $parentController) {
				if (is_subclass_of($controller, $parentController)) {
					$parentMethods = get_class_methods($parentController);
					$methods = array_diff($methods, $parentMethods);
				}
			}
			return $methods;
		}

		private function removePrivateActions(Array $methods) {
			return array_filter($methods, function ($method) {
				return $method{0} != '_';
			});
		}
	}
?>