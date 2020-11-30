//mortality percent

function formCommonProcessForFcr() {

    $('.fcrReportDetails').on('keypress','input[type=text],input[type=number],select, button[type=button]',function (e) {
        if (e.which === 13) {
            /*var index = $('.fcrReportDetails .form-control').index(this) + 1;
            $('.fcrReportDetails .form-control').eq(index).focus().select();*/
            e.preventDefault();
            // Get all focusable elements on the page
            var $canfocus = $('.fcrReportDetails :focusable');
            var index = $canfocus.index(this) + 1;
            // if (index >= $canfocus.length) index = 0;
            if (index >= $canfocus.length){
                index = 0;
            }

            $canfocus.eq(index).focus().select();
        }
    });

    $(document).on('click', '.fcr_details_add_button', function () {
        dataInsertFcrUsingAjax($(this));
    });
    $(document).on('click', '.remove', function(){
        var element = $(this);
        var url = $(this).attr('data-action');
        if (confirm('Are you sure want to delete this record?')) {
            $.post(url, function( data ) {
                element.closest('tr').remove();
            });

        }
    });


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

    $('.fcrReportDetails .mortalityPes, .fcrReportDetails .totalBirds').on('keypress keyup blur',function () {
        var parentElement = $(this).closest('tr');
        var mortalityPes = parentElement.find('.mortalityPes').val();
        var total_birds= parentElement.find('.totalBirds').val();
        if(mortalityPes!=='' && total_birds!=='' && total_birds>0){
            var calculateValue = (parseFloat(mortalityPes)*100)/parseFloat(total_birds);
            parentElement.find('.mortalityPercent').text(parseFloat(calculateValue).toFixed(2));
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

function dataInsertFcrUsingAjax(element) {
    var fcrId = $('.fcr_id').val();
    var parentElement = element.closest('tr');
    var agent=parentElement.find('.agent').val();
    var hatchingDate=parentElement.find('.hatching_date').val();
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

    if(fcrId==='' || hatchingDate ==='' || typeof hatchingDate === "undefined"){
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
                parentElement.find(':input').val('');
                parentElement.find('.mortalityPercent').text('');
                parentElement.find('.perBird').text('');
                parentElement.find('.withoutMortality').text('');
                parentElement.find('.withMortality').text('');
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


