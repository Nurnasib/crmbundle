
function formSubmitProcessForLessCostingFarm(element) {
    var parentElement = element.closest('#less_costing_farm_form');

    parentElement.find(':input').removeClass('is-invalid');

    var less_costing_farm_form_reporting_month = parentElement.find('.less_costing_farm_form_reporting_month').val();

    if (less_costing_farm_form_reporting_month==='' || typeof less_costing_farm_form_reporting_month === "undefined"){
       parentElement.find('.less_costing_farm_form_reporting_month').addClass('is-invalid');
       return false;
    }

    if (parentElement.find('.less_costing_farm_form_hatching_date').length) {
        var less_costing_farm_form_hatching_date = parentElement.find('.less_costing_farm_form_hatching_date').val();
        if (less_costing_farm_form_hatching_date==='' || typeof less_costing_farm_form_hatching_date === "undefined"){
       parentElement.find('.less_costing_farm_form_hatching_date').addClass('is-invalid');

       return false;
    }
    }

    $.ajax({
        url         : $('form#less_costing_farm_form').attr( 'action' ),
        type        : $('form#less_costing_farm_form').attr( 'method' ),
        data        : new FormData($('form#less_costing_farm_form')[0]),
        processData : false,
        contentType : false,
        beforeSend: function() {
            $('.form-submit').html("Loading...").attr('disabled', 'disabled');
        },
        success: function(response){
            $("#process-msg").show();
            $(".alert-success").html(response);
            $(".form-submit").html("SaveAndCreate").prop("disabled", false);
            $('form#less_costing_farm_form')[0].reset();
            console.log(response);
            // location.reload();
            var refreshUrl = Routing.generate('cost_benefit_analysis_less_costing_farm_detail_modal',{'id':response.id});
            $('.less_costing_farm_details_section').load(refreshUrl);
        }
    });




}