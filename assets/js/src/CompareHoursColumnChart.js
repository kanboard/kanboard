Kanboard.CompareHoursColumnChart = function(app) {
    this.app = app;
};

Kanboard.CompareHoursColumnChart.prototype.execute = function() {
    if (this.app.hasId("analytic-compare-hours")) {
        this.show();
    }
};

Kanboard.CompareHoursColumnChart.prototype.show = function() {
    var chart = $("#chart");
    var metrics = chart.data("metrics");
    var labelOpen = chart.data("label-open");
    var labelClosed = chart.data("label-closed");
    var spent = [chart.data("label-spent")];
    var estimated = [chart.data("label-estimated")];
    var categories = [];

    for (var status in metrics) {
        spent.push(parseFloat(metrics[status].time_spent));
        estimated.push(parseFloat(metrics[status].time_estimated));
        categories.push(status == 'open' ? labelOpen : labelClosed);
    }

    c3.generate({
        data: {
            columns: [spent, estimated],
            type: 'bar'
        },
        bar: {
            width: {
                ratio: 0.2
            }
        },
        axis: {
            x: {
                type: 'category',
                categories: categories
            }
        },
        legend: {
           show: true
        }
    });
};
