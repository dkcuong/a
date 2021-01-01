var DASHBOARD = {
    url_get_data_dashboard : '',
    counter : 1,
    action : 'tv1',
    total_slide : 4,
    webroot : '',
    is_full : 0,
    init_page: function() {
        if (DASHBOARD.is_full == 1) {
            $("#modal_warning").modal('show');
        }

        setTimeout(() => {
            DASHBOARD.refresh();
        }, 10000);
    },
    refresh: function() {
        DASHBOARD.counter++;

        if (DASHBOARD.counter > DASHBOARD.total_slide) {
            DASHBOARD.counter = 1;
        }

        var data = {
            "action": DASHBOARD.action,
            "counter": DASHBOARD.counter,
        };

        COMMON.call_ajax({
            url: DASHBOARD.url_get_data_dashboard,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                if(json.status == true){
                    //console.log('data : ' + JSON.stringify(json.params));

                    if (json.params.Schedule) {
                        
                        obj = json.params;
                        
                        $('.movie-title h1').html(obj.MovieLanguage[0].MovieLanguage.name);
                        $('.movie-title h3').html(obj.MovieLanguage[1].MovieLanguage.name);

                        $('.poster').html('<img src="' + DASHBOARD.webroot + obj.Movie.poster + '" alt="">');
                        $('.rating').html(obj.Movie.rating);
                        $('.duration').html(obj.Movie.duration);

                        
                        $('.schedule-time span').html(obj.ScheduleDetail.time_display);
                        $('.hall').html(obj.Hall.code);

                        var seats = obj.seats;
                        var html_str = '';
                        for (var id_row = 0; id_row < seats.length; id_row++) {
                            var row = seats[id_row];

                            html_str += '<div class="row m-0 clearfix justify-content-center">';
                            html_str += '<div class="seat seat-title mr-2">';
                            html_str += '<h3>' + row[id_row].title + '</h3>';
                            html_str += '</div>';

                            for (var id_col = 0; id_col < row.length; id_col++) {
                                var col = row[id_col];

                                if((col.enabled == 1) && (col.status == 1)){
                                    if (col.disability == 1) {
                                        html_str += '<div class="seat disability item text-center">';
                                        html_str += '<i class="fas fa-wheelchair"></i>';
                                        html_str += '</div>';
                                    } else {
                                        html_str += '<div class="seat item text-center">';
                                        html_str += '<h3>' + col.label + '</h3>';
                                        html_str += '</div>';
                                    }
                                } else {
                                    html_str += '<div class="seat item text-center disabled">';
                                    html_str += '<i class="fas fa-times text-white"></i>';
                                    html_str += '</div>';                                
                                }
                            }

                            html_str += '<div class="seat seat-title mr-2">';
                            html_str += '<h3>' + row[id_row].title + '</h3>';
                            html_str += '</div>';
                            html_str += '</div>';
                        }

                        $('.seats').html(html_str);

                        $('.main-contain').removeClass('d-xl-none d-lg-none');
                        $('.main-warning').addClass('d-xl-none d-lg-none');

                        if (obj.is_full == 1) {
                            $("#modal_warning").modal('show');
                        } else {
                            $("#modal_warning").modal('hide');
                        }


                        
                    }
                    setTimeout(() => {
                        DASHBOARD.refresh();
                    }, 10000);
                }else{
                    setTimeout(() => {
                        DASHBOARD.refresh();
                    }, 10000);
                }
            },
            error: function(json) {
                console.log('error detected');
                setTimeout(() => {
                    DASHBOARD.refresh();
                }, 10000);
            }
        });

    }
}