<div class="form-group images-upload-review">
    <div class="row images-upload-row">
        <?php 
            $options = array(
                'class' => 'common-file-upload-review',
                'div' => isset($is_inline) && $is_inline ? 'col-xs-5' : 'col-xs-7',
                'type' => 'file',
                'accept' => "image/*",
                'label' => isset($label) ? $label : false, 
            );
            if(isset($required) && $required){
                $options['required'] = 'required';
            }
            if(isset($disabled) && $disabled){
                $options['disabled'] = 'disabled';
            }
            if(isset($width) && $width){
                $options['image-width'] = $width;
            }else{
                $options['image-width'] = 1444;
            }
            if(isset($height) && $height){
                $options['image-height'] = $height;
            }
            
            echo $this->Form->input($name, $options);
        ?>
        <div class="<?= isset($is_inline) && $is_inline ? 'col-xs-7' : 'col-xs-12' ?>">
            <?php if(isset($img_review_url) && is_string($img_review_url)){ ?>
                <img class="img-responsive img-review" src="<?= $this->webroot . 'img/' . $img_review_url ?>"/>
            <?php } else { ?>
                <img class="img-responsive img-review"/>
            <?php } ?>
        </div>
    </div>
</div>
