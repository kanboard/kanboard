// Calendar related functions
$(document).ready(function () {

    $.getJSON('?controller=calendar&action=getTexts', function (data) {

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
                $.post('?controller=calendar&action=updateevent', {id: id, start: start});
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
    });

    function updateEvents() {
        $('#calendar').fullCalendar('removeEvents');
        $('#calendar').fullCalendar('removeEventSource', $('.Source').val());
        $('#calendar').fullCalendar('addEventSource', $("#calendarurl").attr("data-url"));
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

    $('#form-project_id').change(function () {
        changeDataUrl('project_id', $(this).val());
        updateEvents();
    });

    $('#form-user_id').change(function () {
        changeDataUrl('user_id', $(this).val());
        updateEvents();
    });

    $('#form-category_id').change(function () {
        changeDataUrl('category_id', $(this).val());
        updateEvents();
    });

    $('#form-column_id').change(function () {
        changeDataUrl('column_id', $(this).val());
        updateEvents();
    });

    $('#form-status_id').change(function () {
        changeDataUrl('status_id', $(this).val());
        updateEvents();
    });
});