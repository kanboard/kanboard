(function() {

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
            $.ajax({
                cache: false,
                url: $('.filter-display-mode:not([style="display: none;"]) a').attr('href'),
                success: function(data) {
                    $("#board-container").remove();
                    $("#main").append(data);
                    Kanboard.InitAfterAjax();
                    board_unload_events();
                    board_load_events();
                    compactview_reload();
                    $('.filter-display-mode').toggle();
                }
            });
        });

        Mousetrap.bind("c", function() {
            compactview_toggle();
        });
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
            items: ".draggable-item",
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
                        .addClass(arrow_pos < 1 ? "align-left" : "align-right")
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
                    $('#tooltip-subtasks a').not(".popover").click(function(e) {

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
                $("#board-container").remove();
                $("#main").append(data);
                Kanboard.InitAfterAjax();
                board_load_events();
                compactview_reload();
            }
        });
    }

    // Check if the board have been changed by someone else
    function board_check()
    {
        if (Kanboard.IsVisible()) {
            $.ajax({
                cache: false,
                url: $("#board").attr("data-check-url"),
                statusCode: {
                    200: function(data) {
                        $("#board-container").remove();
                        $("#main").append(data);
                        Kanboard.InitAfterAjax();
                        board_unload_events();
                        board_load_events();
                        compactview_reload();
                    }
                }
            });
        }
    }

    function compactview_load_events()
    {
        jQuery(document).on('click', ".filter-toggle-scrolling", function(e) {
            e.preventDefault();
            compactview_toggle();
        });

        compactview_reload();
    }

    function compactview_toggle()
    {
        var scrolling = Kanboard.GetStorageItem("horizontal_scroll") || 1;
        Kanboard.SetStorageItem("horizontal_scroll", scrolling == 0 ? 1 : 0);
        compactview_reload();
    }

    function compactview_reload()
    {
        if (Kanboard.GetStorageItem("horizontal_scroll") == 0) {

            $(".filter-wide").show();
            $(".filter-compact").hide();

            $("#board-container").addClass("board-container-compact");
            $("#board th").addClass("board-column-compact");
        }
        else {

            $(".filter-wide").hide();
            $(".filter-compact").show();

            $("#board-container").removeClass("board-container-compact");
            $("#board th").removeClass("board-column-compact");
        }
    }

    jQuery(document).ready(function() {

        if (Kanboard.Exists("board")) {
            board_load_events();
            compactview_load_events();
            keyboard_shortcuts();
        }
    });

})();
