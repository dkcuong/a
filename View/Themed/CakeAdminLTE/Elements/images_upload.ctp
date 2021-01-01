<div id="<?= $images_model ?>-list-images">
    <div class="form-group content-images">
        <h3><?= __('images') ?></h3>
    <?php 
        if( isset($this->request->data[$images_model]) && !empty($this->request->data[$images_model]) ){
            foreach ($this->request->data[$images_model] as $key => $image) :
    ?>
            <div class="well well-sm">
                <div class="row">
                    <div class="col-sm-7">
                        <?php 
                            echo $this->element('images_upload_customize', array(
                                'name' => $images_model . '.' . $key . '.image',
                                'label' => false,
                                'is_inline' => true,
                                'img_review_url' => isset($image['path']) ? $image['path'] : ''
                            ));
                        ?>
                        <?= $this->Form->input($images_model . '.' . $key . '.id', array(
                            'type' => 'hidden',
                            'value' => isset($image['id']) ? $image['id'] : ''
                        )); ?>
                        <?= 
                            isset($image['width']) ? $this->Form->input($images_model . '.' . $key . '.width', array(
                                'type' => 'hidden',
                                'value' => $image['width']
                            )) : ''; 
                        ?>
                        <?= 
                            isset($image['height']) ? $this->Form->input($images_model . '.' . $key . '.height', array(
                                'type' => 'hidden',
                                'value' => $image['height']
                            )) : ''; 
                        ?>
                        <?= 
                            isset($image['size']) ? $this->Form->input($images_model . '.' . $key . '.size', array(
                                'type' => 'hidden',
                                'value' => $image['size']
                            )) : ''; 
                        ?>
                    </div>
                    <div class="col-sm-3">
                    <?php if(!(isset($only_image) && $only_image)): ?>
                        <?= $this->Form->input($images_model . '.' . $key . '.is_thumbnail', array(
                            'type' => 'checkbox',
                            'class' => 'chk-is-thumbnail',
                            'label' => __('is_thumbnail'),
                            'checked' => $image['is_thumbnail'],
                        )); ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-2">
                        <div class="pull-right"><a class="btn-remove-item" <?= isset($image['id']) ? 'data-id="' . $image['id'] . '"' : '' ?>">X</a></div>
                    </div>
                </div>
            </div>
    <?php
            endforeach;
        }
    ?>
    </div><!-- .form-group -->
    <div class="row">
        <div class="col-xs-12 text-center dv-manage-info">
            <?= 
                $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('add_image'), '#', array(
                    'class' => 'btn btn-primary btn-new-image',
                    'escape' => false
                ));
            ?>
        </div>
    </div>
</div>

<div id="base-item-<?= $images_model ?>" style="display:none">
    <div class="well well-sm">
        <div class="row">
            <div class="col-sm-7">
                <?php 
                    echo $this->element('images_upload_customize', array(
                        'name' => $images_model . '.ii.image',
                        'label' => false,
                        'is_inline' => true,
                        'required' => true,
                        'disabled' => true,
                    ));
                ?>
            </div>
            <div class="col-sm-3">
            <?php if(!(isset($only_image) && $only_image)): ?>
                <?= $this->Form->input($images_model . '.ii.is_thumbnail', array(
                    'type' => 'checkbox',
                    'class' => 'chk-is-thumbnail',
                    'label' => __('is_thumbnail'),
                    'disabled' => true,
                )); ?>
                <?php endif; ?>
            </div>
            <div class="col-sm-2">
                <div class="pull-right"><a class="btn-remove-item">X</a></div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" charset="utf-8">
    var images_model = '<?= $images_model ?>';

	$(document).ready(function(){
        if($('#' + images_model + '-list-images .content-images .well-sm').length == 0){
            add_new();
        }

        $('.btn-new-image').on('click', function(e) {
            e.preventDefault();
            add_new();
        });

        function add_new(){
            var name = $('#' + images_model + '-list-images .content-images .well-sm:last-child() .common-file-upload-review').attr('name');
            var number = 0;
            if($('#' + images_model + '-list-images .content-images .well-sm').length){
                var name = name.replace('data[' + images_model + '][', '').replace('][image]', '');
                number = parseInt(name) + 1;
            }
            var base_html = $('#base-item-' + images_model).html();
            base_html = base_html.replace(/ii/gi, number);
            $('#' + images_model + '-list-images .content-images').append(base_html);

            $('#' + images_model + '-list-images .content-images .well-sm:last-child() input').removeAttr('disabled');
            if(number == 0){
                $('#' + images_model + '-list-images .content-images .well-sm:first-child() .btn-remove-item').remove();
            }

            init_remove_item();
            init_checkbox_thumbnail();
        }

        init_remove_item();
        init_checkbox_thumbnail();
	});

    function init_remove_item(){
        $('.btn-remove-item').off().on('click', function(){
            var id = $(this).data('id');
            if(id){
                $('#' + images_model + '-list-images .dv-manage-info').append('<input type="hidden" name="data[' + images_model + '][remove_image_ids][]" value="' + id + '" />');
            }
            $(this).closest('.well-sm').remove();
        });
    }

    function init_checkbox_thumbnail(){
        $('.chk-is-thumbnail').off().on('click', function(){
            if($(this).is(':checked')){
                $('.chk-is-thumbnail').prop('checked', false);
                $(this).prop('checked', true);
            }
        });
    }
</script>