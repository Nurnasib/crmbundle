
function formSubmitProcessForFishFeedComplain(element) {
    var parentElement = element.closest('#fish_feed_complain_form');

    parentElement.find(':input').removeClass('is-invalid');

    var fish_feed_complain_form_feedMaill = parentElement.find('#fish_feed_complain_form_feedMaill').val();

    if (fish_feed_complain_form_feedMaill=='' || typeof fish_feed_complain_form_feedMaill === "undefined"){
       parentElement.find('#fish_feed_complain_form_feedMaill').addClass('is-invalid');
       return false;
    }

    $.ajax({
        url         : $('form#fish_feed_complain_form').attr( 'action' ),
        type        : $('form#fish_feed_complain_form').attr( 'method' ),
        data        : new FormData($('form#fish_feed_complain_form')[0]),
        processData : false,
        contentType : false,
        beforeSend: function() {
            $('.form-submit').html("Loading...").attr('disabled', 'disabled');
        },
        success: function(response){
            $("#process-msg").show();
            $(".alert-success").html(response);
            $(".form-submit").html("SaveAndCreate").prop("disabled", false);
            $('form#fish_feed_complain_form')[0].reset();
            console.log(response);
            // location.reload();
            if(response.id){
                var refreshUrl = Routing.generate('crm_fish_feed_complain_refresh');
                $('.fish_feed_complain_details').load(refreshUrl);
            }

           }
    });

}