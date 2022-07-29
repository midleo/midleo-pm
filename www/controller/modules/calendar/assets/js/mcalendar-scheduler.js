document.addEventListener('DOMContentLoaded', function () {
    var thischange = $('#thischange').val();
    var working_start = new Date($('#working_start').val());
    var working_end = new Date($('#working_end').val());
    let calendarEl = document.getElementById('calendar');
    let myCalendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'resourceTimelineDay',
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        timeZone: 'UTC',
        initialDate: working_start,
        aspectRatio: 1.5,
        headerToolbar: {
            left: '',
            center: 'title',
            right: ''
          },
        locale: 'en',
        height: 'auto',
        nowIndicator: true,
        editable: false,
        resourceAreaHeaderContent: 'Task',
        allDaySlot: false,
        slotMinTime: (working_start.getHours()-1)+":00",
        slotMaxTime: (working_end.getHours()+2)+":00",
        themeSystem: 'bootstrap5',
        bootstrapFontAwesome: {
            close: ' mdi mdi-close',
            prev: ' mdi mdi-chevron-left',
            next: ' mdi mdi-chevron-right',
            prevYear: ' mdi mdi-chevron-double-left',
            nextYear: ' mdi mdi-chevron-double-right'
        },
        resources: {
            url: "/calapi/taskscheduleres/" + thischange + "/",
            method: 'POST',
            cache: false
        },
        events: {
            url: "/calapi/taskschedule/" + thischange + "/",
            method: 'POST',
            cache: false
        },
        resourceLabelDidMount: function(info) {
            var questionMark = document.createElement('strong');
            questionMark.innerText = ' Details | ';
      
            info.el.querySelector('.fc-datagrid-cell-main')
              .prepend(questionMark);
      
            var tooltip = new bootstrap.Tooltip(questionMark, {
              title: info.resource.extendedProps.taskinfo,
              placement: 'top',
              trigger: 'hover',
              container: 'body',
              html: true
            });
        }
    });
    myCalendar.render();
    myCalendar.scrollToTime( working_start );
});