if ($(".VisitorsDays").length){
today = new Date();
lastweek = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
Analytics.loadType("VisitorsDays", Analytics.Charts.VisitorsDays, {startDate: lastweek.getFullYear() + "-" + (lastweek.getMonth()+1).pad(2) + "-" +lastweek.getDate().pad(2), endDate: today.getFullYear() + "-" + (today.getMonth()+1).pad(2) + "-" +today.getDate().pad(2)});
}
if ($(".paymentsTimeLine").length){
    today = new Date();
    lastweek = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
    Payments.loadType("PaymentsPerMonth", Payments.Charts.PaymentsPerMonth, {startDate: lastweek.getFullYear() + "-" + (lastweek.getMonth()+1).pad(2) + "-" +lastweek.getDate().pad(2), endDate: today.getFullYear() + "-" + (today.getMonth()+1).pad(2) + "-" +today.getDate().pad(2)});
}
