<?php if(isset($_is_through) && $_is_through){ ?>
	<span class="text-line-through" ><?= h($_text); ?></span>&nbsp;
<?php }else{ ?>
	<span><?= h($_text); ?></span>&nbsp;
<?php } ?>