Kanboard.BoardPolling = function(app) {
    this.app = app;
};

Kanboard.BoardPolling.prototype.execute = function() {
<<<<<<< 4130de3ca21bb17e4bbb7d564f2c198fc5285529
    if (this.app.hasId("board-container")) {
        var interval = parseInt($("table.board-project").attr("data-check-interval"));
=======
    if (this.app.hasId("board")) {
        var interval = parseInt($("#board").attr("data-check-interval"));

>>>>>>> changed to correct file
        if (interval > 0) {
            window.setInterval(this.check.bind(this), interval * 1000);
        }
    }
};

Kanboard.BoardPolling.prototype.check = function() {
    if (this.app.isVisible() && !this.app.get("BoardDragAndDrop").savingInProgress) {
        var self = this;
        this.app.showLoadingIcon();
<<<<<<< 4130de3ca21bb17e4bbb7d564f2c198fc5285529
        // Poll every board
        var pollsinprogress=0;
        $("table.board-project").each(function() {
          var boardid = $(this).attr("data-project-id");
          var url = $(this).attr("data-check-url");
          pollsinprogress++;
=======

        // Poll every board
        $("table[id=board]").each(function() {
          var boardid = $(this).attr("data-project-id");
          var url = $(this).attr("data-check-url");
>>>>>>> changed to correct file
            $.ajax({
                cache: false,
                url: url,
                statusCode: {
                    200: function(data) {
                        self.app.get("BoardDragAndDrop").refresh(boardid,data);
<<<<<<< 4130de3ca21bb17e4bbb7d564f2c198fc5285529
                        pollsinprogress--;
                        if (pollsinprogress <= 0) self.app.hideLoadingIcon();
                    },
                    304: function () {
                        pollsinprogress--;
                        if (pollsinprogress <= 0) self.app.hideLoadingIcon();
=======
                    },
                    304: function () {
                        self.app.hideLoadingIcon();
>>>>>>> changed to correct file
                    }
                }
            });
        });
    }
};
