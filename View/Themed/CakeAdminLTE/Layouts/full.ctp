<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<?= $this->Html->docType('html5'); ?> 
<html>
	<head>
		<?= $this->Html->charset(); ?>

		<title>
			<?= Environment::read('site.name'); ?>:
			<?= $title_for_layout; ?>
		</title>

		<meta name="keywords" content="<?= Environment::read('site.keywords'); ?>">
		<meta name="description" content="<?= Environment::read('site.description'); ?>">

		<?php 
			echo $this->Html->meta('icon');
			echo $this->Html->meta(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no']);
			echo $this->fetch('meta');

			echo $this->Html->css('bootstrap.min.css');
			echo $this->Html->css('ionicons.min.css');
			echo $this->Html->css('google-font.css');
			
			/**
			 * 3rd party DateTime Picker
			 * @link http://www.malot.fr/bootstrap-datetimepicker/
			 */
			// echo $this->Html->css('bootstrap-datetimepicker.min');
			
			echo $this->Html->css('CakeAdminLTE');

			/**
			 * Custom css
			 */

			echo $this->fetch('css');

			echo $this->Html->script(array(
				'jquery.min',
				// 'jquery-ui.min',
				'bootstrap.min',
			), array(
				'block' => 'scriptTop'
			));

			echo $this->fetch('scriptTop');
			
		?>


	</head>

	<body class="skin-blue">
		<div class="wrapper row-offcanvas row-offcanvas-left">
			<section class="content"> 
				<?= $this->Session->flash(); ?>

				<?= $this->fetch('content'); ?>
			</section>
		</div><!-- ./wrapper -->

		<?php
			/**
			 * 3rd party DateTime Picker
			 * @link http://www.malot.fr/bootstrap-datetimepicker/
			 */
			echo $this->Html->script(array(
				// 'bootstrap-datetimepicker.min',
				'CakeAdminLTE/app'
			), array(
				'block' => 'scriptBottom'
			));

			echo $this->fetch('scriptBottom');
		?>
		<script type="text/javascript" charset="utf-8" defer>
			// $(document).ready(function(){

			// });
		</script>
	</body>
</html>