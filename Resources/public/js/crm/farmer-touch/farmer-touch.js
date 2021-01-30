
function formSubmitProcessForFishFarmerTouchReport() {
    var farmer_touch_report_id = $('.farmer_touch_report_id').val();
    if(farmer_touch_report_id === ''){
        return false;
    }
    $.ajax({
        url         : $('form#fish_farmer_touch_report_form').attr( 'action' ),
        type        : $('form#fish_farmer_touch_report_form').attr( 'method' ),
        data        : new FormData($('form#fish_farmer_touch_report_form')[0]),
        processData : false,
        contentType : false,
        beforeSend: function() {
            $('.form-submit').html("Loading...").attr('disabled', 'disabled');
        },
        success: function(response){
            $("#process-msg").show();
            $(".alert-success").html(response);
            // $(".form-submit").html("Complete").prop("disabled", false);
            $('form#fish_farmer_touch_report_form')[0].reset();
            // location.reload();
            // setTimeout( explode, 2000);
                var refreshUrl = Routing.generate('crm_farmer_touch_refresh',{'id':farmer_touch_report_id});
                $("tbody.fishFarmerTouchReportBody").load(refreshUrl);

        }
    });
}