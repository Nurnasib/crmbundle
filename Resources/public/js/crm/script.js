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
        formCommonProcessForFcr();
        if(check === 'edit'){
            formEditSubmitProcess();
        }else{
            formSubmitProcess();
        }
    });
});

$('[data-remodal-id=modal]').remodal({
    modifier: 'with-red-theme',
    closeOnOutsideClick: true
});

$(".timePicker").timepicker({
        timeFormat: 'hh:mm TT'
    }
);

$(document).on('click','.report_complete', function () {
    var crmChickLifeCycleId = $(this).attr('data-chick-life-cycle-id');

    if(crmChickLifeCycleId===''){
        return false;
    }
    $.ajax({
        url    : Routing.generate('crm_chick_life_cycle_complete',{'id':crmChickLifeCycleId}),
        type   : 'post',
        dataType : 'json',
        success: function(response){
            location.reload();
        }
    });
});

$(document).on('click','.cattle_report_complete', function () {
    var crmCattleLifeCycleId = $(this).attr('data-cattle-life-cycle-id');

    if(crmCattleLifeCycleId===''){
        return false;
    }
    $.ajax({
        url    : Routing.generate('crm_cattle_life_cycle_complete',{'id':crmCattleLifeCycleId}),
        type   : 'post',
        dataType : 'json',
        success: function(response){
            console.log(response.message);
            location.reload();
        }
    });
});

$(document).on('click','.report_complete_layer', function () {
    var crmLayerLifeCycleId = $(this).attr('data-layer-life-cycle-id');

    if(crmLayerLifeCycleId===''){
        return false;
    }
    $.ajax({
        url    : Routing.generate('crm_layer_life_cycle_complete',{'id':crmLayerLifeCycleId}),
        type   : 'post',
        dataType : 'json',
        success: function(response){
            console.log(response.message);
            location.reload();
        }
    });
});

function formCommonProcess() {


    $(".add-row").on('click', function(){
        var table = $(this).closest('.table');
        var nrow = table.find('tr:eq(1)').clone();
        nrow.find('td').find('button').removeClass('hide');
        nrow.find("input[type=text]").val("");
        table.append(nrow);
    });

    // Find and remove selected table rows
    $('body').on('click','.remove_row', function(){
        $(this).closest("tr").remove();
    });

    $( ".sticky_section" ).scroll(function() {
        var scroll = $(this).scrollTop();
        if (scroll >= 50) {
            //clearHeader, not clearheader - caps H
            $(".sticky_header").addClass("sticky");
        }
        if (scroll < 50) {
            //clearHeader, not clearheader - caps H
            $(".sticky_header").removeClass("sticky");
        }
        console.log(scroll);
    });

    $(".timePicker").timepicker({
            timeFormat: 'hh:mm TT'
        }
    );

    $('.form-body').slimScroll({
        height: '85%'
    });
    $('[data-toggle="tooltip"]').tooltip();

    $('.mobileLocal').mask("00000-000000", {placeholder: "_____-______"});

    $('.checkboxToggle').bootstrapToggle();

    $('.multi-select2').multiSelect({ selectableOptgroup: true });
    $('.multi-select2-farmer').multiSelect({ selectableOptgroup: false });

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

    $('.monthYearPicker').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'mm-yy',

        onClose: function() {
            $("#ui-datepicker-div").removeClass('monthYearPicker');
            var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
        },

        beforeShow: function() {
            $("#ui-datepicker-div").addClass('monthYearPicker');
            // console.log($(this).val());
            if ((selDate = $(this).val()).length > 0)
            {
                iYear = selDate.substring(selDate.length - 4, selDate.length);
                iMonth = selDate.substring(0, 2);
                // iMonth = jQuery.inArray(selDate.substring(1, 2), $(this).datepicker('option', 'monthNames'));
                // console.log(iMonth);
                iMonth = iMonth-1;
                $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
            }
        }
    });

    $('.monthYearPickerForPreviousTwoMonthEnabled').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'mm-yy',
        autoClose:true,
        minDate:"-2M",
        maxDate: "-1M",

        onClose: function() {
            $("#ui-datepicker-div").removeClass('monthYearPickerForPreviousTwoMonthEnabled');
            var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
        },

        beforeShow: function() {
            $("#ui-datepicker-div").addClass('monthYearPickerForPreviousTwoMonthEnabled');
            // console.log($(this).val());
            if ((selDate = $(this).val()).length > 0)
            {
                iYear = selDate.substring(selDate.length - 4, selDate.length);
                iMonth = selDate.substring(0, 2);
                // iMonth = jQuery.inArray(selDate.substring(1, 2), $(this).datepicker('option', 'monthNames'));
                // console.log(iMonth);
                iMonth = iMonth-1;
                $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
                $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
            }
        }
    });


    $('.chickLifeCycleDetails_section').on('keypress blur','.chickLifeCycleDetails input[type=text], .chickLifeCycleDetails input[type=number], .chickLifeCycleDetails select', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            var $canfocus = $('.chickLifeCycleDetails :focusable');
            var index = $canfocus.index(this) + 1;
            if (index >= $canfocus.length){
                index = 0;
            }
            $canfocus.eq(index).focus().select();
        }
        dataInsertChickUsingAjax($(this));
    });

    $('#cattle_life_cycle_form').on('keypress','.cattle_life_cycle input[type=text], .cattle_life_cycle input[type=number], .cattle_life_cycle select',function (e) {
        if (e.which === 13) {
            e.preventDefault();
            var $canfocus = $('.cattle_life_cycle :focusable');
            var index = $canfocus.index(this) + 1;
            if (index >= $canfocus.length-1){
                index = 0;
            }
            $canfocus.eq(index).focus().select();

            $('#cattle_life_cycle_form').submit(function() {
                return false;
            });
        }
    });

    $('.cattleLifeCycleSection').on('click', '.cattle_life_cycle_details_button', function () {

        formSubmitProcessForCattleLifeCycle();
    });


    $('.layerLifeCycleDetails_section').on('keypress' , '.layerLifeCycleDetails input[type=text], .layerLifeCycleDetails input[type=number], .layerLifeCycleDetails select', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            var $canfocus = $('.layerLifeCycleDetails :focusable');
            var index = $canfocus.index(this) + 1;
            if (index >= $canfocus.length){
                index = 0;
            }
            $canfocus.eq(index).focus().select();
            layerLifeCycleDetailDataInsertUsingAjax($(this));
        }
    });
    $('.layerLifeCycleDetails_section').on('blur' , '.layerLifeCycleDetails input[type=text], .layerLifeCycleDetails input[type=number], .layerLifeCycleDetails select', function (e) {
        layerLifeCycleDetailDataInsertUsingAjax($(this));
    });


