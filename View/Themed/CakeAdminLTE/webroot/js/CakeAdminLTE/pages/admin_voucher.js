var ADMIN_VOUCHER = {
    url_index: '',
    format_date: '',
    url_upgrade_version: '',
    url_get_item_verion: '',
    data_change_status: {},
    message_confirm: '',
    is_have_pending: 0,
    message_confirm_continuous_edit_pending: '',
    init_page: function(){
		// available_type
        ADMIN_VOUCHER.check_available_type();
        COMMON.init_datetimepicker_range($('#publish_date'), $('#unpublish_date'));
        COMMON.init_datetimepicker_range($('#redeem_start_date'), $('#redeem_end_date'));

        // valid multi language
        COMMON.init_validate_form_tabs($("#btn-submit-data"));
        
        // init element number
        COMMON.init_element_number($('.txt-number'));

        // init element decimal
        COMMON.init_element_decimal($('.decimal-number'));
        
		$('#available_type_id').on('change',function (){
			ADMIN_VOUCHER.check_available_type();
        });

		$('.available_type').datetimepicker({
			"showClose" : true,
			"format" : ADMIN_VOUCHER.format_date,
        });
    },
    check_available_type: function(){
        let type = $('#available_type_id').val();
        $('.available_type_dynamic').hide().find('input').attr('disabled', true).attr('required', false);
        $('.available_type_static').hide().find('input').attr('disabled', true).attr('required', false);

        switch(type){
            case '1':
                $('.available_type_static').show().find('input').attr('disabled', false).attr('required', true);
                break;
            case '2':
                $('.available_type_dynamic').show().find('input').attr('disabled',false).attr('required', true);
                break;
        }
    },
}