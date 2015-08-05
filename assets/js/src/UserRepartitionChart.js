function UserRepartitionChart() {
}

UserRepartitionChart.prototype.execute = function() {
    var metrics = $("#chart").data("metrics");
    var columns = [];

    for (var i = 0; i < metrics.length; i++) {
        columns.push([metrics[i].user, metrics[i].nb_tasks]);
    }

    c3.generate({
        data: {
            columns: columns,
            type : 'donut'
        }
    });
};
