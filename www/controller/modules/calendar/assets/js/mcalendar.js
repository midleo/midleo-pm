document.addEventListener('DOMContentLoaded', function () {
  var user = $('#username').val();
  var working_start = $('#working_start').val();
  var working_end = $('#working_end').val();
  var calendarEl = document.getElementById('calendar');
  var calendar = new FullCalendar.Calendar(calendarEl, {
    headerToolbar: {
      left: 'prevYear,prev,next,nextYear today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    initialView: 'timeGridWeek',
    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
    navLinks: true,
    locale: 'en',
    nowIndicator: true,
    weekNumbers: true,
    allDaySlot: false,
    weekNumberCalculation: 'ISO',
    firstDay: 1,
    businessHours: {
      daysOfWeek: [1, 2, 3, 4, 5],
      startTime: working_start,
      endTime: working_end,
    },
    dayMaxEvents: true,
    weekends: false,
    eventDidMount: function(info) {
      var tooltip = new bootstrap.Popover(info.el, {
        title: info.event.extendedProps.description,
        placement: 'top',
        trigger: 'hover',
        container: 'body'
      });
    },
    events: "/calapi/calendar/" + user + "/",
    eventClick: function (arg) {
      if (confirm('Are you sure you want to delete this event?')) {
        var dataString = 'event_id=' + arg.event.id;
        $.ajax({
          type: "POST",
          url: "/calapi/calendar/" + user + "/delete",
          data: dataString,
          success: function (html) { notify(html, 'danger'); arg.event.remove(); }
        });
      }
    },
    editable: true
  });
  calendar.render();
  $(".calalert").hide();
});