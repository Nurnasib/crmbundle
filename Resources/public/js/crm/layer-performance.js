//mortality percent
$.urlParam = function (name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)')
        .exec(window.location.search);
    return (results !== null) ? results[1] || 0 : false;
};
var pathname = window.location.pathname; // Returns path only (/path/example.html)
var url      = window.location.href;     // Returns full URL (https://example.com/path/example.html)
var origin   = window.location.origin;   // Returns base URL (https://example.com)


$(document).on('opened', '.remodal', function () {
    var id = $.urlParam('process');
    var check = $.urlParam('check');

    var url = document.getElementById(id).getAttribute("data-action");
    $('#modal-container').load(url, function(){
        formCommonProcess();
    });
});

$('[data-remodal-id=modal]').remodal({
    modifier: 'with-red-theme',
    closeOnOutsideClick: true
});

function formCommonProcess() {

    $('.form-body').slimScroll({
        height: '85%'
    });
    $('[data-toggle="tooltip"]').tooltip();

    $('.mobileLocal').mask("00000-000000", {placeholder: "_____-______"});

    $('.checkboxToggle').bootstrapToggle();

    $('.multi-select2').multiSelect({ selectableOptgroup: true });

    $('#optgroup').multiSelect({ selectableOptgroup: true });
    $('.select2').select2({
        theme: 'bootstrap4'
    });

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


    $(document).on('click', '.details_add_button', function () {
        dataInsertUsingAjax($(this));
    });

    $('.layer_performance_details .form-control').keypress(function (e) {
        if (e.which === 13) {
            // layerLifeCycleDetailDataInsertUsingAjax($(this));
            var index = $('.layer_performance_details .form-control').index(this) + 1;
            $('.layer_performance_details .form-control').eq(index).focus().select();
        }
    });
}

function dataInsertUsingAjax(element) {
    var layer_performance_id = $('.layer_performance_id').val();
    var parentElement = element.closest('tr');
    var farmer=parentElement.find('.farmer').val();
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
    var feed=parentElement.find('.feed').val();
    var feedMill=parentElement.find('.feed_mill').val();
    var feedType=parentElement.find('.feedType').val();
    var batchNo=parentElement.find('.batch_no').val();
    var remarks=parentElement.find('.remarks').val();

    if(farmer===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('crm_layer_performance_detail_report_add',{'id':layer_performance_id}),
        type   : 'post',
        data   : {
            'farmer':farmer,
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
            'feed':feed,
            'feedMill':feedMill,
            'batchNo':batchNo,
            'remarks':remarks
        },
        dataType : 'json',
        success: function(response){
            // console.log(response.data);
            if(response.status===200){
                // console.log(response.success);
                var refreshUrl = Routing.generate('layer_performance_details_refresh',{'id':layer_performance_id});
                $(".layer_performance_details tbody").load(refreshUrl);
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


