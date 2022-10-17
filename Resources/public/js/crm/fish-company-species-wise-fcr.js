$('.companySpeciesWiseFcrSection').on('keypress','.company_species_wise_fcr input[type=text], .company_species_wise_fcr input[type=number]',function (e) {
    if (e.which === 13) {
        e.preventDefault();
        var $canfocus = $('.company_species_wise_fcr :focusable');
        var index = $canfocus.index(this) + 1;
        if (index >= $canfocus.length){
            index = 0;
        }
        $canfocus.eq(index).focus().select();

        fishCompanySpeciesWiseFcrDetailDataUpdateUsingAjax($(this));
    }
});


$('.companySpeciesWiseFcrSection').on('blur' , '.company_species_wise_fcr input[type=text], .company_species_wise_fcr input[type=number]', function (e) {
    fishCompanySpeciesWiseFcrDetailDataUpdateUsingAjax($(this));
});


function fishCompanySpeciesWiseFcrDetailDataUpdateUsingAjax(element) {
    var fishCompanySpeciesWiseFcr_id= $('.fishCompanySpeciesWiseFcr_id').val();
    var parentElement = element.closest('tr');
    var detailId=element.attr('data-entity-id');
    var dataMetaValue=element.val();
    var dataMetaKey=element.attr('data-key');

    if(detailId===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('fish_company_species_wise_fcr_details_data_update',{'id':detailId}),
        type   : 'post',
        data   : {
            'dataMetaValue':dataMetaValue,
            'dataMetaKey':dataMetaKey,
        },
        dataType : 'json',
        success: function(response){
            if(response.status===200){
                
                $(parentElement).find('.fcrQuantity').text(response.quantity);

                // var refreshUrl = Routing.generate('crm_fish_company_species_wise_fcr_refresh',{'id':fishCompanySpeciesWiseFcr_id});
                // $(".fullMonthCompanySpeciesWiseFcrBody").load(refreshUrl);

            }

        }
    });
}

$(document).on('click', '.remove', function(){
    var element = $(this);
    var url = $(this).attr('data-action');
    if (confirm('Are you sure want to delete this record?')) {
        $.post(url, function( data ) {
            element.closest('tr').remove();
        });

    }
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