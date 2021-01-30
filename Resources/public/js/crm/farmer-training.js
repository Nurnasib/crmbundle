
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
            var htmlOption='';
            $.each( response, function( key, value ) {
                htmlOption += '<div class="form-check form-check-inline">\n' +
                    '  <input name="training_material['+value['id']+']" class="form-check-input" type="checkbox" id="'+value['id']+'" value="'+value['text']+'">\n' +
                    '  <label class="form-check-label" for="'+value['id']+'">'+value['text']+'</label>\n' +
                    '</div>'
            });

            $('#training_materials').html(htmlOption);

        }
    });
}