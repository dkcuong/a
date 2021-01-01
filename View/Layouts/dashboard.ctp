<!DOCTYPE html>
<html lang="en">
	<head>
		<title>
			<?= $title_for_layout; ?>
		</title>

		<?= $this->Html->charset(); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
		
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
                'bootstrap4/bootstrap.min',
                'bootstrap4/all.min',
                'jquery-ui'
			));
			echo $this->fetch('css');

            echo $this->Html->css('dashboard.css?v='.date('U'));

			echo $this->Html->script(array(
				'jquery.min',
				'jquery-ui.min',
                'bootstrap4/bootstrap.min',
                'bootstrap4/all.min',
			), array(
				'block' => 'scriptTop'
            ));
            
			echo $this->fetch('scriptTop');
		?>
		<!--
        <meta http-equiv="refresh" content="5;https://localhost/acx/dashboardpage/<?= $action ?>/<?= $new_counter ?>" />
		-->
	</head>
	<body >
		<div class="div-content-container">
            <?php 
                echo $this->fetch('content');
            ?>
        </div>
    
        <?php 
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