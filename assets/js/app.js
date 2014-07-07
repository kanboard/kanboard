
// Common functions
var Kanboard = (function() {

    return {

        // Display a popup
        Popover: function(e, callback) {
            e.preventDefault();
            e.stopPropagation();

            $.get(e.target.getAttribute("href"), function(content) {

                $("body").append('<div id="popover-container"><div id="popover-content">' + content + '</div></div>');

                $("#popover-container").click(function() {
                    $(this).remove();
                });

                $("#popover-content").click(function(e) {
                    e.stopPropagation();
                });

                if (callback) {
                    callback();
                }
            });
        },

        // Return true if the page is visible
        IsVisible: function()
        {
            var property = "";

            if (typeof document.hidden !== "undefined") {
                property = "visibilityState";
            } else if (typeof document.mozHidden !== "undefined") {
                property = "mozVisibilityState";
            } else if (typeof document.msHidden !== "undefined") {
                property = "msVisibilityState";
            } else if (typeof document.webkitHidden !== "undefined") {
                property = "webkitVisibilityState";
            }

            if (property != "") {
                return document[property] == "visible";
            }

            return true;
        }
    };

})();


// Board related functions
Kanboard.Board = (function() {

    var checkInterval = null;

    // Setup the board
    function board_load_events()
    {
        // Drag and drop
        $(".column").sortable({
            connectWith: ".column",
            placeholder: "draggable-placeholder",
            stop: function(event, ui) {
                board_save();
            }
        });

        // Assignee change
        $(".assignee-popover").click(Kanboard.Popover);

        // Task edit popover
        $(".task-edit-popover").click(function(e) {
            Kanboard.Popover(e, Kanboard.Task.Init);
        });

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
    function board_save()
    {
        var data = [];
        var boardSelector = $("#board");
        var projectId = boardSelector.attr("data-project-id");

        board_unload_events();

        $(".column").each(function() {
            var columnId = $(this).attr("data-column-id");

            $("#column-" + columnId + " .task-board").each(function(index) {
                data.push({
                    "task_id": parseInt($(this).attr("data-task-id")),
                    "position": index + 1,
                    "column_id": parseInt(columnId)
                });
            });
        });

        $.ajax({
            cache: false,
            url: "?controller=board&action=save&project_id=" + projectId,
            data: {"positions": data, "csrf_token": boardSelector.attr("data-csrf-token")},
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

            // Project select box
            $("#board-selector").chosen({
                width: 180
            });

            $("#board-selector").change(function() {
                window.location = "?controller=board&action=show&project_id=" + $(this).val();
            });
        }
    };

})();


// Task related functions
Kanboard.Task = (function() {

    return {
        Init: function() {

            // Datepicker for the due date
            $("#form-date_due").datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'yy-mm-dd'
            });

            // Image preview for attachments
            $(".file-popover").click(Kanboard.Popover);
        }
    };

})();


// Initialization
$(function() {

    if ($("#board").length) {
        Kanboard.Board.Init();
    }
    else {
        Kanboard.Task.Init();
    }
});
