function TaskRepartitionChart() {
}

TaskRepartitionChart.prototype.execute = function() {
    var metrics = $("#chart").data("metrics");
    var columns = [];

    for (var i = 0; i < metrics.length; i++) {
        columns.push([metrics[i].column_title, metrics[i].nb_tasks]);
    }

    c3.generate({
        data: {
            columns: columns,
            type : 'donut'
        }
    });
};
