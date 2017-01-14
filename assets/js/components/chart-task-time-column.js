KB.component('chart-task-time-column', function (containerElement, options) {

    this.render = function () {
        var metrics = options.metrics;
        var plots = [options.label];
        var categories = [];

        for (var i = 0; i < metrics.length; i++) {
            plots.push(metrics[i].time_spent);
            categories.push(metrics[i].title);
        }

        KB.dom(containerElement).add(KB.dom('div').attr('id', 'chart-task-time-column').build());

        c3.generate({
            bindto: '#chart-task-time-column',
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