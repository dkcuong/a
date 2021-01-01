$(document).ready(function() {
    // init input check top is checkall
    $('input.chk-all-schedule').on('click', function(event){
        if($(this).is(":checked")){
            $(this).closest('table').find('.chk-schedule-id').prop('checked', true);
        }else{
            $(this).closest('table').find('.chk-schedule-id').prop('checked', false);
        }
    });
});