// for fish life cycle

    $('#fish_life_cycle_form').on('keypress','.fish_life_cycle input[type=text], .fish_life_cycle input[type=number], .fish_life_cycle select',function (e) {
        if (e.which === 13) {
            e.preventDefault();
            var $canfocus = $('.fish_life_cycle :focusable');
            var index = $canfocus.index(this) + 1;
            if (index >= $canfocus.length-1){
                index = 0;
            }
            $canfocus.eq(index).focus().select();

            $('#fish_life_cycle_form').submit(function() {
                return false;
            });
        }
    });

// for cattle farm visit

    $('#cattle_farm_visit_form').on('keypress','.cattle_farm_visit_report input[type=text], .cattle_farm_visit_report input[type=number], .cattle_farm_visit_report select',function (e) {
        if (e.which === 13) {
            e.preventDefault();
            var $canfocus = $('.cattle_farm_visit_report :focusable');
            var index = $canfocus.index(this) + 1;
            if (index >= $canfocus.length-1){
                index = 0;
            }
            $canfocus.eq(index).focus().select();

            $('#cattle_farm_visit_form').submit(function() {
                return false;
            });
        }
    });

    $('.fishLifeCycleSection').on('click', '.fish_life_cycle_details_button', function () {

        formSubmitProcessForFishLifeCycle();
    });

    $('.cattleFarmVisitSection').on('click', '.cattle_farm_visit_details_button', function () {

        formSubmitProcessForCattleFarmVisit();
    });

    $('#fish_farmer_touch_report_form').on('keypress','.fish_farmer_touch input[type=text], .fish_farmer_touch input[type=number], .fish_farmer_touch select',function (e) {
        if (e.which === 13) {
            e.preventDefault();
            var $canfocus = $('.fish_farmer_touch :focusable');
            var index = $canfocus.index(this) + 1;
            if (index >= $canfocus.length-1){
                index = 0;
            }
            $canfocus.eq(index).focus().select();

            $('#fish_farmer_touch_report_form').submit(function() {
                return false;
            });
        }
    });

    $('#disease_mapping_form').on('keypress','.disease_mapping_table input[type=text], .disease_mapping_table input[type=number], .disease_mapping_table select',function (e) {
        if (e.which === 13) {
            e.preventDefault();
            var $canfocus = $('.disease_mapping_table :focusable');
            var index = $canfocus.index(this) + 1;
            if (index >= $canfocus.length-1){
                index = 0;
            }
            $canfocus.eq(index).focus().select();

            $('#disease_mapping_form').submit(function() {
                return false;
            });
        }
    });


    $('.fishFarmerTouchSection').on('click', '.fish_farmer_touch_report_button', function () {

        formSubmitProcessForFishFarmerTouchReport();
    });

    $('.farmer_training_report_section').on('click', '.farmer_training_report_button', function () {
        formSubmitProcessForFarmerTrainingReport($(this));
    });


    $('.antibiotic_free_farm_section').on('click', '.antibiotic_free_farm_button', function () {
        formSubmitProcessForAntibioticFreeFarm($(this));
    });

    $('.less_costing_farm_section').on('click', '.less_costing_farm_button', function () {
        formSubmitProcessForLessCostingFarm($(this));
    });

    $('.disease_mapping_section').on('click', '.disease_mapping_button', function () {
        formSubmitProcessForDiseaseMapping($(this));
    });

    $('.complain_different_product_section').on('click', '.complain_different_product_button', function () {
        formSubmitProcessForComplainDifferentProduct($(this));
    });

    $('.fish_feed_complain_section').on('click', '.fish_feed_complain_button', function () {
        formSubmitProcessForFishFeedComplain($(this));
    });

    $('.agent_upgradation_report_section').on('click', '.agent_upgradation_report_button', function () {
        formSubmitProcessForAgentUpgradationReport($(this));
    });
}

