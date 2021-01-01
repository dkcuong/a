<div class="form-group">
    <?php
    	$label = (isset($label) ? $label : $field_name);
    	if ($label) {
	    	echo (isset($required) ? '<font style="color:red">*</font>' : '') .  $this->Form->label($label);
    	}
    	if (!isset($placeholder)) {
    		$placeholder = '';
        }
        $id = isset($id) ? $id : $field_name;
    ?>
    <div class="input-group">
		<span class="input-group-addon" ><i class="fa fa-calendar"></i></span>
        <?php
            $option = array(
				'id' => $id,
				'class' => 'form-control datetimepicker' . (isset($class) ? $class : ''),
				'label' => false,
				'placeholder' => $placeholder,
                'type' => 'text',
                'autocomplete' => 'off',
                'readonly' => isset($readonly) ? $readonly : false,
                'required' => isset($required) ? $required : false
            );

            if(isset($value) && $value){
                $option['value'] = $value;
            }

			echo $this->Form->input($field_name, $option);
		?>
    </div>
    <!-- /.input group -->
</div>

<script type="text/javascript">
	$(document).ready(function(){
        $('#<?= $id ?>').datetimepicker({
            'showClose' : true,
            'format' : "<?= isset($format) ? $format : 'YYYY-MM-DD HH:mm:ss'; ?>",
            'useCurrent': false,
            'date': "<?= isset($value) ? $value : '' ?>",
            'viewDate': "<?=  isset($value) ? $value : '' ?>"
        });
	});
</script>