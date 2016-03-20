Kanboard.Notification = function(app) {
    this.app = app;
};

Kanboard.Notification.prototype.execute = function() {
    $(".alert-fade-out").delay(4000).fadeOut(800, function() {
        $(this).remove();
    });
};
