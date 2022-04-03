
function formSubmitProcessForAgentUpgradationReport(element) {
    var parentElement = element.closest('#agent_upgradation_report');

    parentElement.find(':input').removeClass('is-invalid');

        var agent_upgradation_report_form_breedName = parentElement.find('#agent_upgradation_report_form_breedName').val();
        if (agent_upgradation_report_form_breedName==='' || typeof agent_upgradation_report_form_breedName === "undefined"){
       parentElement.find('#agent_upgradation_report_form_breedName').addClass('is-invalid');

       return false;
    }
        var agent_upgradation_report_form_reporting_month = parentElement.find('#agent_upgradation_report_form_reporting_month').val();
        if (agent_upgradation_report_form_reporting_month==='' || typeof agent_upgradation_report_form_reporting_month === "undefined"){
       parentElement.find('#agent_upgradation_report_form_reporting_month').addClass('is-invalid');

       return false;
    }

    $.ajax({
        url         : $('form#agent_upgradation_report').attr( 'action' ),
        type        : $('form#agent_upgradation_report').attr( 'method' ),
        data        : new FormData($('form#agent_upgradation_report')[0]),
        processData : false,
        contentType : false,
        beforeSend: function() {
            $('.form-submit').attr('disabled', 'disabled');
        },
        success: function(response){
            $("#process-msg").show();
            $(".alert-success").html(response);
            $(".form-submit").prop("disabled", false);
            $('form#agent_upgradation_report')[0].reset();
            if(response.id){
                var refreshUrl = Routing.generate('agent_upgradation_report_refresh',{'id':response.id});
                $('.agent_upgradation_report_detail').load(refreshUrl);
            }

        }
    });




}