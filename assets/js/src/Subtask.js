Kanboard.Subtask = function(app) {
    this.app = app;
};

Kanboard.Subtask.prototype.listen = function() {
    var self = this;
    this.dragAndDrop();

    $(document).on("click", ".subtask-toggle-status", function(e) {
        var el = $(this);
        e.preventDefault();

        $.ajax({
            cache: false,
            url: el.attr("href"),
            success: function(data) {
                if (el.hasClass("subtask-refresh-table")) {
                    $(".subtasks-table").replaceWith(data);
                } else {
                    el.replaceWith(data);
                }

                self.dragAndDrop();
            }
        });
    });

    $(document).on("click", ".subtask-toggle-timer", function(e) {
        var el = $(this);
        e.preventDefault();

        $.ajax({
            cache: false,
            url: el.attr("href"),
            success: function(data) {
                $(".subtasks-table").replaceWith(data);
                self.dragAndDrop();
            }
        });
    });
};

Kanboard.Subtask.prototype.dragAndDrop = function() {
    var self = this;

    $(".draggable-row-handle").mouseenter(function() {
        $(this).parent().parent().addClass("draggable-item-hover");
    }).mouseleave(function() {
        $(this).parent().parent().removeClass("draggable-item-hover");
    });

    $(".subtasks-table tbody").sortable({
        forcePlaceholderSize: true,
        handle: "td:first i",
        helper: function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });

            return ui;
        },
        stop: function(event, ui) {
            var subtask = ui.item;
            subtask.removeClass("draggable-item-selected");
            self.savePosition(subtask.data("subtask-id"), subtask.index() + 1);
        },
        start: function(event, ui) {
            ui.item.addClass("draggable-item-selected");
        }
    }).disableSelection();
};

Kanboard.Subtask.prototype.savePosition = function(subtaskId, position) {
    var url = $(".subtasks-table").data("save-position-url");
    var self = this;

    this.app.showLoadingIcon();

    $.ajax({
        cache: false,
        url: url,
        contentType: "application/json",
        type: "POST",
        processData: false,
        data: JSON.stringify({
            "subtask_id": subtaskId,
            "position": position
        }),
        complete: function() {
            self.app.hideLoadingIcon();
        }
    });
};
