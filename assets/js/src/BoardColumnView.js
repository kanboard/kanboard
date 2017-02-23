Kanboard.BoardColumnView = function(app) {
    this.app = app;
};

Kanboard.BoardColumnView.prototype.execute = function() {
    if (this.app.hasId("board")) {
        this.render();
    }
};

Kanboard.BoardColumnView.prototype.listen = function() {
    var self = this;

    $(document).on("click", ".board-toggle-column-view", function() {
        self.toggle($(this).data("column-id"));
    });
};

Kanboard.BoardColumnView.prototype.onBoardRendered = function() {
    this.render();
};

Kanboard.BoardColumnView.prototype.render = function() {
    var self = this;

    $(".board-column-header").each(function() {
        var columnId = $(this).data('column-id');
        if (localStorage.getItem("hidden_column_" + columnId)) {
            self.hideColumn(columnId);
        }
    });
};

Kanboard.BoardColumnView.prototype.toggle = function(columnId) {
    if (localStorage.getItem("hidden_column_" + columnId)) {
        this.showColumn(columnId);
    }
    else {
        this.hideColumn(columnId);
    }
    this.app.get("BoardDragAndDrop").dragAndDrop();
};

Kanboard.BoardColumnView.prototype.hideColumn = function(columnId) {
    $(".board-column-" + columnId + " .board-column-expanded").hide();
    $(".board-column-" + columnId + " .board-column-collapsed").show();
    $(".board-column-header-" + columnId + " .board-column-expanded").hide();
    $(".board-column-header-" + columnId + " .board-column-collapsed").show();

    $(".board-column-header-" + columnId).each(function() {
        $(this).removeClass("board-column-compact");
        $(this).addClass("board-column-header-collapsed");
    });

    $(".board-column-" + columnId).each(function() {
        $(this).addClass("board-column-task-collapsed");
    });

    $(".board-column-" + columnId + " .board-rotation").each(function() {
        $(this).css("width", $(".board-column-" + columnId + "").height());
    });

    localStorage.setItem("hidden_column_" + columnId, 1);
};

Kanboard.BoardColumnView.prototype.showColumn = function(columnId) {
    $(".board-column-" + columnId + " .board-column-expanded").show();
    $(".board-column-" + columnId + " .board-column-collapsed").hide();
    $(".board-column-header-" + columnId + " .board-column-expanded").show();
    $(".board-column-header-" + columnId + " .board-column-collapsed").hide();

    $(".board-column-header-" + columnId).removeClass("board-column-header-collapsed");
    $(".board-column-" + columnId).removeClass("board-column-task-collapsed");

    if (localStorage.getItem("horizontal_scroll") == 0) {
        $(".board-column-header-" + columnId).addClass("board-column-compact");
    }

    localStorage.removeItem("hidden_column_" + columnId);
};
