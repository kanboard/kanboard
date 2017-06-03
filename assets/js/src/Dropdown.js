Kanboard.Dropdown = function(app) {
    this.app = app;
};

// TODO: rewrite this code
Kanboard.Dropdown.prototype.listen = function() {
    var self = this;

    $(document).on('click', function() {
        self.close();
    });

    $(document).on('click', '.dropdown-menu', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        self.close();

        var submenu = $(this).next('ul');
        var offset = $(this).offset();

        // Clone the submenu outside of the column to avoid clipping issue with overflow
        $("body").append(jQuery("<div>", {"id": "dropdown"}));
        submenu.clone().appendTo("#dropdown");

        var clone = $("#dropdown ul");
        clone.addClass('dropdown-submenu-open');

        var submenuHeight = clone.outerHeight();
        var submenuWidth = clone.outerWidth();

        if (offset.top + submenuHeight - $(window).scrollTop() < $(window).height() || $(window).scrollTop() + offset.top < submenuHeight) {
            clone.css('top', offset.top + $(this).height());
        }
        else {
            clone.css('top', offset.top - submenuHeight - 5);
        }

        if (offset.left + submenuWidth > $(window).width()) {
            clone.css('left', offset.left - submenuWidth + $(this).outerWidth());
        }
        else {
            clone.css('left', offset.left);
        }

        if (document.getElementById('dropdown') !== null) {
            KB.trigger('dropdown.afterRender');
        }
    });

    $(document).on('click', '.dropdown-submenu-open li', function(e) {
    	
        if ($(e.target).is('li')) {
            KB.trigger('dropdown.clicked');

            var element = $(this).find('a:visible');

            if (element.length > 0) {
                element[0].click(); // Calling native click() not the jQuery one
            }
        }
    });
};

Kanboard.Dropdown.prototype.close = function() {
    if (document.getElementById('dropdown') !== null) {
        KB.trigger('dropdown.beforeDestroy');
    }

    $("#dropdown").remove();
};
