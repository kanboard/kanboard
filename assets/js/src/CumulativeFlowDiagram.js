function CumulativeFlowDiagram() {
}

CumulativeFlowDiagram.prototype.execute = function() {

    var metrics = $("#chart").data("metrics");
    var columns = [];
    var groups = [];
    var categories = [];
    var inputFormat = d3.time.format("%Y-%m-%d");
    var outputFormat = d3.time.format($("#chart").data("date-format"));

    for (var i = 0; i < metrics.length; i++) {

        for (var j = 0; j < metrics[i].length; j++) {

            if (i == 0) {
                columns.push([metrics[i][j]]);

                if (j > 0) {
                    groups.push(metrics[i][j]);
                }
            }
            else {

                columns[j].push(metrics[i][j]);

                if (j == 0) {
                    categories.push(outputFormat(inputFormat.parse(metrics[i][j])));
                }
            }
        }
    }

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
