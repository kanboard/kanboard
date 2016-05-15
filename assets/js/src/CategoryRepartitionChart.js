Kanboard.CategoryRepartitionChart = function(app) {
    this.app = app;
};

Kanboard.CategoryRepartitionChart.prototype.execute = function() {
    if (this.app.hasId("analytic-category-repartition")) {
        this.show();
    }
};

Kanboard.CategoryRepartitionChart.prototype.show = function() {
    var metrics = $("#chart").data("metrics");
    var columns = [];

    for (var i = 0; i < metrics.length; i++) {
        columns.push([metrics[i].category_name, metrics[i].nb_tasks]);
    }

    c3.generate({
        data: {
            columns: columns,
            type : 'donut'
        }
    });
};
