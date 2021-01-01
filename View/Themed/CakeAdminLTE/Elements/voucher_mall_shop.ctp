<div id="<?= $model_shop ?>-list-locations">
    <div class="row">
        <div class="col-xs-2">
            <label><?= __d('company', 'mall') . ' - ' . __d('company', 'shop') ?>:</label>
        </div>
        <div class="col-xs-10">
            <div class="form-group content-locations" >
                <?php 
                    if( isset($this->request->data[$model_shop]) && !empty($this->request->data[$model_shop]) ){
                        foreach ($this->request->data[$model_shop] as $key => $item) :
                ?>
                        <div class="row-location well well-sm">
                            <div class="row" style="margin-top:5px">
                                <div class="col-xs-4">
                                    <?= $this->Form->input($model_shop . '.' . $key . '.mall_id', array(
                                        'empty' => __('please_select'),
                                        'data-live-search' => true,
                                        'label' => "<font color='red'>*</font>" . __d('company', 'mall'),
                                        'placeholder' => __d('company', 'mall'),
                                        'class' => 'form-control ddl-filter-mall selectpicker',
                                        'value' => $item['mall_id'],
                                        'required' => true,
                                        'options' => $malls
                                    )); ?>
                                </div>
                                <div class="col-xs-7">
                                    <?= $this->Form->input($model_shop . '.' . $key . '.shop_id', array(
                                        'data-live-search' => true,
                                        'multiple' => true,
                                        'label' => "<font color='red'>*</font>" . __d('company', 'shop'),
                                        'placeholder' => __d('company', 'shop'),
                                        'class' => 'form-control ddl-shops selectpicker',
                                        'value' => $item['shop_id'],
                                        'required' => true,
                                        'data-actions-box' => true,
                                        'options' => $exist_shops[$item['mall_id']]
                                    )); ?>
                                </div>
                                <div class="col-xs-1">
                                    <div class="pull-right"><a class="btn-remove-item">X</a></div>
                                </div>
                            </div>
                        </div>
                <?php
                        endforeach;
                    }
                ?>
            </div>
            <div class="row">
                <div class="col-xs-12 text-center dv-manage-info">
                    <?= 
                        $this->Html->link('<i class="glyphicon glyphicon-plus"></i> '.__d('voucher', 'add_mall_shop'), '#', array(
                            'class' => 'btn btn-primary btn-new-location',
                            'escape' => false
                        ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="base-item-<?= $model_shop ?>" style="display:none">
    <div class="row-location well well-sm">
        <div class="row" style="margin-top:5px">
            <div class="col-xs-4">
                <?= $this->Form->input($model_shop . '.ii.mall_id', array(
                    'empty' => __('please_select'),
                    'data-live-search' => true,
                    'label' => "<font color='red'>*</font>" . __d('company', 'mall'),
                    'placeholder' => __d('company', 'mall'),
                    'class' => 'form-control ddl-filter-mall',
                    'disabled' => true,
                    'required' => true,
                    'options' => $malls
                )); ?>
            </div>
            <div class="col-xs-7">
                <?= $this->Form->input($model_shop . '.ii.shop_id', array(
                    'data-live-search' => true,
                    'multiple' => true,
                    'label' => "<font color='red'>*</font>" . __d('company', 'shop'),
                    'placeholder' => __d('company', 'shop'),
                    'class' => 'form-control ddl-shops',
                    'disabled' => true,
                    'required' => true,
                    'data-actions-box' => true,
                    'options' => array()
                )); ?>
            </div>
            <div class="col-xs-1">
                <div class="pull-right"><a class="btn-remove-item">X</a></div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" charset="utf-8">
    var get_shop_by_mall = '<?= Router::url(array('plugin'=>'company', 'controller'=> 'shops', 'action'=>'get_data_select'), true); ?>';
    var location_model = '<?= $model_shop ?>';
    var voucher_id = '<?= isset($voucher_id) ? $voucher_id : '' ?>';
    var is_delete_mall_shop = '<?= isset($is_delete_mall_shop) ? $is_delete_mall_shop : '' ?>';
    var text_please_select = '<?= __('please_select') ?>';

	$(document).ready(function(){
        if($('#' + location_model + '-list-locations .content-locations .row-location').length == 0){
            add_new();
        }

        $('.btn-new-location').on('click', function(e) {
            e.preventDefault();
            add_new();
        });

        function add_new(){
            var name = $('#' + location_model + '-list-locations .content-locations .row-location:last-child() .ddl-filter-mall').last().attr('name');
            var number = 0;
            if($('#' + location_model + '-list-locations .content-locations .row-location').length){
                var name = name.replace('data[' + location_model + '][', '').replace('][mall_id]', '');
                number = parseInt(name) + 1;
            }
            var base_html = $('#base-item-' + location_model).html();
            base_html = base_html.replace(/ii/gi, number);
            $('#' + location_model + '-list-locations .content-locations').append(base_html);

            $('#' + location_model + '-list-locations .content-locations .row-location:last-child() input, #' + location_model + '-list-locations .content-locations .row-location:last-child() select').removeAttr('disabled');
            $('#' + location_model + '-list-locations .content-locations .row-location:last-child() select').addClass('selectpicker').selectpicker();

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

        $('#' + location_model + '-list-locations').on('change', '.ddl-filter-mall', function(){
            var parent_element = $(this).closest('.row-location');
            load_shop($(this), parent_element);
        });

        function remove_item(element){
            $(element).closest('.row-location').remove();
        }

        function load_shop(mall_element, parent_element){
            var mall_id = $(mall_element).val();
            if(mall_id){
                COMMON.call_ajax({
                    url: get_shop_by_mall,
                    type: 'GET',
                    data: { 
                        mall_id: mall_id,
                        voucher_id: voucher_id,
                        is_delete_mall_shop: is_delete_mall_shop
                    },
                    dataType: 'json',
                    success: function(result){
                        var html = '<option value="">' + text_please_select + '</option>';
                        if(result.status){
                            $.each(result.params, function(key, item){
                                html += '<option value="' + key + '">' + item + '</option>';
                            });
                        }

                        $(parent_element).find('select.ddl-shops').html(html).selectpicker('refresh');
                    },
                    error: function(error){
                        alert("Get data for route is error!")
                    }
                });
            }
        }
	});
</script>