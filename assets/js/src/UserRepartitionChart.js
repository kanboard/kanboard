Kanboard.UserRepartitionChart = function(app) {
    this.app = app;
};

Kanboard.UserRepartitionChart.prototype.execute = function() {
    if (this.app.hasId("analytic-user-repartition")) {
        this.show();
    }
};

Kanboard.UserRepartitionChart.prototype.show = function() {
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
