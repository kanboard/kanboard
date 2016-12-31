Kanboard.BoardCollapsedMode = function(app) {
    this.app = app;
};

Kanboard.BoardCollapsedMode.prototype.toggle = function() {
    var self = this;
    this.app.showLoadingIcon();

    $.ajax({
        cache: false,
        url: $('.filter-display-mode:not([style="display: none;"]) a').attr('href'),
        success: function(data) {
            $('.filter-display-mode').toggle();
            self.app.get("BoardDragAndDrop").refresh(data);
        }
    });
};