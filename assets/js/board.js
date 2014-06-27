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

        // Tooltips
        $(".task-board-tooltip").tooltip({
            track: false,
            position: { my: 'left-20 top', at: 'center bottom+9', using: function( position, feedback ) {
                $( this ).css( position );
                var arrow_pos = feedback.target.left + feedback.target.width / 2 - feedback.element.left - 20
                $( "<div>" )
                    .addClass("tooltip-arrow")
                    .addClass(feedback.vertical)
                    .addClass(arrow_pos == 0 ? "align-left" : "align-right")
                    .appendTo(this);
            }},
            content: function(e) {
                var content = $(this).attr('data-content');
                 if (content)
                    return content;
                var href = $(this).attr('href');
                if (!href)
                    return;
                element = $(this)
                $.get(href, function setTooltipContent(data) {
                    $('.ui-tooltip-content:visible').html(data);
                    $('.ui-tooltip-content:visible a').click(function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var href = $(this).attr('href');
                        $.get(href, setTooltipContent);
                    });
                });
                return '<i class="fa fa-refresh fa-spin fa-2x"></i>';
            }
        }).on("mouseenter", function () {
            var _this = this;
            $(this).tooltip("open");
            $(".ui-tooltip").on("mouseleave", function () {
                $(_this).tooltip('close');
            });
        }).on("mouseleave focusout", function (e) {
            e.stopImmediatePropagation();

            var _this = this;
            setTimeout(function () {
                if (!$(".ui-tooltip:hover").length)
                    $(_this).tooltip("close");
            }, 100);
        });

        // Redirect to the task details page
        $("[data-task-url]").each(function() {
            $(this).click(function() {
                window.location = $(this).attr("data-task-url");
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
        $("[data-task-url]").off();
        clearInterval(checkInterval);
    }

    // Save and refresh the board
    function board_save(taskId, columnId, position)
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
                "position": position,
            }),
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
        if (Kanboard.IsVisible()) {
            $.ajax({
                cache: false,
                url: $("#board").attr("data-check-url"),
                statusCode: {
                    200: function(data) {
                        $("#board").remove();
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