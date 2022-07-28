document.addEventListener('DOMContentLoaded', function () {
    var thischange = $('#thischange').val();
    var working_start = $('#working_start').val();
    var working_end = $('#working_end').val();
    let calendarEl = document.getElementById('calendar');
    let myCalendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'resourceTimelineWeek',
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        timeZone: 'UTC',
        aspectRatio: 1.5,
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
        },
        locale: 'en',
        //height: 'auto',
        nowIndicator: true,
        editable: false,
        resourceAreaHeaderContent: 'Task',
        allDaySlot: false,
        themeSystem: 'bootstrap',
        bootstrapFontAwesome: {
            close: ' mdi mdi-close',
            prev: ' mdi mdi-chevron-left',
            next: ' mdi mdi-chevron-right',
            prevYear: ' mdi mdi-chevron-double-left',
            nextYear: ' mdi mdi-chevron-double-right'
        },
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5],
            startTime: working_start,
            endTime: working_end,
        },
        weekNumberCalculation: 'ISO',
        resources: {
            url: "/calapi/taskscheduleres/" + thischange + "/",
            method: 'POST',
            cache: false
        },
        events: {
            url: "/calapi/taskschedule/" + thischange + "/",
            method: 'POST',
            cache: false
        }
    });
    myCalendar.render();
});