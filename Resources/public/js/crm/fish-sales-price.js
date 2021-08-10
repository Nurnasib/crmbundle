$('.fishSalesPrice_section').on('keypress','.fishSalesPrice_body_section input[type=number]',function (e) {
    if (e.which === 13) {
        e.preventDefault();
        var $canfocus = $('.fishSalesPrice_body_section :focusable');
        var index = $canfocus.index(this)+1;
        if (index >= $canfocus.length){
            index = 0;
        }
        $canfocus.eq(index).focus().select();
    }
    fishSalesPriceDataUpdateUsingAjax($(this));
});
$('.fishSalesPrice_section').on('blur','.fishSalesPrice_body_section input[type=number]',function (e) {
    fishSalesPriceDataUpdateUsingAjax($(this));
});


function fishSalesPriceDataUpdateUsingAjax(element) {

    var entityId=element.attr('data-id');
    var price=element.val();

    if(entityId==''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('fish_sales_price_data_update',{'id':entityId}),
        type   : 'post',
        data   : {
            'price':price
        },
        dataType : 'json',
        success: function(response){
            console.log(response);
        }
    });

}