function dataInsertChickUsingAjax(element) {
    var parentElement = element.closest('tr');
    var crmChickLifeCycleDetailId=parentElement.find('.crmChickLifeCycleDetails').val();
    var reportingDate=parentElement.find('.reportingDate').val();
    // var totalBirds=parentElement.find('.totalBirds').val();
    var ageDays=parentElement.find('.ageDays').val();
    var mortalityPes=parentElement.find('.mortalityPes').val();
    var weightAchieved=parentElement.find('.weightAchieved').val();
    var feedTotalKg=parentElement.find('.feedTotalKg').val();
    var feedType=parentElement.find('.feedType').val();

    var proDate=parentElement.find('.proDate').val();
    var batchNo=parentElement.find('.batchNo').val();
    var remarks=parentElement.find('.remarks').val();

    if(crmChickLifeCycleDetailId===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('crm_chick_life_cycle_edit',{'id':crmChickLifeCycleDetailId}),
        type   : 'post',
        data   : {
            'reportingDate':reportingDate,
            // 'totalBirds':totalBirds,
            'ageDays':ageDays,
            'mortalityPes':mortalityPes,
            'weightAchieved':weightAchieved,
            'feedTotalKg':feedTotalKg,
            'feedType':feedType,
            'proDate':proDate,
            'batchNo':batchNo,
            'remarks':remarks
        },
        dataType : 'json',
        success: function(response){
            console.log('ok');
            parentElement.find('.mortalityPercent').text(response.mortalityPercent);
            parentElement.find('.weightStandard').text(response.weightStandard);
            parentElement.find('.feedStandard').text(response.feedStandard);
            parentElement.find('.perBird').text(response.perBird);
            parentElement.find('.withoutMortality').text(response.withoutMortality);
            parentElement.find('.withMortality').text(response.withMortality);
        }
    });
}

function layerLifeCycleDetailDataInsertUsingAjax(element) {
    var parentElement = element.closest('tr');
    var crmLayerLifeCycleDetailId=element.attr('data-entity-id');
    var dataMetaKey=element.attr('data-meta-key');
    var dataInputType=element.attr('data-input-type');
    var dataMetaValue=element.val();

    if(crmLayerLifeCycleDetailId===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('crm_layer_life_cycle_details_edit',{'id':crmLayerLifeCycleDetailId}),
        type   : 'post',
        data   : {
            'dataMetaKey':dataMetaKey,
            'dataMetaValue':dataMetaValue,
            'dataInputType':dataInputType
        },
        dataType : 'json',
        success: function(response){
            $( ".presentBird" ).each(function( index ) {
                $( this ).text(response.presentBird[index]) ;
            });

            $( ".eggProduction" ).each(function( index ) {
                $( this ).text(response.eggProduction[index]) ;
            });
            // parentElement.find('.eggProduction').text(response.eggProduction);
            parentElement.find('.targetWeight').text(response.targetWeight);
            parentElement.find('.targetFeedPerBird').text(response.targetFeedPerBird);
            parentElement.find('.targetEggProduction').text(response.targetEggProduction);
            parentElement.find('.eggWeightStandard').text(response.eggWeightStandard);

        }
    });
}


function initIntegerMask(el){
    $(el).inputmask("integer", {removeMaskOnSubmit: false});
}

