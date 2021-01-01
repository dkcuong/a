var ADMIN_MEMBER_VOUCHER = {
    url_get_members: '',
    url_index: '',
    message_confirm_push_all: '',
    sender_id: '',
    receiver_id: 0,
    member_coupon_id: '',
    init_page: function(){
	    // push method
        ADMIN_MEMBER_VOUCHER.check_method();

        ADMIN_MEMBER_VOUCHER.init_autocomplete_member();
        
		$('#push_method').on('change',function (){
			ADMIN_MEMBER_VOUCHER.check_method();
        });

        $('#confirmSubmission').on('click', function(event) {
            $('.push-to-someone').find('.alert-choose-member').remove();

			var slug = parseInt($('#push_method').val());
	        switch (slug) {
				case 1:
		  	    	if(confirm(ADMIN_MEMBER_VOUCHER.message_confirm_push_all)){
                        $('#send_voucher_form').submit();
                    }
                    break;
                case 3:
                    if($('input[name="data[MemberCouponDistribution][member_remark][]"]').length == 0){
                        $('.push-to-someone').prepend('<div class="alert alert-warning alert-choose-member">' +
	                        '<button type="button" class="close" data-dismiss="alert">×</button>' +
	                        ADMIN_MEMBER_VOUCHER.message_must_choose_member + '</div>');
                    }else{
                        $('#send_voucher_form').submit();
                    }
                    break;
				default:
                    $('#send_voucher_form').submit();
					break;
			}
        });
    },
    check_method: function(){
        var slug = parseInt($('#push_method').val());
        $('.push-to-someone').hide().find('textarea').attr('disabled',true);
        $('.push-to-member-group').hide().find('select').attr('disabled',true);
        $('.push-by-criteria').hide().find('input,select').attr('disabled',true);
        
        switch (slug) {
            case 2:
                $('.push-to-member-group').show().find('select').attr('disabled',false);
                break;
            case 3:
                $('.push-to-someone').show().find('textarea').attr('disabled',false);
                break;
            case 4:
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
                    url: ADMIN_MEMBER_VOUCHER.url_get_members,
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
                    '<input type="hidden" name="data[MemberCouponDistribution][member_remark][]" value="' + ui.item.value + '" class="txt-member-token" />' +
                '</span>');
                $('.push-to-someone').find('.alert-choose-member').remove();
                ADMIN_MEMBER_VOUCHER.init_remove_member();
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