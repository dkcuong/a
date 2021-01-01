<?php if (isset($images) && $images): ?>
	<div class="row">
    <?php foreach ($images as $image): ?>
		<div class="col-xs-2">
            <?= $this->Html->image($image["path"], array('class' => 'img-detail-list')) ?>
		</div>
	<?php endforeach ?>
	</div>
<?php endif ?>
<!-- /languages -->
