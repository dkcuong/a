var ADMIN_SHOP = {
    url_index: '',
    url_get_item_version: '',
    url_upgrade_version: '',
    data_change_status: {},
    message_confirm: '',
    is_have_pending: 0,
    message_confirm_continuous_edit_pending: '',
    init_page: function(){
        COMMON.init_datetimepicker_range($('#publish_date'), $('#unpublish_date'));
        COMMON.init_datetimepicker_range($('#start_date'), $('#end_date'));
        // init element decimal
        COMMON.init_element_decimal($('.decimal-number'));

        COMMON.init_validate_form_tabs($("#btn-submit-data"));
    },
    init_edit_form: function(){
        $('#item-edit-form').on('submit', function(event){
            if(ADMIN_SHOP.is_have_pending == 1){
                var result = confirm(ADMIN_SHOP.message_confirm_continuous_edit_pending);
                if (result == false) {
                    if($('#btn-submit-data').length){
                        $('#btn-submit-data').removeAttr('disabled');
                    }
                    event.preventDefault();
                } 
            }
        });
    },
    init_detail_page: function(){
        var hashtag = window.location.hash.substr(1);
        if(hashtag){
            $('a[href="#' + hashtag+ '"]').trigger('click');
        }

        $('.btn-approve').off().on('click', function(){
            ADMIN_SHOP.data_change_status = {
                id: $(this).data('id'),
                version_id: $(this).data('detail-id'),
                status: 1
            };

            ADMIN_SHOP.init_confirm_change_status('Approve');
        });

        $('.btn-reject').off().on('click', function(){
            ADMIN_SHOP.data_change_status = {
                id: $(this).data('id'),
                version_id: $(this).data('detail-id'),
                status: 4
            };

            ADMIN_SHOP.init_confirm_change_status('Reject');
        });

        $('.btn-view-detail').on('click', function(){
            var id = $(this).data('id');

            COMMON.call_ajax({
                url: ADMIN_SHOP.url_get_item_version + '/' + id,
                type: 'GET',
                dataType: 'text',
                success: function(result){
                    $('.item-detail-modal .modal-body').html(result);
                    $('.item-detail-modal').modal('show');
                    ADMIN_SHOP.init_action_popup_view_detail();
                },
                error: function(error){
                    alert("Get version of item is error!")
                }
            });
        });
    },
    init_action_popup_view_detail: function(){
        $('.btn-approve-modal').off().on('click', function(){
            ADMIN_SHOP.data_change_status = {
                id: $(this).data('id'),
                version_id: $(this).data('detail-id'),
                status: 1
            };

            ADMIN_SHOP.init_confirm_change_status('Approve');
        });

        $('.btn-reject-modal').off().on('click', function(){
            ADMIN_SHOP.data_change_status = {
                id: $(this).data('id'),
                version_id: $(this).data('detail-id'),
                status: 4
            };

            ADMIN_SHOP.init_confirm_change_status('Reject');
        });
    },
    init_confirm_change_status: function(action){
        var message = ADMIN_SHOP.message_confirm.replace("[action]", action);
        $('.confirm-change-status-modal .modal-body h3').text(message);
        $('.confirm-change-status-modal').modal('show');
        
        $('.btn-confirm-yes').off().on('click', function(){
            COMMON.call_ajax({
                url: ADMIN_SHOP.url_upgrade_version + '/' + ADMIN_SHOP.data_change_status.id,
                type: 'POST',
                data: {
                    version_id: ADMIN_SHOP.data_change_status.version_id,
                    status: ADMIN_SHOP.data_change_status.status
                },
                dataType: 'json',
                success: function(result){
                    if(typeof(result.status) != "undefined"){
                        alert(result.params.message);
                        if(result.status === true){
                            window.location.href = ADMIN_SHOP.url_index;
                        }
                    }else{
                        alert(action + " this item was FAILED!");
                    }
                },
                error: function(error){
                    alert(action + " this item was FAILED!");
                }
            });
        });
    }
}