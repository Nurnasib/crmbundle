
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
                htmlOption += '<tr>' +
                    ' <td> <input name="training_material['+value['id']+']" class="form-check-input" type="checkbox" id="'+value['id']+'" value="'+value['text']+'"></td>' +
                    ' <td>'+value['text']+'</td>' +
                    ' <td> <input name="training_material_qty['+value['id']+']" class="form-control" type="number" min="0" value=""></td>' +
                    '</tr>'
            });

            $('#training_materials').html(htmlOption);

        }
    });
}