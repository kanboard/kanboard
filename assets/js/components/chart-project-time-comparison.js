Vue.component('chart-project-time-comparison', {
    props: ['metrics', 'labelSpent', 'labelEstimated', 'labelClosed', 'labelOpen'],
    template: '<div id="chart"></div>',
    ready: function () {
        var spent = [this.labelSpent];
        var estimated = [this.labelEstimated];
        var categories = [];

        for (var status in this.metrics) {
            spent.push(this.metrics[status].time_spent);
            estimated.push(this.metrics[status].time_estimated);
            categories.push(status === 'open' ? this.labelOpen : this.labelClosed);
        }

        c3.generate({
            data: {
                columns: [spent, estimated],
                type: 'bar'
            },
            bar: {
                width: {
                    ratio: 0.2
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: categories
                }
            },
            legend: {
                show: true
            }
        });
    }
});
