
$(document).on('click', '.details_add_button', function () {
    layerPerformanceDetailDataInsertUsingAjax($(this));
});

$(document).on('keypress','.layer_performance_details input[type=text], .layer_performance_details input[type=number], .layer_performance_details select',function (e) {
    if (e.which === 13) {

        e.preventDefault();
        var $canfocus = $('.layer_performance_details :focusable');
        var index = $canfocus.index(this) + 1;
        if (index >= $canfocus.length-1){
            index = 0;
        }

        $canfocus.eq(index).focus().select();
    }
});

function layerPerformanceDetailDataInsertUsingAjax(element) {
    var customerId=$('.customerId').val();
    var report_id = $('.report_id').val();
    var visitId = $('.visitId').val();
    var parentElement = element.closest('tr');
    var totalBirds=parentElement.find('.totalBirds').val();
    var ageWeek=parentElement.find('.ageWeek').val();
    var bodyWeightAchieved=parentElement.find('.bodyWeightAchieved').val();
    var feedIntakePerBird=parentElement.find('.feedIntakePerBird').val();
    var eggProductionAchieved=parentElement.find('.eggProductionAchieved').val();
    var eggWeightAchieved=parentElement.find('.eggWeightAchieved').val();
    var production_date=parentElement.find('.production_date').val();
    var hatchery=parentElement.find('.hatchery').val();
    var color=parentElement.find('.color').val();
    var disease=parentElement.find('.disease').val();
    var breed=parentElement.find('.breed').val();
    var feedMill=parentElement.find('.feed_mill').val();
    var feedType=parentElement.find('.feedType').val();
    var batchNo=parentElement.find('.batch_no').val();
    var remarks=parentElement.find('.remarks').val();

    if(report_id===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('crm_layer_performance_detail_report_add',{'visit':visitId, 'id':report_id}),
        type   : 'post',
        data   : {
            'customerId':customerId,
            'totalBirds':totalBirds,
            'ageWeek':ageWeek,
            'bodyWeightAchieved':bodyWeightAchieved,
            'feedIntakePerBird':feedIntakePerBird,
            'eggProductionAchieved':eggProductionAchieved,
            'eggWeightAchieved':eggWeightAchieved,
            'feedType':feedType,
            'productionDate':production_date,
            'hatchery':hatchery,
            'color':color,
            'disease':disease,
            'breed':breed,
            'feedMill':feedMill,
            'batchNo':batchNo,
            'remarks':remarks
        },
        dataType : 'json',
        success: function(response){

            if(response.status===200){

                var refreshUrl = Routing.generate('layer_performance_details_refresh',{'id':report_id});
                $(".layer_performance_details tbody").load(refreshUrl);
            }
            parentElement.find(':input').val('');
            parentElement.find('select').val('');
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


