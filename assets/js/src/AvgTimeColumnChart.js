function AvgTimeColumnChart(app) {
    this.app = app;
}

AvgTimeColumnChart.prototype.execute = function() {
    var metrics = $("#chart").data("metrics");
    var plots = [$("#chart").data("label")];
    var categories = [];

    for (var column_id in metrics) {
        plots.push(metrics[column_id].average);
        categories.push(metrics[column_id].title);
    }

    c3.generate({
        data: {
            columns: [plots],
            type: 'bar'
        },
        bar: {
            width: {
                ratio: 0.5
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
           show: false
        }
    });
};
