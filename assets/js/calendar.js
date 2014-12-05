$(document).ready(function () {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        editable: true,
        eventDrop: function (event, delta, revertFunc) {
            alert(event.title + " was dropped on " + event.start.format());
        },
        eventLimit: true // allow "more" link when too many events
    });

    function updateEvents() {
        $('#calendar').fullCalendar('removeEvents');
        $('#calendar').fullCalendar('removeEventSource', $('.Source').val());
        $('#calendar').fullCalendar('addEventSource', $("#calendarurl").attr("data-url"));
    }

    $('#form-project_id').prop("disabled", true);
    $("#form-status_id option[value='1']").attr('selected',true);

    updateEvents();

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
        changeDataUrl('project_id',$(this).val());
        updateEvents();
    });
    
    $('#form-user_id').change(function () {
        changeDataUrl('user_id',$(this).val());
        updateEvents();
    });
    

    $('#form-category_id').change(function () {
        changeDataUrl('category_id',$(this).val());
        updateEvents();
    });
    
    $('#form-column_id').change(function () {
        changeDataUrl('column_id',$(this).val());
        updateEvents();
    });
    
    $('#form-status_id').change(function () {
        changeDataUrl('status_id',$(this).val());
        updateEvents();
    });
});