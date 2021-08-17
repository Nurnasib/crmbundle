
function formSubmitProcessForAntibioticFreeFarm(element) {
    var parentElement = element.closest('#antibiotic_free_farm_form');

    parentElement.find(':input').removeClass('is-invalid');

    var antibiotic_free_farm_form_reporting_month = parentElement.find('#antibiotic_free_farm_form_reporting_month').val();

    if (antibiotic_free_farm_form_reporting_month=='' || typeof antibiotic_free_farm_form_reporting_month === "undefined"){
       parentElement.find('#antibiotic_free_farm_form_reporting_month').addClass('is-invalid');
       return false;
    }

    var antibiotic_free_farm_form_hatching_date = parentElement.find('#antibiotic_free_farm_form_hatching_date').val();

    if (antibiotic_free_farm_form_hatching_date=='' || typeof antibiotic_free_farm_form_hatching_date === "undefined"){
       parentElement.find('#antibiotic_free_farm_form_hatching_date').addClass('is-invalid');
       return false;
    }

    $.ajax({
        url         : $('form#antibiotic_free_farm_form').attr( 'action' ),
        type        : $('form#antibiotic_free_farm_form').attr( 'method' ),
        data        : new FormData($('form#antibiotic_free_farm_form')[0]),
        processData : false,
        contentType : false,
        beforeSend: function() {
            $('.form-submit').html("Loading...").attr('disabled', 'disabled');
        },
        success: function(response){
            $("#process-msg").show();
            $(".alert-success").html(response);
            $(".form-submit").html("SaveAndCreate").prop("disabled", false);
            $('form#antibiotic_free_farm_form')[0].reset();
            console.log(response);
            // location.reload();
            var refreshUrl = Routing.generate('antibiotic_free_farm_detail_modal',{'id':response.id});
            $('.antibiotic_free_farm_details_section').load(refreshUrl);
            /*var inst = $('[data-remodal-id=modal]').remodal();
            setTimeout(function(){
                inst.open();
                formCommonProcess();
                console.log(response.id);
            }, 50);*/

        }
    });




}