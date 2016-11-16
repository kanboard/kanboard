Vue.component('calendar', {
    props: ['saveUrl', 'checkUrl'],
    template: '<div id="calendar"></div>',
    ready: function() {
        var self = this;
        var calendar = $('#calendar');

        calendar.fullCalendar({
            locale: $("body").data("js-lang"),
            editable: true,
            eventLimit: true,
            defaultView: "month",
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            eventDrop: function(event) {
                $.ajax({
                    cache: false,
                    url: self.saveUrl,
                    contentType: "application/json",
                    type: "POST",
                    processData: false,
                    data: JSON.stringify({
                        "task_id": event.id,
                        "date_due": event.start.format()
                    })
                });
            },
            viewRender: function() {
                var url = self.checkUrl;
                var params = {
                    "start": calendar.fullCalendar('getView').start.format(),
                    "end": calendar.fullCalendar('getView').end.format()
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
        });
    }
});
