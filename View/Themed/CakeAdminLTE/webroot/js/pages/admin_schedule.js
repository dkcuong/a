var ADMIN_SCHEDULE = {
    url_get_movie_type: '',
    layout: '',
    edit_mode: false,
    movie_type_id: 0,
    init_page: function(){

        if (ADMIN_SCHEDULE.edit_mode) {
            ADMIN_SCHEDULE.get_movie_type();
        }

        $('#movie_id').on('change', function(e){
            e.preventDefault();
            ADMIN_SCHEDULE.get_movie_type();
        }); 
    },
    get_movie_type: function() {
        var movie_id = $('#movie_id').val();

        var data = {
            "movie_id": movie_id,
        };

        COMMON.call_ajax({
            url: ADMIN_SCHEDULE.url_get_movie_type,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                if(json.status == true){
                    console.log(JSON.stringify(json));

                    let html = '';
                    if(json.params){
                        $.each(json.params, function(key, item){
                            if (ADMIN_SCHEDULE.edit_mode && ADMIN_SCHEDULE.movie_type_id && ADMIN_SCHEDULE.movie_type_id == key) {
                                html += '<option value="' + key + '" selected="selected">' + item + '</option>';
                            } else {
                                html += '<option value="' + key + '">' + item + '</option>';
                            }
                        });
                    }
                    console.log('html : ' + html);

                    $('#movie_type_id').html(html);
                    $('#movie_type_id').selectpicker('refresh');

                }else{
                    return {};
                }
            }
        });



    },
};