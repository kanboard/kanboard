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
                var currentValue = metrics[i][j];

                if (i === 0) {
                    if (j > 0) {
                        groups.push(currentValue);
                        columns.push([currentValue]);
                    }
                } else {
                    if (j > 0) {
                        columns[j - 1].push(currentValue);
                    } else {
                        categories.push(outputFormat(inputFormat.parse(currentValue)));
                    }
                }
            }
        }

        KB.dom(containerElement).add(KB.dom('div').attr('id', 'chart').build());

        c3.generate({
            data: {
                // Example: [["Column1", 1, 2, 3], ["Column2", 1, 2, 3]]
                // Note: values are reversed to make sure the columns are stacked in the right order
                columns: columns.reverse(),
                type: 'area-spline',
                groups: [groups],

                // Note: Use specified order otherwise C3js reorder series
                order: null
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
