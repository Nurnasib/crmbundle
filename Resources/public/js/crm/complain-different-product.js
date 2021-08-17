
function formSubmitProcessForComplainDifferentProduct(element) {
    var parentElement = element.closest('#complain_different_product_form');

    parentElement.find(':input').removeClass('is-invalid');

    var complain_different_product_form_productName = parentElement.find('#complain_different_product_form_productName').val();

    if (complain_different_product_form_productName=='' || typeof complain_different_product_form_productName === "undefined"){
       parentElement.find('#complain_different_product_form_productName').addClass('is-invalid');
       return false;
    }

    $.ajax({
        url         : $('form#complain_different_product_form').attr( 'action' ),
        type        : $('form#complain_different_product_form').attr( 'method' ),
        data        : new FormData($('form#complain_different_product_form')[0]),
        processData : false,
        contentType : false,
        beforeSend: function() {
            $('.form-submit').html("Loading...").attr('disabled', 'disabled');
        },
        success: function(response){
            $("#process-msg").show();
            $(".alert-success").html(response);
            $(".form-submit").html("SaveAndCreate").prop("disabled", false);
            $('form#complain_different_product_form')[0].reset();
            console.log(response);
            // location.reload();
            if(response.id){
                var refreshUrl = Routing.generate('complain_different_product_detail_modal',{'id':response.id});
                $('.complain_different_product_details').load(refreshUrl);
            }

           }
    });

}