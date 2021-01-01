var COMMON = {
    model: '',
    schedule: '',
    staff_id: 0,
    member_id: 0,
    order_id: 0,
    movie_id: 0,
    movie_type_id: 0,
    token: '',
    main_payment_method: '',
    schedule_detail_id: 0,
    total_ticket_bought: 0,
    order: '',
    order_detail: '',    
    data_print: '',
    grand_total: 0,
    total_amount: 0,
    disc_member_percentage: 0,
    service_charge_percentage: 0,
    coupon_code : '',
    print_type: '',
    retry: 0,
    country_code_registration: '',
    phone_registration: '',
    registration_fee: 0,
    is_member_register: 0,
    url_schedule_detail: '',
    url_home: '',
    url_summary: '',
    url_payment: '',
    url_create_trans: '',
    url_do_login: '',
    url_do_payment: '',
    url_get_member: '',
    url_check_phone_registration: '',
    url_update_order_member: '',
    url_check_coupon: '',
    url_redeem_ecoupon: '',
    url_check_qrcode_validity: '',
    url_change_password: '',
    url_get_schedule: '',
    url_get_schedule_detail: '',
    url_hold_order: '',
    webroot: '',
    booked_seat: null,
    booked_seat_disability: null,
    payment_data: null,
    order_detail_coupon: [],
    item_bought: null,

    init_page: function() {
        $('.div-active-date').hover(() => {
            $('.div-option-date').removeClass('hidden');
        },() => {
            $('.div-option-date').addClass('hidden');
        });

        $('.btn-login').on('click', function(e){
            e.preventDefault();
            var username = $('#LoginUsername').val();
            var password = $('#LoginPassword').val();
    
    
            if ((username != '') && (password != '')) {
                var data = {
                    "username": username,
                    "password": password,
                    "model_code": navigator.userAgent
                };
    
                console.log('data : ' + JSON.stringify(data));
    
                $('.btn-login').attr('disabled', true);
                COMMON.call_ajax({
                    url: COMMON.url_do_login,
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    success: function(json) {
                        console.log('json value : ' + JSON.stringify(json));
                        if(json.status == true){
                            console.log('on succeed');
                            $('.btn-login').attr('disabled', false);
                            window.location = COMMON.url_home;
                        }else{
                            alert(json.message);
                            $('.btn-login').attr('disabled', false);
                        }
                    },
                    error: function(json) {
                        console.log("Request time out, please try again in a moment");
                        $('.btn-login').attr('disabled', false);
                    }
                });
            } else {
                alert(COMMON.err_user_pass_wrong);
            }            
        });

        //schedulingpage : change date
        $('.div-option-date a').on('click', function(e){
            e.preventDefault();
            $( ".div-option-date a" ).each(function() {
                $(this).removeClass('active');
            });
            $(this).addClass('active');
            var target = $(this).data('target');
            let obj = JSON.parse(COMMON.schedule);
            //console.log('obj : ' + JSON.stringify(obj[target]));

            $('.date-active').html(obj[target].label);

            var html_str = '';

            if ($(this).hasClass('scheduling')) {
                /*
                if (obj[target].Schedule && obj[target].Schedule.length > 0) {
                    for(var i = 0; i < obj[target].Schedule.length; i++) {
                        var disabled = (obj[target].Schedule[i].status == 0) ? 'disabled' : '';

                        var is_movie_showing = false;
                        if (obj[target].Schedule[i].status != 0) {
                            is_movie_showing = true;
                            html_str += '<a href="' + COMMON.url_schedule_detail + '/index/' + obj[target].Schedule[i].id + '">';
                            html_str += '<div class="div-schedule-item content biggest black-brown ' + disabled + '"> <div class="div-schedule-item-cover"></div>' + obj[target].Schedule[i].time + '</div>';
                            html_str += '</a>';
                        }
                    }
                    if (!is_movie_showing) {
                        html_str += '<div> No Schedule </div>';
                    }
                }
                $('.div-schedule-container').html(html_str);
                */
                if (COMMON.movie_id > 0 && COMMON.movie_type_id > 0) {
                    COMMON.get_schedule_detail(target);
                } else {
                    alert('data_invalid');
                }
            } else if ($(this).hasClass('ticketing')) {
                COMMON.get_schedule(target);
            }
            $('.div-option-date').addClass('hidden');
        });

        $('.link-schedule-detail').on('click', function(e){
            var id = $(this).data('id');
            var total_schedule = $(this).data('total_schedule');

            if (id == 0 || total_schedule <= 0 ) {
                alert("this move doesnt have schedule yet");
                e.preventDefault();
            }
        }); 
        

        $('.btn-pos-seat').on('click', function(e){
            e.preventDefault();
            if ($(this).hasClass('enabled') && !$(this).hasClass('blocked')) {
                if ($(this).hasClass('sold') || $(this).hasClass('not-for-sell')) {
                    alert('This seat is taken, please choose another seat.');
                } else {
                    var id = $(this).data('id');
                    var is_disability = $(this).data('disability');

                    if ($(this).hasClass('selected')) {
                        $(this).removeClass('selected');
                        var index = COMMON.booked_seat.indexOf(id);
                        COMMON.booked_seat.splice(index, 1);

                        if (is_disability == 1) {
                            index = COMMON.booked_seat_disability.indexOf(id);
                            COMMON.booked_seat_disability.splice(index, 1);
                        }

                    } else {
                        $(this).addClass('selected');
                        COMMON.booked_seat.push(id);

                        if (is_disability == 1) {
                            COMMON.booked_seat_disability.push(id);
                        }
                    }

                    if (COMMON.booked_seat.length > 0) {
                        $('.btn-pos-submit').removeClass('disabled');
                    } else {
                        $('.btn-pos-submit').addClass('disabled');
                    }

                    console.log('booked disability : ' + JSON.stringify(COMMON.booked_seat_disability));
                }
            }
        });

        $('.btn-pos-submit').on('click', function(e){
            e.preventDefault();
            if (!$(this).hasClass('disabled')) {
                $( ".div-payment-input input" ).each(function( index ) {
                    $(this).val('0');
                });

                $('.disability-ticket-type').val(COMMON.booked_seat_disability.length);
                $('.main-ticket-type').val(COMMON.booked_seat.length - COMMON.booked_seat_disability.length);

                var value_total = 0;
                $( ".div-payment-input input" ).each(function( index ) {
                    var myvalue = $(this).val();
                    myvalue = (isNaN(myvalue)) ? 0 : (myvalue * 1);
                    value_total += myvalue;
                });
                $('.div-dialog-footer .div-summary').html(value_total + ' of ' + COMMON.booked_seat.length + ' ticket(s)');

                $('.dialog-ticket-type').removeClass('hidden');
            }
        }); 

        $('.btn-checkout').on('click', function(e){
            e.preventDefault();
            var value_total = 0;
            $( ".div-payment-input input" ).each(function( index ) {
                var myvalue = $(this).val();
                myvalue = (isNaN(myvalue)) ? 0 : (myvalue * 1);
                value_total += myvalue;
            });

            if (COMMON.booked_seat.length > value_total) {
                alert('You need to choose ticket type for all ticket bought.');
                return false;
            }

            COMMON.create_trans();
        }); 

        $('.btn-summary-checkout').on('click', function(e){
            COMMON.update_member();

            /*
            if (COMMON.member_id > 0) {
                COMMON.update_member();
            } else {
                window.location = COMMON.url_payment+'/index/order/'+COMMON.order_id;
            }
            */
        }); 

        $('.div-payment-item-box a').on('click', function(e){
            var id = $(this).data('id');
            var value = $(this).data('value');
            var type = $(this).data('type');

            if (!$(this).hasClass('disabled')) {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                    var index = COMMON.payment_data[type].findIndex(obj => obj.id === id);
                    COMMON.payment_data[type].splice(index, 1);

                    //hide the detail coupon number box
                    $('#coupon-number_' + id).addClass('hidden');
                    if ((!COMMON.payment_data[2] || COMMON.payment_data[2].length <= 0) &&
                        (!COMMON.payment_data[3] || COMMON.payment_data[3].length <= 0)) {
                        $('.div-payment-number').addClass('hidden');
                    }
                    COMMON.assign_coupon_and_calculate_grand_total(type);
                } else {
                    if (type == 1){

                        $( ".div-payment-container a.type_1.selected" ).each(function( index ) {
                            $(this).removeClass('selected');
                        });

                        COMMON.main_payment_method = $(this).find(".title").html();
                        if (COMMON.payment_data[type]) {
                            COMMON.payment_data[type].splice(0, 1);
                        }
                    } else {
                        //show the detail coupon number box

                        $('.div-payment-number').removeClass('hidden');
                        $('#coupon-number_' + id).removeClass('hidden');
                        $('#coupon-number_' + id).find('input').focus();
                    }

                    $(this).addClass('selected');
                    if (!COMMON.payment_data[type]) {
                        COMMON.payment_data[type] = [];
                    }
                    COMMON.payment_data[type].push({'id' : id, 'type' : type, 'value' : value, 'amount':0, 'number' : []});
                    if (type == 1){
                        COMMON.assign_coupon_and_calculate_grand_total(type);
                    }
                }

                if (COMMON.payment_data.length > 0) {
                    $('.btn-pay').removeClass('disabled');
                } else {
                    $('.btn-pay').addClass('disabled');
                }

                console.log('payment data : ' + JSON.stringify(COMMON.payment_data));
            }
        }); 

        $('.div-coupon-number-input input').on('keyup', function(e){
            var myvalue = $(this).val();
            if (myvalue.trim() != '') {
                $(this).parent().children('.btn-submit-number').removeClass('disabled');
            } else {
                $(this).parent().children('.btn-submit-number').addClass('disabled');
            }
        }); 

        $('.btn-submit-number').on('click', function(e){
            var id = $(this).data('id');
            var type = $(this).data('type');
            var value = $('#coupon-number_'+id+' input').val();
            if (value.trim() != "") {
                //if this is exchange ticket then check the ticket number and 
                //check to make sure that the exchange ticket not exceed the ticket it self
                var index = COMMON.payment_data[type].findIndex(obj => obj.id === id);

                var valid = true;
                if (type == 3 && COMMON.model == 'Order') {
                    var total_ticket_number = 0;
                    for(var i = 0; i < COMMON.payment_data[type].length; i++) {
                        total_ticket_number += COMMON.payment_data[type][i].number.length;
                    }
                    
                    valid = (COMMON.total_ticket_bought > total_ticket_number) ? true : false;
                }

                if (valid) {
                    COMMON.payment_data[type][index].number.push(value.trim());
                    console.log('payment data : ' + JSON.stringify(COMMON.payment_data));

                    COMMON.draw_coupon_number(id, type, COMMON.payment_data[type][index].number);
                    $('#coupon-number_'+id+' .div-coupon-number-list').removeClass('middle');
                    $('#coupon-number_'+id+' .div-coupon-number-empty').addClass('hidden');
                    $('#coupon-number_'+id+' ul').removeClass('hidden');
                    $('#coupon-number_'+id+' input').val('');

                    COMMON.assign_coupon_and_calculate_grand_total(type);
                } else {
                    alert('1 Exchange Ticket can only be used for 1 ticket');
                }
            } else {
                alert('coupon number can not be empty');
            }
        }); 

        $('.div-coupon-number-list ul').on("click", ".link-remove", function(e) {
            var id = $(this).data('id');
            var type = $(this).data('type');
            var value = $(this).data('value');

            console.log('im here');

            var index = COMMON.payment_data[type].findIndex(obj => obj.id === id);
            var index_detail = COMMON.payment_data[type][index].number.indexOf(value);
            COMMON.payment_data[type][index].number.splice(index_detail, 1);
            console.log('payment data : ' + JSON.stringify(COMMON.payment_data));

            COMMON.assign_coupon_and_calculate_grand_total(type);

            if (COMMON.payment_data[type][index].number.length > 0) {
                COMMON.draw_coupon_number(id, type, COMMON.payment_data[type][index].number);
            } else {
                $('#coupon-number_'+id+' .div-coupon-number-list').addClass('middle');
                $('#coupon-number_'+id+' .div-coupon-number-empty').removeClass('hidden');
                $('#coupon-number_'+id+' ul').addClass('hidden');
            };
        }); 

        $('.div-payment-input button').on('click', function(e){
            var id = $(this).data('id');
            var value = $('#txt-number_'+id).val();
            value = (isNaN(value)) ? 0 : (value * 1);

            var value_total = 0;
            $( ".div-payment-input input" ).each(function( index ) {
                var myvalue = $(this).val();
                myvalue = (isNaN(myvalue)) ? 0 : (myvalue * 1);
                value_total += myvalue;
            });

            if ($(this).hasClass('plus')) {
                if (value_total >= COMMON.booked_seat.length) {
                    value = value;
                } else {
                    value++;
                    value_total++;
                }
                $('#txt-number_'+id).val((value));
            } else if ($(this).hasClass('minus')) {
                if (value <= 0) {
                    value = 0;
                } else {
                    value--;
                    value_total--;
                }
                $('#txt-number_'+id).val((value));
            }

            $('.div-dialog-footer .div-summary').html(value_total + ' of ' + COMMON.booked_seat.length + ' ticket(s)');

        }); 

        $('.div-payment-input input').on('keyup', function(e){
            var value_total = 0;
            $( ".div-payment-input input" ).each(function( index ) {
                var myvalue = $(this).val();
                //myvalue = (isNaN(myvalue)) ? 0 : (myvalue * 1);

                if (isNaN(myvalue) || ((myvalue * 1) < 0)) {
                    myvalue = 0;
                    $(this).val(myvalue);
                } else {
                    myvalue = myvalue * 1;
                }

                value_total += myvalue;
            });

            var value = $(this).val();

            if (COMMON.booked_seat.length < value_total) {
                $(this).val(COMMON.booked_seat.length - (value_total - value));
                $('.div-dialog-footer .div-summary').html(COMMON.booked_seat.length + ' of ' + COMMON.booked_seat.length + ' ticket(s)');
            }   
        }); 

        $('.btn-set-member').on('click', function(e){
            if ($(this).hasClass('member-set')) {
                $(this).removeClass('member-set');
                $(this).html('MEMBER CODE');
                $('.div-discount-member .div-summary-item').addClass('hidden');
                COMMON.disc_member_percentage = 0;
                COMMON.draw_item_bought_list();
            } else {
                $('.edit-member-input').val('');
                $('.dialog-set-member').removeClass('hidden');
                $('.edit-member-input').focus();
            }
        }); 

        $('.btn-create-member').on('click', function(e){
            if ($(this).hasClass('member-set')) {
                $(this).removeClass('member-set');
                $(this).html('MEMBER CODE');
                $('.div-discount-member .div-summary-item').addClass('hidden');
                COMMON.disc_member_percentage = 0;
                COMMON.draw_item_bought_list();
            } else {
                $('.edit-country-code').val('+852');
                $('.edit-phone').val('');
                $('.dialog-create-member').removeClass('hidden');
                $('.edit-country-code').focus();
            }
        }); 

        $('.link-member-close').on('click', function(e){
            $('.dialog-set-member').addClass('hidden');
            $('.dialog-create-member').addClass('hidden');
        }); 

        $('.edit-member-input').on('change', function(e){
            COMMON.set_member();
        }); 

        $('.btn-register').on('click', function(e){
            //do member registration
            COMMON.check_phone_registration();
        }); 

        $('.div-ecoupon .edit-qrcode-input').on('change', function(e){
            COMMON.coupon_code = $(this).val();
            COMMON.check_coupon(COMMON.coupon_code);
        }); 


        $('.btn-remove-member').on('click', function(e){
            $('.div-member-name span').html('');
            $('.div-member-id').html('');

            $('.member-info-fullname').html('');
            $('.member-info-phone').html('');
            $('.member-info-expired_date').html('');

            $('.div-member-empty').removeClass('hidden');
            $('.btn-set-member').removeClass('hidden');
            $('.btn-create-member').removeClass('hidden');
            $('.btn-remove-member').addClass('hidden');
            $('.div-member-info').addClass('hidden');

            COMMON.member_id = 0;
            COMMON.disc_member_percentage = 0;

            COMMON.country_code_registration = '';
            COMMON.phone_registration = '';
            COMMON.is_member_register = 0;

            COMMON.registration_fee = 0;

            COMMON.calculate_total_summary();
            $('.div-discount-member .div-summary-item').addClass('hidden');
        }); 

        $('.btn-cancel').on('click', function(e){
            $('.div-dialog-container').addClass('hidden');
        }); 

        $('.dialog-payment-success .btn-close').on('click', function(e){
            window.location = COMMON.url_home;
        }); 

        $('.dialog-seat-unavailable .btn-close').on('click', function(e){
            window.location.reload();
        }); 

        $('.btn-close').on('click', function(e){
            $('.div-dialog-container').addClass('hidden');
        }); 

        $('.btn-pay').on('click', function(e){
            console.log('COMMON.grand_total : ' + COMMON.grand_total);
            console.log('COMMON.payment_data[1] : ' + JSON.stringify(COMMON.payment_data[1]));
            if (COMMON.grand_total > 0 && (!COMMON.payment_data[1] || COMMON.payment_data[1].length <= 0)) {
                alert('You have to settle all amount to complete the checkout');
            } else {
                var payment_method_code = (COMMON.main_payment_method == "") ? "COUPON" : COMMON.main_payment_method;
                $('.div-dialog-verification .content span').html(payment_method_code);
                $('.dialog-verification').removeClass('hidden');
            }
        }); 

        $('.btn-pay-print').on('click', function(e){
            if (!$(this).hasClass('disabled')) {

                var printer_name = $(this).data('printer_name');
                var printer_address = $(this).data('printer_address');
                var printer_port = $(this).data('printer_port');
                var printer_count = $(this).data('count');

                COMMON.do_payment(printer_name, printer_address, printer_port, printer_count);
            }
        }); 

        $('.btn-summary-print').on('click', function(e){
            var data_obj = JSON.parse(COMMON.data_print);

            var obj = data_obj.ticket;
            var obj_receipt =  data_obj.receipt;

            if (!$(this).hasClass('disabled')) {
                var printer_name = $(this).data('printer_name');
                var printer_address = $(this).data('printer_address');
                var printer_port = $(this).data('printer_port');
                var printer_count = $(this).data('count');

                TICKET_PRINTER.printer_name = printer_name;
                TICKET_PRINTER.printer_address = printer_address;
                TICKET_PRINTER.printer_port = printer_port;
                TICKET_PRINTER.printer_count = printer_count;

                $('.btn-summary-print').addClass('disabled');
                $('.btn-payprint-' + printer_count).html('Processing...');

                TICKET_PRINTER.initialize(COMMON.retry).then((result) => {
                    setTimeout(() => {
                        for(var i = 0; i < obj.length; i++) {
                            //obj i-th represent single ticket
                            TICKET_PRINTER.printTicket(obj[i]);
                            // alert('printing ... ticket ' + (i + 1));
                        }
                    }, 3000);
                }).then((result) => {
                    setTimeout(() => {
                        TICKET_PRINTER.printTicketReceipt( obj_receipt );
                        $('.dialog-payment-success').removeClass('hidden');
                    }, 5000);
                }).catch(function( err ){
                    setTimeout(() => {
                        // $('.dialog-payment-success').removeClass('hidden');
                        $('.btn-summary-print').removeClass('disabled');
                        alert("Printer is not detected");
                    }, 5000);
                });
            }
        }); 

        $('.btn-clear-all').on('click', function(e){
            COMMON.item_bought = [];
            $('.div-summary-list').html('');
            COMMON.draw_item_bought_list();
        }); 

        $('.div-item-list-container a').on('click', function(e){
            var id = $(this).data('id');
            var code = $(this).data('code');
            var price = $(this).data('price');

            COMMON.item_bought.push({'id' : id, 'code' : code, 'price' : price, 'qty' : 1});
            COMMON.draw_item_bought_list();

        }); 

        $('.btn-snack-checkout').on('click', function(e){
            if (COMMON.item_bought.length > 0) {
                COMMON.create_purchase_trans();
            } else {
                alert('Item bought can not be empty');
            }
            
        }); 
        
        $('.btn-redeem').on('click', function(e){
            $('.div-coupon').removeClass('hidden');
            $('.div-ecoupon').addClass('hidden');
            $('.dialog-scan-ecoupon-success').addClass('hidden');
        }); 
        
        $('.edit-qrcode-coupon-input').on('change', function(e){
            var physical_coupon_code = $(this).val();
            COMMON.redeem_ecoupon(physical_coupon_code);
        }); 

        $('.div-dialog-redeem-coupon-success .btn-exit').on('click', function(e){
            location.reload();
        }); 

        $('.div-ticket .edit-qrcode-input').on('change', function(e){
            COMMON.check_qrcode_validity($(this).val());
        }); 

        $('.div-summary-list').on("click", ".div-summary-item .btn-delete", function(e) {
            var index = $(this).data('index');
            COMMON.item_bought.splice(index, 1);
            COMMON.draw_item_bought_list();
        }); 

        $('.btn-cancel-change').on("click", function(e) {
            window.location = COMMON.url_home;
        }); 

        $('.btn-submit-change').on("click", function(e) {
            COMMON.do_change_password();
        }); 

        $('#btn-rotate').on('click', function(e){
            if ($('.div-seatingpage-subsection').hasClass('rotate')) {
                $('.div-seatingpage-subsection').removeClass('rotate');
            } else {
                $('.div-seatingpage-subsection').addClass('rotate');
            }
        }); 
        
        $('.link-reprint').on('click', function(e){
            e.preventDefault();
            var id = $(this).data('id');
            var inv_number = $(this).data('inv_number');
            COMMON.order_id = 0;
            COMMON.order_id = id;

            var label = '';
            if ($(this).hasClass('print-ticket')) {
                COMMON.print_type = 'print-ticket';
                label = 'ticket';
            } else if ($(this).hasClass('print-receipt')) {
                COMMON.print_type = 'print-receipt';
                label = 'receipt';
            }

            $('.div-dialog-reprint .content span.print_type').html(label);
            $('.div-dialog-reprint .content span.inv_number').html(inv_number);

            $('.div-dialog-reprint').removeClass('hidden');
        });

        $('.btn-reprint').on('click', function(e){
            var id = COMMON.order_id;

            var printType = COMMON.print_type;

            var printer_name = $(this).data('printer_name');
            var printer_address = $(this).data('printer_address');
            var printer_port = $(this).data('printer_port');
            var printer_count = $(this).data('count');

            COMMON.do_reprint(id, printType, printer_name, printer_address, printer_port, printer_count);
        });

        $('.link-void').on('click', function(e){
            e.preventDefault();
            var id = $(this).data('id');
            var inv_number = $(this).data('inv_number');

            COMMON.order_id = id;

            $('.div-dialog-void .content span').html(inv_number);

            $('.div-dialog-void').removeClass('hidden');
        });

        $('.btn-void').on('click', function(e){
            var id = COMMON.order_id;
            COMMON.order_id = 0;

            COMMON.do_void(id);
        });

        $('.btn-summary-hold').on('click', function(e){
            COMMON.do_hold_order();
        });

    },
    restore_order_trans: function() {
        if (COMMON.order_id > 0) {
            COMMON.booked_seat = [];            
            COMMON.booked_seat_disability = [];

            $( ".btn-pos-seat.selected" ).each(function() {
                var id = $(this).data('id');
                COMMON.booked_seat.push(id);

                if ($(this).hasClass('disability')) {
                    COMMON.booked_seat_disability.push(id);
                }                
            });

            if (COMMON.booked_seat.length > 0) {
                $('.btn-pos-submit').removeClass('disabled');
            } else {
                $('.btn-pos-submit').addClass('disabled');
            }
        }
    },
    restore_purchase_trans: function() {
        // console.log('COMMON.order_id : ' + COMMON.order_id);
        // console.log('COMMON.order : ' + COMMON.order);
        if (COMMON.order_id > 0) {
            var tmpobj = JSON.parse(COMMON.order);
            var objPurchase = tmpobj.Purchase;
            var objPurchaseDetail = tmpobj.PurchaseDetail;

            for(var i = 0; i < objPurchaseDetail.length; i++) {
                COMMON.item_bought.push({
                    'id' : objPurchaseDetail[i].item_id, 
                    'code' : objPurchaseDetail[i].Item.code, 
                    'price' : objPurchaseDetail[i].price, 
                    'qty' : 1});
            }

            if (objPurchase.member_id && (objPurchase.member_id > 0)) {
                COMMON.disc_member_percentage = objPurchase.discount_percentage;
                $('.div-discount-member .div-summary-item').removeClass('hidden');
                $('.btn-set-member').html('REMOVE MEMBER');
                $('.btn-set-member').addClass('member-set');
            }


            COMMON.draw_item_bought_list();
        }
    },
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
    create_trans: function() {
        var ticket_types = [];
        var tmp_disability = JSON.parse(JSON.stringify(COMMON.booked_seat_disability));
        var tmp_booked_seat = JSON.parse(JSON.stringify(COMMON.booked_seat));
        $.each($('.div-payment-input .txt-number.disability-ticket-type'), function(){
            var id = $(this).data('id');
            var value = $(this).val();
            value = (isNaN(value)) ? 0 : (value * 1);
            for (var i = 0; i < value; i ++) {
                var schedule_detail_layout_id = tmp_disability[0];
                ticket_types.push({'schedule_detail_layout_id': schedule_detail_layout_id, 'id' : id});
                tmp_disability.splice(0, 1);
                var index = tmp_booked_seat.indexOf(schedule_detail_layout_id);
                tmp_booked_seat.splice(index, 1);
            }
        });

        $.each($('.div-payment-input .txt-number'), function(){
            if (!$(this).hasClass('disability-ticket-type')) {
                var id = $(this).data('id');
                var value = $(this).val();
                value = (isNaN(value)) ? 0 : (value * 1);

                for (var i = 0; i < value; i ++) {
                    var schedule_detail_layout_id = tmp_booked_seat[0];
                    ticket_types.push({'schedule_detail_layout_id': schedule_detail_layout_id, 'id' : id});
                    tmp_booked_seat.splice(0, 1);
                }
            }
        });
        
        var data = {
            "order_id": COMMON.order_id,
            "ticket_type": ticket_types,
            "staff_id": COMMON.staff_id,
            "token": COMMON.token,
            "schedule_detail_id": COMMON.schedule_detail_id,
        };

        console.log('data : ' + JSON.stringify(data));
        console.log('COMMON.booked_seat : ' + JSON.stringify(COMMON.booked_seat));

        $('.btn-checkout').attr('disabled', true);

        COMMON.call_ajax({
            url: COMMON.url_create_trans,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                //console.log('json value : ' + JSON.stringify(json));
                if(json.status == true){
                    //console.log('return value : ' + JSON.stringify(json.params));
                    window.location = COMMON.url_summary+'/index/'+json.params.Order.id;
                }else{
                    if (json.params && json.params.error && json.params.error == 'seat_not_available') {
                        $('.dialog-seat-unavailable').removeClass('hidden');
                    } else {
                        alert(json.message);
                    }
                }
                $('.btn-checkout').attr('disabled', false);
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
                $('.btn-checkout').attr('disabled', false);
            }
        });
        
    },
    set_member: function() {

        var data = {
            "code": $('.edit-member-input').val(),
            "token": COMMON.token,
        };

        COMMON.call_ajax({
            url: COMMON.url_get_member,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                console.log('json value : ' + JSON.stringify(json));
                if(json.status == true){
                    var member = json.params.Member;

                    COMMON.disc_member_percentage = member.discount_member;
                    if ($('.dialog-set-member').hasClass('snackpage-member')) {
                        
                        COMMON.draw_item_bought_list();
                        $('.div-discount-member .div-summary-item').removeClass('hidden');
                        $('.btn-set-member').html('REMOVE MEMBER');
                        $('.btn-set-member').addClass('member-set');
                    } else {
                        COMMON.calculate_total_summary();
                        $('.div-discount-member .div-summary-item').removeClass('hidden');
                        $('.div-registration-member .div-summary-item').addClass('hidden');

                        //$('.div-member-name span').html(member.first_name);
                        $('.div-member-name span').html(member.name);
                        $('.div-member-id').html('ID : ' + member.code);

                        //$('.member-info-fullname').html(member.first_name + ' ' + member.last_name);
                        $('.member-info-fullname').html(member.name);
                        $('.member-info-phone').html(member.country_code + '-' + "****" + member.phone.substring(4));
                        $('.member-info-expired_date').html(member.expired_date_label);

                        $('.div-member-empty').addClass('hidden');
                        $('.btn-set-member').addClass('hidden');
                        $('.btn-create-member').addClass('hidden');
                        $('.btn-remove-member').removeClass('hidden');
                        $('.div-member-info').removeClass('hidden');

                        COMMON.country_code_registration = '';
                        COMMON.phone_registration = '';
                        COMMON.is_member_register = 0;
                    }
                    $('.dialog-set-member').addClass('hidden');
                    COMMON.member_id = member.id;
                }else{
                    alert(json.message);
                }
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
            }
        });
        
    },
    update_member: function() {
        //window.location = COMMON.url_payment+'/index/'+COMMON.order_id;

        var data = {
            "order_id": COMMON.order_id,
            "member_id": COMMON.member_id,

            "country_code": COMMON.country_code_registration,
            "phone": COMMON.phone_registration,
            "is_member_register": COMMON.is_member_register,

            "token": COMMON.token,
            "staff_id": COMMON.staff_id,
        };

        console.log('data : ' + JSON.stringify(data));


        COMMON.call_ajax({
            url: COMMON.url_update_order_member,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                console.log('json value : ' + JSON.stringify(json));
                if(json.status == true){
                    window.location = COMMON.url_payment+'/index/order/'+COMMON.order_id;
                }else{
                    alert(json.message);
                }
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
            }
        });
        
    },
    do_payment: function(printer_name, printer_address, printer_port, printer_count) {
        var data = {
            "order_id": COMMON.order_id,
            "token": COMMON.token,
            "staff_id": COMMON.staff_id,
            "payment_method" : COMMON.payment_data,
            "order_detail_coupon" : COMMON.order_detail_coupon,
        };

        if (COMMON.model == 'Purchase') {
            var data = {
                "purchase_id": COMMON.order_id,
                "token": COMMON.token,
                "staff_id": COMMON.staff_id,
                "payment_method" : COMMON.payment_data,
            };
        }

        $('.btn-pay-print').addClass('disabled');
        $('.btn-payprint-' + printer_count).html('Processing...');

        COMMON.call_ajax({
            url: COMMON.url_do_payment,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                console.log('json value : ' + JSON.stringify(json));
                if(json.status == true){
                    var obj = json.params.ticket;
                    var obj_receipt = json.params.receipt;

                    console.log('ticket : ' + JSON.stringify(obj));
                    console.log('receipt : ' + JSON.stringify(obj_receipt));

                    if (COMMON.model == 'Order') {

                        TICKET_PRINTER.printer_name = printer_name;
                        TICKET_PRINTER.printer_address = printer_address;
                        TICKET_PRINTER.printer_port = printer_port;
                        TICKET_PRINTER.printer_count = printer_count;

                        TICKET_PRINTER.initialize(COMMON.retry).then((result) => {
                            if (COMMON.main_payment_method == 'CASH') {
                                TICKET_PRINTER.kickDrawer();
                            }
                        }, (result) =>{
                            result.reject();
                        }).then((result) => {
                            setTimeout(() => {
                                for(var i = 0; i < obj.length; i++) {
                                    //obj i-th represent single ticket
                                    TICKET_PRINTER.printTicket(obj[i]);
                                    // alert('printing ... ticket ' + (i + 1));
                                }
                            }, 3000);
                        }).then((result) => {
                            setTimeout(() => {
                                TICKET_PRINTER.printTicketReceipt( obj_receipt );
                                $('.dialog-payment-success').removeClass('hidden');
                            }, 5000);
                        }).catch(function( err ){
                            setTimeout(() => {
                                $('.dialog-payment-success').removeClass('hidden');
                                alert("Printer is not detected");
                            }, 5000);
                        });
                    } else if (COMMON.model == 'Purchase') {

                        TICKET_PRINTER.printer_name = printer_name;
                        TICKET_PRINTER.printer_address = printer_address;
                        TICKET_PRINTER.printer_port = printer_port;
                        TICKET_PRINTER.printer_count = printer_count;

                        TICKET_PRINTER.initialize(COMMON.retry).then((result) => {
                                if (COMMON.main_payment_method == 'CASH') {
                                    TICKET_PRINTER.kickDrawer();
                                }
                            }, (result) =>{
                                result.reject();
                        }).then((result) => {
                            setTimeout(() => {
                                TICKET_PRINTER.printTuckshopReceipt( obj_receipt );
                                $('.dialog-payment-success').removeClass('hidden');
                            }, 5000);
                        }).catch(function( err ){
                            setTimeout(() => {
                                $('.dialog-payment-success').removeClass('hidden');
                                alert("Printer is not detected");
                            }, 5000);
                        });
                    }
                }else{
                    alert(json.message);
                    $('.btn-pay-print').removeClass('disabled');
                }
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
                $('.btn-pay-print').removeClass('disabled');
            }
        });

    },
    draw_coupon_number: function(id, type, array_number) {
        var html_str = '';

        for(var i = 0; i < array_number.length; i++) {
            html_str += '<li>';
            html_str += '<img src="' + COMMON.webroot + '/general/img-checked.png" class="img-checked">';
            html_str += '<div class="list-number content smallest light">Coupon Number' + (i+1) + '</div>';
            html_str += '<div class="coupon-number-content content smallest light">' + array_number[i] + '</div>';
            html_str += '<a class="link-remove" data-id="'+id+'" data-type="'+type+'" data-value="'+array_number[i]+'"><img src="' + COMMON.webroot + '/general/img-close.png" class="img-close"></a>';
            html_str += '</li>';
        }
        $('#coupon-number_'+id+' .div-coupon-number-list ul').html(html_str);
    },
    assign_coupon_and_calculate_grand_total: function(type) {
        //COMMON.payment_data[type][index].number

        /*
            if type == 3
                1. build the array for all the coupon number (do it for coupon with type = 3)
                2. build the array for order detail and then assign the coupon and at the same time, calculate the total amount
            calculate the total discount from coupon with type = 2
            and then calculate all and put the result in grand total
        */

        var objOrder = JSON.parse(COMMON.order);
        var objOrderDetail = JSON.parse(COMMON.order_detail);
        var ticket = [];
        var total_amount = objOrder.total_amount;
        var grand_total = 0;
        
        if (COMMON.model == 'Order') {
            if (COMMON.payment_data[3]) {
                for (var i = 0; i < COMMON.payment_data[3].length ; i++) {
                    for (var j = 0; j < COMMON.payment_data[3][i].number.length ; j++) {
                        ticket.push({
                            'id' : COMMON.payment_data[3][i].id, 
                            'type' : COMMON.payment_data[3][i].type, 
                            'value' : COMMON.payment_data[3][i].value, 
                            'number' : COMMON.payment_data[3][i].number[j]});
                    }
                }
            }

            total_amount = 0;
            COMMON.order_detail_coupon = [];
            var index = -1;
            for (var i = 0; i < objOrderDetail.length ; i++) {
                index++;
                var discount = 0;
                var subtotal = objOrderDetail[i].OrderDetail.subtotal * 1;
                var payment_method_id = 0;
                var tmp_type = 0;
                var value = 0;
                var number = '';
                if (index < ticket.length) {
                    discount = (ticket[index].value > 0) ? objOrderDetail[i].OrderDetail.subtotal - ticket[index].value : (objOrderDetail[i].OrderDetail.subtotal * 1);
                    subtotal = (ticket[index].value > 0) ? (ticket[index].value * 1) : 0;
                    payment_method_id = ticket[index].id;
                    tmp_type = ticket[index].type;
                    value = ticket[index].value;
                    number = ticket[index].number;
                }
                total_amount += subtotal;
                COMMON.order_detail_coupon.push({
                    'order_detail_id' : objOrderDetail[i].OrderDetail.id,
                    'price' : objOrderDetail[i].OrderDetail.price,
                    'service_charge' : objOrderDetail[i].OrderDetail.service_charge,
                    'discount' : discount,
                    'subtotal' : subtotal,
                    'payment_method_id' : payment_method_id,
                    'type' : tmp_type,
                    'value' : value,
                    'number' : number
                });
            }

            console.log('COMMON.order_detail_coupon : ' + JSON.stringify(COMMON.order_detail_coupon));
        }

        var discount_percentage = objOrder.discount_percentage * 1;
        var discount_amount = total_amount * (discount_percentage/100);
        var discount_coupon = 0;
        if (COMMON.payment_data[2]) {
            for (var i = 0; i < COMMON.payment_data[2].length ; i++) {
                discount_coupon += (COMMON.payment_data[2][i].value * COMMON.payment_data[2][i].number.length);
            }
        }
        discount_amount -= discount_coupon;
        grand_total = total_amount - discount_amount;
        grand_total = grand_total + (objOrder.registration_fee * 1);

        console.log('registration_fee : ' + objOrder.registration_fee);

        if (grand_total <= 0) {
            grand_total = 0;
        }
        COMMON.grand_total = grand_total;

        console.log('total_amount : ' + total_amount);
        console.log('discount_amount : ' + discount_amount);
        console.log('grand_total : ' + grand_total);

        if (COMMON.payment_data[1] && COMMON.payment_data[1].length > 0) {
            COMMON.payment_data[1][0].amount = grand_total.toFixed(1);
        }

        $('.div-amount-total').html('Total:&nbsp ' + grand_total.toFixed(1));



    },
    calculate_grand_total: function(type) {
        //COMMON.payment_data[type][index].number

        /*
            if type == 3
                1. build the array for all the coupon number (do it for coupon with type = 3)
                2. build the array for order detail and then assign the coupon and at the same time, calculate the total amount
            calculate the total discount from coupon with type = 2
            and then calculate all and put the result in grand total
        */

        var objOrder = JSON.parse(COMMON.order);
        var objOrderDetail = JSON.parse(COMMON.order_detail);
        var ticket = [];
        var total_amount = 0;
        var grand_total = 0;
        
        //if (type == 3) {
            if (COMMON.payment_data[3]) {
                for (var i = 0; i < COMMON.payment_data[3].length ; i++) {
                    for (var j = 0; j < COMMON.payment_data[3][i].number.length ; j++) {
                        ticket.push({
                            'id' : COMMON.payment_data[3][i].id, 
                            'type' : COMMON.payment_data[3][i].type, 
                            'value' : COMMON.payment_data[3][i].value, 
                            'number' : COMMON.payment_data[3][i].number[j]});
                    }
                }
            }

            total_amount = 0;
            COMMON.order_detail_coupon = [];
            var index = -1;
            for (var i = 0; i < objOrderDetail.length ; i++) {
                index++;
                var discount = 0;
                var subtotal = objOrderDetail[i].OrderDetail.subtotal * 1;
                var payment_method_id = 0;
                var tmp_type = 0;
                var value = 0;
                var number = '';
                if (index < ticket.length) {
                    discount = (ticket[index].value > 0) ? objOrderDetail[i].OrderDetail.subtotal - ticket[index].value : (objOrderDetail[i].OrderDetail.subtotal * 1);
                    subtotal = (ticket[index].value > 0) ? (ticket[index].value * 1) : 0;
                    payment_method_id = ticket[index].id;
                    tmp_type = ticket[index].type;
                    value = ticket[index].value;
                    number = ticket[index].number;
                }
                total_amount += subtotal;
                COMMON.order_detail_coupon.push({
                    'order_detail_id' : objOrderDetail[i].OrderDetail.id,
                    'price' : objOrderDetail[i].OrderDetail.price,
                    'service_charge' : objOrderDetail[i].OrderDetail.service_charge,
                    'discount' : discount,
                    'subtotal' : subtotal,
                    'payment_method_id' : payment_method_id,
                    'type' : tmp_type,
                    'value' : value,
                    'number' : number
                });
            }

            console.log('COMMON.order_detail_coupon : ' + JSON.stringify(COMMON.order_detail_coupon));
        //}

        var discount_percentage = objOrder.discount_percentage * 1;
        var discount_amount = total_amount * (discount_percentage/100);
        var discount_coupon = 0;
        if (COMMON.payment_data[2]) {
            for (var i = 0; i < COMMON.payment_data[2].length ; i++) {
                discount_coupon += (COMMON.payment_data[2][i].value * COMMON.payment_data[2][i].number.length);
            }
        }
        discount_amount -= discount_coupon;
        grand_total = total_amount - discount_amount;

        if (grand_total <= 0) {
            grand_total = 0;
        }
        COMMON.grand_total = grand_total;

        console.log('total_amount : ' + total_amount);
        console.log('discount_amount : ' + discount_amount);
        console.log('grand_total : ' + grand_total);

        if (COMMON.payment_data[1] && COMMON.payment_data[1].length > 0) {
            COMMON.payment_data[1][0].amount = grand_total.toFixed(2);
        }

        $('.div-amount-total').html('Total:&nbsp ' + grand_total.toFixed(2));



    },
    draw_item_bought_list: function () {
        var total = 0;
        var html_str = '';
        var total_service_charge = 0;
        for (var i = 0; i < COMMON.item_bought.length ; i++) {
            var price = COMMON.item_bought[i].price * 1;
            html_str += '<div class="div-summary-item content light smallest">';
            html_str += '<div class="div-movie-title">' + COMMON.item_bought[i].code + '</div>';
            html_str += '<div class="div-qty">' + COMMON.item_bought[i].qty + 'x</div>';
            html_str += '<div class="div-amount">HKD ' + price.toFixed(1) + '</div>';
            html_str += '<a class="btn-delete" data-index="' + i + '"><img src="' + COMMON.webroot + '/general/img-close.png"></a>';
            html_str += '</div>';
            var subtotal = (COMMON.item_bought[i].price * 1);
            total += subtotal;

            // var service_charge = subtotal * (COMMON.service_charge_percentage/100);
            // total_service_charge += (service_charge.toFixed(2) * 1);
        }

        var total_amount = total + total_service_charge;
        var disc_amount_tmp = total_amount * (COMMON.disc_member_percentage/100);
        var disc_amount = disc_amount_tmp.toFixed(1) * 1;
        var grand_total = total_amount - disc_amount;
        grand_total = grand_total.toFixed(2) * 1;

        if (COMMON.disc_member_percentage > 0) {
            html_str += '<div class="div-discount-member content light">';
            html_str += '<div class="div-summary-item content light smallest">';
            html_str += '<div class="div-movie-title">Member Discount</div>';
            html_str += '<div class="div-amount">HKD - </div>';
            html_str += '</div>';
            html_str += '</div>';
        }

        $('.div-summary-list').html(html_str);
        //$('.div-service-charge  .div-amount').html('HKD ' + total_service_charge);
        $('.div-discount-member  .div-amount').html('HKD - ' + disc_amount);
        $('.div-summary-amount .div-amount').html('<span>Total:</span>HKD ' + grand_total);

        /*
        if (total_service_charge > 0) {
            $('.div-service-charge  .div-amount').removeClass('hidden');
        } else {
            $('.div-service-charge  .div-amount').addClass('hidden');
        }
        */
    },
    create_purchase_trans: function () {
        var data = {
            "purchase_id": COMMON.order_id,
            "items_bought": COMMON.item_bought,
            "member_id": COMMON.member_id,
            "staff_id": COMMON.staff_id,
            "token": COMMON.token,
        };

        $('.btn-checkout').attr('disabled', true);

        COMMON.call_ajax({
            url: COMMON.url_create_trans,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                //console.log('json value : ' + JSON.stringify(json));
                if(json.status == true){
                    window.location = COMMON.url_payment+'/index/purchase/'+json.params.Purchase.id;
                }else{
                    alert(json.message);
                }
                $('.btn-checkout').attr('disabled', false);
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
                $('.btn-checkout').attr('disabled', false);
            }
        });        
    },
    calculate_total_summary: function() {
        console.log('grand total : ' + COMMON.grand_total);
        console.log('disc member : ' + COMMON.disc_member_percentage);
        
        var disc_amount = COMMON.total_amount * (COMMON.disc_member_percentage/100);
        var disc_amount = disc_amount.toFixed(2) * 1;
        var grand_total_tmp = COMMON.total_amount - disc_amount;

        if (COMMON.is_member_register > 0) {
            grand_total_tmp = grand_total_tmp + COMMON.registration_fee;
        }

        var grand_total = grand_total_tmp.toFixed(2) * 1;
        $('.div-discount-member  .div-amount').html('HKD - ' + disc_amount);
        $('.div-summary-amount .div-amount').html('<span>Total:</span>HKD ' + grand_total);
    },
    check_coupon: function(coupon_code) {
        var data = {
            "coupon_code": coupon_code,
            "staff_id": COMMON.staff_id,
            "token": COMMON.token,
        };

        $('.btn-checkout').attr('disabled', true);

        COMMON.call_ajax({
            url: COMMON.url_check_coupon,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                //console.log('json value : ' + JSON.stringify(json));
                if(json.status == true){
                    $('.dialog-scan-ecoupon-success').removeClass('hidden');
                }else{
                    alert(json.message);
                }
                $('.div-ecoupon .edit-qrcode-input').val('');
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
            }
        }); 
    },
    redeem_ecoupon: function(physical_coupon_code) {
        var data = {
            "physical_coupon_code": physical_coupon_code,
            "coupon_code": COMMON.coupon_code,
            "staff_id": COMMON.staff_id,
            "token": COMMON.token,
        };

        $('.btn-checkout').attr('disabled', true);

        COMMON.call_ajax({
            url: COMMON.url_redeem_ecoupon,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                //console.log('json value : ' + JSON.stringify(json));
                if(json.status == true){
                    $('.dialog-redeem-coupon-success').removeClass('hidden');
                    $('.dialog-redeem-coupon-success .coupon-code').html(COMMON.coupon_code);
                }else{
                    alert(json.message);
                }
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
            }
        }); 
    },
    check_qrcode_validity: function(qrcode_str) {
        var data = {
            "qrcode_str": qrcode_str,
            "staff_id": COMMON.staff_id,
            "token": COMMON.token,
        };

        $('.btn-checkout').attr('disabled', true);

        COMMON.call_ajax({
            url: COMMON.url_check_qrcode_validity,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                //console.log('json value : ' + JSON.stringify(json));
                if(json.status == true){
                    var data_trans = json.params;
                    console.log('data trans : ' + JSON.stringify(data_trans));
                    if (data_trans.status == 3) {
                        //status = paid
                        window.location = COMMON.url_summary+'/index/'+data_trans.id;
                    } else if (data_trans.status == 2) {
                        //status = not-paid
                        window.location = COMMON.url_payment+'/index/'+data_trans.type+'/'+data_trans.id;
                    }
                    
                }else{
                    alert(json.message);
                }
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
            }
        }); 
    },
    do_change_password: function() {
        var new_password = $('#new-pass').val();
        var confirm_password = $('#confirm-pass').val();
        if (new_password == confirm_password) {
            var data = {
                "current_password": $('#cur-pass').val(),
                "new_password": new_password,
                "staff_id": COMMON.staff_id,
                "token": COMMON.token,
            };

            $('.btn-submit-change').attr('disabled', true);

            COMMON.call_ajax({
                url: COMMON.url_change_password,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(json) {
                    // console.log('json value : ' + JSON.stringify(json));
                    if(json.status == true){
                        alert('password has change ...');
                        window.location = COMMON.url_home;
                    }else{
                        alert(json.message);
                        $('.btn-submit-change').attr('disabled', false);
                    }
                },
                error: function(json) {
                    console.log("Request time out, please try again in a moment");
                    $('.btn-submit-change').attr('disabled', false);
                }
            }); 
        } else {
            alert('New password is not same with confirm password.');
        }
    },
    do_reprint: function(order_id, printType, printer_name, printer_address, printer_port, printer_count) {
        var data = {
            "trans_id": order_id,
            "staff_id": COMMON.staff_id,
            "token": COMMON.token,
        };

        TICKET_PRINTER.printer_name = printer_name;
        TICKET_PRINTER.printer_address = printer_address;
        TICKET_PRINTER.printer_port = printer_port;
        TICKET_PRINTER.printer_count = printer_count;

        $('.btn-reprint').addClass('disabled');
        var original_text = $('.btn-payprint-' + printer_count).html();

        if (printType == 'print-ticket' || printType == 'print-receipt') {
            $('.btn-payprint-' + printer_count).html('Processing...');
            COMMON.call_ajax({
                url: COMMON.url_get_data_reprint,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(json) {
                    if(json.status == true){
                        var obj = json.params.ticket;
                        var obj_receipt = json.params.receipt;

                        console.log('obj : ' + JSON.stringify(obj));
                        console.log('obj_receipt : ' + JSON.stringify(obj_receipt));

                        TICKET_PRINTER.initialize(COMMON.retry).then((result) => {

                            if (printType == 'print-ticket') {
                                setTimeout(() => {
                                    for(var i = 0; i < obj.length; i++) {
                                        //obj i-th represent single ticket
                                        TICKET_PRINTER.printTicket(obj[i]);
                                    }
                                    $('.btn-payprint-' + printer_count).html(original_text);
                                    $('.btn-reprint').removeClass('disabled');
                                    $('.div-dialog-reprint').addClass('hidden');
                                }, 3000);
                            } else {
                                setTimeout(() => {
                                    TICKET_PRINTER.printTicketReceipt( obj_receipt );
                                    $('.btn-payprint-' + printer_count).html(original_text);
                                    $('.btn-reprint').removeClass('disabled');
                                    $('.div-dialog-reprint').addClass('hidden');
                                }, 3000);
                            }
                            
                        }).catch(function( err ){
                            setTimeout(() => {
                                // $('.dialog-payment-success').removeClass('hidden');
                                $('.btn-payprint-' + printer_count).html(original_text);
                                $('.btn-reprint').removeClass('disabled');
                                alert("Printer is not detected");
                            }, 3000);
                        });

                    }else{
                        alert(json.message);
                        $('.btn-payprint-' + printer_count).html(original_text);
                        $('.btn-reprint').removeClass('disabled');
                    }
                },
                error: function(json) {
                    console.log("Request time out, please try again in a moment");
                    $('.btn-payprint-' + printer_count).html(original_text);
                    $('.btn-reprint').removeClass('disabled');
                }
            }); 
        } else {
            alert('print type invalid');
        }
    },
    do_void: function(order_id) {
        var data = {
            "trans_id": order_id,
            "staff_id": COMMON.staff_id,
            "token": COMMON.token,
        };

        $('.btn-void').addClass('disabled');

        COMMON.call_ajax({
            url: COMMON.url_void,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                if(json.status == true){
                    alert('Transaction has been void');
                    location.reload();
                }else{
                    alert(json.message);
                    $('.btn-void').removeClass('disabled');
                    $('.div-dialog-void').addClass('hidden');
                }
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
                $('.btn-void').removeClass('disabled');
                $('.div-dialog-void').addClass('hidden');
            }
        }); 
    },
    get_schedule: function(parm_date) {
        var data = {
            "parm_date": parm_date,
            "staff_id": COMMON.staff_id,
            "token": COMMON.token,
        };

        COMMON.call_ajax({
            url: COMMON.url_get_schedule,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                if(json.status == true){
                    obj = json.params;

                    console.log('obj : ' + JSON.stringify(obj));

                    html_str = '';
                    if (obj.length > 0) {
                        for(var i = 0; i < obj.length; i++) {                        
                            html_str += '<div class="div-movie-item">';
                            html_str += '<a href="' + COMMON.url_schedule_detail + '/index/' + obj[i].id + '/' + parm_date + '" class="link-schedule-detail" data-id="' + obj[i].id + '" >';
                            html_str += '<img src="' + COMMON.webroot + obj[i].poster + '" class="img-poster">';
                            html_str += '<div class="div-movie-title content smallest">' + obj[i].title + ' (' + obj[i].rating + ')</div>';
                            html_str += '<div class="div-movie-type content smallest">' + obj[i].movie_type + '</div>';
                            html_str += '</a>';
                            html_str += '</div>';
                        }
                    } else {
                        html_str = '<div class="content smallest"> No schedule for ' + $('.date-active').html() + ' </div>';
                    }
                    $('.div-movie-list-container').html(html_str);

                }else{
                    alert(json.message);
                }
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
            }
        }); 

    },
    get_schedule_detail: function(parm_date) {
        var data = {
            "parm_date": parm_date,
            "movie_type_id": COMMON.movie_type_id,
            "movie_id": COMMON.movie_id,
            "staff_id": COMMON.staff_id,
            "token": COMMON.token,
        };

        COMMON.call_ajax({
            url: COMMON.url_get_schedule_detail,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                if(json.status == true){
                    obj = json.params;

                    console.log('obj : ' + JSON.stringify(obj));

                    html_str = '';
                    if (obj.length > 0) {
                        for(var i = 0; i < obj.length; i++) {
                            html_str += '<a href="' + COMMON.url_schedule_detail + '/index/' + obj[i].id + '">';
                            html_str += '<div class="div-schedule-item content biggest black-brown"> <div class="div-schedule-item-cover"></div>' + obj[i].time + '<div class="div-hall-code content light super-small">' + obj[i].hall + '</div></div>';
                            html_str += '</a>';
                        }
                    } else {
                        html_str = '<div class="content smallest"> No schedule for ' + $('.date-active').html() + ' </div>';
                    }
                    $('.div-schedule-container').html(html_str);
                }else{
                    alert(json.message);
                }
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
            }
        }); 

    },
    do_hold_order: function() {
        var data = {
            "order_id": COMMON.order_id,
            "remark": $('.txt-remarks').val(),
            "staff_id": COMMON.staff_id,
            "token": COMMON.token,
        };

        console.log('data : ' + JSON.stringify(data));

        COMMON.call_ajax({
            url: COMMON.url_hold_order,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                if(json.status == true){

                    $('.dialog-payment-success').removeClass('hidden');
                }else{
                    alert(json.message);
                }
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
            }
        }); 
    },
    check_phone_registration: function() {

        var data = {
            "country_code": $('.edit-country-code').val(),
            "phone": $('.edit-phone').val(),

            "token": COMMON.token,
            "staff_id": COMMON.staff_id,            
        };

        COMMON.registration_fee = 0;

        COMMON.call_ajax({
            url: COMMON.url_check_phone_registration,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function(json) {
                console.log('json value : ' + JSON.stringify(json));
                if(json.status == true){
                    var result = json.params;
 
                    COMMON.member_id = 0;
                    COMMON.country_code_registration = $('.edit-country-code').val();
                    COMMON.phone_registration = $('.edit-phone').val();
                    COMMON.is_member_register = 1;
        
                    COMMON.registration_fee = result.registration_fee * 1;

                    COMMON.disc_member_percentage = result.discount_member;
                    COMMON.calculate_total_summary();

                    $('.dialog-create-member').addClass('hidden');
                    $('.edit-country-code').val('');
                    $('.edit-phone').val('');

                    $('.div-discount-member .div-summary-item').removeClass('hidden');
                    $('.div-registration-amount').html('HKD ' + COMMON.registration_fee.toFixed(1));
                    


                    $('.div-member-name span').html('NEW MEMBER');
                    $('.div-member-id').html('ID : ' + '');
        
                    $('.member-info-fullname').html('NEW MEMBER');
                    $('.member-info-phone').html(COMMON.country_code_registration + '-' + "****" + COMMON.phone_registration.substring(4));
                    $('.member-info-expired_date').html('');
        
                    $('.div-member-empty').addClass('hidden');
                    $('.btn-set-member').addClass('hidden');
                    $('.btn-create-member').addClass('hidden');
                    $('.btn-remove-member').removeClass('hidden');
                    $('.div-member-info').removeClass('hidden');                 

                }else{
                    alert(json.message);
                }
            },
            error: function(json) {
                console.log("Request time out, please try again in a moment");
            }
        });
        
    },
}