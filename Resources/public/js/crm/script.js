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

    $('#datatable').on('keypress', 'input,select,textarea', function (e) {

        if (e.which == 13) {
            e.preventDefault();
            switch ($(this).attr('class')) {
                case 'form-control totalBirds':
                    dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.ageDays').focus().select();
                    break;

                case 'form-control ageDays':
                    dataInsertUsingAjax($(this));
                    getSonaliWeightStandardUsingAjax($(this));
                    $(this).closest('tr').find('.mortalityPes').focus().select();
                    break;

                case 'form-control mortalityPes':
                    dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.weightAchieved').focus().select();
                    break;

                case 'form-control weightAchieved':
                    dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.feedTotalKg').focus().select();
                    break;


                case 'form-control feedTotalKg':
                    dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.feedStandard').focus().select();
                    break;

                case 'form-control feedStandard':
                    dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.feedType').focus().select();
                    break;

                case 'form-control feedType':
                    dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.proDate').focus();
                    break;

                case 'form-control proDate datePicker hasDatepicker':
                    dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.batchNo').focus().select();
                    break;

                case 'form-control batchNo':
                    dataInsertUsingAjax($(this));
                    $(this).closest('tr').find('.remarks').focus().select();
                    break;

                case 'form-control remarks':
                    dataInsertUsingAjax($(this));
                    $(this).closest('tr').next('tr').find('.totalBirds').focus().select();
                    break;
            }
        }
    });

    $('.cattle_life_cycle .form-control').keypress(function (e) {
        if (e.which === 13) {
            var index = $('.cattle_life_cycle .form-control').index(this) + 1;
            $('.cattle_life_cycle .form-control').eq(index).focus().select();
        }
    });

    $('.layerLifeCycleDetails .form-control').keypress(function (e) {
        if (e.which === 13) {
            layerLifeCycleDetailDataInsertUsingAjax($(this));
            var index = $('.layerLifeCycleDetails .form-control').index(this) + 1;
            $('.layerLifeCycleDetails .form-control').eq(index).focus().select();
        }
    });

}

function dataInsertUsingAjax(element) {
    var parentElement = element.closest('tr');
    var crmChickLifeCycleDetailId=parentElement.find('.crmChickLifeCycleDetails').val();
    var totalBirds=parentElement.find('.totalBirds').val();
    var ageDays=parentElement.find('.ageDays').val();
    var mortalityPes=parentElement.find('.mortalityPes').val();
    // var mortalityPercent=parentElement.find('.mortalityPercent').val();
    var weightStandard=parentElement.find('.weightStandard').val();
    var weightAchieved=parentElement.find('.weightAchieved').val();
    var feedTotalKg=parentElement.find('.feedTotalKg').val();
    // var perBird=parentElement.find('.perBird').val();
    var feedStandard=parentElement.find('.feedStandard').val();
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
            'totalBirds':totalBirds,
            'ageDays':ageDays,
            'mortalityPes':mortalityPes,
            // 'mortalityPercent':mortalityPercent,
            'weightStandard':weightStandard,
            'weightAchieved':weightAchieved,
            'feedTotalKg':feedTotalKg,
            // 'perBird':perBird,
            'feedStandard':feedStandard,
            // 'withoutMortality':withoutMortality,
            // 'withMortality':withMortality,
            'feedType':feedType,
            'proDate':proDate,
            'batchNo':batchNo,
            'remarks':remarks
        },
        dataType : 'json',
        success: function(response){
            parentElement.find('.mortalityPercent').text(response.mortalityPercent);
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
            parentElement.find('.presentBird').text(response.presentBird);
            parentElement.find('.eggProduction').text(response.eggProduction);
            parentElement.find('.targetWeight').text(response.targetWeight);
            parentElement.find('.targetFeedPerBird').text(response.targetFeedPerBird);
            parentElement.find('.targetEggProduction').text(response.targetEggProduction);
            parentElement.find('.eggWeightStandard').text(response.eggWeightStandard);
            /*parentElement.find('.perBird').text(response.perBird);
            parentElement.find('.withoutMortality').text(response.withoutMortality);
            parentElement.find('.withMortality').text(response.withMortality);*/
        }
    });
}

function getSonaliWeightStandardUsingAjax(element) {
    var parentElement = element.closest('tr');

    var ageDays = element.val();

    if(ageDays===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('crm_sonali_weight_standard_by_age'),
        type   : 'post',
        data   : {
            'ageDays':ageDays
        },
        dataType : 'json',
        success: function(response){
            parentElement.find('.weightStandard').val(response.weightStandard);
        }
    });
}

