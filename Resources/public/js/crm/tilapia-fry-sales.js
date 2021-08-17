$('.tilapia_fry_sales_add_button').on('click',function () {
    fishSalesPriceDataUpdateInsertAjax($(this));
});

$('.tilapia_fry_sales_competitor_add_button').on('click',function () {
    fishSalesPriceCompetitorDataUpdateInsertAjax($(this));
});


function fishSalesPriceDataUpdateInsertAjax(element) {

    var closestTr=element.closest('tr');
    var agent_id= closestTr.find('.agent_id').val();
    var month= closestTr.find('.month').val();
    var quantity= closestTr.find('.quantity').val();

    if(agent_id==''||month==''||quantity==''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('tilapia_fry_sales_data_insert'),
        type   : 'post',
        data   : {
            'agent_id':agent_id,
            'month':month,
            'quantity':quantity,
        },
        dataType : 'json',
        success: function(response){

            if(response.status===200){
                closestTr.find('.agent_id').val('');
                closestTr.find('.month').val('');
                closestTr.find('.quantity').val('');

                var refreshUrl = Routing.generate('crm_tilapia_fry_sales_refresh');
                $('.tilapiaFrySales_nourish_table tbody').load(refreshUrl);

            }
        }
    });

}

function fishSalesPriceCompetitorDataUpdateInsertAjax(element) {

    var closestTr=element.closest('tr');
    var agent_id= closestTr.find('.agent_id').val();
    var feed_id= closestTr.find('.feed_id').val();
    var month= closestTr.find('.month').val();
    var quantity= closestTr.find('.quantity').val();

    if(agent_id==''||feed_id==''||month==''||quantity==''){
        return false;
    }

    $.ajax({
        url    : Routing.generate('competitor_tilapia_fry_sales_data_insert'),
        type   : 'post',
        data   : {
            'agent_id':agent_id,
            'feed_id':feed_id,
            'month':month,
            'quantity':quantity,
        },
        dataType : 'json',
        success: function(response){
console.log(response);
            if(response.status===200){
                closestTr.find('.agent_id').val('');
                closestTr.find('.feed_id').val('');
                closestTr.find('.month').val('');
                closestTr.find('.quantity').val('');

               /* var refreshUrl = Routing.generate('crm_tilapia_fry_sales_refresh');
                $('.tilapiaFrySales_nourish_table tbody').load(refreshUrl);*/

            }
        }
    });

}