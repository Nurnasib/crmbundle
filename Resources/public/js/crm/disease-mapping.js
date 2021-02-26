
function formSubmitProcessForDiseaseMapping(element) {
    var parentElement = element.closest('#disease_mapping_form');

    parentElement.find(':input').removeClass('is-invalid');

    if (parentElement.find('.disease_visiting_date').length) {
        var disease_visiting_date = parentElement.find('.disease_visiting_date').val();
        if (disease_visiting_date==='' || typeof disease_visiting_date === "undefined"){
       parentElement.find('.disease_visiting_date').addClass('is-invalid');

       return false;
    }
    }

    $.ajax({
        url         : $('form#disease_mapping_form').attr( 'action' ),
        type        : $('form#disease_mapping_form').attr( 'method' ),
        data        : new FormData($('form#disease_mapping_form')[0]),
        processData : false,
        contentType : false,
        beforeSend: function() {
            $('.form-submit').html("Loading...").attr('disabled', 'disabled');
        },
        success: function(response){
            $("#process-msg").show();
            $(".alert-success").html(response);
            $(".form-submit").html("SaveAndCreate").prop("disabled", false);
            $('form#disease_mapping_form')[0].reset();
            if(response.id){
                var refreshUrl = Routing.generate('disease_mapping_detail_modal',{'id':response.id});
                $('.disease_mapping_detail').load(refreshUrl);
            }

        }
    });




}