KB.component('chart-project-estimated-actual-column', function (containerElement, options) {

    this.render = function () {
        var spent = [options.labelSpent];
        var estimated = [options.labelEstimated];
        var columns = [];

        for (var column in options.metrics) {
            spent.push(options.metrics[column].hours_spent);
            estimated.push(options.metrics[column].hours_estimated);
            columns.push(options.metrics[column].title);
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
                    categories: columns
                }
            },
            legend: {
                show: true
            }
        });
    };
});