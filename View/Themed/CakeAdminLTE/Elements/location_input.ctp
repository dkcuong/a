<div id="<?= $location_model ?>-list-locations">
    <div class="form-group content-locations" >
        <h3><?= __('locations') ?></h3>
    <?php 
        if( isset($this->request->data[$location_model]) && !empty($this->request->data[$location_model]) ){
            foreach ($this->request->data[$location_model] as $key => $item) :
    ?>
            <div class="row row-location">
                <div class="col-xs-1"></div>
                <div class="col-xs-4">
                    <?= $this->Form->input($location_model . '.' . $key . '.district', array(
                        'label' => '<font color="red">*</font>' . __('district'),
                        'required' => true,
                        'placeholder' => __('district'),
                        'class' => 'form-control txt-district',
                        'value' => $item['district'],
                    )); ?>
                </div>
                <div class="col-xs-6">
                    <?= $this->Form->input($location_model . '.' . $key . '.address', array(
                        'label' => '<font color="red">*</font>' . __('address'),
                        'required' => true,
                        'placeholder' => __('address'),
                        'class' => 'form-control txt-address',
                        'value' => $item['address'],
                    )); ?>
                    <?= $this->Form->input($location_model . '.' . $key . '.id', array(
                        'type' => 'hidden',
                        'value' => isset($item['id']) ? $item['id'] : ''
                    )); ?>
                </div>
                <div class="col-xs-1">
                    <div class="pull-right"><a class="btn-remove-item">X</a></div>
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
                $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__('add_location'), '#', array(
                    'class' => 'btn btn-primary btn-new-location',
                    'escape' => false
                ));
            ?>
        </div>
    </div>
</div>

<div id="base-item-<?= $location_model ?>" style="display:none">
    <div class="row row-location" style="margin-top:5px">
        <div class="col-xs-1">
        </div>
        <div class="col-xs-4">
            <?= $this->Form->input($location_model . '.ii.district', array(
                'label' => '<font color="red">*</font>' . __('district'),
                'required' => true,
                'disabled' => true,
                'placeholder' => __('district'),
                'class' => 'form-control txt-district',
            )); ?>
        </div>
        <div class="col-xs-6">
            <?= $this->Form->input($location_model . '.ii.address', array(
                'label' => '<font color="red">*</font>' . __('address'),
                'placeholder' => __('address'),
                'class' => 'form-control txt-address',
                'disabled' => true,
            )); ?>
        </div>
        <div class="col-xs-1">
            <div class="pull-right"><a class="btn-remove-item">X</a></div>
        </div>
    </div>
</div>


<script type="text/javascript" charset="utf-8">
    var location_model = '<?= $location_model ?>';

	$(document).ready(function(){
        if($('#' + location_model + '-list-locations .content-locations .row-location').length == 0){
            add_new();
        }

        $('.btn-new-location').on('click', function(e) {
            e.preventDefault();
            add_new();
        });

        function add_new(){
            var name = $('#' + location_model + '-list-locations .content-locations .row-location:last-child() .txt-address').attr('name');
            var number = 0;
            if($('#' + location_model + '-list-locations .content-locations .row-location').length){
                var name = name.replace('data[' + location_model + '][', '').replace('][address]', '');
                number = parseInt(name) + 1;
            }
            var base_html = $('#base-item-' + location_model).html();
            base_html = base_html.replace(/ii/gi, number);
            $('#' + location_model + '-list-locations .content-locations').append(base_html);

            $('#' + location_model + '-list-locations .content-locations .row-location:last-child() input').removeAttr('disabled');
            
            if(number == 0){
                $('#' + location_model + '-list-locations .content-locations .row-location:first-child() .btn-remove-item').remove();
            }

            $('.btn-remove-item').off().on('click', function(){
                remove_item($(this));
            });
        }

        $('#' + location_model + '-list-locations').on('click', '.btn-remove-item', function(){
            remove_item($(this));
        });

        function remove_item(element){
            var id = $(element).data('id');
            if(id){
                $('#' + location_model + '-list-locations .dv-manage-info').append('<input type="hidden" name="data[' + location_model + '][remove_location_ids][]" value="' + id + '" />');
            }
            $(element).closest('.row-location').remove();
        }
	});
</script>