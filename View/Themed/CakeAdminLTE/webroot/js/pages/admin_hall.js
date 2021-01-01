var ADMIN_HALL = {
    seat_layout: new Array(),
    layout: '',
    init_page: function(){
        $('#btn-seat-generate').on('click', function(e){
            e.preventDefault();
            ADMIN_HALL.generate_seat();
        }); 
        
        $('#panel-seat-edit').on("click", ".btn-seat", function(e) {

            var target_row = $(this).data("row");
            var target_column = $(this).data("column");

            if ($(this).hasClass('enabled')) 
            {
                $('#btn-seat-'+target_row+'-'+target_column).val(0);
                $(this).removeClass('enabled');
                $('#btn-seat-layout-'+target_row+'-'+target_column).removeClass('enabled');
            } 
            else 
            {
                $('#btn-seat-'+target_row+'-'+target_column).val(1);
                $(this).addClass('enabled');
                $('#btn-seat-layout-'+target_row+'-'+target_column).addClass('enabled');
            }
            ADMIN_HALL.build_string();
        });

        $('#panel-seat-edit').on("contextmenu", ".btn-seat", function(e) {
            e.preventDefault();

            var target_row = $(this).data("row");
            var target_column = $(this).data("column");

            if ($(this).hasClass('enabled')) 
            {
                if ($(this).hasClass('vegetable')) {
                    $('#vegetable-seat-'+target_row+'-'+target_column).val(0);
                    $(this).removeClass('vegetable');
                    $('#btn-seat-layout-'+target_row+'-'+target_column).removeClass('vegetable');

                    //set to blocked
                    $('#blocked-seat-'+target_row+'-'+target_column).val(1);
                    $(this).addClass('blocked');
                    $('#btn-seat-layout-'+target_row+'-'+target_column).addClass('blocked');
                } else if ($(this).hasClass('blocked')) {
                    //set to available
                    $('#blocked-seat-'+target_row+'-'+target_column).val(0);
                    $(this).removeClass('blocked');
                    $('#btn-seat-layout-'+target_row+'-'+target_column).removeClass('blocked');
                } else {
                    $('#vegetable-seat-'+target_row+'-'+target_column).val(1);
                    $(this).addClass('vegetable');
                    $('#btn-seat-layout-'+target_row+'-'+target_column).addClass('vegetable');
                }
            }
            ADMIN_HALL.build_string();
            return false;
        });
    },
    set_layout: function() {
        let obj = JSON.parse(ADMIN_HALL.layout);

        for(var i = 0; i < obj.length; i++) {
            if (!(obj[i].row_number in ADMIN_HALL.seat_layout)) {
                ADMIN_HALL.seat_layout[obj[i].row_number] = new Array();
            }
            var seat_enabled = (obj[i].enabled) ? 1 :0;
            var vegetable_seat = (obj[i].is_disability_seat) ? 1 :0;
            var blocked_seat = (obj[i].is_blocked_seat) ? 1 :0;
            ADMIN_HALL.seat_layout[obj[i].row_number][obj[i].column_number] = {id: obj[i].id, enabled : seat_enabled, vegetabled : vegetable_seat, blocked : blocked_seat};
        }
        ADMIN_HALL.draw_seat();
    },
    generate_seat: function() {

        $('#panel-seat-edit').html('');
        $('#panel-seat-layout').html('');

        ADMIN_HALL.seat_layout = new Array();

        var row = $('#row_number').val();
        var column = $('#column_number').val();

        $('#HallMaxSeat').val(row * column);

        for(var i = 0; i < row; i++) {
            ADMIN_HALL.seat_layout[i] = new Array(column * 1).fill({ enabled : 1, vegetabled : 0, blocked : 0 });
        }

        if(row > 0 && column > 0) {
           ADMIN_HALL.draw_seat(); 
       }

    },
    draw_seat: function() {
        var str = "";
        var str_layout = "";
        var index = -1;

        for(var i = 0; i < ADMIN_HALL.seat_layout.length; i++) {
            var row_title = String.fromCharCode(65 + i);
            str += "<div class='row-seat'><div class='row-title'>" + row_title + "</div>";
            str_layout += "<div class='row-seat'><div class='row-title'>" + row_title + "</div>";
            for(var j = 0; j < ADMIN_HALL.seat_layout[i].length; j++) {    
                var enabled_style = (ADMIN_HALL.seat_layout[i][j].enabled == 1) ? 'enabled' :  '';
                var vegetabled_style = (ADMIN_HALL.seat_layout[i][j].vegetabled == 1) ? ' vegetable' :  '';
                var blocked_style = (ADMIN_HALL.seat_layout[i][j].blocked == 1) ? ' blocked' :  '';
                index++;
                str += "<input type='hidden' id='row_" + i + "' value='" + i + "'>";
                str += "<input type='hidden' id='col_" + j + "' value='" + j + "'>";
                str += "<input type='hidden' id='id_" + i + "-" + j + "' value='" + ADMIN_HALL.seat_layout[i][j].id + "'>";
                str += "<input type='hidden' id='btn-seat-" + i + "-" + j + "' value='" + ADMIN_HALL.seat_layout[i][j].enabled + "'>";
                str += "<input type='hidden' id='vegetable-seat-" + i + "-" + j + "' value='" + ADMIN_HALL.seat_layout[i][j].vegetabled + "'>";
                str += "<input type='hidden' id='blocked-seat-" + i + "-" + j + "' value='" + ADMIN_HALL.seat_layout[i][j].blocked + "'>";
                str += "<button type='button' class='div-seat btn-seat " + enabled_style + vegetabled_style + blocked_style + "' data-row='" + i + "' data-column='" + j + "'></button>";
                str_layout += "<div id='btn-seat-layout-" + i + "-" + j + "' class='div-seat " + enabled_style + vegetabled_style + blocked_style + "' data-row='" + i + "' data-column='" + j + "'></div>";
            }
            str += "</div>";
            str_layout += "</div>";
        }

        $('#panel-seat-edit').eq(0).html(str);
        $('#panel-seat-layout').eq(0).html(str_layout);

        ADMIN_HALL.build_string();
    },

    build_string: function(){
        
        var data = [];

        for(var i = 0; i < ADMIN_HALL.seat_layout.length; i++) {
            for(var j = 0; j < ADMIN_HALL.seat_layout[i].length; j++) {    
                var item = {};
                item.row_number = $('#row_'+i).val();
                item.column_number = $('#col_'+j).val();
                item.id = $('#id_'+i+'-'+j).val();
                item.enabled = $('#btn-seat-'+i+'-'+j).val();
                item.vegetable = $('#vegetable-seat-'+i+'-'+j).val();
                item.blocked = $('#blocked-seat-'+i+'-'+j).val();
                data.push(item);
            }
        }

        $('#HallDetail').val(JSON.stringify(data));
    }

};