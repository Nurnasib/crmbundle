// companyWiseFeedSaleReportCalculation();
// lab_service_section
$('.companyWiseFeedSale_section').on('keypress','.companyWiseFeedSale_body_section input[type=number]',function (e) {
    if (e.which === 13) {
        e.preventDefault();
        var $canfocus = $('.companyWiseFeedSale_body_section :focusable');
        var index = $canfocus.index(this)+1;
        if (index >= $canfocus.length){
            index = 0;
        }
        $canfocus.eq(index).focus().select();
        // companyWiseFeedSaleDataInsertUsingAjax($(this))
    }
    companyWiseFeedSaleReportCalculation($(this));
});
$('.lab_service_section').on('blur','.active .lab_service_table input[type=number]',function (e) {
    companyWiseFeedSaleDataInsertUsingAjax($(this))
});

$('.lab_tab').on('click',function () {
    companyWiseFeedSaleReportCalculation()
});



function companyWiseFeedSaleDataInsertUsingAjax(element) {
    var entityId=element.attr('data-entity-id');
    var dataMetaKey=element.attr('data-meta-key');
    var dataMetaValue=element.val();

    if(entityId===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('lab_service_edit',{'id':entityId}),
        type   : 'post',
        data   : {
            'dataMetaKey':dataMetaKey,
            'dataMetaValue':dataMetaValue
        },
        dataType : 'json',
        success: function(response){
            // parentElement.find('.eggProduction').text(response.eggProduction);
            element.val(response.value);
            companyWiseFeedSaleReportCalculation();
        }
    });

}

function companyWiseFeedSaleReportCalculation(element) {

    var result = [];
        var total=0;
        var entityId='';

        var parentElement = $(element).closest('tr');
    parentElement.find('.productAndQty').each (function (index, td) {
            var value =$(td).val();
            var entityId = $(td).attr('data-entity-id');
            var productId =$(td).attr('data-product-id');
            if (!value) value = 0;
            total += parseFloat(value);
            // if(!result[index]) result[index] = 0;
            // result[index] += parseInt($(val).text());
            result[productId] = value;
        });
    parentElement.find('.line_total').text(total);
console.log(entityId);
// console.log(total);


    console.log(result);
}