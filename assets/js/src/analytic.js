(function() {

    // CFD diagram
    function drawCfd()
    {
        var metrics = $("#chart").data("metrics");
        var columns = [];
        var groups = [];

        for (var i = 0; i < metrics.length; i++) {

            for (var j = 0; j < metrics[i].length; j++) {

                if (i == 0) {
                    columns.push([metrics[i][j]]);

                    if (j > 0) {
                        groups.push(metrics[i][j]);
                    }
                }
                else {
                    columns[j].push(metrics[i][j]);
                }
            }
        }

        c3.generate({
            data: {
                columns: columns,
                x: metrics[0][0],
                type: 'area-spline',
                groups: [groups]
            },
            axis: {
                x: {
                    type: 'timeseries',
                    tick: {
                        format: $("#chart").data("date-format")
                    }
                }
            }
        });
    }

    // Burndown chart
    function drawBurndown()
    {
        var metrics = $("#chart").data("metrics");
        var columns = [[$("#chart").data("label-total")]];

        for (var i = 0; i < metrics.length; i++) {

            for (var j = 0; j < metrics[i].length; j++) {

                if (i == 0) {
                    columns.push([metrics[i][j]]);
                }
                else {
                    columns[j + 1].push(metrics[i][j]);

                    if (j > 0) {

                        if (columns[0][i] == undefined) {
                            columns[0].push(0);
                        }

                        columns[0][i] += metrics[i][j];
                    }
                }
            }
        }

        c3.generate({
            data: {
                columns: columns,
                x: metrics[0][0]
            },
            axis: {
                x: {
                    type: 'timeseries',
                    tick: {
                        format: $("#chart").data("date-format")
                    }
                }
            }
        });
    }

    // Draw task repartition chart
    function drawTaskRepartition()
    {
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
    }

    // Draw user repartition chart
    function drawUserRepartition()
    {
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
    }

    // Draw budget chart
    function drawBudget()
    {
        var metrics = $("#chart").data("metrics");
        var labels = $("#chart").data("labels");

        var columns = [
            [labels["date"]],
            [labels["in"]],
            [labels["left"]],
            [labels["out"]]
        ];

        var colors = {};
        colors[labels["in"]] = '#5858FA';
        colors[labels["left"]] = '#04B404';
        colors[labels["out"]] = '#DF3A01';

        for (var i = 0; i < metrics.length; i++) {
            columns[0].push(metrics[i]["date"]);
            columns[1].push(metrics[i]["in"]);
            columns[2].push(metrics[i]["left"]);
            columns[3].push(metrics[i]["out"]);
        }

        c3.generate({
            data: {
                x: columns[0][0],
                columns: columns,
                colors: colors,
                type : 'bar'
            },
            axis: {
                x: {
                    type: 'timeseries',
                    tick: {
                        format: $("#chart").data("date-format")
                    }
                }
            }
        });
    }

    jQuery(document).ready(function() {

        if (Kanboard.Exists("analytic-task-repartition")) {
            drawTaskRepartition();
        }
        else if (Kanboard.Exists("analytic-user-repartition")) {
            drawUserRepartition();
        }
        else if (Kanboard.Exists("analytic-cfd")) {
            drawCfd();
        }
        else if (Kanboard.Exists("analytic-burndown")) {
            drawBurndown();
        }
        else if (Kanboard.Exists("budget-chart")) {
            drawBudget();
        }
    });

})();
