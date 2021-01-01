<?php
    header("Cache-Control: no-store, must-revalidate, max-age=0");
    header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>
			<?= $title_for_layout; ?>
		</title>

		<?= $this->Html->charset(); ?>
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"> -->
		
		<?php 
			echo $this->Html->meta('icon');
			echo $this->Html->meta(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no']);
			echo $this->fetch('meta');
        ?>
        
        <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap" rel="stylesheet">

        <?php 
        	echo $this->Html->css(array(
                'reset',
                'fontawesome.min',
                'ionicons.min',
                'datepicker/datepicker3',
                'datetimepicker/bootstrap-datetimepicker.min',
                'bootstrap.min',
                'font-awesome.min',
                'jquery-ui',
                '../js/swiper/css/swiper.min'
			));
			echo $this->fetch('css');

            echo $this->Html->css('style.css?v=1');

			echo $this->Html->script(array(
				'jquery.min',
				'jquery-ui.min',
                'bootstrap.min',
                'moment.min',
                'swiper/js/swiper.min.js',
                'datepicker/bootstrap-datepicker',
                'datetimepicker/bootstrap-datetimepicker.min'
			), array(
				'block' => 'scriptTop'
            ));
            
			echo $this->fetch('scriptTop');
		?>
	</head>
	<body >
		<div class="div-content-container">
            <?= $this->element('header'); ?>
            <?php 
                echo $this->fetch('content');
            ?>
        </div>
        
        <!--
        <section class="content-footer">
            <?= $this->element('footer'); ?>
        </section>
        -->
        <?php 
            /*
            echo $this->Html->script(array(
                'common'
            ), array(
                'block' => 'scriptBottom'
            ));

            echo $this->fetch('scriptBottom');
            */
            echo $this->Html->script('common.js?v=1'); 
            echo $this->fetch('script'); 

        ?>
        <script type="text/javascript">
			$('.btn-change-language').on('click', function(){
				$('<form action="<?= $this->here; ?>" method="post"><input type="hidden" name="set_new_language" value="' + $(this).data('lang') + '"/><input type="hidden" name="origin_trigger" value="frontend"/></form>')
					.appendTo('body').submit();
			});
		</script>
	</body>
</html>