Kanboard.Calendar = (function() {

    // Show the empty calendar
    function show_calendar()
    {
        var calendar = $("#calendar");
        var translations = calendar.data("translations");

        calendar.fullCalendar({
            editable: true,
            eventLimit: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            eventDrop: move_calendar_event,
            monthNames: [translations.January, translations.February, translations.March, translations.April, translations.May, translations.June, translations.July, translations.August, translations.September, translations.October, translations.November, translations.December],
            monthNamesShort: [translations.Jan, translations.Feb, translations.Mar, translations.Apr, translations.May, translations.Jun, translations.Jul, translations.Aug, translations.Sep, translations.Oct, translations.Nov, translations.Dec],
            buttonText: {today: translations.Today},
            dayNames: [translations.Sunday, translations.Monday, translations.Tuesday, translations.Wednesday, translations.Thursday, translations.Friday, translations.Saturday],
            dayNamesShort: [translations.Sun, translations.Mon, translations.Tue, translations.Wed, translations.Thu, translations.Fri, translations.Sat]
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
        var filters = Kanboard.GetStorageItem('calendar_filters');
        
        if (filters !== "undefined" && filters !== "") {
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
        
        Kanboard.SetStorageItem("calendar_filters", JSON.stringify(filters));
        refresh_calendar(filters);
    }

    return {
        Init: function() {
            show_calendar();
            load_filters();
        }
    };

})();
