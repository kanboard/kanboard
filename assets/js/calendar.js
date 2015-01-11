// Calendar related functions
$(document).ready(function () {

    var checkInterval = null;

    $.getJSON('?controller=calendar&action=gettexts&project_id=' + $('#form-project_id').val(), function (data) {

        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            editable: true,
            eventDrop: function (event, delta, revertFunc) {
                var id = event.id;
                var start = event.start.format();
                $.ajax({
                    cache: false,
                    url: '?controller=calendar&action=updateevent&project_id=' + $('#form-project_id').val(),
                    contentType: "application/json",
                    type: "POST",
                    processData: false,
                    data: JSON.stringify({
                        "id": id,
                        "start": start,
                    }),
                    success: function (data) {
                        updateEvents();
                    }
                });
            },
            eventLimit: true, // allow "more" link when too many events
            monthNames: [data.January, data.February, data.March, data.April, data.May, data.June, data.July, data.August, data.September, data.October, data.November, data.December],
            monthNamesShort: [data.Jan, data.Feb, data.Mar, data.Apr, data.May, data.Jun, data.Jul, data.Aug, data.Sep, data.Oct, data.Nov, data.Dec],
            buttonText: {today: data.today},
            dayNames: [data.Sunday, data.Monday, data.Tuesday, data.Wednesday, data.Thursday, data.Friday, data.Saturday],
            dayNamesShort: [data.Sun, data.Mon, data.Tue, data.Wed, data.Thu, data.Fri, data.Sat]
        });

        //init
        updateEvents();
        $('#form-project_id').prop("disabled", true);
        $("#form-status_id option[value='1']").attr('selected', true);
        
        var projectId = $('#form-project_id').val();
        $("#form-user_id").val(localStorage.getItem("filters_" + projectId + "_form-user_id") || -1);
        $("#form-category_id").val(localStorage.getItem("filters_" + projectId + "_form-category_id") || -1);
        $("#form-column_id").val(localStorage.getItem("filters_" + projectId + "_form-column_id") || -1);
        $("#form-swimlane_id").val(localStorage.getItem("filters_" + projectId + "_form-swimlane_id") || -1);
        $("#form-color_id").val(localStorage.getItem("filters_" + projectId + "_form-color_id") || -1);
        $("#form-status_id").val(localStorage.getItem("filters_" + projectId + "_form-status_id") || -1);
        
        // Automatic refresh
        var interval = parseInt($("#calendarurl").attr("data-interval"));

        if (interval > 0) {
            checkInterval = window.setInterval(updateEvents, interval * 1000);
        }
    });

    function updateEvents() {
        $('#calendar').fullCalendar('removeEvents');
        //remove eventssource might be buggy, it might be not fully removed. so events are duplicated after changing the month
        //i.e.: https://code.google.com/p/fullcalendar/issues/detail?id=678
        $('#calendar').fullCalendar('removeEventSource', $("#calendarurl").attr("data-url_saved"));
        $('#calendar').fullCalendar('addEventSource', $("#calendarurl").attr("data-url"));
        $("#calendarurl").attr("data-url_saved", $("#calendarurl").attr("data-url"));
    }

    function changeDataUrl(parameter, value) {
        var queryParameters = {}, queryString = $("#calendarurl").attr("data-url"),
                re = /([^&=]+)=([^&]*)/g, m;
        // Creates a map with the query string parameters
        while (m = re.exec(queryString)) {
            queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
        }
        // Add new parameters or update existing ones
        queryParameters[parameter] = value;

        var newUrl = $.param(queryParameters);
        newUrl = decodeURIComponent(newUrl);
        $("#calendarurl").attr("data-url", newUrl);
    }
    
    function updateLocalStorage(parameter, value) {
        if (typeof(Storage) !== "undefined") {
            localStorage.setItem("filters_" + $('#form-project_id').val() + "_form-" + parameter, value);
        }
    }

    $('#form-project_id').change(function () {
        changeDataUrl('project_id', $(this).val());
        updateLocalStorage('project_id', $(this).val());
        updateEvents();
    });

    $('#form-user_id').change(function () {
        changeDataUrl('user_id', $(this).val());
        updateLocalStorage('user_id', $(this).val());
        updateEvents();
    });

    $('#form-category_id').change(function () {
        changeDataUrl('category_id', $(this).val());
        updateLocalStorage('category_id', $(this).val());
        updateEvents();
    });

    $('#form-column_id').change(function () {
        changeDataUrl('column_id', $(this).val());
        updateLocalStorage('column_id', $(this).val());
        updateEvents();
    });

    $('#form-status_id').change(function () {
        changeDataUrl('status_id', $(this).val());
        updateLocalStorage('status_id', $(this).val());
        updateEvents();
    });
    
    $('#form-swimlane_id').change(function () {
        changeDataUrl('swimlane_id', $(this).val());
        updateLocalStorage('swimlane_id', $(this).val());
        updateEvents();
    });
    
    $('#form-color_id').change(function () {
        changeDataUrl('color_id', $(this).val());
        updateLocalStorage('color_id', $(this).val());
        updateEvents();
    }); 
});