function formSubmitProcessForCattleLifeCycle() {
    var cattleLifeCycle_id = $('.cattleLifeCycle_id').val();
    if(cattleLifeCycle_id===""){
        return false;
    }
        $.ajax({
            url         : $('form#cattle_life_cycle_form').attr( 'action' ),
            type        : $('form#cattle_life_cycle_form').attr( 'method' ),
            data        : new FormData($('form#cattle_life_cycle_form')[0]),
            processData : false,
            contentType : false,
            beforeSend: function() {
                $('.form-submit').html("Loading...").attr('disabled', 'disabled');
            },
            success: function(response){
                $("#process-msg").show();
                $(".alert-success").html(response);
                $(".form-submit").html("Complete").prop("disabled", false);
                $('form#cattle_life_cycle_form')[0].reset();
                // location.reload();
                // setTimeout( explode, 2000);
                if(cattleLifeCycle_id>0){
                    var refreshUrl = Routing.generate('crm_cattle_life_cycle_refresh',{'id':cattleLifeCycle_id});
                    $("tbody.dairyLifeCycleDetailsSection").load(refreshUrl);
                }
            }
        });
    }

function formSubmitProcessForFishLifeCycle() {
    var fishLifeCycle_id = $('.fishLifeCycle_id').val();
    if(fishLifeCycle_id===""){
        return false;
    }
        $.ajax({
            url         : $('form#fish_life_cycle_form').attr( 'action' ),
            type        : $('form#fish_life_cycle_form').attr( 'method' ),
            data        : new FormData($('form#fish_life_cycle_form')[0]),
            processData : false,
            contentType : false,
            beforeSend: function() {
                $('.form-submit').html("Loading...").attr('disabled', 'disabled');
            },
            success: function(response){
                $("#process-msg").show();
                $(".alert-success").html(response);
                $(".form-submit").html("Complete").prop("disabled", false);
                $('form#fish_life_cycle_form')[0].reset();
                // location.reload();
                // setTimeout( explode, 2000);
                if(fishLifeCycle_id!=''){
                    var refreshUrl = Routing.generate('crm_fish_life_cycle_refresh',{'id':fishLifeCycle_id});
                    $("tbody.fishLifeCycleDetailsSection").load(refreshUrl);
                }
            }
        });
    }

// cattle_farm_visit_form
function formSubmitProcessForCattleFarmVisit() {
    var cattleFarmVisit_id = $('.cattleFarmVisit_id').val();
    if(cattleFarmVisit_id===""){
        return false;
    }
        $.ajax({
            url         : $('form#cattle_farm_visit_form').attr( 'action' ),
            type        : $('form#cattle_farm_visit_form').attr( 'method' ),
            data        : new FormData($('form#cattle_farm_visit_form')[0]),
            processData : false,
            contentType : false,
            beforeSend: function() {
                $('.form-submit').html("Loading...").attr('disabled', 'disabled');
            },
            success: function(response){
                $('form#cattle_farm_visit_form')[0].reset();
                // location.reload();
                // setTimeout( explode, 2000);
                if(cattleFarmVisit_id!==''){
                    var refreshUrl = Routing.generate('crm_cattle_farm_visit_detail_refresh',{'id':cattleFarmVisit_id});
                    $("tbody.cattleFarmVisitDetailsSection").load(refreshUrl);
                }
            }
        });
    }

function formSubmitProcess() {
    var cattleLifeCycle_id = $('.cattleLifeCycle_id').val();

    $("#chick_life_cycle_form").validate({

        rules: {
            "customer_form[name]": {required: true},
            "customer_form[mobile]": {
                required: true,
                remote:window.location.pathname+"creatable/available"
            }

        },

        messages: {

            "customer_form[name]": "Enter user full name",
            "customer_form[mobile]":{
                required: "Please enter your mobile no.",
                remote: jQuery.validator.format("{0} username is already in use!")
            }
        },
        submitHandler: function(form) {
            $(".form-submit").prop("disabled", true);
            $.ajax({
                url         : $('form#chick_life_cycle_form').attr( 'action' ),
                type        : $('form#chick_life_cycle_form').attr( 'method' ),
                data        : new FormData($('form#chick_life_cycle_form')[0]),
                processData : false,
                contentType : false,
                beforeSend: function() {
                    $('.form-submit').html("Loading...").attr('disabled', 'disabled');
                },
                success: function(response){
                    $("#process-msg").show();
                    $(".alert-success").html(response);
                    $(".form-submit").html("Complete").prop("disabled", false);
                    $('form#chick_life_cycle_form')[0].reset();
                    if(cattleLifeCycle_id>0){
                        var refreshUrl = Routing.generate('crm_cattle_life_cycle_refresh',{'id':cattleLifeCycle_id});
                        $("tbody.dairyLifeCycleDetailsSection").load(refreshUrl);
                    }else {
                        location.reload();
                    }
                }
            });
        }
    });
}

