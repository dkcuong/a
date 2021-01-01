
<div class="pull-left">
<p>
	<small><?= $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')));?></small>
</p>
</div>

<?php
	$params = $this->Paginator->params();

	$extra_pagintion_parameters = array();

	if( isset($this->request->data) && !empty($this->request->data) ){
		$extra_pagintion_parameters = array(
			'url' => $this->request->data
		);
	}

	$this->Paginator->options($extra_pagintion_parameters);
	
	if ($params['pageCount'] > 1) {
?>
<div class="pull-right">
		<ul class="pagination pagination-sm">
			<?php
				echo $this->Paginator->prev('&larr; Previous', array('class' => 'prev','tag' => 'li','escape' => false), '<a onclick="return false;">&larr; Previous</a>', array('class' => 'prev disabled','tag' => 'li','escape' => false));
				echo $this->Paginator->numbers(array('separator' => '','tag' => 'li','currentClass' => 'active','currentTag' => 'a'));
				echo $this->Paginator->next('Next &rarr;', array('class' => 'next','tag' => 'li','escape' => false), '<a onclick="return false;">Next &rarr;</a>', array('class' => 'next disabled','tag' => 'li','escape' => false));
			?>
		</ul>
</div>
<?php } ?>