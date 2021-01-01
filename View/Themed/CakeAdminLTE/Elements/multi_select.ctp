<?php if (isset($field_name) && !empty($field_name)): ?>
	<div class="form-group">
	<?php 
		$form_setting = array(
			'class' => 'form-control selectpicker ' . (isset($class) ? $class : ''),
			'title' => isset($placeholder) ? $placeholder : 'Please select',	
			'data-live-search' => $live_search,
            'multiple' => $multiple,
            'options' => isset($options) ? $options : array(),
			'selected' => isset ($selecteds) ? $selecteds : '',
			'label' => isset($label) ? $label : '',
		);
		if (isset($id) && !empty($id)) {
			$form_setting['id'] = $id;
		}
		if (isset($label) && !empty($label)) {
			$form_setting['label'] = $label;
		}else{
			$form_setting['label'] = false;
		}
	 ?>
		<?= $this->Form->input($field_name, $form_setting ); ?>
	</div><!-- .form-group -->
<?php endif ?>