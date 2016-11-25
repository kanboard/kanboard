KB.component('chart-project-time-comparison', function (containerElement, options) {

    this.render = function () {
        var spent = [options.labelSpent];
        var estimated = [options.labelEstimated];
        var categories = [];

        for (var status in options.metrics) {
            spent.push(options.metrics[status].time_spent);
            estimated.push(options.metrics[status].time_estimated);
            categories.push(status === 'open' ? options.labelOpen : options.labelClosed);
        }

        KB.dom(containerElement).add(KB.dom('div').attr('id', 'chart').build());

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
});
