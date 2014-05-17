(function () {

    var checkInterval = null;

    // Setup the board
    function board_load_events()
    {
        $(".column").sortable({
            connectWith: ".column",
            placeholder: "draggable-placeholder",
            stop: function(event, ui) {
                board_save();
            }
        });

        var interval = parseInt($("#board").attr("data-check-interval"));

        if (interval > 0) {
            checkInterval = window.setInterval(board_check, interval * 1000);
        }
    }

    // Stop events
    function board_unload_events()
    {
        clearInterval(checkInterval);
    }

    // Save and refresh the board
    function board_save()
    {
        var data = [];
        var projectId = $("#board").attr("data-project-id");

        board_unload_events();

        $(".column").each(function() {
            var columnId = $(this).attr("data-column-id");

            $("#column-" + columnId + " .task").each(function(index) {
                data.push({
                    "task_id": parseInt($(this).attr("data-task-id")),
                    "position": index + 1,
                    "column_id": parseInt(columnId)
                });
            });
        });

        $.ajax({
            url: "?controller=board&action=save&project_id=" + projectId,
            data: {positions: data},
            type: "POST",
            success: function(data) {
                $("#board").remove();
                $("#main").append(data);
                board_load_events();
                applyFilter(getSelectedUserFilter(), hasDueDateFilter());
            }
        });
    }

    // Check if a board have been changed by someone else
    function board_check()
    {
        var projectId = $("#board").attr("data-project-id");
        var timestamp = $("#board").attr("data-time");

        if (projectId != undefined && timestamp != undefined) {
            $.ajax({
                url: "?controller=board&action=check&project_id=" + projectId + "&timestamp=" + timestamp,
                statusCode: {
                    200: function(data) {
                        $("#board").remove();
                        $("#main").append(data);
                        board_unload_events();
                        board_load_events();
                        applyFilter(getSelectedUserFilter(), hasDueDateFilter());
                    }
                }
            });
        }
    }

    // Get the selected user id
    function getSelectedUserFilter()
    {
        return $("#form-user_id").val();
    }

    // Return true if the filter is activated
    function hasDueDateFilter()
    {
        return $("#filter-due-date").hasClass("filter-on");
    }

    // Apply user or date filter (change tasks opacity)
    function applyFilter(selectedUserId, filterDueDate)
    {
        $("[data-task-id]").each(function(index, item) {

            var ownerId = item.getAttribute("data-owner-id");
            var dueDate = item.getAttribute("data-due-date");

            if (ownerId != selectedUserId && selectedUserId != -1) {
                item.style.opacity = "0.2";
            }
            else {
                item.style.opacity = "1.0";
            }

            if (filterDueDate && (dueDate == "" || dueDate == "0")) {
                item.style.opacity = "0.2";
            }
        });
    }

    // Load filter events
    function filter_load_events()
    {
        $("#form-user_id").change(function() {
            applyFilter(getSelectedUserFilter(), hasDueDateFilter());
        });

        $("#filter-due-date").click(function(e) {
            $(this).toggleClass("filter-on");
            applyFilter(getSelectedUserFilter(), hasDueDateFilter());
            e.preventDefault();
        });
    }

    // Initialization
    $(function() {
        board_load_events();
        filter_load_events();
    });

}());
