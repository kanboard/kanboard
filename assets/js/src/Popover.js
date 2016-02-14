function Popover(app) {
    this.app = app;
    this.router = new Router();
    this.router.addRoute('screenshot-zone', Screenshot);
}

Popover.prototype.isOpen = function() {
    return $('#popover-container').size() > 0;
};

Popover.prototype.open = function(link) {
    var self = this;
    self.app.dropdown.close();

    $.get(link, function(content) {
        $("body").prepend('<div id="popover-container"><div id="popover-content">' + content + '</div></div>');
        self.app.refresh();
        self.router.dispatch(this.app);
        self.afterOpen();
    });
};

Popover.prototype.close = function(e) {
    if (this.isOpen()) {

        if (e) {
            e.preventDefault();
        }

        $('#popover-container').remove();
    }
};

Popover.prototype.onClick = function(e) {
    e.preventDefault();
    e.stopPropagation();

    var target = e.currentTarget || e.target;
    var link = target.getAttribute("href");

    if (! link) {
        link = target.getAttribute("data-href");
    }

    if (link) {
        this.open(link);
    }
};

Popover.prototype.listen = function() {
    $(document).on("click", ".popover", this.onClick.bind(this));
    $(document).on("click", ".close-popover", this.close.bind(this));
    $(document).on("click", "#popover-container", this.close.bind(this));
    $(document).on("click", "#popover-content", function(e) { e.stopPropagation(); });
};

Popover.prototype.afterOpen = function() {
    var self = this;
    var popoverForm = $("#popover-content .popover-form");

    // Submit forms with Ajax request
    if (popoverForm) {
        popoverForm.on("submit", function(e) {
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: popoverForm.attr("action"),
                data: popoverForm.serialize(),
                success: function(data, textStatus, request) {
                    self.afterSubmit(data, request, self);
                }
            });
        });
    }

    // Submit link with Ajax request
    $(document).on("click", ".popover-link", function(e) {
        e.preventDefault();

        $.ajax({
            type: "GET",
            url: $(this).attr("href"),
            success: function(data, textStatus, request) {
                self.afterSubmit(data, request, self);
            }
        });
    });
};

Popover.prototype.afterSubmit = function(data, request, self) {
    var redirect = request.getResponseHeader("X-Ajax-Redirect");

    if (redirect) {
        window.location = redirect === 'self' ? window.location.href.split("#")[0] : redirect;
    }
    else {
        $("#popover-content").html(data);
        $("#popover-content input[autofocus]").focus();
        self.afterOpen();
    }
};
