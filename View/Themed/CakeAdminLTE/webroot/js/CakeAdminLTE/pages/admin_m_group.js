var ADMIN_M_GROUP = {
    url_get_members: '',
    url_index: '',
    message_confirm_push_all: '',
    sender_id: '',
    receiver_id: 0,
    member_coupon_id: '',
    init_page: function(){
        COMMON.init_element_number($('#txt_phone'));
        COMMON.init_datepicker(false);

	    // push method
        ADMIN_M_GROUP.check_method();

        ADMIN_M_GROUP.init_autocomplete_member();
        
		$('#ddl_group_type').on('change',function (){
			ADMIN_M_GROUP.check_method();
        });

        $('#confirmSubmission').on('click', function(event) {
            $('.push-to-someone').find('.alert-choose-member').remove();

			var slug = parseInt($('#ddl_group_type').val());
	        switch (slug) {
                case 1:
                    if($('input[name="data[MGroup][member_remark][]"]').length == 0){
                        $('.push-to-someone').prepend('<div class="alert alert-warning alert-choose-member">' +
	                        '<button type="button" class="close" data-dismiss="alert">×</button>' +
	                        ADMIN_M_GROUP.message_must_choose_member + '</div>');
                    }else{
                        $('#create_member_group').submit();
                    }
                    break;
				default:
                    $('#create_member_group').submit();
					break;
			}
        });
    },
    check_method: function(){
        var slug = parseInt($('#ddl_group_type').val());
        $('.push-to-someone').hide().find('textarea').attr('disabled',true);
        $('.push-by-criteria').hide().find('input,select').attr('disabled',true);
        
        switch (slug) {
            case 1:
                $('.push-to-someone').show().find('textarea').attr('disabled',false);
                break;
            case 2:
                $('.push-by-criteria').show().find('input,select').attr('disabled',false);
                break;
            case 'push-to-all':
                break;
            default:
                break;
        }
        $('.selectpicker').selectpicker('refresh');
    },
    init_autocomplete_member: function(){
        $('.member-autocomplete').autocomplete({
            delay: 500,
            source: function(request, response) {
                var data = {
                    "text": request.term,
                    "member_ids": [],
                    "field_search": 'phone'
                };

                $.each($('.txt-member-token'), function(){
                    data['member_ids'].push($(this).val());
                });

                COMMON.call_ajax({
                    url: ADMIN_M_GROUP.url_get_members,
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(json) {
                        if(json.status == true){
                            response($.map(json.params, function(item, key) {
                                return {
                                    label: item,
                                    value: parseInt(key)
                                }
                            }));
                        }else{
                            return {};
                        }
                    }
                });
            }, 
            select: function(event, ui) {
                $('.member-autocomplete').val('');
                $('.list-member-name').append('<span>' + 
                    ui.item.label + '<i class="fa fa-remove"></i>' +
                    '<input type="hidden" name="data[MGroup][member_remark][]" value="' + ui.item.value + '" class="txt-member-token" />' +
                '</span>');
                $('.push-to-someone').find('.alert-choose-member').remove();
                ADMIN_M_GROUP.init_remove_member();
                return false;
            },
            focus: function(event, ui) {
                return false;
            }
        });
    },
    init_remove_member: function(){
        $('.list-member-name i').on('click', function(){
            $(this).parent().remove();
        });
    },
}