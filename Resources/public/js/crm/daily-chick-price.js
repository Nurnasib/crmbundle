
$(document).on('keypress','.dailyChickPriceDetail input[type=number]',function (e) {
    if (e.which === 13) {

        e.preventDefault();
        var $canfocus = $('.dailyChickPriceDetail :focusable');
        var index = $canfocus.index(this) + 1;
        if (index >= $canfocus.length){
            index = 0;
        }

        $canfocus.eq(index).focus().select();

        dailyChickPriceDetailDataInsertUsingAjax($(this));
    }
});

function dailyChickPriceDetailDataInsertUsingAjax(element) {

    var parentElement = element.closest('tr');
    var thisElementValue = element.val();
    var daily_chick_price_details_id = element.attr('data-id');

    if(daily_chick_price_details_id===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('day_old_chick_price_update',{'id':daily_chick_price_details_id}),
        type   : 'post',
        data   : {
            'price':thisElementValue
        },
        dataType : 'json',
        success: function(response){
            // console.log(response.data);
            if(response.status===200){
                element.val(response.price);
            }
        }
    });
}


$('.datePicker').datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: "dd-mm-yy",
    yearRange: "-90:+00",
    showOn: "both",
    showButtonPanel: true,
    buttonImage: "/assets/images/icon-calendar-green.png",
    buttonImageOnly: true
});

$(document).on('change', '.agent', function(e) {
    var element = $(this);
    var id = $(this).val();
    element.closest('tr').find('.agent_address').val('');
    element.closest('tr').find('.agent_mobile').val('');
    if(id==''){
        return false;
    }
    var url = Routing.generate('get_core_agent_find_ajax',{'id':id});
    $.get(url, function(data){
        element.closest('tr').find('.agent_address').val(data[0]['address']);
        element.closest('tr').find('.agent_mobile').val(data[0]['mobile']);
    });

});


