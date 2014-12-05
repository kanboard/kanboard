$(document).ready(function () {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        editable: true,
        eventDrop: function(event, delta, revertFunc) {
            alert(event.title + " was dropped on " + event.start.format());
        },
        eventLimit: true // allow "more" link when too many events
    });
        
    function updateEvents(){
        $('#calendar').fullCalendar('removeEvents');
        $('#calendar').fullCalendar('removeEventSource', $('.Source').val());
        $('#calendar').fullCalendar('addEventSource', $("#calendarurl").attr("data-url"));
    }
    
    updateEvents();
    
    
    $('#form-user_id').change(function() {
        alert( "Userid changed." );
        var newUrl = $("#calendarurl").attr("data-url");
        $("#calendarurl").attr("data-url", newUrl);
    });
    
    $('#form-category_id').change(function() {
        alert( "Category changed." );
        var newUrl = $("#calendarurl").attr("data-url");
        $("#calendarurl").attr("data-url", newUrl);
    });
});