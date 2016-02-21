function CompareHoursColumnChart(app) {
    this.app = app;
}

CompareHoursColumnChart.prototype.execute = function() {
    var metrics = $("#chart").data("metrics");
    var labelOpen = $("#chart").data("label-open");
    var labelClosed = $("#chart").data("label-closed");
    var spent = [$("#chart").data("label-spent")];
    var estimated = [$("#chart").data("label-estimated")];
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
