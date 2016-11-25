KB.component('chart-project-avg-time-column', function (containerElement, options) {

    this.render = function () {
        var metrics = options.metrics;
        var plots = [options.label];
        var categories = [];

        for (var column_id in metrics) {
            plots.push(metrics[column_id].average);
            categories.push(metrics[column_id].title);
        }

        KB.dom(containerElement).add(KB.dom('div').attr('id', 'chart').build());

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
                        format: KB.utils.formatDuration
                    }
                }
            },
            legend: {
                show: false
            }
        });
    };
});