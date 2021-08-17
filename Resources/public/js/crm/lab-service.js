labServiceReportCalculation();
// lab_service_section
$('.lab_service_section').on('keypress','.active  .lab_service_table input[type=number]',function (e) {
    if (e.which === 13) {
        e.preventDefault();
        var $canfocus = $('.active .lab_service_table :focusable');
        var index = $canfocus.index(this)+1;
        if (index >= $canfocus.length){
            index = 0;
        }
        $canfocus.eq(index).focus().select();
        labServiceDataInsertUsingAjax($(this))
    }
});
$('.lab_service_section').on('blur','.active .lab_service_table input[type=number]',function (e) {
    labServiceDataInsertUsingAjax($(this))
});

$('.lab_tab').on('click',function () {
    labServiceReportCalculation()
});



function labServiceDataInsertUsingAjax(element) {
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
            labServiceReportCalculation();
        }
    });

}

function labServiceReportCalculation() {

    var result = [];
    $('.active .lab_service_table tbody tr').each(function(index, tr) {
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

    $('.active .lab_service_table tfoot tr').each(function(index, tr) {

        var grandTotal = 0;

        $(tr).find('td').each (function (index, td) {
            grandTotal+=result[index];
            $(tr).find('.total_month_'+index).text(parseFloat(result[index]).toFixed(2));
        });

        $(tr).find('.grand_total').text(parseFloat(grandTotal).toFixed(2));
    });
}