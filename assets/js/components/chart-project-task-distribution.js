Vue.component('chart-project-task-distribution', {
    props: ['metrics'],
    template: '<div id="chart"></div>',
    ready: function () {
        var columns = [];

        for (var i = 0; i < this.metrics.length; i++) {
            columns.push([this.metrics[i].column_title, this.metrics[i].nb_tasks]);
        }

        c3.generate({
            data: {
                columns: columns,
                type : 'donut'
            }
        });
    }
});
