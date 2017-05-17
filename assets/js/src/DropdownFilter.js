Kanboard.DropdownFilter = function(app) {
    this.app = app;
};

Kanboard.DropdownFilter.prototype.listen = function() {
    var self = this;

    $(document).on('click', '.dropdown-submenu-open li.dropdown-filter', function(e) {
        // prevent dropdown close on filter input click
        e.preventDefault();
        e.stopImmediatePropagation();
    });

    KB.on('dropdown.afterRender', function() {
        // only if dropdown pops up above
        if ($('#dropdown .dropdown-submenu-open').hasClass('dropdown-submenu-open--above')) {
            var $dropdown = $('.dropdown-submenu-open');

            var dropdown_offset = $dropdown.offset();
            var dropdown_outerHeight = $dropdown.outerHeight();
            
            var dropdown_bottom = window.innerHeight - (dropdown_offset.top + dropdown_outerHeight);

            // set bottom position instead of top for dropdown to avoid glitches while filtering 
            $dropdown.css('top', '')
                .css('bottom', dropdown_bottom);

            // move filter input's li to bottom of ul
            $('.dropdown-submenu-open li.dropdown-filter').detach()
                .appendTo('.dropdown-submenu-open');
        }

        var $filter_input = $('.dropdown-submenu-open li.dropdown-filter input.dropdown-filter-input');

        $filter_input.focus();

        // filter lis on type
        $filter_input.off('keyup')
            .on('keyup', function(event) {
                var filter_value = $(this).val().toLowerCase();

                $(this).closest('.dropdown-submenu-open').find('li:not(.dropdown-filter, .dropdown-filter-form)')
                    .each(function() {
                        if ($(this).text().toLowerCase().search(filter_value) > -1) {
                            $(this).show();
                        }
                        else {
                            $(this).hide();
                        }
                });
        });
    });
};
