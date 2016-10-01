Kanboard.Session = function(app) {
    this.app = app;
};

Kanboard.Session.prototype.execute = function() {
    window.setInterval(this.checkSession, 60000);
};

Kanboard.Session.prototype.checkSession = function() {
    if (! $(".form-login").length) {
        $.ajax({
            cache: false,
            url: $("body").data("status-url"),
            statusCode: {
                401: function() {
                    window.location = $("body").data("login-url");
                }
            }
        });
    }
};
