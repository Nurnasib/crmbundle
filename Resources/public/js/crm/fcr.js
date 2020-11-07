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

    $('#fcrReportDetails').on('keypress', 'input,select,textarea', function (e) {

        if (e.which == 13) {
            e.preventDefault();
            switch ($(this).attr('class')) {

                case 'form-control agent':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.hatchingDate').focus().select();
                    break;

                case 'form-control hatchingDate datePicker hasDatepicker':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.totalBirds').focus().select();
                    break;

                case 'form-control totalBirds':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.ageDays').focus().select();
                    break;

                case 'form-control ageDays':
                    // dataInsertUsingAjax($(this));
                    // getSonaliWeightStandardUsingAjax($(this));
                    $(this).closest('tr').find('.mortalityPes').focus().select();
                    break;

                case 'form-control mortalityPes':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.weightAchieved').focus().select();
                    break;

                case 'form-control weightAchieved':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.feedTotalKg').focus().select();
                    break;


                case 'form-control feedTotalKg':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.hatchery').focus().select();
                    break;


                case 'form-control hatchery':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.breed').focus().select();
                    break;


                case 'form-control breed':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.feed').focus().select();
                    break;


                case 'form-control BEFORE feed':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.feedMill').focus().select();
                    break;

                case 'form-control AFTER feed':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.remarks').focus().select();
                    break;

                case 'form-control feedMill':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.feedType').focus().select();
                    break;

                case 'form-control feedType':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.proDate').focus().select();
                    break;

                case 'form-control proDate datePicker hasDatepicker':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.batchNo').focus().select();
                    break;

                case 'form-control batchNo':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.remarks').focus().select();
                    break;

                case 'form-control remarks':
                    // dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.agent').focus().select();
                    break;

            }
        }
    });

    $(document).on('click', '.fcr_details_add_button', function () {
        dataInsertUsingAjax($(this));
    });



    $('.mortalityPes, .totalBirds').on('keypress keyup blur',function () {
        var mortalityPes = $('.mortalityPes').val();
        var total_birds= $('.totalBirds').val();
        if(mortalityPes!=='' && total_birds!=='' && total_birds>0){
            var calculateValue = (parseFloat(mortalityPes)*100)/parseFloat(total_birds);
            $('.mortalityPercent').text(parseFloat(calculateValue).toFixed(2));
        }

    });


//feed per bird

    $('.feedTotalKg, .totalBirds').on('keypress keyup blur',function () {
        var total_birds= $('.totalBirds').val();
        var feedTotalKg=$('.feedTotalKg').val();
        if(feedTotalKg !=='' && total_birds!=='' && total_birds>0){
            var calculateValue=(parseFloat(feedTotalKg)/(total_birds))*(1000);
            $('.perBird').text(parseFloat(calculateValue).toFixed(2));
        }
    });
//fcr without mortality

    $('.feedTotalKg, .totalBirds, .weightAchieved').on('keypress keyup blur',function () {
        var total_birds= $('.totalBirds').val();
        var feedTotalKg=$('.feedTotalKg').val();
        var weightAchieved=$('.weightAchieved').val();
        if((feedTotalKg !== '' && total_birds!=='' && total_birds>0 && weightAchieved !==''&& weightAchieved>0)){
            var cal=(parseFloat(feedTotalKg)/parseFloat(total_birds));
            var calculateValue=(parseFloat(cal)/weightAchieved)*1000;

            $('.withoutMortality').text(parseFloat(calculateValue).toFixed(2));
        }
    });

//fcr with mortality

    $('.feedTotalKg, .totalBirds,.mortalityPes,.weightAchieved').on('keypress keyup blur',function () {
        var total_birds= $('.totalBirds').val();
        var feedTotalKg=$('.feedTotalKg').val();
        var weightAchieved=$('.weightAchieved').val();
        var mortalityPes = $('.mortalityPes').val();


        if((feedTotalKg !=='' && total_birds!=='' && total_birds>0)&&(weightAchieved !==''&&weightAchieved>0)){
            var netTotalBird = total_birds-mortalityPes;
            var cal=(parseFloat(feedTotalKg)/ parseFloat(netTotalBird));
            var calculateValue=(parseFloat(cal)/weightAchieved)*1000;

            $('.withMortality').text(parseFloat(calculateValue).toFixed(2));
        }
    });

}


function dataInsertUsingAjax(element) {
    var fcrId = $('.fcr_id').val();
    var parentElement = element.closest('tr');
    var agent=parentElement.find('.agent').val();
    var hatchingDate=parentElement.find('.hatchingDate').val();
    var totalBirds=parentElement.find('.totalBirds').val();
    var ageDays=parentElement.find('.ageDays').val();
    var mortalityPes=parentElement.find('.mortalityPes').val();
    var weightAchieved=parentElement.find('.weightAchieved').val();
    var feedTotalKg=parentElement.find('.feedTotalKg').val();
    var hatchery=parentElement.find('.hatchery').val();
    var breed=parentElement.find('.breed').val();
    var feed=parentElement.find('.feed').val();
    var feedMill=parentElement.find('.feedMill').val();
    var feedType=parentElement.find('.feedType').val();
    var proDate=parentElement.find('.proDate').val();
    var batchNo=parentElement.find('.batchNo').val();
    var remarks=parentElement.find('.remarks').val();

    if(agent===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('crm_fcr_detail_report_add',{'id':fcrId}),
        type   : 'post',
        data   : {
            'agent':agent,
            'hatchingDate':hatchingDate,
            'totalBirds':totalBirds,
            'ageDays':ageDays,
            'mortalityPes':mortalityPes,
            'weightAchieved':weightAchieved,
            'feedTotalKg':parseFloat(feedTotalKg).toFixed(2),
            'hatchery':hatchery,
            'breed':breed,
            'feed':feed,
            'feedMill':feedMill,
            'feedType':feedType,
            'proDate':proDate,
            'batchNo':batchNo,
            'remarks':remarks
        },
        dataType : 'json',
        success: function(response){
            if(response.status===200){
                var refreshUrl = Routing.generate('fcr_details_refresh',{'id':fcrId});
                $("#fcrReportDetails tbody").load(refreshUrl);
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


