
companyWiseFeedSaleCalculation();

$('.companyWiseFeedSale_section').on('keypress','.companyWiseFeedSale_body_section input[type=number]',function (e) {
    if (e.which === 13) {
        e.preventDefault();
        var $canfocus = $('.companyWiseFeedSale_body_section :focusable');
        var index = $canfocus.index(this)+1;
        if (index >= $canfocus.length){
            index = 0;
        }
        $canfocus.eq(index).focus().select();
    }
    companyWiseFeedSaleDataInsertUsingAjax($(this));
});
$('.companyWiseFeedSale_section').on('blur','.companyWiseFeedSale_body_section input[type=number]',function (e) {
    companyWiseFeedSaleDataInsertUsingAjax($(this))
});


function companyWiseFeedSaleDataInsertUsingAjax(element) {

    var result = [];
    var total=0;
    var parentElement = $(element).closest('tr');
    parentElement.find('.productAndQty').each (function (index, td) {
            var value =$(td).val();
            var productId =$(td).attr('data-product-id');
            if (!value) value = 0;
            total += parseFloat(value);
            result[productId] = value;
        });

    var entityId=element.attr('data-entity-id');
    var dataMetaValue=result;

    if(entityId===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('company_wise_feed_sale_edit',{'id':entityId}),
        type   : 'post',
        data   : {
            'dataMetaValue':dataMetaValue
        },
        dataType : 'json',
        success: function(response){
            parentElement.find('.line_total').text(total);

            companyWiseFeedSaleCalculation();
        }
    });

}


function companyWiseFeedSaleCalculation() {


    $('.companyWiseFeedSale_table').each(function(index, table) {
        var result = [];
        $(table).find('tbody > tr').each(function(index, tr) {
            var total=0;
            $(tr).find('.productAndQty').each (function (index, td) {
                var productId =$(td).attr('data-product-id');
                var value =$(td).val();
                if (!value) value = 0;
                if(!result[productId]) result[productId] = 0;
                result[productId] += parseFloat(value);
            });

        });

        $(table).find('tfoot > tr').each(function(index, tr) {

            var grandTotal = 0;

            $(tr).find('.col_total').each (function (index, td) {
                var productId =$(td).attr('data-product-id');
                grandTotal+=result[productId];
                $(tr).find('.col_total_'+productId).text(parseFloat(result[productId]).toFixed(2));
            });

            $(tr).find('.grand_total').text(parseFloat(grandTotal).toFixed(2));
        });

        $(table).find('tbody > tr').each(function(index, tr) {
            var total= $(table).find('tfoot > tr').find('.grand_total').text();
            $(tr).find('.line_total').each (function (index, td) {
                var value =$(td).text();
                if (!value) value = 0;
                value = parseFloat(value).toFixed(4);
                total = parseFloat(total).toFixed(4);
                var calculationValue = (value*100)/total;

                $(tr).find('.lineMarketShare').text(parseFloat(calculationValue).toFixed(2));
            });

        });
    });


}