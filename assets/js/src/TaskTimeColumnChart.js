function TaskTimeColumnChart(app) {
    this.app = app;
}

TaskTimeColumnChart.prototype.execute = function() {
    var metrics = $("#chart").data("metrics");
    var plots = [$("#chart").data("label")];
    var categories = [];

    for (var i = 0; i < metrics.length; i++) {
        plots.push(metrics[i].time_spent);
        categories.push(metrics[i].title);
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
