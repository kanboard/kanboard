Kanboard.BoardDragAndDrop = function(app) {
    this.app = app;
    this.savingInProgress = false;
};

Kanboard.BoardDragAndDrop.prototype.execute = function() {
    if (this.app.hasId("board")) {
        this.executeListeners();
        this.dragAndDrop();
    }
};

Kanboard.BoardDragAndDrop.prototype.dragAndDrop = function() {
    var self = this;
    var dropzone = $(".board-task-list");
    var params = {
        forcePlaceholderSize: true,
        tolerance: "pointer",
        connectWith: ".sortable-column:visible",
        placeholder: "draggable-placeholder",
        items: ".draggable-item",
        stop: function(event, ui) {
            var task = ui.item;
            var taskId = task.attr('data-task-id');
            var taskPosition = task.attr('data-position');
            var taskColumnId = task.attr('data-column-id');
            var taskSwimlaneId = task.attr('data-swimlane-id');

            var newColumnId = task.parent().attr("data-column-id");
            var newSwimlaneId = task.parent().attr('data-swimlane-id');
            var newPosition = task.index() + 1;

            task.removeClass("draggable-item-selected");

            if (newColumnId != taskColumnId || newSwimlaneId != taskSwimlaneId || newPosition != taskPosition) {
                self.changeTaskState(taskId);
                self.save(taskId, taskColumnId, newColumnId, newPosition, newSwimlaneId);
            }
        },
        start: function(event, ui) {
            ui.item.addClass("draggable-item-selected");
            ui.placeholder.height(ui.item.height());
        }
    };

    if (isMobile.any) {
        $(".task-board-sort-handle").css("display", "inline");
        params.handle = ".task-board-sort-handle";
    }

    // Set dropzone height to the height of the table cell
    dropzone.each(function() {
        $(this).css("min-height", $(this).parent().height());
    });

    dropzone.sortable(params);
};

Kanboard.BoardDragAndDrop.prototype.changeTaskState = function(taskId) {
    var task = $("div[data-task-id=" + taskId + "]");
    task.addClass('task-board-saving-state');
    task.find('.task-board-saving-icon').show();
};

Kanboard.BoardDragAndDrop.prototype.save = function(taskId, srcColumnId, dstColumnId, position, swimlaneId) {
    var self = this;
    self.app.showLoadingIcon();
    self.savingInProgress = true;

    $.ajax({
        cache: false,
        url: $("#board").data("save-url"),
        contentType: "application/json",
        type: "POST",
        processData: false,
        data: JSON.stringify({
            "task_id": taskId,
            "src_column_id": srcColumnId,
            "dst_column_id": dstColumnId,
            "swimlane_id": swimlaneId,
            "position": position
        }),
        success: function(data) {
            self.refresh(data);
            self.savingInProgress = false;
        },
        error: function() {
            self.app.hideLoadingIcon();
            self.savingInProgress = false;
        },
        statusCode: {
            403: function(data) {
                window.alert(data.responseJSON.message);
                document.location.reload(true);
            }
        }
    });
};

Kanboard.BoardDragAndDrop.prototype.refresh = function(data) {
    $("#board-container").replaceWith(data);

    this.app.hideLoadingIcon();
    this.executeListeners();
    this.dragAndDrop();
};

Kanboard.BoardDragAndDrop.prototype.executeListeners = function() {
    for (var className in this.app.controllers) {
        var controller = this.app.get(className);

        if (typeof controller.onBoardRendered === "function") {
            controller.onBoardRendered();
        }
    }
};
