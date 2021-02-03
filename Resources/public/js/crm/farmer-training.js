
$(document).on('change','.breed_name', function () {
    var breed_name = $(this).val();
    getFarmerTrainingMaterialsByBreedName(breed_name);
});


function getFarmerTrainingMaterialsByBreedName(breedName) {
    if(breedName === ''){
        return false;
    }
    $.ajax({
        url    : Routing.generate('crm_farmer_training_material_ajax',{'id':breedName}),
        type   : 'post',
        dataType : 'json',
        success: function(response){
            console.log(response.species);
            var htmlOption='';
            $.each( response.materials, function( key, value ) {
                htmlOption += '<tr>' +
                    ' <td> <input name="training_material['+value['id']+']" type="checkbox" id="'+value['id']+'" value="'+value['text']+'"></td>' +
                    ' <td>'+value['text']+'</td>' +
                    ' <td> <input name="training_material_qty['+value['id']+']" class="form-control" type="number" min="0" value=""></td>' +
                    '</tr>'
            });

            $('#training_materials').html(htmlOption);

            var htmlSpecies='';
            $.each( response.species, function( key, value ) {
                htmlSpecies += '<tr>' +
                    ' <td>'+value['text']+'</td>' +
                    ' <td> <input name="species_capacity['+value['id']+']" class="form-control" type="number" min="0" value=""></td>' +
                    '</tr>'
            });

            $('#species_capacity').html(htmlSpecies);

        }
    });

}

function formSubmitProcessForFarmerTrainingReport(element) {
    var parentElement = element.closest('#farmer_training_report_form');

    parentElement.find(':input').removeClass('is-invalid');

    var farmer_training_report_form_training_date = parentElement.find('#farmer_training_report_form_training_date').val();

    if (farmer_training_report_form_training_date=='' || typeof farmer_training_report_form_training_date === "undefined"){
       parentElement.find('#farmer_training_report_form_training_date').addClass('is-invalid');
       return false;
    }

    var farmer_training_report_form_breed_name = parentElement.find('#farmer_training_report_form_breed_name').val();

    if (farmer_training_report_form_breed_name=='' || typeof farmer_training_report_form_breed_name === "undefined"){
       parentElement.find('#farmer_training_report_form_breed_name').addClass('is-invalid');
       return false;
    }

    $.ajax({
        url         : $('form#farmer_training_report_form').attr( 'action' ),
        type        : $('form#farmer_training_report_form').attr( 'method' ),
        data        : new FormData($('form#farmer_training_report_form')[0]),
        processData : false,
        contentType : false,
        beforeSend: function() {
            $('.form-submit').html("Loading...").attr('disabled', 'disabled');
        },
        success: function(response){
            $("#process-msg").show();
            $(".alert-success").html(response);
            // $(".form-submit").html("Complete").prop("disabled", false);
            $('form#farmer_training_report_form')[0].reset();
            console.log(response);
            // location.reload();
            // setTimeout( explode, 2000);
             var refreshUrl = Routing.generate('farmer_training_report_detail_modal',{'id':response.id});
             $(".farmer_training_report_section").load(refreshUrl);

        }
    });
}