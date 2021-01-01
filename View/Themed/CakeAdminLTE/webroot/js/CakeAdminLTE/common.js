var COMMON = {
    _URL: window.URL || window.webkitURL,
    call_ajax: function(params){
        $.ajax({
            url: params.url,
            type: params.type,
            data: params.data,
            dataType: params.dataType,
            beforeSend: function(){

            },
            success: params.success,
            error: params.error,
            complete: function(){

            }
        })
    },
    init_validate_form_tabs: function(btn_submit){
        $(btn_submit).on('click', function(){
            var form = $(this).closest("form");
            $(form).find(".tab-content [required=required]").each(function(key, item){
                if($(item).val() === ''){
                    $(item).focus();
                    var tab = $(item).closest('.tab-pane').attr('id');
                    $('form a[href="#' + tab + '"]').tab('show');
                    return false;
                }
            })
        })
    },
    init_element_number: function(element){
        $(element).on('keypress', function(evt){
            var charCode = (evt.which) ? evt.which : evt.keyCode
            return !(charCode > 31 && (charCode < 48 || charCode > 57));
        });
    },
    init_element_decimal: function(element){
        $(element).on('keypress', function(evt){
            $(this).val($(this).val().replace(/[^0-9\.]/g,''));
            if ((evt.which != 46 || $(this).val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }
        });
    },
    init_datepicker: function(is_over_now){
        var option = {
            "showClose" : true,
            "format" : "YYYY-MM-DD",
        };

        if(is_over_now){
            var today = new Date();
            option['minDate'] = today;
        }
        
        $('.datepicker').datetimepicker(option);
    },
    init_datetimepicker: function(){
        $('.datetimepicker').datetimepicker({
            "showClose" : true,
            "format" : "YYYY-MM-DD HH:mm:ss",	
        });
    },
    init_timepicker: function(format){
        $('.timepicker').datetimepicker({
            "showClose" : true,
            "format" : format,
        });
    },
    init_monthdaypicker: function(){
        $('.monthdaypicker').datetimepicker({
            "showClose" : true,
            "format" : "MM-DD",
        });
    },
    init_yearmonthpicker: function(){
        $('.yearmonthpicker').datepicker({
            "viewMode": "months", 
            "minViewMode": "months", 
            "showClose" : true,
            "format" : "yyyy-mm",
        });
    },
    init_datetimepicker_range: function(start_element, end_element){
        if($(start_element).val() != ''){
            $(end_element).data("DateTimePicker").minDate($(start_element).val());
        }

        if($(end_element).val() != ''){
            $(start_element).data("DateTimePicker").maxDate($(end_element).val());
        }

        $(start_element).on("dp.change", function (e) {
            $(end_element).data("DateTimePicker").minDate(e.date);
        });

        // datetime picker period_end changed
        $(end_element).on("dp.change", function (e) {
            $(start_element).data("DateTimePicker").maxDate(e.date);
        });
    },
    read_url: function (input) {
        var width = $(input).attr('image-width');
        var height = $(input).attr('image-height');
        if (input.files && input.files[0]) {
            img = new Image();
            var objectUrl = COMMON._URL.createObjectURL(input.files[0]);
            img.onload = function () {
                // check width height if exist data.
                if(width && height){
                    width = parseFloat(width);
                    height = parseFloat(height);
                    if(this.width < width){
                        $(input).val('');
                        alert('Please choose image what\'s width is bigger than ' + width + 'px and ratio (height / width) is ' + Math.round(height / width * 100) / 100);
                        return;
                    }

                    var min_ratio = (height - 10) / width;
                    var max_ratio = (height + 10) / width;
                    var ratio = this.height / this.width;
                    if(!(min_ratio < ratio && ratio < max_ratio)){
                        $(input).val('');
                        alert('Please choose image what\'s width is bigger than ' + width + 'px and ratio (height / width) is ' +  Math.round(height / width * 100) / 100);
                        return;
                    }
                }
                var review_element = $(input).closest('.images-upload-review').find('.img-review');
                var reader = new FileReader();
                
                reader.onload = function (e) {
                    $(review_element).attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);

                COMMON._URL.revokeObjectURL(objectUrl);
            };
            img.src = objectUrl;
        }
    },
    is_valid_date: function(month, day) {
        var date = new Date("2020-" + month + "-" + day);
        return date.getDate() == day;
    },
    init_visible_column_table: function(){
        // if($('.table-responsive table').length && $('.table-responsive table thead th').length > 7){
        //     // init function and add html

        //     var mycache = [];
        //     if (COMMON.column_cache != "") {
        //         mycache = JSON.parse(COMMON.column_cache);
        //     }
        //     var all_col = [];

        //     var total_column = $('.table-responsive table thead th').length;
        //     var html_column = '<div class="dv-cover-header-table">' +
        //         '<i class="fa fa-2x fa-cog btn-cog-column-table"></i>' +
        //         '<div class="dv-list-header-table"><ul>';

        //     $('.table-responsive table thead th').each(function(index, item){
        //         var name = "";
        //         if($(item).find('a').length){
        //             name = $(item).find('a').text(); 
        //         }else{
        //             name = $(item).text();
        //         }
                
        //         html_column += '<li><label><input type="checkbox" class="chk-column-table" value="' + index + '" ';

        //         var is_show = false;
        //         if ((COMMON.column_cache == "") && (index < 5 || index >= (total_column - 2))) {
        //             //if cache doesnt exists, follow default rule
        //             is_show = true;
        //         } else if ((COMMON.column_cache != "") && mycache && (mycache.indexOf(index) > -1)) {
        //             //if cache exists, follow cache
        //             is_show = true;
        //         }

        //         if(is_show) {
        //             html_column += 'checked="checked" ';
        //             $('.table-responsive table thead th:nth-child(' + (index + 1) + ')').show();
        //             $('.table-responsive table tbody td:nth-child(' + (index + 1) + ')').show();
        //             all_col.push(index);
        //         }else{
        //             $('.table-responsive table thead th:nth-child(' + (index + 1) + ')').hide();
        //             $('.table-responsive table tbody td:nth-child(' + (index + 1) + ')').hide();
        //         }
        //         html_column += '> ' + name + '</label></li>';
        //     });

        //     html_column += '</ul></div></div> ';
        //     $('.box-header h3.box-title').after(html_column);

        //     // init action of this page
        //     $('.btn-cog-column-table').on('click', function(){
        //         if($('.dv-list-header-table').width() == 0){
        //             $('.dv-list-header-table').css('width', '350px');
        //             $('.dv-list-header-table').css('display', 'block');
        //         }else{
        //             $('.dv-list-header-table').css('width', '0px');
        //             setTimeout(function(){
        //                 $('.dv-list-header-table').css('display', 'none');
        //             }, 500);
        //         }
        //     });

        //     $('.chk-column-table').on('click', function () {
        //         let is_check = 0;
        //         if($(this).is(':checked')){
        //             is_check = 1;
        //         }
        //         var index = parseInt($(this).val());
        //         COMMON.set_unset_column(index, all_col, is_check); //1 for set, 0 for unset
        //     });

        //     // $('.chk-column-table').on('ifUnchecked', function () {
        //     //     var index = parseInt($(this).val());
        //     //     COMMON.set_unset_column(index, all_col, 0); //1 for set, 0 for unset
        //     // });
        // }
    },
    set_unset_column: function(col_index, all_col, is_set){
        //var col_name = $('.table-responsive table thead th:nth-child(' + col_index + ')').text();
        if ((is_set == 1) && (all_col.indexOf(col_index) < 0)) {
            //if its checked and the col name doesnt exists in all_col then add it 
            //remember, all_col will be saved to the redis cache
            all_col.push(col_index);
        } else if ((is_set == 0) && (all_col.indexOf(col_index) > -1)) {
            //if its unchecked and the col name exists in all_col then remove it 
            //remember, all_col will be saved to the redis cache
            all_col.splice(all_col.indexOf(col_index), 1);
        }

        COMMON.call_ajax({
            url: COMMON.url_update_cache,   
            type: 'POST',
            data : { 
                'module_name' : COMMON.module_name,
                'visible_col' : JSON.stringify(all_col),
                'is_set' : is_set,
            },  
            success: function(result){
                var resultarr = JSON.parse(result);
                if (resultarr["status"]) {
                    if (is_set == 1) {
                        $('.table-responsive table thead th:nth-child(' + (col_index + 1) + ')').show();
                        $('.table-responsive table tbody td:nth-child(' + (col_index + 1) + ')').show();
                    } else {
                        $('.table-responsive table thead th:nth-child(' + (col_index + 1) + ')').hide();
                        $('.table-responsive table tbody td:nth-child(' + (col_index + 1) + ')').hide();
                    }
                }
            },
            error: function(error){
            }
        });   
    },
}

$(document).ready(function(){
    $(document).on('change', '.common-file-upload-review', function(){
        COMMON.read_url(this);
    });
});