Kanboard.Calendar = (function() {

    var filter_storage_key = "";

    // Show the empty calendar
    function show_calendar()
    {
        var calendar = $("#calendar");
        var translations = calendar.data("translations");

        calendar.fullCalendar({
            lang: $("body").data("js-lang"),
            editable: true,
            eventLimit: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            viewRender: load_filters,
            eventDrop: move_calendar_event
        });
    }

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

    // Refresh the calendar events
    function refresh_calendar(filters)
    {
        var calendar = $("#calendar");
        var url = calendar.data("check-url");
        var params = {
            "start": calendar.fullCalendar('getView').start.format(),
            "end": calendar.fullCalendar('getView').end.format()
        }

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
    function load_filters()
    {
        var filters = Kanboard.GetStorageItem(filter_storage_key);
        
        if (filters !== "") {
            filters = JSON.parse(filters);

            for (var filter in filters) {
                $("select[name=" + filter + "]").val(filters[filter]);
            }
        }

        refresh_calendar(filters || {});

        $('.calendar-filter').change(apply_filters);
    }

    // Apply filters on change
    function apply_filters()
    {
        var filters = {};

        $('.calendar-filter').each(function(index, element) {
            filters[$(this).attr("name")] = $(this).val();
        });
        
        Kanboard.SetStorageItem(filter_storage_key, JSON.stringify(filters));
        refresh_calendar(filters);
    }

    jQuery(document).ready(function() {

        if (Kanboard.Exists("calendar")) {
            filter_storage_key = "calendar_filters_" + $("#calendar").data("project-id");
            show_calendar();
            load_filters();
        }
    });

})();
