function BudgetChart() {
}

BudgetChart.prototype.execute = function() {
    var categories = [];
    var metrics = $("#chart").data("metrics");
    var labels = $("#chart").data("labels");
    var inputFormat = d3.time.format("%Y-%m-%d");
    var outputFormat = d3.time.format($("#chart").data("date-format"));

    var columns = [
        [labels["in"]],
        [labels["left"]],
        [labels["out"]]
    ];

    var colors = {};
    colors[labels["in"]] = '#5858FA';
    colors[labels["left"]] = '#04B404';
    colors[labels["out"]] = '#DF3A01';

    for (var i = 0; i < metrics.length; i++) {
        categories.push(outputFormat(inputFormat.parse(metrics[i]["date"])));
        columns[0].push(metrics[i]["in"]);
        columns[1].push(metrics[i]["left"]);
        columns[2].push(metrics[i]["out"]);
    }

    c3.generate({
        data: {
            columns: columns,
            colors: colors,
            type : 'bar'
        },
        bar: {
            width: {
                ratio: 0.25
            }
        },
        grid: {
            x: {
                show: true
            },
            y: {
                show: true
            }
        },
        axis: {
            x: {
                type: 'category',
                categories: categories
            }
        }
    });
};
