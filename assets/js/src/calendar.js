Kanboard.Calendar = (function() {

    var filter_storage_key = "";

    // Save the new due date for a moved task
    function move_calendar_event(calendar_event)
    {
        $.ajax({
            cache: false,
            url: $("#calendar").data("save-url"),
            contentType: "application/json",
            type: "POST",
            processData: false,
            data: JSON.stringify({
                "task_id": calendar_event.id,
                "date_due": calendar_event.start.format()
            })
        });
    }

    // Show the user calendar
    function show_user_calendar()
    {
        var calendar = $("#user-calendar");

        calendar.fullCalendar({
            lang: $("body").data("js-lang"),
            editable: false,
            eventLimit: true,
            height: Kanboard.Exists("dashboard-calendar") ? 500 : "auto",
            defaultView: "agendaWeek",
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            viewRender: refresh_user_calendar
        });
    }

    // Refresh the calendar events
    function refresh_user_calendar()
    {
        var calendar = $("#user-calendar");
        var url = calendar.data("check-url");
        var params = {
            "start": calendar.fullCalendar('getView').start.format(),
            "end": calendar.fullCalendar('getView').end.format(),
            "user_id": calendar.data("user-id")
        };

        for (var key in params) {
            url += "&" + key + "=" + params[key];
        }

        $.getJSON(url, function(events) {
            calendar.fullCalendar('removeEvents');
            calendar.fullCalendar('addEventSource', events);
            calendar.fullCalendar('rerenderEvents');
        });
    }

    // Show the project calendar
    function show_project_calendar()
    {
        var calendar = $("#calendar");

        calendar.fullCalendar({
            lang: $("body").data("js-lang"),
            editable: true,
            eventLimit: true,
            defaultView: "month",
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            viewRender: load_project_filters,
            eventDrop: move_calendar_event
        });
    }

    // Refresh the calendar events
    function refresh_project_calendar(filters)
    {
        var calendar = $("#calendar");
        var url = calendar.data("check-url");
        var params = {
            "start": calendar.fullCalendar('getView').start.format(),
            "end": calendar.fullCalendar('getView').end.format()
        };

        jQuery.extend(params, filters);

        for (var key in params) {
            url += "&" + key + "=" + params[key];
        }

        $.getJSON(url, function(events) {
            calendar.fullCalendar('removeEvents');
            calendar.fullCalendar('addEventSource', events);
            calendar.fullCalendar('rerenderEvents');
        });
    }

    // Restore saved filters
    function load_project_filters()
    {
        var filters = Kanboard.GetStorageItem(filter_storage_key);

        if (filters !== "") {
            filters = JSON.parse(filters);

            for (var filter in filters) {
                $("select[name=" + filter + "]").val(filters[filter]);
            }
        }

        refresh_project_calendar(filters || {});

        $('.calendar-filter').change(apply_project_filters);
    }

    // Apply filters on change
    function apply_project_filters()
    {
        var filters = {};

        $('.calendar-filter').each(function() {
            filters[$(this).attr("name")] = $(this).val();
        });

        Kanboard.SetStorageItem(filter_storage_key, JSON.stringify(filters));
        refresh_project_calendar(filters);
    }

    jQuery(document).ready(function() {

        if (Kanboard.Exists("calendar")) {
            filter_storage_key = "calendar_filters_" + $("#calendar").data("project-id");
            show_project_calendar();
            load_project_filters();
        }
        else if (Kanboard.Exists("user-calendar")) {
            show_user_calendar();
        }
    });

})();
