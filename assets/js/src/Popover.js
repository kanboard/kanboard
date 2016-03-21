Kanboard.Popover = function(app) {
    this.app = app;
};

Kanboard.Popover.prototype.listen = function() {
    var self = this;

    $(document).on("click", ".popover", function(e) {
        self.onClick(e);
    });

    $(document).on("click", ".close-popover", function(e) {
        self.close(e);
    });

    $(document).on("click", "#popover-container", function(e) {
        self.close(e);
    });

    $(document).on("click", "#popover-content", function(e) {
        e.stopPropagation();
    });
};

Kanboard.Popover.prototype.onClick = function(e) {
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

Kanboard.Popover.prototype.isOpen = function() {
    return $('#popover-container').size() > 0;
};

Kanboard.Popover.prototype.open = function(link) {
    var self = this;

    $.get(link, function(content) {
        $("body").prepend('<div id="popover-container"><div id="popover-content">' + content + '</div></div>');
        self.executeOnOpenedListeners();
    });
};

Kanboard.Popover.prototype.close = function(e) {
    if (this.isOpen()) {
        if (e) {
            e.preventDefault();
        }

        $("#popover-container").remove();
        this.executeOnClosedListeners();
    }
};

Kanboard.Popover.prototype.ajaxReload = function(data, request, self) {
    var redirect = request.getResponseHeader("X-Ajax-Redirect");

    if (redirect) {
        window.location = redirect === 'self' ? window.location.href.split("#")[0] : redirect;
    }
    else {
        $("#popover-content").html(data);
        $("#popover-content input[autofocus]").focus();
        self.executeOnOpenedListeners();
    }
};

Kanboard.Popover.prototype.executeOnOpenedListeners = function() {
    for (var className in this.app.controllers) {
        var controller = this.app.get(className);

        if (typeof controller.onPopoverOpened === "function") {
            controller.onPopoverOpened();
        }
    }

    this.afterOpen();
};

Kanboard.Popover.prototype.executeOnClosedListeners = function() {
    for (var className in this.app.controllers) {
        var controller = this.app.get(className);

        if (typeof controller.onPopoverClosed === "function") {
            controller.onPopoverClosed();
        }
    }
};

Kanboard.Popover.prototype.afterOpen = function() {
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
                    self.ajaxReload(data, request, self);
                },
                beforeSend: function() {
                    var button = $('.popover-form button[type="submit"]');
                    button.html('<i class="fa fa-spinner fa-pulse"></i> ' + button.html());
                    button.attr("disabled", true);
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
                self.ajaxReload(data, request, self);
            }
        });
    });

    // Autofocus fields (html5 autofocus works only with page onload)
    $("[autofocus]").each(function() {
        $(this).focus();
    });

    this.app.datePicker();
    this.app.autoComplete();
};
