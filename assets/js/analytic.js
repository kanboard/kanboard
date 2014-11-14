
Kanboard.Analytic = (function() {

    return {
        Init: function() {

            if (Kanboard.Exists("analytic-task-repartition")) {
                Kanboard.Analytic.TaskRepartition.Init();
            }
            else if (Kanboard.Exists("analytic-user-repartition")) {
                Kanboard.Analytic.UserRepartition.Init();
            }
        }
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

        var svg = dimple.newSvg("#chart", 700, 350);

        var chart = new dimple.chart(svg, series);
        chart.addMeasureAxis("p", labels["nb_tasks"]);
        var ring = chart.addSeries(labels["column_title"], dimple.plot.pie);
        ring.innerRadius = "50%";
        chart.addLegend(0, 0, 100, 100, "left");
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

        var svg = dimple.newSvg("#chart", 700, 350);

        var chart = new dimple.chart(svg, series);
        chart.addMeasureAxis("p", labels["nb_tasks"]);
        var ring = chart.addSeries(labels["user"], dimple.plot.pie);
        ring.innerRadius = "50%";
        chart.addLegend(0, 0, 100, 100, "left");
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
