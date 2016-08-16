Kanboard.BoardDragAndDrop = function(app) {
    this.app = app;
    this.savingInProgress = false;
};

Kanboard.BoardDragAndDrop.prototype.execute = function() {
    console.log(this.app.hasId("board-container"));
    if (this.app.hasId("board-container")) {
        this.dragAndDrop();
        this.executeListeners();
    }
};

Kanboard.BoardDragAndDrop.prototype.dragAndDrop = function() {
    var self = this;

    var dropzone = $(".board-task-list");

    // Run for every Board List, connecting the Items within the same project id
    dropzone.each(function() {
        $(this).css("min-height", $(this).parent().height());
        var project_id = $(this).closest("table.board-project").attr("data-project-id");
        var params = {
            forcePlaceholderSize: true,
            tolerance: "pointer",
            connectWith: ".board-task-list[data-project-id=" + project_id + "]",
            placeholder: "draggable-placeholder",
            items: ".draggable-item[data-project-id=" + project_id + "]",
            stop: function(event, ui) {
                console.log("draggable stop");
                var task = ui.item;
                var taskId = task.attr('data-task-id');
                var taskPosition = task.attr('data-position');
                var taskColumnId = task.attr('data-column-id');
                var taskSwimlaneId = task.attr('data-swimlane-id');

                var newColumnId = task.parent().attr("data-column-id");
                var newSwimlaneId = task.parent().attr('data-swimlane-id');
                var newPosition = task.index() + 1;

                var boardId = task.closest("table").attr("data-project-id");
                var saveURL = task.closest("table").attr("data-save-url");

                task.removeClass("draggable-item-selected");

                if (newColumnId != taskColumnId || newSwimlaneId != taskSwimlaneId || newPosition != taskPosition) {
                    self.changeTaskState(boardId, taskId);
                    self.save(saveURL, boardId, taskId, newColumnId, newPosition, newSwimlaneId);
                }
            },
            start: function(event, ui) {
                console.log("draggable start");
                ui.item.addClass("draggable-item-selected");
                ui.placeholder.height(ui.item.height());
            }

        };
        if (isMobile.any) {
            $(".task-board-sort-handle").css("display", "inline");
            params["handle"] = ".task-board-sort-handle";
        }
        $(this).sortable(params);
    });
};

Kanboard.BoardDragAndDrop.prototype.changeTaskState = function(boardId, taskId) {
    var board = $("table[data-project-id=" + boardId + "]");
    var task = board.find("div[data-task-id=" + taskId + "]");
    task.addClass('task-board-saving-state');
    task.find('.task-board-saving-icon').show();
};

Kanboard.BoardDragAndDrop.prototype.save = function(saveURL, boardId, taskId, columnId, position, swimlaneId) {
    var self = this;
    self.app.showLoadingIcon();
    self.savingInProgress = true;

    $.ajax({
        cache: false,
        url: saveURL,
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
            self.refresh(boardId, data);
            self.savingInProgress = false;
        },
        error: function() {
            self.app.hideLoadingIcon();
            self.savingInProgress = false;
        }
    });
};

Kanboard.BoardDragAndDrop.prototype.refresh = function(boardId, data) {
    $("div[id=board-container][data-project-id=" + boardId + "]").replaceWith(data);

    this.app.hideLoadingIcon();
    this.dragAndDrop();
    this.executeListeners();
};

Kanboard.BoardDragAndDrop.prototype.executeListeners = function() {
    for (var className in this.app.controllers) {
        var controller = this.app.get(className);

        if (typeof controller.onBoardRendered === "function") {
            controller.onBoardRendered();
        }
    }
};