function formEditSubmitProcess() {

    $("#chick_life_cycle_form").validate({

        rules: {
            "customer_form[name]": {required: true},
            "customer_form[mobile]": {
                required: true,
                remote:window.location.pathname+"editable/available"
            }
        },

        messages: {
            "customer_form[name]": "Enter user full name",
            "customer_form[mobile]":{
                required: "Please enter your mobile no.",
                remote: jQuery.validator.format("{0} username is already in use!")
            }
        },
        submitHandler: function(form) {

            $(".form-submit").prop("disabled", true);
            $.ajax({
                url         : $('form#chick_life_cycle_form').attr( 'action' ),
                type        : $('form#chick_life_cycle_form').attr( 'method' ),
                data        : new FormData($('form#chick_life_cycle_form')[0]),
                processData : false,
                contentType : false,
                beforeSend: function() {
                    $('.form-submit').html("Loading...").attr('disabled', 'disabled');
                },
                success: function(response){
                    $("#process-msg").show();
                    $(".alert-success").html(response);
                    setTimeout( explode, 2000);
                    location.reload();
                }
            });
        }
    });
}


//feed per bird

/*$('.feedTotalkg, .totalBirds').on('keypress keyup blur',function () {
    var totalbirds= $('.totalBirds').val();
    var feedTotalkg=$('.feedTotalkg').val();
    if(feedTotalkg != '' && totalbirds!=''){
        var calculation=(parseFloat(feedTotalkg)/(totalbirds))*(1000);
        $('.perBird').val(calculation);
        $('.perBird').text(calculation);
    }
});*/
//fcr without mortality

/*$('.feedTotalkg, .totalBirds, .weightAchieved').on('keypress keyup blur',function () {
    var totalbirds= $('.totalBirds').val();
    var feedTotalkg=$('.feedTotalkg').val();
    var weightAchieved=$('.weightAchieved').val();
    if((feedTotalkg != '' && totalbirds!='')&&(weightAchieved !='')){
        var cal=(parseFloat(feedTotalkg)/(totalbirds));
        var calculation=(parseFloat(cal)/weightAchieved)*1000;

        $('.withoutMortality').val(calculation);
        $('.withoutMortality').text(calculation);
    }
});*/

//fcr with mortality

/*$('.feedTotalkg, .totalBirds,.mortality_pes,.weightAchieved').on('keypress keyup blur',function () {
    var totalbirds= $('.totalBirds').val();
    var feedTotalkg=$('.feedTotalkg').val();
    var weightAchieved=$('.weightAchieved').val();
    var mortalityPes = $('.mortality_pes').val();

    if((feedTotalkg != '' && totalbirds!='')&&(weightAchieved !=''&& mortalityPes!='')){

        var cal=(parseFloat(feedTotalkg)/ (totalbirds-mortalityPes));
        var calculation=(parseFloat(cal)/weightAchieved)*1000;

        $('.withMortality').val(calculation);
        $('.withMortality').text(calculation);
    }
});*/


var count = 0;

$('.addmore').click(function(){

    var el = $(this);
    var crm_visit_id = $('.crm_visit_id').val();
    var farmer_section = el.closest('tr.farmer_section');
    var farmer_purpose = farmer_section.find('.farmer_purpose').val();
    var farmer_firm_type = farmer_section.find('.farmer_firm_type').val();
    var farmer_report = farmer_section.find('.farmer_report').val();
    var farmer = farmer_section.find('.farmer').val();
    var farmer_capacity = farmer_section.find('.farmer_capacity').val();
    var farmer_comments = farmer_section.find('.farmer_comments').val();
    
    if(farmer_purpose.length==0 || farmer==='' || farmer_firm_type==='' || farmer_report===''){
        alert('Please enter required field.');
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_add'),
        type: 'POST',
        dataType: 'json',
        data:{
            'crm_visit_id':crm_visit_id,
            'purpose_multiple':farmer_purpose,
            'farmer_firm_type':farmer_firm_type,
            'farmer_report':farmer_report,
            'farmer':farmer,
            'farmer_capacity':farmer_capacity,
            'comments':farmer_comments,
            'process':'farmer'
        },
        success: function(response) {
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'farmer'});
                $(".crm_detail_farmer_section").load(refreshUrl);

                farmer_section.find('.farmer_purpose').val('');
                farmer_section.find('.farmer_firm_type').val('');
                farmer_section.find('.farmer_report').val('');
                farmer_section.find('.farmer').val('');
                farmer_section.find('.farmer_phone').val('');
                farmer_section.find('.farmer_capacity').val('');
                farmer_section.find('.farmer_comments').val('');
            }
        }

    })

});

$('.crm_detail_farmer_section').on('click', '.row-remove', function(){
    var crm_visit_id = $('.crm_visit_id').val();
    var id = $(this).attr('data-id');
    if(id === ''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_delete',{'id':id}),
        type: 'GET',
        success: function (response) {
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'farmer'});
                $(".crm_detail_farmer_section").load(refreshUrl);
            }
        }
    })
});


