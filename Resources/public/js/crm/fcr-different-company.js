fcrDifferentCompanyReportCalculation();

$('.fcr_different_company_section').on('keypress','.fcr_different_company_table input[type=number]',function (e) {
    if (e.which === 13) {
        e.preventDefault();
        var $canfocus = $('.fcr_different_company_table :focusable');
        var index = $canfocus.index(this)+1;
        if (index >= $canfocus.length){
            index = 0;
        }
        $canfocus.eq(index).focus().select();
        fcrDifferentCompanyDataInsertUsingAjax($(this))
    }
});
$('.fcr_different_company_section').on('blur','.fcr_different_company_table input[type=number]',function (e) {
    fcrDifferentCompanyDataInsertUsingAjax($(this))
});


function fcrDifferentCompanyDataInsertUsingAjax(element) {
    var entityId=element.attr('data-entity-id');
    var dataMetaKey=element.attr('data-meta-key');
    var dataMetaValue=element.val();

    if(entityId===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('fcr_different_company_edit',{'id':entityId}),
        type   : 'post',
        data   : {
            'dataMetaKey':dataMetaKey,
            'dataMetaValue':dataMetaValue
        },
        dataType : 'json',
        success: function(response){
            // parentElement.find('.eggProduction').text(response.eggProduction);
            element.val(response.value);
            fcrDifferentCompanyReportCalculation();
        }
    });

}



function fcrDifferentCompanyReportCalculation() {

    var result = [];
    $('.fcr_different_company_table tbody tr').each(function(index, tr) {
        var total=0;
        $(tr).find('td').each (function (index, td) {

            var value =$(td).find('.month_'+index).val();
            if (!value) value = 0;
            total += parseFloat(value);
            if(!result[index]) result[index] = 0;
            // result[index] += parseInt($(val).text());
            result[index] += parseFloat(value);
        });
        $(tr).find('td.total').text(total);

    });

    $('.fcr_different_company_table tfoot tr').each(function(index, tr) {

        var grandTotal = 0;

        $(tr).find('td').each (function (index, td) {
            grandTotal+=result[index];
            $(tr).find('.total_month_'+index).text(parseFloat(result[index]).toFixed(2));
        });

        $(tr).find('.grand_total').text(parseFloat(grandTotal).toFixed(2));
    });
}
