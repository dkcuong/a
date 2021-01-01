<?php
	$id_prefix = ""; $id_string = "";

	if( isset($key) ){
		if( $key != "flash" ){
			$id_prefix = "flash-";
		}

		$id_string = 'id="' . $id_prefix . h($key) . '"';
	}
?>
<div <?= $id_string; ?> class="message-info alert alert-success">
	<div class="row">
		<div class="col-xs-12 message-content">
			<?php print h($message); ?>
		</div>
	</div>

	<?php 
		if( isset($params) && !empty($params) ){
			foreach ($params as $p_key => $p_value) {
	?>
				<div class="row">
					<p class="message-extra">
						<span class="col-xs-12 col-sm-3 message-extra-key"><?php print h($p_key) . ": "; ?></span>
						<span class="col-xs-12 col-sm-9 message-extra-value"><?php print h($p_value); ?></span>
					</p>
				</div>
	<?php	
			}
		}
	?>
</div>