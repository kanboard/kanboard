function LeadCycleTimeChart(app) {
    this.app = app;
}

LeadCycleTimeChart.prototype.execute = function() {
    var metrics = $("#chart").data("metrics");
    var cycle = [$("#chart").data("label-cycle")];
    var lead = [$("#chart").data("label-lead")];
    var categories = [];

    var types = {};
    types[$("#chart").data("label-cycle")] = 'area';
    types[$("#chart").data("label-lead")] = 'area-spline';

    var colors = {};
    colors[$("#chart").data("label-lead")] = '#afb42b';
    colors[$("#chart").data("label-cycle")] = '#4e342e';

    for (var i = 0; i < metrics.length; i++) {
        cycle.push(parseInt(metrics[i].avg_cycle_time));
        lead.push(parseInt(metrics[i].avg_lead_time));
        categories.push(metrics[i].day);
    }

    c3.generate({
        data: {
            columns: [
                lead,
                cycle
            ],
            types: types,
            colors: colors
        },
        axis: {
            x: {
                type: 'category',
                categories: categories
            },
            y: {
                tick: {
                    format: this.app.formatDuration
                }
            }
        }
    });
};
