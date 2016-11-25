KB.component('chart-project-burndown', function (containerElement, options) {

    this.render = function () {
        var metrics = options.metrics;
        var columns = [[options.labelTotal]];
        var categories = [];
        var inputFormat = d3.time.format("%Y-%m-%d");
        var outputFormat = d3.time.format(options.dateFormat);

        for (var i = 0; i < metrics.length; i++) {

            for (var j = 0; j < metrics[i].length; j++) {

                if (i === 0) {
                    columns.push([metrics[i][j]]);
                } else {
                    columns[j + 1].push(metrics[i][j]);

                    if (j > 0) {

                        if (columns[0][i] === undefined) {
                            columns[0].push(0);
                        }

                        columns[0][i] += metrics[i][j];
                    }

                    if (j === 0) {
                        categories.push(outputFormat(inputFormat.parse(metrics[i][j])));
                    }
                }
            }
        }

        KB.dom(containerElement).add(KB.dom('div').attr('id', 'chart').build());

        c3.generate({
            data: {
                columns: columns
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
