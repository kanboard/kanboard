Kanboard.Budget = (function() {

    jQuery(document).ready(function() {

        if (Kanboard.Exists("budget-chart")) {

            var labels =$("#chart").data("labels");
            var serie = $("#chart").data("serie");
            var types = ["in", "out", "left"];
            var data = [];

            for (var i = 0; i < serie.length; i++) {

                for (var j = 0; j < types.length; j++) {
                    var row = {};
                    row[labels["date"]] = serie[i]["date"];
                    row[labels["value"]] = serie[i][types[j]];
                    row[labels["type"]] = labels[types[j]];

                    data.push(row);
                }
            }

            var svg = dimple.newSvg("#chart", 750, 600);
            var myChart = new dimple.chart(svg, data);

            var x = myChart.addCategoryAxis("x", [labels["date"], labels["type"]]);
            x.addOrderRule(labels["date"]);

            myChart.addMeasureAxis("y", labels["value"]);

            myChart.addSeries(labels["type"], dimple.plot.bar);
            myChart.addLegend(65, 10, 510, 20, "right");
            myChart.draw();
        }
    });

})();