<?php 
	$_type = isset($_type)?$_type:'image/*';
	$_label = isset($_label)?$_label:$_name;
	$_preview = isset($_preview)?$_preview:'';
	$_preview_file = isset($_preview_file)?$_preview_file:'';
	$_preview_thumb = isset($_preview_thumb)?$_preview_thumb:'';
 ?>
<div class="form-group">
	<?= $this->Form->label(__($_label)); ?>
	<div class="fileupload fileupload-new" data-provides="fileupload">
	    <span class="btn btn-primary btn-file">
	        <span class="fileupload-new">Select file</span>
	        <span class="fileupload-exists">Change</span> 
			<?= $this->Form->file($_name, array('class' => 'form-control', 'accept'=>$_type, 'label' => false)); ?>
	    </span>
	    <span class="fileupload-preview col-sm-offset-1">
	    	<?php if($_preview_file) echo $_preview_file; ?>
	    	<?php if($_preview) echo $this->Html->image('/'.$_preview, array('height'=>150)); ?>
	    	<?php if($_preview_thumb) echo $this->Html->image('/'.$_preview_thumb, array('height'=>150)); ?>
	    </span>
	    <a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a>
	</div>
</div>