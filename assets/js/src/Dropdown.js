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
        self.close();

        var submenu = $(this).next('ul');
        var submenuHeight = 240;
        var offset = $(this).offset();
        var height = $(this).height();

        // Clone the submenu outside of the column to avoid clipping issue with overflow
        $("body").append(jQuery("<div>", {"id": "dropdown"}));
        submenu.clone().appendTo("#dropdown");

        var clone = $("#dropdown ul");
        clone.css('left', offset.left);

        if (offset.top + submenuHeight - $(window).scrollTop() > $(window).height()) {
            clone.css('top', offset.top - submenuHeight - height);
        }
        else {
            clone.css('top', offset.top + height);
        }

        clone.addClass('dropdown-submenu-open');
    });
};

Dropdown.prototype.close = function() {
    $("#dropdown").remove();
};