$('.addAgent').click(function(){
    var el = $(this);
    var crm_visit_id = $('.crm_visit_id').val();
    var agent_section = el.closest('tr.agent_section');
    var agentPurpose = agent_section.find('.agentPurpose').val();
    var agentSinglePurpose = agent_section.find('.agentSinglePurpose').val();
    var agent = agent_section.find('.agent').val();
    var agentComments = agent_section.find('.agentComments').val();
    if(agentPurpose.length==0 || agent===''){
        alert('Please enter required field.');
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_add'),
        type: 'POST',
        dataType: 'json',
        data:{
            'crm_visit_id':crm_visit_id,
            'purpose':agentSinglePurpose,
            'purpose_multiple':agentPurpose,
            'agent':agent,
            'comments':agentComments,
            'process':'agent'
        },
        success: function(response) {
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'agent'});
                $(".crm_detail_agent_section").load(refreshUrl);

                agent_section.find('.agentPurpose').val('');
                agent_section.find('.agent').val('');
                agent_section.find('.agentComments').val('');
                agent_section.find('input[type=text]').val('');
            }
        }

    })

});

$('#agent-clone-block').on('click', '.row-remove', function(){
    var crm_visit_id = $('.crm_visit_id').val();
    var id = $(this).attr('data-id');
    if(id === ''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_delete',{'id':id}),
        type: 'GET',
        success: function (response) {
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'agent'});
                $(".crm_detail_agent_section").load(refreshUrl);
            }
        }
    })
});

$('.addOtherAgent').click(function(){
    var el = $(this);
    var crm_visit_id = $('.crm_visit_id').val();
    var agent_section = el.closest('tr.other_agent_section');
    var agentPurpose = agent_section.find('.other_agent_purpose').val();
    var agent = agent_section.find('.other_agent').val();
    var agentComments = agent_section.find('.other_agent_comments').val();
    if(agentPurpose.length==0 || agent===''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_add'),
        type: 'POST',
        dataType: 'json',
        data:{
            'crm_visit_id':crm_visit_id,
            'purpose_multiple':agentPurpose,
            'agent':agent,
            'comments':agentComments,
            'process':'other-agent'
        },
        success: function(response) {
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'other-agent'});
                $(".crm_detail_other_agent_section").load(refreshUrl);
            }
        }

    })

});

$('#other-agent-clone-block').on('click', '.row-remove', function(){
    var crm_visit_id = $('.crm_visit_id').val();
    var id = $(this).attr('data-id');
    if(id === ''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_delete',{'id':id}),
        type: 'GET',
        success: function (response) {
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'other-agent'});
                $(".crm_detail_other_agent_section").load(refreshUrl);
            }
        }
    })
});

$('.addSubAgent').click(function(){
    var el = $(this);
    var crm_visit_id = $('.crm_visit_id').val();
    var agent_section = el.closest('tr.sub_agent_section');
    var agentPurpose = agent_section.find('.sub_agent_purpose').val();
    var agent = agent_section.find('.sub_agent').val();
    var agentComments = agent_section.find('.sub_agent_comments').val();
    if(agentPurpose.length==0 || agent===''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_add'),
        type: 'POST',
        dataType: 'json',
        data:{
            'crm_visit_id':crm_visit_id,
            'purpose_multiple':agentPurpose,
            'agent':agent,
            'comments':agentComments,
            'process':'sub-agent'
        },
        success: function(response) {
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'sub-agent'});
                $(".crm_detail_sub_agent_section").load(refreshUrl);
            }
        }

    })

});

$('#sub-agent-clone-block').on('click', '.row-remove', function(){
    var crm_visit_id = $('.crm_visit_id').val();
    var id = $(this).attr('data-id');
    if(id === ''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_delete',{'id':id}),
        type: 'GET',
        success: function (response) {
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'sub-agent'});
                $(".crm_detail_sub_agent_section").load(refreshUrl);
            }
        }
    })
});


$(document).on('click', '.meta-remove', function(){
    var id = $(this).attr('data-id');
    var url = $(this).attr('data-action');
    $.MessageBox({
        buttonFail  : "No",
        buttonDone  : "Yes",
        message     : "Are you sure want to delete this record?"
    }).done(function(){
        $.get(url, function( data ) {
            $('remove-'+id).remove();
            $(this).closest('.clone-remove').remove();
        });
    });
});

$('#farmerModal').on('hidden.bs.modal', function(event) {
    $('#farmerModal').find('.cultureSpeciesArea').html('');
});

$(document).on('click','.farmerModalButton', function () {
    $('#farmerModal').modal('show');
});

