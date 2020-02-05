Kanboard.BoardVerticalScrolling = function(app) {
    this.app = app;
};

Kanboard.BoardVerticalScrolling.prototype.execute = function() {
    if (this.app.hasId("board")) {
        this.render();
    }
};

Kanboard.BoardVerticalScrolling.prototype.listen = function() {
    var self = this;

    $(document).on('click', ".filter-vert-toggle-collapse", function(e) {
        e.preventDefault();
        self.toggle();
    });
};

Kanboard.BoardVerticalScrolling.prototype.onBoardRendered = function() {
    this.render();
};

Kanboard.BoardVerticalScrolling.prototype.toggle = function() {
    var self = this;
    var scrolling = localStorage.getItem("vertical_scroll") || 1;
    localStorage.setItem("vertical_scroll", scrolling == 0 ? 1 : 0);

    var task_lists = $(".board-task-list");
    task_lists.each(function() {
        // clear min-height so that it can be properly recalculated
        $(this).css("min-height", "");
    });
    this.render();

    // set up drag and drop again (resets min-height)
    self.app.get("BoardDragAndDrop").dragAndDrop();
};

Kanboard.BoardVerticalScrolling.prototype.render = function() {
    if (localStorage.getItem("vertical_scroll") == 0) {
        $(".filter-vert-expand").show();
        $(".filter-vert-collapse").hide();

        $("#board td .board-task-list").addClass("board-task-list-compact");
    }
    else {
        $(".filter-vert-expand").hide();
        $(".filter-vert-collapse").show();

        $("#board td .board-task-list").removeClass("board-task-list-compact");
    }
};