function initIntegerMask(el){
    $(el).inputmask("integer", {removeMaskOnSubmit: false});
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
                    $(".form-submit").html("SaveAndCreate").prop("disabled", false);
                    $('form#chick_life_cycle_form')[0].reset();
                    // location.reload();
                    // setTimeout( explode, 2000);
                    console.log(cattleLifeCycle_id);
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


$('.mortality_pes, .totalBirds').on('keypress keyup blur',function () {
    var mortalityPes = $('.mortality_pes').val();
    var totalbirds= $('.totalBirds').val();
    if(mortalityPes!='' && totalbirds!=''){
        var calculateValue = (parseFloat(mortalityPes)*100)/parseFloat(totalbirds);
        $('.mortality_percent').val(calculateValue);
        $('.mortality_percent').text(calculateValue);
    }

});
//feed per bird

$('.feedTotalkg, .totalBirds').on('keypress keyup blur',function () {
    var totalbirds= $('.totalBirds').val();
    var feedTotalkg=$('.feedTotalkg').val();
    if(feedTotalkg != '' && totalbirds!=''){
        var calculation=(parseFloat(feedTotalkg)/(totalbirds))*(1000);
        $('.perBird').val(calculation);
        $('.perBird').text(calculation);
    }
});
//fcr without mortality

$('.feedTotalkg, .totalBirds, .weightAchieved').on('keypress keyup blur',function () {
    var totalbirds= $('.totalBirds').val();
    var feedTotalkg=$('.feedTotalkg').val();
    var weightAchieved=$('.weightAchieved').val();
    if((feedTotalkg != '' && totalbirds!='')&&(weightAchieved !='')){
        var cal=(parseFloat(feedTotalkg)/(totalbirds));
        var calculation=(parseFloat(cal)/weightAchieved)*1000;

        $('.withoutMortality').val(calculation);
        $('.withoutMortality').text(calculation);
    }
});

//fcr with mortality

$('.feedTotalkg, .totalBirds,.mortality_pes,.weightAchieved').on('keypress keyup blur',function () {
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
});


var count = 0;

$('.addmore').click(function(){

    var el = $(this);
    var crm_visit_id = $('.crm_visit_id').val();
    var farmer_section = el.closest('tr.farmer_section');
    var farmer_purpose = farmer_section.find('.farmer_purpose').val();
    var farmer_firm_type = farmer_section.find('.farmer_firm_type').val();
    var farmer = farmer_section.find('.farmer').val();
    var farmer_capacity = farmer_section.find('.farmer_capacity').val();
    var farmer_comments = farmer_section.find('.farmer_comments').val();

    if(farmer_purpose==='' || farmer==='' || farmer_firm_type===''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_add'),
        type: 'POST',
        dataType: 'json',
        data:{
            'crm_visit_id':crm_visit_id,
            'purpose':farmer_purpose,
            'farmer_firm_type':farmer_firm_type,
            'farmer':farmer,
            'farmer_capacity':farmer_capacity,
            'comments':farmer_comments,
            'process':'farmer'
        },
        success: function(response) {
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'farmer'});
                $(".crm_detail_farmer_section").load(refreshUrl);
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
    var agent = agent_section.find('.agent').val();
    var agentComments = agent_section.find('.agentComments').val();
    if(agentPurpose==='' || agent===''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_add'),
        type: 'POST',
        dataType: 'json',
        data:{
            'crm_visit_id':crm_visit_id,
            'purpose':agentPurpose,
            'agent':agent,
            'comments':agentComments,
            'process':'agent'
        },
        success: function(response) {
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_visit_item_refresh',{'id':crm_visit_id,'process':'agent'});
                $(".crm_detail_agent_section").load(refreshUrl);
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
    if(agentPurpose==='' || agent===''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_add'),
        type: 'POST',
        dataType: 'json',
        data:{
            'crm_visit_id':crm_visit_id,
            'purpose':agentPurpose,
            'farmer':agent,
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
    if(agentPurpose==='' || agent===''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_add'),
        type: 'POST',
        dataType: 'json',
        data:{
            'crm_visit_id':crm_visit_id,
            'purpose':agentPurpose,
            'farmer':agent,
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


$(document).on('click', '#crm-farmer-btn', function(e) {

    e.preventDefault();
    var name =$(this).closest("form").find(".name").val();
    var mobile = $(this).closest("form").find(".mobile").val();

    if (name === "") {
        alert("Name must be filled out");
        return false;
    }
    else if(mobile==="" || mobile ===null){
        alert("Your mobile number is Invalid :" +mobile);
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
        }
    });

});

$(document).on('click', '#crm-other-agent-btn', function(e) {

    e.preventDefault();
    var name =$(this).closest("form").find(".name").val();
    var mobile = $(this).closest("form").find(".mobile").val();

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
            $('form#otherAgentForm')[0].reset();
        }
    });

});

$(document).on('click', '#crm-sub-agent-btn', function(e) {

    e.preventDefault();
    var name =$(this).closest("form").find(".name").val();
    var mobile = $(this).closest("form").find(".mobile").val();

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
            $('form#subAgentForm')[0].reset();
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
$(document).on('change', '.other_agent', function(e) {
    var element = $(this);
    var id = $(this).val();
    element.closest('tr').find('.other_agent_address').val('');
    element.closest('tr').find('.other_agent_mobile').val('');
    if(id==''){
        return false;
    }
    var url = Routing.generate('get_farmer_ajax',{'id':id});
    $.get(url, function(data){
        element.closest('tr').find('.other_agent_address').val(data[0]['address']);
        element.closest('tr').find('.other_agent_mobile').val(data[0]['phone']);
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
    var url = Routing.generate('get_farmer_ajax',{'id':id});
    $.get(url, function(data){
        element.closest('tr').find('.sub_agent_address').val(data[0]['address']);
        element.closest('tr').find('.sub_agent_mobile').val(data[0]['phone']);
    });

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


