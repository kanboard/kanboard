Kanboard.App = function() {
    this.controllers = {};
};

Kanboard.App.prototype.get = function(controller) {
    return this.controllers[controller];
};

Kanboard.App.prototype.execute = function() {
    for (var className in Kanboard) {
        if (className !== "App") {
            var controller = new Kanboard[className](this);
            this.controllers[className] = controller;

            if (typeof controller.execute === "function") {
                controller.execute();
            }

            if (typeof controller.listen === "function") {
                controller.listen();
            }

            if (typeof controller.focus === "function") {
                controller.focus();
            }

            if (typeof controller.keyboardShortcuts === "function") {
                controller.keyboardShortcuts();
            }
        }
    }

    this.focus();
    this.chosen();
    this.keyboardShortcuts();
    this.datePicker();
    this.autoComplete();
};

Kanboard.App.prototype.keyboardShortcuts = function() {
    var self = this;

    // Submit form
    Mousetrap.bindGlobal("mod+enter", function() {
        $("form").submit();
    });

    // Open board selector
    Mousetrap.bind("b", function(e) {
        e.preventDefault();
        $('#board-selector').trigger('chosen:open');
    });

    // Close popover and dropdown
    Mousetrap.bindGlobal("esc", function() {
        self.get("Popover").close();
        self.get("Dropdown").close();
    });

    // Show keyboard shortcut
    Mousetrap.bind("?", function() {
        self.get("Popover").open($("body").data("keyboard-shortcut-url"));
    });
};

Kanboard.App.prototype.focus = function() {
    // Auto-select input fields
    $(document).on('focus', '.auto-select', function() {
        $(this).select();
    });

    // Workaround for chrome
    $(document).on('mouseup', '.auto-select', function(e) {
        e.preventDefault();
    });
};

Kanboard.App.prototype.chosen = function() {
    $(".chosen-select").each(function() {
        var searchThreshold = $(this).data("search-threshold");

        if (searchThreshold === undefined) {
            searchThreshold = 10;
        }

        $(this).chosen({
            width: "180px",
            no_results_text: $(this).data("notfound"),
            disable_search_threshold: searchThreshold
        });
    });

    $(".select-auto-redirect").change(function() {
        var regex = new RegExp($(this).data('redirect-regex'), 'g');
        window.location = $(this).data('redirect-url').replace(regex, $(this).val());
    });
};

Kanboard.App.prototype.datePicker = function() {
    // Datepicker translation
    $.datepicker.setDefaults($.datepicker.regional[$("body").data("js-lang")]);

    // Datepicker
    $(".form-date").datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: 'yy-mm-dd',
        constrainInput: false
    });

    // Datetime picker
    $(".form-datetime").datetimepicker({
        controlType: 'select',
        oneLine: true,
        dateFormat: 'yy-mm-dd',
        // timeFormat: 'h:mm tt',
        constrainInput: false
    });
};

Kanboard.App.prototype.autoComplete = function() {
    $(".autocomplete").each(function() {
        var input = $(this);
        var field = input.data("dst-field");
        var extraField = input.data("dst-extra-field");

        if ($('#form-' + field).val() == '') {
            input.parent().find("button[type=submit]").attr('disabled','disabled');
        }

        input.autocomplete({
            source: input.data("search-url"),
            minLength: 1,
            select: function(event, ui) {
                $("input[name=" + field + "]").val(ui.item.id);

                if (extraField) {
                    $("input[name=" + extraField + "]").val(ui.item[extraField]);
                }

                input.parent().find("button[type=submit]").removeAttr('disabled');
            }
        });
    });
};

Kanboard.App.prototype.hasId = function(id) {
    return !!document.getElementById(id);
};

Kanboard.App.prototype.showLoadingIcon = function() {
    $("body").append('<span id="app-loading-icon">&nbsp;<i class="fa fa-spinner fa-spin"></i></span>');
};

Kanboard.App.prototype.hideLoadingIcon = function() {
    $("#app-loading-icon").remove();
};

Kanboard.App.prototype.formatDuration = function(d) {
    if (d >= 86400) {
        return Math.round(d/86400) + "d";
    }
    else if (d >= 3600) {
        return Math.round(d/3600) + "h";
    }
    else if (d >= 60) {
        return Math.round(d/60) + "m";
    }

    return d + "s";
};

Kanboard.App.prototype.isVisible = function() {
    var property = "";

    if (typeof document.hidden !== "undefined") {
        property = "visibilityState";
    } else if (typeof document.mozHidden !== "undefined") {
        property = "mozVisibilityState";
    } else if (typeof document.msHidden !== "undefined") {
        property = "msVisibilityState";
    } else if (typeof document.webkitHidden !== "undefined") {
        property = "webkitVisibilityState";
    }

    if (property != "") {
        return document[property] == "visible";
    }

    return true;
};
