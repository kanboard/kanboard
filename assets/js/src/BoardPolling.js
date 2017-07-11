Kanboard.BoardPolling = function(app) {
    this.app = app;
};

Kanboard.BoardPolling.prototype.execute = function() {
    if (this.app.hasId("board")) {
        var interval = parseInt($("#board").attr("data-check-interval"));

        if (interval > 0) {
            window.setInterval(this.check.bind(this), interval * 1000);
        }
    }
};

Kanboard.BoardPolling.prototype.check = function() {
    if (KB.utils.isVisible() && !this.app.get("BoardDragAndDrop").savingInProgress) {
        var self = this;
        this.app.showLoadingIcon();

        $.ajax({
            cache: false,
            url: $("#board").data("check-url"),
            statusCode: {
                200: function(data) {
                    self.app.get("BoardDragAndDrop").refresh(data);
                },
                304: function () {
                    self.app.hideLoadingIcon();
                }
            }
        });
    }
};
