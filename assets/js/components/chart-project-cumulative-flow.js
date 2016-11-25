KB.component('chart-project-cumulative-flow', function (containerElement, options) {

    this.render = function () {
        var metrics = options.metrics;
        var columns = [];
        var groups = [];
        var categories = [];
        var inputFormat = d3.time.format("%Y-%m-%d");
        var outputFormat = d3.time.format(options.dateFormat);

        for (var i = 0; i < metrics.length; i++) {

            for (var j = 0; j < metrics[i].length; j++) {

                if (i === 0) {
                    columns.push([metrics[i][j]]);

                    if (j > 0) {
                        groups.push(metrics[i][j]);
                    }
                } else {

                    columns[j].push(metrics[i][j]);

                    if (j === 0) {
                        categories.push(outputFormat(inputFormat.parse(metrics[i][j])));
                    }
                }
            }
        }

        KB.dom(containerElement).add(KB.dom('div').attr('id', 'chart').build());

        c3.generate({
            data: {
                columns: columns,
                type: 'area-spline',
                groups: [groups]
            },
            axis: {
                x: {
                    type: 'category',
                    categories: categories
                }
            }
        });
    };
});
