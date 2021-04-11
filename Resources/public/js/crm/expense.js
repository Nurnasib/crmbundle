
$(document).on('keypress','.expense_form_body input[type=text], .expense_form_body input[type=number], .expense_form_body select',function (e) {
    if (e.which === 13) {

        e.preventDefault();
        var $canfocus = $('.expense_form_body :focusable');
        var index = $canfocus.index(this) + 1;
        if (index >= $canfocus.length-1){
            index = 0;
        }
        $canfocus.eq(index).focus().select();

        $('#expense_form').submit(function() {
            return false;
        });
    }
});

$(document).on('click', '.expense_form_button', function () {

    formSubmitExpenseForm();
});


function formSubmitExpenseForm() {


    $.ajax({
        url         : $('form#expense_form').attr( 'action' ),
        type        : $('form#expense_form').attr( 'method' ),
        data        : new FormData($('form#expense_form')[0]),
        processData : false,
        contentType : false,
        success: function(response){

            // $('form#expense_form')[0].reset();

        }
    });
}



