
Kanboard.Analytic = (function() {

    return {
        Init: function() {

            if (Kanboard.Exists("analytic-task-repartition")) {
                Kanboard.Analytic.TaskRepartition.Init();
            }
            else if (Kanboard.Exists("analytic-user-repartition")) {
                Kanboard.Analytic.UserRepartition.Init();
            }
            else if (Kanboard.Exists("analytic-cfd")) {
                Kanboard.Analytic.CFD.Init();
            }
        }
    };

})();

Kanboard.Analytic.CFD = (function() {

    function fetchData()
    {
        jQuery.getJSON($("#chart").attr("data-url"), function(data) {
            drawGraph(data.metrics, data.labels, data.columns);
        });
    }

    function drawGraph(metrics, labels, columns)
    {
        var series = prepareSeries(metrics, labels);

        var svg = dimple.newSvg("#chart", "100%", 380);
        var chart = new dimple.chart(svg, series);

        var x = chart.addCategoryAxis("x", labels['day']);
        x.addOrderRule("Date");

        chart.addMeasureAxis("y", labels['total']);

        var s = chart.addSeries(labels['column'], dimple.plot.area);
        s.addOrderRule(columns.reverse());

        chart.addLegend(10, 10, 500, 30, "left");
        chart.draw();
    }

    function prepareSeries(metrics, labels)
    {
        var series = [];

        for (var i = 0; i < metrics.length; i++) {

            var row = {};
            row[labels['column']] = metrics[i]['column_title'];
            row[labels['day']] = metrics[i]['day'];
            row[labels['total']] = metrics[i]['total'];
            series.push(row);
        }

        return series;
    }

    return {
        Init: fetchData
    };

})();

Kanboard.Analytic.TaskRepartition = (function() {

    function fetchData()
    {
        jQuery.getJSON($("#chart").attr("data-url"), function(data) {
            drawGraph(data.metrics, data.labels);
        });
    }

    function drawGraph(metrics, labels)
    {
        var series = prepareSeries(metrics, labels);

        var svg = dimple.newSvg("#chart", "100%", 350);

        var chart = new dimple.chart(svg, series);
        chart.addMeasureAxis("p", labels["nb_tasks"]);
        var ring = chart.addSeries(labels["column_title"], dimple.plot.pie);
        ring.innerRadius = "50%";
        chart.addLegend(0, 0, 100, "100%", "left");
        chart.draw();
    }

    function prepareSeries(metrics, labels)
    {
        var series = [];

        for (var i = 0; i < metrics.length; i++) {

            var serie = {};
            serie[labels["nb_tasks"]] = metrics[i]["nb_tasks"];
            serie[labels["column_title"]] = metrics[i]["column_title"];

            series.push(serie);
        }

        return series;
    }

    return {
        Init: fetchData
    };

})();

Kanboard.Analytic.UserRepartition = (function() {

    function fetchData()
    {
        jQuery.getJSON($("#chart").attr("data-url"), function(data) {
            drawGraph(data.metrics, data.labels);
        });
    }

    function drawGraph(metrics, labels)
    {
        var series = prepareSeries(metrics, labels);

        var svg = dimple.newSvg("#chart", "100%", 350);

        var chart = new dimple.chart(svg, series);
        chart.addMeasureAxis("p", labels["nb_tasks"]);
        var ring = chart.addSeries(labels["user"], dimple.plot.pie);
        ring.innerRadius = "50%";
        chart.addLegend(0, 0, 100, "100%", "left");
        chart.draw();
    }

    function prepareSeries(metrics, labels)
    {
        var series = [];

        for (var i = 0; i < metrics.length; i++) {

            var serie = {};
            serie[labels["nb_tasks"]] = metrics[i]["nb_tasks"];
            serie[labels["user"]] = metrics[i]["user"];

            series.push(serie);
        }

        return series;
    }

    return {
        Init: fetchData
    };

})();
