var ADMIN_PUSH = {
    url_get_members: '',
    message_confirm_push_all: '',
    message_must_choose_member: '',
    person_field: '',
    model: '',
    init_page: function(){

        ADMIN_PUSH.init_autocomplete_member();

	    // push method
        ADMIN_PUSH.check_method();
        
		$('#push_method').on('change',function (){
			ADMIN_PUSH.check_method();
		});
        $('#btn-submit-data').on('click', function(event) {
            event.preventDefault();
            $('.push-to-someone').find('.alert-choose-member').remove();

			var slug = $('#push_method').find("option:selected").data('slug');
	        switch (slug) {
				case 'send-to-all':
		  	    	if(confirm(ADMIN_PUSH.message_confirm_push_all)){
                        $('#notification-add-form').submit();
                    }
                    break;
                case 'send-to-spesific-person':
                    if($('input[name="data[' + ADMIN_PUSH.model + '][' + ADMIN_PUSH.person_field + '][]"]').length == 0){
                        $('.push-to-someone').prepend('<div class="alert alert-warning alert-choose-member">' +
	                        '<button type="button" class="close" data-dismiss="alert">×</button>' +
	                        ADMIN_PUSH.message_must_choose_member + '</div>');
                    }else{
                        $('#notification-add-form').submit();
                    }
                    break;
				default:
                    $('#notification-add-form').submit();
					break;
			}
        });
    },
    init_autocomplete_member: function(){
        $('.member-autocomplete').autocomplete({
            delay: 500,
            source: function(request, response) {
                var data = {
                    "text": request.term,
                    "member_ids": []
                };

                $.each($('.txt-member-token'), function(){
                    data['member_ids'].push($(this).val());
                });

                COMMON.call_ajax({
                    url: ADMIN_PUSH.url_get_members,
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
                    '<input type="hidden" name="data[' + ADMIN_PUSH.model + '][' + ADMIN_PUSH.person_field + '][]" value="' + ui.item.value + '" class="txt-member-token" />' +
                '</span>');
                $('.push-to-someone').find('.alert-choose-member').remove();
                ADMIN_PUSH.init_remove_member();
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
    check_method: function(){
        var slug = $('#push_method').find("option:selected").data('slug');

        $('.push-to-someone').hide().find('.list-member-name').html('');

        switch (slug) 
        {
            case 'send-to-spesific-person':
                $('.push-to-someone').show();
                break;
            case 'send-to-all':
                break;
            default:
                break;
        }
    },
}