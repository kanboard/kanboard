Kanboard.Dashboard = (function() {

    jQuery(document).ready(function() {

        var state = Kanboard.GetStorageItem("dashboard_view");

        if (state) {

            var sections = JSON.parse(state);

            for (var section in sections) {
                $("#dashboard-" + section).toggle(sections[section]);
            }

            hideColumns();
        }
    });

    jQuery(document).on('click', ".dashboard-toggle", function(e) {
        e.preventDefault();

        $("#dashboard-" + $(this).data("toggle")).toggle();
        hideColumns();

        var sections = ["projects", "tasks", "subtasks", "activities"];
        var state = {};

        for (var i = 0; i < sections.length; i++) {
            state[sections[i]] = $("#dashboard-" + sections[i]).is(":visible");
        }

        Kanboard.SetStorageItem("dashboard_view", JSON.stringify(state));
    });

    function hideColumns()
    {
        if ($(".dashboard-right-column > div:visible").size() > 0) {
            $(".dashboard-left-column").removeClass("dashboard-single-column");
        }
        else {
            $(".dashboard-left-column").addClass("dashboard-single-column");
        }

        if ($(".dashboard-left-column > div:visible").size() > 0) {
            $(".dashboard-right-column").removeClass("dashboard-single-column");
        }
        else {
            $(".dashboard-right-column").addClass("dashboard-single-column");
        }
    }

})();