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
        var offset = $(this).offset();

        // Clone the submenu outside of the column to avoid clipping issue with overflow
        $("body").append(jQuery("<div>", {"id": "dropdown"}));
        submenu.clone().appendTo("#dropdown");

        var clone = $("#dropdown ul");
        clone.addClass('dropdown-submenu-open');

        var submenuHeight = clone.outerHeight();
        var submenuWidth = clone.outerWidth();

        if (offset.top + submenuHeight - $(window).scrollTop() > $(window).height()) {
            clone.css('top', offset.top - submenuHeight - 5);
        }
        else {
            clone.css('top', offset.top + $(this).height());
        }

        if (offset.left + submenuWidth > $(window).width()) {
            clone.css('left', offset.left - submenuWidth + $(this).outerWidth());
        }
        else {
            clone.css('left', offset.left);
        }
    });

    $(document).on('click', '.dropdown-submenu-open li', function(e) {
        if ($(e.target).is('li')) {
            $(this).find('a:visible')[0].click(); // Calling native click() not the jQuery one
        }
    });

    // User mention autocomplete
    $('textarea[data-mention-search-url]').textcomplete([{
        match: /(^|\s)@(\w*)$/,
        search: function (term, callback) {
            var url = $('textarea[data-mention-search-url]').data('mention-search-url');
            $.getJSON(url, { q: term })
                .done(function (resp) { callback(resp); })
                .fail(function ()     { callback([]);   });
        },
        replace: function (value) {
            return '$1@' + value + ' ';
        },
        cache: true
    }], {className: "textarea-dropdown"});
};

Dropdown.prototype.close = function() {
    $("#dropdown").remove();
};
