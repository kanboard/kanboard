Kanboard.BoardTask = function(app) {
    this.app = app;
};

Kanboard.BoardTask.prototype.listen = function() {
    var self = this;

    $(document).on("click", ".task-board-change-assignee", function(e) {
        e.preventDefault();
        e.stopPropagation();
        self.app.get("Popover").open($(this).data('url'));
    });

    $(document).on("click", ".task-board", function(e) {
        if (e.target.tagName != "A" && e.target.tagName != "IMG") {
            window.location = $(this).data("task-url");
        }
    });
};

Kanboard.BoardTask.prototype.keyboardShortcuts = function() {
    var self = this;

    if (self.app.hasId("board")) {
        Mousetrap.bind("n", function () {
            self.app.get("Popover").open($("#board").data("task-creation-url"));
        });
    }
};
