(function() {

    // CFD diagram
    function drawCfd()
    {
        var metrics = $("#chart").data("metrics");
        var columns = [];
        var groups = [];
        var categories = [];
        var inputFormat = d3.time.format("%Y-%m-%d");
        var outputFormat = d3.time.format($("#chart").data("date-format"));

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

                    if (j == 0) {
                        categories.push(outputFormat(inputFormat.parse(metrics[i][j])));
                    }
                }
            }
        }

        c3.generate({
            data: {
                columns: columns,
                type: 'area-spline',
                groups: [groups]
            },
            axis: {
                x: {
                    type: 'category',
                    categories: categories
                }
            }
        });
    }

    // Burndown chart
    function drawBurndown()
    {
        var metrics = $("#chart").data("metrics");
        var columns = [[$("#chart").data("label-total")]];
        var categories = [];
        var inputFormat = d3.time.format("%Y-%m-%d");
        var outputFormat = d3.time.format($("#chart").data("date-format"));

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

                    if (j == 0) {
                        categories.push(outputFormat(inputFormat.parse(metrics[i][j])));
                    }
                }
            }
        }

        c3.generate({
            data: {
                columns: columns
            },
            axis: {
                x: {
                    type: 'category',
                    categories: categories
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
        var categories = [];
        var metrics = $("#chart").data("metrics");
        var labels = $("#chart").data("labels");
        var inputFormat = d3.time.format("%Y-%m-%d");
        var outputFormat = d3.time.format($("#chart").data("date-format"));

        var columns = [
            [labels["in"]],
            [labels["left"]],
            [labels["out"]]
        ];

        var colors = {};
        colors[labels["in"]] = '#5858FA';
        colors[labels["left"]] = '#04B404';
        colors[labels["out"]] = '#DF3A01';

        for (var i = 0; i < metrics.length; i++) {
            categories.push(outputFormat(inputFormat.parse(metrics[i]["date"])));
            columns[0].push(metrics[i]["in"]);
            columns[1].push(metrics[i]["left"]);
            columns[2].push(metrics[i]["out"]);
        }

        c3.generate({
            data: {
                columns: columns,
                colors: colors,
                type : 'bar'
            },
            bar: {
                width: {
                    ratio: 0.25
                }
            },
            grid: {
                x: {
                    show: true
                },
                y: {
                    show: true
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: categories
                }
            }
        });
    }

    // Draw chart for average time spent into each column
    function drawAvgTimeColumn()
    {
        var metrics = $("#chart").data("metrics");
        var plots = [$("#chart").data("label")];
        var categories = [];

        for (var column_id in metrics) {
            plots.push(metrics[column_id].average);
            categories.push(metrics[column_id].title);
        }

        c3.generate({
            data: {
                columns: [plots],
                type: 'bar'
            },
            bar: {
                width: {
                    ratio: 0.5
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: categories
                },
                y: {
                    tick: {
                        format: formatDuration
                    }
                }
            },
            legend: {
               show: false
            }
        });
    }

    // Draw chart for average time spent into each column
    function drawTaskTimeColumn()
    {
        var metrics = $("#chart").data("metrics");
        var plots = [$("#chart").data("label")];
        var categories = [];

        for (var i = 0; i < metrics.length; i++) {
            plots.push(metrics[i].time_spent);
            categories.push(metrics[i].title);
        }

        c3.generate({
            data: {
                columns: [plots],
                type: 'bar'
            },
            bar: {
                width: {
                    ratio: 0.5
                }
            },
            axis: {
                x: {
                    type: 'category',
                    categories: categories
                },
                y: {
                    tick: {
                        format: formatDuration
                    }
                }
            },
            legend: {
               show: false
            }
        });
    }

    // Draw lead and cycle time for the project
    function drawLeadAndCycleTime()
    {
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
                        format: formatDuration
                    }
                }
            }
        });
    }

    function formatDuration(d)
    {
        if (d >= 86400) {
            return Math.round(d/86400) + "d";
        }
        else if (d >= 3600) {
            return Math.round(d/3600) + "h";
        }
        else if (d >= 60) {
            return Math.round(d/60) + "m";
        }

        return d + "s";
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
        else if (Kanboard.Exists("analytic-avg-time-column")) {
            drawAvgTimeColumn();
        }
        else if (Kanboard.Exists("analytic-task-time-column")) {
            drawTaskTimeColumn();
        }
        else if (Kanboard.Exists("analytic-lead-cycle-time")) {
            drawLeadAndCycleTime();
        }
    });

})();
