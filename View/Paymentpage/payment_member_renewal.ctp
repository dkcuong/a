<section>
	<form method="POST" style="display: none" action="<?= $actionURL ?>" id="form-recon">
		<?php foreach ($payment_array as $key => $value) { ?>
			<input type="text" name="<?php print $key; ?>" value="<?php print $value; ?>">
		<?php } ?>
		<input type="submit" value="Submit">
	</form>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#form-recon").submit();
		})
	</script>
</section>