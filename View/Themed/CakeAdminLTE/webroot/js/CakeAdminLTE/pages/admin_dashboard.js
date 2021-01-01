var ADMIN_DASHBOARD = {
    url_gender_member: '',
    url_birthday_member: '',
    url_report_high_spending: '',
    url_report_visit: '',
    doughnut_options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var dataset = data.datasets[tooltipItem.datasetIndex];
                    var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
                        return parseInt(previousValue) + parseInt(currentValue);
                    });
                    var currentValue = parseInt(dataset.data[tooltipItem.index]);
                    var precentage = Math.floor(((currentValue/total) * 100)+0.5);  
                    return precentage + "%";
                }
            }
        }
    },
    bar_options: {
        responsive: true,
        legend: {
            position: 'top',
        },
        title: {
            display: true,
            text: 'Chart.js Bar Chart'
        }
    },
    init_page: function() {
        if($('#gender_member').length){
            ADMIN_DASHBOARD.init_gender_member();
        }
        if($('#birthday_member').length){
            ADMIN_DASHBOARD.init_birthday_member();
        }
        if($('#high_spending').length){
            ADMIN_DASHBOARD.init_report_high_spending();
        }
        if($('#most_visited').length){
            ADMIN_DASHBOARD.init_report_visit();
        }
    },
    init_gender_member: function() {
        COMMON.call_ajax({
            url: ADMIN_DASHBOARD.url_gender_member,
            type: 'GET',
            dataType: 'json',
            success: function(result){
                if(result.status) {
                    new Chart('gender_member', {
                        type: 'doughnut',
                        data: {
                            datasets: [{
                                data: result.params.data,
                                backgroundColor: result.params.background_color,
                            }],
                            labels: result.params.labels
                        },
                        options: ADMIN_DASHBOARD.doughnut_options
                    });
                } else {
                    alert(result.message);
                }
            },
            error: function(error){
                alert("Get data for Gender Member is Error!")
            }
        });
    },
    init_birthday_member: function() {
        COMMON.call_ajax({
            url: ADMIN_DASHBOARD.url_birthday_member,
            type: 'GET',
            dataType: 'json',
            success: function(result){
                if(result.status) {
                    new Chart('birthday_member', {
                        type: 'bar',
                        data: {
                            labels: result.params.labels,
                            datasets: [{
                                label: "No. Member",
                                barPercentage: 0.5,
                                barThickness: 6,
                                maxBarThickness: 8,
                                minBarLength: 2,
                                data: result.params.data
                            }]
                        },
                        options: ADMIN_DASHBOARD.bar_options
                    });
                } else {
                    alert(result.message);
                }
            },
            error: function(error){
                alert("Get data for Gender Member is Error!")
            }
        });
    },
    init_report_high_spending: function() {
        COMMON.call_ajax({
            url: ADMIN_DASHBOARD.url_report_high_spending,
            type: 'GET',
            dataType: 'json',
            success: function(result){
                var html = '<tr><td colspan="3">There is not any member spending</td></tr>';
                if(result.status) {
                    if(result.params.length > 0){
                        html = "";
                        $.each(result.params, function(index, item){
                            html += ("<tr>" + 
                                    "<td>" + (index + 1) + "</td>" +
                                    "<td>" + item.phone + "</td>" +
                                    "<td>" + item.spending + "</td>" +
                                "</tr>");
                        });
                    }
                } else {
                    alert(result.message);
                }
                $("#high_spending tbody").html(html);
            },
            error: function(error){
                alert("Get data for Report High Spending is Error!")
            }
        });
    },
    init_report_visit: function() {
        COMMON.call_ajax({
            url: ADMIN_DASHBOARD.url_report_visit,
            type: 'GET',
            dataType: 'json',
            success: function(result){
                var html = '<tr><td colspan="3">There is not any visited member</td></tr>';
                if(result.status) {
                    if(result.params.length > 0){
                        html = "";
                        $.each(result.params, function(index, item){
                            html += ("<tr>" + 
                                    "<td>" + (index + 1) + "</td>" +
                                    "<td>" + item['Member'].phone + "</td>" +
                                    "<td>" + item['0'].total_invoices + "</td>" +
                                "</tr>");
                        });
                    }
                } else {
                    alert(result.message);
                }
                $("#most_visited tbody").html(html);
            },
            error: function(error){
                alert("Get data for Report Visit is Error!")
            }
        });
    },
}