$(document).on('click', '#crm-farmer-btn', function(e) {

    var crm_visit_id = $('body').find('.crm_visit_id').val();
    e.preventDefault();
    var name =$(this).closest("form").find(".name").val();
    var mobile = $(this).closest("form").find(".mobile").val();
    var agent = $(this).closest("form").find(".agent").val();
    var other_agent = $(this).closest("form").find(".other_agent").val();
    var sub_agent = $(this).closest("form").find(".sub_agent").val();
    var farmerType = $(this).closest("form").find(".farmer_type").val();
    var location = $(this).closest("form").find(".location").val();
    var feed_id = $(this).closest("form").find(".feed_id").val();

    if (name === "") {
        alert("Name must be filled out");
        return false;
    }
    else if(mobile==="" || mobile ===null){
        alert("Your mobile number is Invalid " +mobile);
        return false;
    }
    else if((agent==="" || agent ===null) && (other_agent==="" || other_agent ===null) && (sub_agent==="" || sub_agent ===null)){
        alert("Agent Or Other Agent Or Sub Agent is required");
        return false;
    }
    else if(location==="" || location ===null){
        alert("Location is required");
        return false;
    }
    else if(feed_id==="" || feed_id ===null){
        alert("Feed is required");
        return false;
    }
    else if(farmerType==="" || farmerType ===null){
        alert("Farmer Type is required");
        return false;
    }

    $.ajax({
        url         : $('form#farmerForm').attr( 'action' ),
        type        : $('form#farmerForm').attr( 'method' ),
        data        : new FormData($('form#farmerForm')[0]),
        processData : false,
        contentType : false,
        success: function (data) {
            
            $('form#farmerForm')[0].reset();
            
            // if(data.id){

            $('#farmerModal').find('.cultureSpeciesArea').html('');
            $('#farmerModal').modal('hide');
            $('#farmerModal').on('hidden.bs.modal', function () {
                $('#farmerModal').removeClass('show').removeClass('in');
                $('#farmerModal').attr("aria-hidden","true");
                $("#farmerModal").removeAttr("style");
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });
            console.log(data);
            $('.alert').remove();
            if(data.status==200){
                jQuery('body').find('.messages').append('<div class="alert alert-dismissible alert-success fade in show" role="alert">\n' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                    '<span aria-hidden="true">×</span>\n' +
                    '</button>\n' +data.message +
                    '</div>');

            }

            if(data.status==409){
                jQuery('body').find('.messages').append('<div class="alert alert-dismissible alert-danger fade in show" role="alert">\n' +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                    '<span aria-hidden="true">×</span>\n' +
                    '</button>\n' +data.message +
                    '</div>');
            }

            // }
        }
    });

});


$(document).on('click','.otherAgentModalButton', function () {
    $('#otherAgentModal').modal('show');
});

$(document).on('click', '#crm-other-agent-btn', function(e) {

    e.preventDefault();
    var name =$(this).closest("form").find(".name").val();
    var mobile = $(this).closest("form").find(".mobile").val();
    var crm_visit_id = $('body').find('.crm_visit_id').val();

    if (name === "") {
        alert("Name must be filled out");
        return false;
    }
    else if(mobile==="" || mobile ===null){
        alert("Your mobile number is Invalid :" +mobile);
        return false;
    }
    var form= $("#otherAgent");
    $.ajax({
        url         : $('form#otherAgentForm').attr( 'action' ),
        type        : $('form#otherAgentForm').attr( 'method' ),
        data        : new FormData($('form#otherAgentForm')[0]),
        processData : false,
        contentType : false,
        success: function (data) {
            console.log(data);
            $('form#otherAgentForm')[0].reset();
            $('#otherAgentModal').modal('hide');
            $('#otherAgentModal').on('hidden.bs.modal', function () {
                $('#otherAgentModal').removeClass('show').removeClass('in');
                $('#otherAgentModal').attr("aria-hidden","true");
                $("#otherAgentModal").removeAttr("style");
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });
            
            if(data[0].status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'other-agent'});
                $(".crm_detail_other_agent_section").load(refreshUrl);
            }

        }
    });
    // $('#otherAgentModal').modal('hide');

});


$(document).on('click','.subAgentModalButton', function () {
    $('#subAgentModal').modal('show');
});


