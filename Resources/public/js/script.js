//mortality percent

$('.mortality_pes, .totalBirds').on('keypress keyup blur',function () {
    var mortalityPes = $('.mortality_pes').val();
    var totalbirds= $('.totalBirds').val();
    if(mortalityPes!='' && totalbirds!=''){
        var calculateValue = (parseFloat(mortalityPes)*100)/parseFloat(totalbirds);
        $('.mortality_percent').val(calculateValue);
        $('.mortality_percent').text(calculateValue);
    }

});
//feed per bird

$('.feedTotalkg, .totalBirds').on('keypress keyup blur',function () {
    var totalbirds= $('.totalBirds').val();
    var feedTotalkg=$('.feedTotalkg').val();
    if(feedTotalkg != '' && totalbirds!=''){
        var calculation=(parseFloat(feedTotalkg)/(totalbirds))*(1000)
        $('.perBird').val(calculation);
        $('.perBird').text(calculation);
    }
});
//fcr without mortality

$('.feedTotalkg, .totalBirds, .weightAchieved').on('keypress keyup blur',function () {
    var totalbirds= $('.totalBirds').val();
    var feedTotalkg=$('.feedTotalkg').val();
    var weightAchieved=$('.weightAchieved').val();
    if((feedTotalkg != '' && totalbirds!='')&&(weightAchieved !='')){
        var cal=(parseFloat(feedTotalkg)/(totalbirds));
        var calculation=(parseFloat(cal)/weightAchieved)*1000;

        $('.withoutMortality').val(calculation);
        $('.withoutMortality').text(calculation);
    }
});

//fcr with mortality

$('.feedTotalkg, .totalBirds,.mortality_pes,.weightAchieved').on('keypress keyup blur',function () {
    var totalbirds= $('.totalBirds').val();
    var feedTotalkg=$('.feedTotalkg').val();
    var weightAchieved=$('.weightAchieved').val();
    var mortalityPes = $('.mortality_pes').val();

    if((feedTotalkg != '' && totalbirds!='')&&(weightAchieved !=''&& mortalityPes!='')){

        var cal=(parseFloat(feedTotalkg)/ (totalbirds-mortalityPes));
        var calculation=(parseFloat(cal)/weightAchieved)*1000;

        $('.withMortality').val(calculation);
        $('.withMortality').text(calculation);
    }
});

