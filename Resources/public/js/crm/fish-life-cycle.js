$('.fishLifeCycleSection').on('keypress','.fish_life_cycle input[type=text], .fish_life_cycle input[type=number], .fish_life_cycle select.hatchery',function (e) {
    if (e.which === 13) {
        e.preventDefault();
        var $canfocus = $('.fish_life_cycle :focusable');
        var index = $canfocus.index(this) + 1;
        if (index >= $canfocus.length){
            index = 0;
        }
        $canfocus.eq(index).focus().select();

        fishLifeCycleDetailDataUpdateUsingAjax($(this));
    }
});


$('.fishLifeCycleSection').on('blur' , '.fish_life_cycle input[type=text], .fish_life_cycle input[type=number], .fish_life_cycle select.hatchery', function (e) {
    fishLifeCycleDetailDataUpdateUsingAjax($(this));
});

$('.fishLifeCycleSection').on('click' , '.addFishLifeCycleDetailSpecies', function (e) {
    var element=$(this);
    var fishLifeCycle_id = $('.fishLifeCycle_id').val();
    var entityId = $(element).attr('data-details-entity-id');
    var feedType = $(element).closest('tr').find('.feed_type').val();
    var main_culture_species = $(element).closest('tr').find('.main_culture_species').val();
    var feed_consumption_kg = $(element).closest('tr').find('.feed_consumption_kg').val();
    if(typeof entityId == 'undefined'){
        return false;
    }
    if(entityId==''|| feedType=='' || main_culture_species=='' || feed_consumption_kg==''){
        alert('Please enter value');
        return false;
    }
    $.ajax({
        url    : Routing.generate('fish_life_cycle_detail_species_data_insert',{'id':entityId}),
        type   : 'post',
        data   : {
            'feedType':feedType,
            'main_culture_species':main_culture_species,
            'feed_consumption_kg':feed_consumption_kg
        },
        dataType : 'json',
        success: function(response){
            if(response.status===200){

                console.log(response.currentFeedConsumptionKg);
                $('.'+response.id+'_feedType').text(response.feedType);
                $('.'+response.id+'_mainCultureSpecies').text(response.mainCultureSpecieses);
                $('.'+response.id+'_totalInitialWeight').text(response.totalInitialWeight);
                $('.'+response.id+'_currentCultureDayst').text(response.currentCultureDays);
                $('.'+response.id+'_weightGainKg').text(response.weightGainKg);
                $('.'+response.id+'_currentFcr').text(response.currentFcr);
                $('.'+response.id+'_currentAdg').text(response.currentAdg);
                $('.'+response.id+'_finalWeightGm').text(response.finalWeightGm);
                $('.'+response.id+'_finalWeightKg').text(response.finalWeightKg);
                $('.'+response.id+'_totalDayOfCulture').text(response.totalDayOfCulture);
                $('.'+response.id+'_currentFeedConsumptionKg').text(response.currentFeedConsumptionKg);
                $('.'+response.id+'_totalFeedConsumptionKg').text(response.totalFeedConsumptionKg);
                $('.'+response.id+'_finalFcr').text(response.finalFcr);
                $('.'+response.id+'_finalAdg').text(response.finalAdg);
                $('.'+response.id+'_srPercentage').text(response.srPercentage);
                $('.'+response.id+'_totalSeedCost').text(response.totalSeedCost);
                $('.'+response.id+'_totalFeedCost').text(response.totalFeedCost);
                $('.'+response.id+'_feedCostPerKgFish').text(response.feedCostPerKgFish);
                $('.'+response.id+'_totalCost').text(response.totalCost);
                $('.'+response.id+'_productionCostPerKgFish').text(response.productionCostPerKgFish);
                $('.'+response.id+'_totalIncome').text(response.totalIncome);
                $('.'+response.id+'_netProfitOrLoss').text(response.netProfitOrLoss);
                $('.'+response.id+'_retuneOverInvestment').text(response.retuneOverInvestment);
                console.log(fishLifeCycle_id);
                var refreshUrl = Routing.generate('crm_fish_life_cycle_refresh',{'id':fishLifeCycle_id});
                $(".fishLifeCycleDetailsSection").load(refreshUrl);
            }
        }
    })
});


