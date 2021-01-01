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
		    echo $this->Html->css('fontawesome.min.css');
			echo $this->Html->css('ionicons.min.css');			
			
			/**
			 * 3rd party DateTime Picker
			 * @link http://www.malot.fr/bootstrap-datetimepicker/
			 */
			echo $this->Html->css(array(
				'datepicker/datepicker3',
				'datetimepicker/bootstrap-datetimepicker.min',
				'bootstrap-select/bootstrap-select.min',
				'google-font',
				'jquery-ui',
				'CakeAdminLTE',
				'upload',
				'fancybox/jquery.fancybox',
			));

			echo $this->Html->css('custom-style.css?v=1');
			echo $this->fetch('css');

			echo $this->Html->script(array(
				'jquery.min',
				'jquery-ui.min',
                'bootstrap.min',
                'CakeAdminLTE/common_location'
			), array(
				'block' => 'scriptTop'
			));
			echo $this->fetch('scriptTop');			
		?>
		<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js" type="text/javascript" charset="utf-8"></script>
         -->

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-135966205-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-135966205-1');
        </script>

		<script type="text/javascript" charset="utf-8">
			var cakephp = {
				base: "<?= Router::url('/'); ?>",
			}
		</script>
	</head>
	<body class="skin-blue fixed">
		<?= $this->element('menu/top_menu'); ?>
		<div class="wrapper row-offcanvas row-offcanvas-left">
			<?= $this->element('menu/left_sidebar'); ?>
		
			<!-- Right side column. Contains the navbar and content of the page -->
		    <aside class="right-side">  
		    	<section class="content-header">
				    <h1>
				        <?= $title_for_layout; ?>
				        <small><?= __('console') ?></small>
				    </h1>
				    <ol class="breadcrumb">
                        <li>
                            <?= $this->Html->link('<span>' . __('home') . '</span>', 
                                    array( 'plugin' => 'dashboard', 'controller' => 'dashboard', 'action' => 'index', 'admin' => true ), 
                                    array('escape' => false)
                                ); ?>
                        </li>
				        <li class="active"><?= $title_for_layout; ?></li>
				    </ol>
                </section> 
                <div class="content">
                    <?= $this->Session->flash(); ?>
                    <?php if (isset($is_mobile_device) && $is_mobile_device == true){ ?>
                        <h4><?= __('not_used_in_mobile') ?></h4>
                    <?php 
                        } else{	
                            echo $this->fetch('content'); 
                        }
                    ?>
                </div>		
			</aside><!-- /.right-side -->
		</div><!-- ./wrapper -->
		<?php
			echo $this->Html->script(array(
				'plugins/datepicker/bootstrap-datepicker',
				'CakeAdminLTE/moment.min',
				'plugins/datetimepicker/bootstrap-datetimepicker.min',
				'CakeAdminLTE/app',
				'ckeditor/ckeditor',
				'plugins/bootstrap-select/bootstrap-select.min',
				'plugins/fancybox/jquery.fancybox',
				'upload',
				'CakeAdminLTE/common'
			), array(
				'block' => 'scriptBottom'
			));

			echo $this->fetch('scriptBottom');
			echo $this->fetch('script');
		?>
		<script type="text/javascript">
			$('.btn-change-language').on('click', function(){
				$('<form action="<?= $this->here; ?>" method="post"><input name="set_new_language" value="' + $(this).data('lang') + '"/></form>')
					.appendTo('body').submit();
			});
		</script>
	</body>
</html>
<?php //echo $this->element('sidebar_select'); ?>
