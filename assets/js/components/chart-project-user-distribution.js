Vue.component('chart-project-user-distribution', {
    props: ['metrics'],
    template: '<div id="chart"></div>',
    ready: function () {
        var columns = [];

        for (var i = 0; i < this.metrics.length; i++) {
            columns.push([this.metrics[i].user, this.metrics[i].nb_tasks]);
        }

        c3.generate({
            data: {
                columns: columns,
                type : 'donut'
            }
        });
    }
});
