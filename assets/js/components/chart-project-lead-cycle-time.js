KB.component('chart-project-lead-cycle-time', function (containerElement, options) {

    this.render = function () {
        var metrics = options.metrics;
        var cycle = [options.labelCycle];
        var lead = [options.labelLead];
        var categories = [];

        var types = {};
        types[options.labelCycle] = 'area';
        types[options.labelLead] = 'area-spline';

        var colors = {};
        colors[options.labelLead] = '#afb42b';
        colors[options.labelCycle] = '#4e342e';

        for (var i = 0; i < metrics.length; i++) {
            cycle.push(parseInt(metrics[i].avg_cycle_time));
            lead.push(parseInt(metrics[i].avg_lead_time));
            categories.push(metrics[i].day);
        }

        KB.dom(containerElement).add(KB.dom('div').attr('id', 'chart').build());

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
                        format: KB.utils.formatDuration
                    }
                }
            }
        });
    };
});
