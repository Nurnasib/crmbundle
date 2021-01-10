$(document).on('click', '.crmFatteningPerformanceDetailAdd', function () {
    fatteningDataInsertUsingAjax($(this));
});

$(document).on('click', '.crmDairyPerformanceDetailAdd', function () {
    dairyDataInsertUsingAjax($(this));
});


$(document).on('keypress','.cattle_performance_report input[type=text], .cattle_performance_report input[type=number], .cattle_performance_report select',function (e) {
    if (e.which === 13) {
        /*var index = $('.fcrReportDetails .form-control').index(this) + 1;
        $('.fcrReportDetails .form-control').eq(index).focus().select();*/
        e.preventDefault();
        // Get all focusable elements on the page
        var $canfocus = $('.cattle_performance_report :focusable');
        var index = $canfocus.index(this) + 1;
        // if (index >= $canfocus.length) index = 0;
        if (index >= $canfocus.length-1){
            index = 0;
        }

        $canfocus.eq(index).focus().select();
    }
});

function fatteningDataInsertUsingAjax(element) {
    var cattlePerformance_id = $('.cattlePerformance_id').val();
    var parentElement = element.closest('tr');
    var customerId=parentElement.find('.customerId').val();
    var breed_type=parentElement.find('.breed_type').val();
    var feed_type=parentElement.find('.feed_type').val();
    var visiting_date=parentElement.find('.visiting_date').val();
    var age_of_cattle_month=parentElement.find('.age_of_cattle_month').val();
    var previous_body_weight=parentElement.find('.previous_body_weight').val();
    var present_body_weight=parentElement.find('.present_body_weight').val();
    var duration_of_bwt_difference=parentElement.find('.duration_of_bwt_difference').val();
    var consumption_feed_intake_ready_feed=parentElement.find('.consumption_feed_intake_ready_feed').val();
    var consumption_feed_intake_conventional=parentElement.find('.consumption_feed_intake_conventional').val();

    var fodder_green_grass_kg=parentElement.find('.fodder_green_grass_kg').val();
    var fodder_straw_kg=parentElement.find('.fodder_straw_kg').val();
    var name_of_ready_feed=parentElement.find('.name_of_ready_feed').val();
    var remarks=parentElement.find('.remarks').val();

    if(cattlePerformance_id===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('crm_fattening_performance_detail_report_add',{'id':cattlePerformance_id}),
        type   : 'post',
        data   : {
            'customerId':customerId,
            'breed_type':breed_type,
            'feed_type':feed_type,
            'visiting_date':visiting_date,
            'age_of_cattle_month':age_of_cattle_month,
            'previous_body_weight':previous_body_weight,
            'present_body_weight':present_body_weight,
            'duration_of_bwt_difference':duration_of_bwt_difference,
            'consumption_feed_intake_ready_feed':consumption_feed_intake_ready_feed,
            'consumption_feed_intake_conventional':consumption_feed_intake_conventional,
            'fodder_green_grass_kg':fodder_green_grass_kg,
            'fodder_straw_kg':fodder_straw_kg,
            'name_of_ready_feed':name_of_ready_feed,
            'remarks':remarks
        },
        dataType : 'json',
        success: function(response){
            // console.log(response.data);
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_cattle_performance_detail_refresh',{'id':cattlePerformance_id});
                $('body').find("tbody.cattlePerformanceDetailsSection").load(refreshUrl);
                parentElement.find(':input').not('.customerId').val('');
                parentElement.find('select').val('');
                parentElement.find('.customerId').val(customerId);
            }
        }
    });
}

function dairyDataInsertUsingAjax(element) {
    var cattlePerformance_id = $('.cattlePerformance_id').val();
    var parentElement = element.closest('tr');
    var customerId=parentElement.find('.customerId').val();
    var breed_type=parentElement.find('.breed_type').val();
    var feed_type=parentElement.find('.feed_type').val();
    var visiting_date=parentElement.find('.visiting_date').val();
    var age_of_cattle_month=parentElement.find('.age_of_cattle_month').val();
    var present_body_weight=parentElement.find('.present_body_weight').val();

    var lactation_no=parentElement.find('.lactation_no').val();
    var age_of_lactation=parentElement.find('.age_of_lactation').val();
    var average_weight_per_day=parentElement.find('.average_weight_per_day').val();
    var milk_fat_percentage=parentElement.find('.milk_fat_percentage').val();

    var consumption_feed_intake_ready_feed=parentElement.find('.consumption_feed_intake_ready_feed').val();
    var consumption_feed_intake_conventional=parentElement.find('.consumption_feed_intake_conventional').val();
    var fodder_green_grass_kg=parentElement.find('.fodder_green_grass_kg').val();
    var fodder_straw_kg=parentElement.find('.fodder_straw_kg').val();
    var name_of_ready_feed=parentElement.find('.name_of_ready_feed').val();
    var remarks=parentElement.find('.remarks').val();

    if(cattlePerformance_id===''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('crm_dairy_performance_detail_report_add',{'id':cattlePerformance_id}),
        type   : 'post',
        data   : {
            'customerId':customerId,
            'breed_type':breed_type,
            'feed_type':feed_type,
            'visiting_date':visiting_date,
            'age_of_cattle_month':age_of_cattle_month,
            'present_body_weight':present_body_weight,
            'lactation_no':lactation_no,
            'age_of_lactation':age_of_lactation,
            'average_weight_per_day':average_weight_per_day,
            'milk_fat_percentage':milk_fat_percentage,
            'consumption_feed_intake_ready_feed':consumption_feed_intake_ready_feed,
            'consumption_feed_intake_conventional':consumption_feed_intake_conventional,
            'fodder_green_grass_kg':fodder_green_grass_kg,
            'fodder_straw_kg':fodder_straw_kg,
            'name_of_ready_feed':name_of_ready_feed,
            'remarks':remarks
        },
        dataType : 'json',
        success: function(response){
            if(response.status===200){
                var refreshUrl = Routing.generate('crm_cattle_performance_detail_refresh',{'id':cattlePerformance_id});
                $('body').find("tbody.cattlePerformanceDetailsSection").load(refreshUrl);
                parentElement.find(':input').not('.customerId').val('');
                parentElement.find('select').val('');
                // parentElement.find('.customerId').val(customerId);
            }
        }
    });
}


