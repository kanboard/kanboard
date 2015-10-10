function Router() {
    this.routes = {};
}

Router.prototype.addRoute = function(id, controller) {
    this.routes[id] = controller;
};

Router.prototype.dispatch = function(app) {
    for (var id in this.routes) {
        if (document.getElementById(id)) {
            var controller = Object.create(this.routes[id].prototype);
            this.routes[id].apply(controller, [app]);
            controller.execute();
            break;
        }
    }
};

jQuery(document).ready(function() {
    var app = new App();
    var router = new Router();
    router.addRoute('board', Board);
    router.addRoute('calendar', Calendar);
    router.addRoute('screenshot-zone', Screenshot);
    router.addRoute('analytic-task-repartition', TaskRepartitionChart);
    router.addRoute('analytic-user-repartition', UserRepartitionChart);
    router.addRoute('analytic-cfd', CumulativeFlowDiagram);
    router.addRoute('analytic-burndown', BurndownChart);
    router.addRoute('analytic-avg-time-column', AvgTimeColumnChart);
    router.addRoute('analytic-task-time-column', TaskTimeColumnChart);
    router.addRoute('analytic-lead-cycle-time', LeadCycleTimeChart);
    router.addRoute('gantt-chart', Gantt);
    router.dispatch(app);
    app.listen();
});
