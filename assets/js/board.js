// Board related functions
Kanboard.Board = (function() {

    var checkInterval = null;

    // Setup the board
    function board_load_events()
    {
        // Drag and drop
        $(".column").sortable({
            delay: 300,
            distance: 5,
            connectWith: ".column",
            placeholder: "draggable-placeholder",
            stop: function(event, ui) {
                board_save(
                    ui.item.attr('data-task-id'),
                    ui.item.parent().attr("data-column-id"),
                    ui.item.index() + 1
                );
            }
        });

        // Assignee change
        $(".assignee-popover").click(Kanboard.Popover);

        // Category change
        $(".category-popover").click(Kanboard.Popover);

        // Task edit popover
        $(".task-edit-popover").click(function(e) {
            Kanboard.Popover(e, Kanboard.Init);
        });

        // Description popover
        $(".task-description-popover").click(Kanboard.Popover);

        // Redirect to the task details page
        $("[data-task-id]").each(function() {
            $(this).click(function() {
                window.location = "?controller=task&action=show&task_id=" + $(this).attr("data-task-id");
            });
        });

        // Automatic refresh
        var interval = parseInt($("#board").attr("data-check-interval"));

        if (interval > 0) {
            checkInterval = window.setInterval(board_check, interval * 1000);
        }
    }

    // Stop events
    function board_unload_events()
    {
        $("[data-task-id]").off();
        clearInterval(checkInterval);
    }

    // Save and refresh the board
    function board_save(taskId, columnId, position)
    {
        var boardSelector = $("#board");
        var projectId = boardSelector.attr("data-project-id");

        board_unload_events();

        $.ajax({
            cache: false,
            url: "?controller=board&action=save&project_id=" + projectId,
            data: {
                "task_id": taskId,
                "column_id": columnId,
                "position": position,
                "csrf_token": boardSelector.attr("data-csrf-token"),
            },
            type: "POST",
            success: function(data) {
                $("#board").remove();
                $("#main").append(data);
                board_load_events();
                filter_apply();
            }
        });
    }

    // Check if a board have been changed by someone else
    function board_check()
    {
        var boardSelector = $("#board");
        var projectId = boardSelector.attr("data-project-id");
        var timestamp = boardSelector.attr("data-time");

        if (Kanboard.IsVisible() && projectId != undefined && timestamp != undefined) {
            $.ajax({
                cache: false,
                url: "?controller=board&action=check&project_id=" + projectId + "&timestamp=" + timestamp,
                statusCode: {
                    200: function(data) {
                        boardSelector.remove();
                        $("#main").append(data);
                        board_unload_events();
                        board_load_events();
                        filter_apply();
                    }
                }
            });
        }
    }

    // Apply user or date filter (change tasks opacity)
    function filter_apply()
    {
        var selectedUserId = $("#form-user_id").val();
        var selectedCategoryId = $("#form-category_id").val();
        var filterDueDate = $("#filter-due-date").hasClass("filter-on");

        $("[data-task-id]").each(function(index, item) {

            var ownerId = item.getAttribute("data-owner-id");
            var dueDate = item.getAttribute("data-due-date");
            var categoryId = item.getAttribute("data-category-id");

            if (ownerId != selectedUserId && selectedUserId != -1) {
                item.style.opacity = "0.2";
            }
            else {
                item.style.opacity = "1.0";
            }

            if (filterDueDate && (dueDate == "" || dueDate == "0")) {
                item.style.opacity = "0.2";
            }

            if (categoryId != selectedCategoryId && selectedCategoryId != -1) {
                item.style.opacity = "0.2";
            }
        });
    }

    // Load filter events
    function filter_load_events()
    {
        $("#form-user_id").change(filter_apply);

        $("#form-category_id").change(filter_apply);

        $("#filter-due-date").click(function(e) {
            $(this).toggleClass("filter-on");
            filter_apply();
            e.preventDefault();
        });
    }

    return {
        Init: function() {
            board_load_events();
            filter_load_events();
        }
    };

})();