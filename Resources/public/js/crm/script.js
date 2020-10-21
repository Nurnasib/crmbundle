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

function formCommonProcess() {

    $('.form-body').slimScroll({
        height: '400px'
    });
    $('[data-toggle="tooltip"]').tooltip();

    $('.mobileLocal').mask("00000-000000", {placeholder: "_____-______"});

    $('.checkboxToggle').bootstrapToggle();

    $('.multi-select2').multiSelect({ selectableOptgroup: true });

    $('#optgroup').multiSelect({ selectableOptgroup: true });
    $('.select2').select2({
        theme: 'bootstrap4'
    });
}


function formSubmitProcess() {

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
                    $('form#chick_life_cycle_form')[0].reset();
                    $("#process-msg").show();
                    $(".alert-success").html(response);
                    setTimeout( explode, 2000);
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
        var calculation=(parseFloat(feedTotalkg)/(totalbirds))*(1000)
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
    var farmer = farmer_section.find('.farmer').val();
    var farmer_capacity = farmer_section.find('.farmer_capacity').val();
    var farmer_comments = farmer_section.find('.farmer_comments').val();
    if(farmer_purpose==='' || farmer===''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_visit_item_add'),
        type: 'POST',
        dataType: 'json',
        data:{
            'crm_visit_id':crm_visit_id,
            'purpose':farmer_purpose,
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

