function Dropdown() {
}

Dropdown.prototype.listen = function() {
    var self = this;

    $(document).on('click', function() {
        self.close();
    });

    $(document).on('click', '.dropdown-menu', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var submenu = $(this).next('ul');
        var submenuHeight = 240;

        if (! submenu.is(':visible')) {
            self.close();

            if ($(this).offset().top + submenuHeight - $(window).scrollTop() > $(window).height()) {
                submenu.addClass('dropdown-submenu-open dropdown-submenu-top');
            }
            else {
                submenu.addClass('dropdown-submenu-open');
            }
        }
        else {
            self.close();
        }
    });
};

Dropdown.prototype.close = function() {
    $('.dropdown-submenu-open').removeClass('dropdown-submenu-open');
};
