Kanboard.BoardCollapsedMode = function(app) {
    this.app = app;
};

Kanboard.BoardCollapsedMode.prototype.keyboardShortcuts = function() {
    var self = this;

    if (self.app.hasId("board-container")) {
        Mousetrap.bind("s", function() {
            self.toggle();
        });
    }
};

Kanboard.BoardCollapsedMode.prototype.toggle = function() {
    var self = this;
    this.app.showLoadingIcon();

    var url = $('.filter-display-mode:not([style="display: none;"]) a').attr('href');

    if (self.app.hasId("bigboard")) {
      self.refreshAll(url)
    } else {
      var project_id = $("table.board-project").attr("data-project-id");
      self.refreshOne(project_id, url);
    }
};

Kanboard.BoardCollapsedMode.prototype.refreshOne = function(boardId, url) {
  var self = this;

  $.ajax({
      cache: false,
      url: url,
      success: function(data) {
          $('.filter-display-mode').toggle();
          self.app.get("BoardDragAndDrop").refresh(boardId,data);
      }
  });
};

Kanboard.BoardCollapsedMode.prototype.refreshAll = function(url) {
  var self = this;

  $.ajax({
      cache: false,
      url: url,
      success: function(data) {
          $('.filter-display-mode').toggle();
          $("div[id=bigboard]").replaceWith(data);

          self.app.hideLoadingIcon();
          self.app.get("BoardDragAndDrop").dragAndDrop();
          self.app.get("BoardDragAndDrop").executeListeners();
      }
  });
};
