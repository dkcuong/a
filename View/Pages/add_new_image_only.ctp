<div class="well well-sm">
	<div class="row images-upload-row">
        <div class="col-sm-4 col-xs-12">
            <?php
                $label = '<font color="red">*</font>'.'Video'
                    . '<br> Full Screen : Width : 1920 - Height : 1080 - Ratio : 16-9'
                    . '<br> Before Expand : Frame Width : 1430 - Frame Height : 805 - Ratio : 16-9';
                if ($images_model == 'ProductIngredient') {
                    $label = 'Ingredient (360px * 430px) <a href="#notes"><sup class="notes-link">(1)</sup></a>';
                } else if ($images_model == 'ProductImage') {
                    $label = 'Product Image in Homepage & Product Details(Desktop & Mobile) (800px * 800px) <a href="#notes"><sup class="notes-link">(1)</sup></a>';
                }

                echo $this->Form->input($images_model.'.'.$count.'.Movie', array(
                    'div' => 'col-xs-12',
                    'type' => 'file',
                    'accept' => "video/*",
                    'required' => true,
                    'label' => $label
                ));
            ?>
        </div>
        <div class="col-sm-6 col-xs-12">
            <?php
            echo $this->element('images_upload_customize', array(
                'name' => 'MovieTrailer'.'.'.$count.'.Poster',
                'label' => "<font color='red'>*</font>" . __d('movie', 'thumbnail'). ' (Width: 1920 px - Height: 760 px - Ratio: 1.2)',
                'required' => true,
                'img_review_url' => isset($this->request->data['MovieTrailer']['poster']) ? $this->request->data['Movie']['poster'] : ''
            ));
            ?>
        </div>
		<div class="col-sm-2 col-xs-9 images-buttons text-right">
			<?php
				echo $this->Html->link('<i class="glyphicon glyphicon-remove"></i>', '#', array(
					'class' => 'btn-remove-image',
					'escape' => false
				));
			?>
		</div>

		<div class="form-group-label col-xs-12">
			<span class="image-type-limitation"></span>
		</div>
	</div>
</div>