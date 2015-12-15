function CompareHoursColumnChart(app) {
    this.app = app;
}

CompareHoursColumnChart.prototype.execute = function() {
    var metrics = $("#chart").data("metrics");
    var spent = [$("#chart").data("label-spent")];
    var estimated = [$("#chart").data("label-estimated")];
    var categories = [];

    for (var status in metrics) {
        spent.push(parseInt(metrics[status].time_spent));
        estimated.push(parseInt(metrics[status].time_estimated));
        categories.push(status);
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
            },
            y: {
                tick: {
                    format: this.app.formatDuration
                }
            }
        },
        legend: {
           show: true
        }
    });
};