$(document).on('click', '#crm-sub-agent-btn', function(e) {

    e.preventDefault();
    var name =$(this).closest("form").find(".name").val();
    var mobile = $(this).closest("form").find(".mobile").val();
    var crm_visit_id = $('body').find('.crm_visit_id').val();
    if (name === "") {
        alert("Name must be filled out");
        return false;
    }
    else if(mobile==="" || mobile ===null){
        alert("Your mobile number is Invalid :" +mobile);
        return false;
    }
    var form= $("#subAgent");
    $.ajax({
        url         : $('form#subAgentForm').attr( 'action' ),
        type        : $('form#subAgentForm').attr( 'method' ),
        data        : new FormData($('form#subAgentForm')[0]),
        processData : false,
        contentType : false,
        success: function (data) {
            // console.log(data[0].status);
            $('form#subAgentForm')[0].reset();
            if(data[0].status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'sub-agent'});
                $(".crm_detail_sub_agent_section").load(refreshUrl);
            }
            $('#subAgentModal').modal('hide');
            $('#subAgentModal').on('hidden.bs.modal', function () {
                $('#subAgentModal').removeClass('show').removeClass('in');
                $('#subAgentModal').attr("aria-hidden","true");
                $("#subAgentModal").removeAttr("style");
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
            });

        }
    });

});

$(document).on('change', '.farmer', function(e) {
    var element = $(this);
    var id = $(this).val();
    element.closest('tr').find('.farmer_address').val('');
    element.closest('tr').find('.farmer_phone').val('');
    if(id==''){
        return false;
    }
    var url = Routing.generate('get_farmer_ajax',{'id':id});
    $.get(url, function(data){
        element.closest('tr').find('.farmer_address').val(data[0]['address']);
        element.closest('tr').find('.farmer_phone').val(data[0]['phone']);
    });

});

$(document).on('change','.farmer_type',function(){
    var element = $(this);
    var farmerTypeId = $(this).val();

    if(farmerTypeId===''){
        alert('ok')
        element.closest('div.modal-body').find('.cultureSpeciesArea').html('');
         return false;
    }
    $.ajax({
        url: Routing.generate('species_name_by_parent_id_ajax',{'id':farmerTypeId})
    }).done(function(data) {
        element.closest('div.modal-body').find('.cultureSpeciesArea').html(data);
    });
}).change();

$(document).on('change','.farmer_firm_type',function(){
    var element = $(this);
    var farmTypeId = $(this).val();

    if(farmTypeId===''){
        element.closest('tr').find('.farmer_report').find('option').remove();
        element.closest('tr').find('.farmer_report').append($('<option>', {value:'', text:'Select Report'}));
        // for modal
        if($('.modal.show').length){
            element.closest('div.modal-body').find('.farmer_report').find('option').remove();
            element.closest('div.modal-body').find('.farmer_report').append($('<option>', {value:'', text:'Select Report'}));
        }
         return false;
    }
    $.ajax({
        url: Routing.generate('crm_report_farm_type_ajax',{'id':farmTypeId})
    }).done(function(data) {

        element.closest('tr').find('.farmer_report').find('option').remove();
        element.closest('tr').find('.farmer_report').append($('<option>', {value:'', text:'Select Report'}));
// for modal
        if($('.modal.show').length){
            element.closest('div.modal-body').find('.farmer_report').find('option').remove();
            element.closest('div.modal-body').find('.farmer_report').append($('<option>', {value:'', text:'Select Report'}));
        }
        $.each(data, function(i, item) {
            element.closest('tr').find('.farmer_report').append($('<option>', {value:item.id, text:item.name}));
            if($('.modal.show').length){
                element.closest('div.modal-body').find('.farmer_report').append($('<option>', {value:item.id, text:item.name}));
            }
        });

    });
});

$(document).on('change', '.other_agent', function(e) {
    var element = $(this);
    var id = $(this).val();
    element.closest('tr').find('.other_agent_address').val('');
    element.closest('tr').find('.other_agent_mobile').val('');
    if(id==''){
        return false;
    }
    var url = Routing.generate('get_core_agent_find_ajax',{'id':id});
    $.get(url, function(data){
        element.closest('tr').find('.other_agent_address').val(data[0]['address']);
        element.closest('tr').find('.other_agent_mobile').val(data[0]['mobile']);
    });

});
$(document).on('change', '.sub_agent', function(e) {
    var element = $(this);
    var id = $(this).val();
    element.closest('tr').find('.sub_agent_address').val('');
    element.closest('tr').find('.sub_agent_mobile').val('');
    if(id==''){
        return false;
    }
    var url = Routing.generate('get_core_agent_find_ajax',{'id':id});
    $.get(url, function(data){
        element.closest('tr').find('.sub_agent_address').val(data[0]['address']);
        element.closest('tr').find('.sub_agent_mobile').val(data[0]['mobile']);
    });

});

$(document).on('change', '.agent', function(e) {
    var element = $(this);
    var id = $(this).val();
    console.log(id);
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

$(".modal").on("hidden.bs.modal", function(){
    // $(".modal-body").html("");
    $(this).find('form')[0].reset();
    // $('form#farmerForm')[0].reset();
});




