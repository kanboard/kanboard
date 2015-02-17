Kanboard.Board = (function() {

    var checkInterval = null;

    function on_popover(e)
    {
        e.preventDefault();
        e.stopPropagation();

        Kanboard.Popover(e, Kanboard.InitAfterAjax);
    }

    function keyboard_shortcuts()
    {
        Mousetrap.bind("n", function() {

            Kanboard.OpenPopover(
                $("#board").data("task-creation-url"),
                Kanboard.InitAfterAjax
            );
        });

        Mousetrap.bind("s", function() {
            stack_toggle();
        });
    }

    // Collapse/Expand tasks
    function stack_load_events()
    {
        $(".filter-expand-link").click(function(e) {
            e.preventDefault();
            stack_expand();
            Kanboard.SetStorageItem(stack_key(), "expanded");
        });

        $(".filter-collapse-link").click(function(e) {
            e.preventDefault();
            stack_collapse();
            Kanboard.SetStorageItem(stack_key(), "collapsed");
        });

        stack_show();
    }

    function stack_key()
    {
        var projectId = $('#board').data('project-id');
        return "board_stacking_" + projectId;
    }

    function stack_collapse()
    {
        $(".filter-collapse").hide();
        $(".task-board-collapsed").show();

        $(".filter-expand").show();
        $(".task-board-expanded").hide();
    }

    function stack_expand()
    {
        $(".filter-collapse").show();
        $(".task-board-collapsed").hide();

        $(".filter-expand").hide();
        $(".task-board-expanded").show();
    }

    function stack_toggle()
    {
        var state = Kanboard.GetStorageItem(stack_key()) || "expanded";

        if (state === "expanded") {
            stack_collapse();
            Kanboard.SetStorageItem(stack_key(), "collapsed");
        }
        else {
            stack_expand();
            Kanboard.SetStorageItem(stack_key(), "expanded");
        }
    }

    function stack_show()
    {
        var state = Kanboard.GetStorageItem(stack_key()) || "expanded";

        if (state === "expanded") {
            stack_expand();
        }
        else {
            stack_collapse();
        }
    }

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
                    ui.item.index() + 1,
                    ui.item.parent().attr('data-swimlane-id')
                );
            }
        });

        // Task popover
        $("#board").on("click", ".task-board-popover", on_popover);

        // Redirect to the task details page
        $("#board").on("click", ".task-board", function() {
            window.location = $(this).data("task-url");
        });

        // Tooltips for tasks
        $(".task-board-tooltip").tooltip({
            track: false,
            position: {
                my: 'left-20 top',
                at: 'center bottom+9',
                using: function(position, feedback) {

                    $(this).css(position);

                    var arrow_pos = feedback.target.left + feedback.target.width / 2 - feedback.element.left - 20;

                    $("<div>")
                        .addClass("tooltip-arrow")
                        .addClass(feedback.vertical)
                        .addClass(arrow_pos == 0 ? "align-left" : "align-right")
                        .appendTo(this);
                }
            },
            content: function(e) {
                var href = $(this).attr('data-href');

                if (! href) {
                    return;
                }

                var _this = this;
                $.get(href, function setTooltipContent(data) {

                    $('.ui-tooltip-content:visible').html(data);

                    var tooltip = $('.ui-tooltip:visible');

                    // Clear previous position, it interferes with the updated position computation
                    tooltip.css({ top: '', left: '' });

                    // Remove arrow, it will be added when repositionning
                    tooltip.children('.tooltip-arrow').remove();

                    // Reposition the tooltip
                    var position = $(_this).tooltip("option", "position");
                    position.of = $(_this);
                    tooltip.position(position);

                    // Toggle subtasks status
                    $('#tooltip-subtasks a').click(function(e) {

                        e.preventDefault();
                        e.stopPropagation();

                        if ($(this).hasClass("popover-subtask-restriction")) {
                            Kanboard.OpenPopover($(this).attr('href'));
                            $(_this).tooltip('close');
                        }
                        else {
                            $.get($(this).attr('href'), setTooltipContent);
                        }
                    });
                });

                return '<i class="fa fa-refresh fa-spin fa-2x"></i>';
            }
        }).on("mouseenter", function() {

            var _this = this;
            $(this).tooltip("open");

            $(".ui-tooltip").on("mouseleave", function () {
                $(_this).tooltip('close');
            });

        }).on("mouseleave focusout", function (e) {

            e.stopImmediatePropagation();
            var _this = this;

            setTimeout(function () {
                if (! $(".ui-tooltip:hover").length) {
                    $(_this).tooltip("close");
                }
            }, 100);
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
        clearInterval(checkInterval);
    }

    // Save and refresh the board
    function board_save(taskId, columnId, position, swimlaneId)
    {
        board_unload_events();

        $.ajax({
            cache: false,
            url: $("#board").attr("data-save-url"),
            contentType: "application/json",
            type: "POST",
            processData: false,
            data: JSON.stringify({
                "task_id": taskId,
                "column_id": columnId,
                "swimlane_id": swimlaneId,
                "position": position
            }),
            success: function(data) {
                $("#board").remove();
                $("#main").append(data);
                Kanboard.InitAfterAjax();
                board_load_events();
                filter_apply();
                stack_show();
            }
        });
    }

    // Check if a board have been changed by someone else
    function board_check()
    {
        if (Kanboard.IsVisible()) {
            $.ajax({
                cache: false,
                url: $("#board").attr("data-check-url"),
                statusCode: {
                    200: function(data) {
                        $("#board").remove();
                        $("#main").append(data);
                        Kanboard.InitAfterAjax();
                        board_unload_events();
                        board_load_events();
                        filter_apply();
                        stack_show();
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
        var filterDueDate = $("#more-filters option[value=filter-due-date]").is(":selected")
        var filterRecent = $("#more-filters option[value=filter-recent]").is(":selected")
	    var projectId = $('#board').data('project-id');

        $("[data-task-id]").each(function(index, item) {

            var ownerId = item.getAttribute("data-owner-id");
            var dueDate = item.getAttribute("data-due-date");
            var categoryId = item.getAttribute("data-category-id");
            var recent = item.matches(".task-board-recent");

            if (ownerId != selectedUserId && selectedUserId != -1) {
                item.style.display = "none";
            }
            else {
                item.style.display = "block";
            }

            if (filterDueDate && (dueDate == "" || dueDate == "0")) {
                item.style.display = "none";
            }

            if (categoryId != selectedCategoryId && selectedCategoryId != -1) {
                item.style.display = "none";
            }

            if (filterRecent && ! recent) {
                item.style.display = "none";
            }
        });

        // Save filter settings
        Kanboard.SetStorageItem("board_filter_" + projectId + "_form-user_id", selectedUserId);
        Kanboard.SetStorageItem("board_filter_" + projectId + "_form-category_id", selectedCategoryId);
        Kanboard.SetStorageItem("board_filter_" + projectId + "_filter-due-date", ~~(filterDueDate));
        Kanboard.SetStorageItem("board_filter_" + projectId + "_filter-recent", ~~(filterRecent));
    }

    // Load filter events
    function filter_load_events()
    {
	    var projectId = $('#board').data('project-id');

        $("#form-user_id").chosen({
            width: "180px"
        });

        $("#form-category_id").chosen({
            width: "200px"
        });

        $("#more-filters").chosen({
            width: "30%"
        });

        $(".apply-filters").change(function(e) {
            filter_apply();
        });

        // Get and set filters from localStorage
        $("#form-user_id").val(Kanboard.GetStorageItem("board_filter_" + projectId + "_form-user_id") || -1);
        $("#form-user_id").trigger("chosen:updated");

        $("#form-category_id").val(Kanboard.GetStorageItem("board_filter_" + projectId + "_form-category_id") || -1);
        $("#form-category_id").trigger("chosen:updated");

        if (+Kanboard.GetStorageItem("board_filter_" + projectId + "_filter-due-date")) {
            $("#more-filters option[value=filter-due-date]").attr("selected", true);
        }

        if (+Kanboard.GetStorageItem("board_filter_" + projectId + "_filter-recent")) {
            $("#more-filters option[value=filter-recent]").attr("selected", true);
        }

        $("#more-filters").trigger("chosen:updated");

    	filter_apply();
    }

    jQuery(document).ready(function() {

        if (Kanboard.Exists("board")) {
            board_load_events();
            filter_load_events();
            stack_load_events();
            keyboard_shortcuts();
        }
    });

})();
