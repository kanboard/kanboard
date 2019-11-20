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
        }
    }

    this.focus();
    this.datePicker();
    this.autoComplete();
    this.tagAutoComplete();
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

Kanboard.App.prototype.datePicker = function() {
    var bodyElement = $("body");
    var dateFormat = bodyElement.data("js-date-format");
    var timeFormat = bodyElement.data("js-time-format");
    var lang = $("html").attr("lang");

    $.datepicker.setDefaults($.datepicker.regional[lang]);
    $.timepicker.setDefaults($.timepicker.regional[lang]);

    // Datepicker
    $(".form-date").datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: dateFormat,
        constrainInput: false
    });

    // Datetime picker
    $(".form-datetime").datetimepicker({
        dateFormat: dateFormat,
        timeFormat: timeFormat,
        constrainInput: false,
        amNames: ['am', 'AM'],
        pmNames: ['pm', 'PM']
    });
};

Kanboard.App.prototype.tagAutoComplete = function() {
    $(".tag-autocomplete").select2({
        tags: true
    });
};

Kanboard.App.prototype.autoComplete = function() {
    $(".autocomplete").each(function() {
        var input = $(this);
        var field = input.data("dst-field");
        var extraFields = input.data("dst-extra-fields");

        if ($('#form-' + field).val() === '') {
            input.parent().find("button[type=submit]").attr('disabled','disabled');
        }

        input.autocomplete({
            source: input.data("search-url"),
            minLength: 1,
            select: function(event, ui) {
                $("input[name=" + field + "]").val(ui.item.id);

                if (extraFields) {
                    var fields = extraFields.split(',');

                    for (var i = 0; i < fields.length; i++) {
                        var fieldName = fields[i].trim();
                        $("input[name=" + fieldName + "]").val(ui.item[fieldName]);
                    }
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