$('.fishLifeCycleSection').on('change' , '.feed_type', function (e) {
    var element=$(this);
    var feedType = $(element).val();
    
    if(typeof feedType == 'undefined'){
        return false;
    }
    if(feedType==''){
        return false;
    }
    $.ajax({
        url: Routing.generate('crm_main_culture_species_name',{'id':feedType})
    }).done(function(data) {

        $(element).closest('tr').find('.main_culture_species').find('option').remove();
        $(element).closest('tr').find('.main_culture_species').append($('<option>', {value:'', text:'Choose Culture Species'}));

        $.each(data, function(i, item) {
            $(element).closest('tr').find('.main_culture_species').append($('<option>', {value:item.id, text:item.name}));
        });

    });
});


function fishLifeCycleDetailDataUpdateUsingAjax(element) {
    var fishLifeCycle_id = $('.fishLifeCycle_id').val();
    var parentElement = element.closest('tr');
    var crmFishLifeCycleDetailId=element.attr('data-entity-id');
    var dataMetaKey=element.attr('data-meta-key');
    var dataInputType=element.attr('data-input-type');
    var dataMetaValue=element.val();

    if(typeof crmFishLifeCycleDetailId == 'undefined'){
        return false;
    }
    if(crmFishLifeCycleDetailId==''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('fish_life_cycle_details_data_update',{'id':crmFishLifeCycleDetailId}),
        type   : 'post',
        data   : {
            'dataMetaKey':dataMetaKey,
            'dataMetaValue':dataMetaValue,
            'dataInputType':dataInputType
        },
        dataType : 'json',
        success: function(response){
            if(response.status===200){

                $('.'+response.id+'_feedType').text(response.feedType);
                $('.'+response.id+'_mainCultureSpecies').text(response.mainCultureSpecieses);
                $('.'+response.id+'_totalInitialWeight').text(response.totalInitialWeight);
                $('.'+response.id+'_currentCultureDayst').text(response.currentCultureDays);
                $('.'+response.id+'_weightGainKg').text(response.weightGainKg);
                $('.'+response.id+'_currentFcr').text(response.currentFcr);
                $('.'+response.id+'_currentAdg').text(response.currentAdg);
                $('.'+response.id+'_finalWeightGm').text(response.finalWeightGm);
                $('.'+response.id+'_finalWeightKg').text(response.finalWeightKg);
                $('.'+response.id+'_totalDayOfCulture').text(response.totalDayOfCulture);
                $('.'+response.id+'_currentFeedConsumptionKg').text(response.currentFeedConsumptionKg);
                $('.'+response.id+'_totalFeedConsumptionKg').text(response.totalFeedConsumptionKg);
                $('.'+response.id+'_finalFcr').text(response.finalFcr);
                $('.'+response.id+'_finalAdg').text(response.finalAdg);
                $('.'+response.id+'_srPercentage').text(response.srPercentage);
                $('.'+response.id+'_totalSeedCost').text(response.totalSeedCost);
                $('.'+response.id+'_totalFeedCost').text(response.totalFeedCost);
                $('.'+response.id+'_feedCostPerKgFish').text(response.feedCostPerKgFish);
                $('.'+response.id+'_totalCost').text(response.totalCost);
                $('.'+response.id+'_productionCostPerKgFish').text(response.productionCostPerKgFish);
                $('.'+response.id+'_totalIncome').text(response.totalIncome);
                $('.'+response.id+'_netProfitOrLoss').text(response.netProfitOrLoss);
                $('.'+response.id+'_retuneOverInvestment').text(response.retuneOverInvestment);
console.log(fishLifeCycle_id);
                var refreshUrl = Routing.generate('crm_fish_life_cycle_refresh',{'id':fishLifeCycle_id});
                $(".fishLifeCycleDetailsSection").load(refreshUrl);
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