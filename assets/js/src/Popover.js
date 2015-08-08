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
        $("body").append('<div id="popover-container"><div id="popover-content">' + content + '</div></div>');
        self.router.dispatch();
        self.app.listen();
    });
};

Popover.prototype.close = function(e) {
    if (e) {
        e.preventDefault();
    }

    $('#popover-container').remove();
};

Popover.prototype.onClick = function(e) {
    e.preventDefault();
    e.stopPropagation();

    var link = e.target.getAttribute("href");

    if (! link) {
        link = e.target.getAttribute("data-href");
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
