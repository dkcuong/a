$(document).ready(function() {
    $('.my-box').each(function(i,v){
        var box = $(this).children(".box").first();
     
        // Find the body and the footer
        var bf = box.find(".box-body, .box-footer");
    
        if (!box.hasClass("collapsed-box")) {
            box.addClass("collapsed-box");

            // Convert minus into plus
            $(this).children(".fa-minus").removeClass("fa-minus").addClass("fa-plus");
            bf.slideUp();
        } else {
            box.removeClass("collapsed-box");

            // Convert plus into minus
            $(this).children(".fa-plus").removeClass("fa-plus").addClass("fa-minus");
            bf.slideDown();
        }

        var is_all = true;
        $.each($(this).find('.chk-permission-id'), function(key, item){
            if(!$(item).is(":checked")){
                is_all = false;
                return;
            }
        });

        if(is_all){
            $(this).find('.chk-all-permission').prop('checked', true);
        }
    });

    // collapse -> +
    $('#collapse').click(function(){
        $('.my-box').each(function(i,v){
            var box = $(this).children(".box").first();
         
            // Find the body and the footer
            var bf = box.find(".box-body, .box-footer");
        
            if (!box.hasClass("collapsed-box")) 
            {
                box.addClass("collapsed-box");
                var plus = box.find('.fa-minus');
                plus.removeClass("fa-minus").addClass("fa-plus");
                bf.slideUp();
            }

        });
    });

    // expand -> -
    $('#expand').click(function(){
        $('.my-box').each(function(i,v){
            var box = $(this).children(".box").first();
         
            // Find the body and the footer
            var bf = box.find(".box-body, .box-footer");
        
            if (box.hasClass("collapsed-box")) 
            {
                box.removeClass("collapsed-box");
                var plus = box.find('.fa-plus');
                plus.removeClass("fa-plus").addClass("fa-minus");
                bf.slideDown();
            }
        });
    });

    // init input check top is checkall
    $('input.chk-all-permission').on('click', function(event){
        if($(this).is(":checked")){
            $(this).closest('table').find('.chk-permission-id').prop('checked', true);
        }else{
            $(this).closest('table').find('.chk-permission-id').prop('checked', false);
        }
    });

    $('input.chk-permission-id').on('click', function(event){
        if($(this).is(':checked')){
            var is_all = true;
            $.each($(this).closest('table').find('.chk-permission-id'), function(key, item){
                if(!$(item).is(":checked")){
                    is_all = false;
                    return;
                }
            });
            if(is_all){
                $(this).closest('table').find('.chk-all-permission').prop('checked', true);
            }
        }else{
            $(this).closest('table').find('.chk-all-permission').prop('checked', false);
        }
    });
});