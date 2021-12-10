document.addEventListener('DOMContentLoaded', function () {
  var user = $('#username').val();
  var working_start = $('#working_start').val();
  var working_end = $('#working_end').val();
  let calendarEl = document.getElementById('calendar');
  let myCalendar = new FullCalendar.Calendar(calendarEl, {
    headerToolbar: {
      left: 'prevYear,prev,next,nextYear today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    initialView: 'dayGridMonth',
    schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
    navLinks: true,
    locale: 'en',
    nowIndicator: true,
    weekNumbers: true,
    allDaySlot: false,
    themeSystem: 'bootstrap',
    bootstrapFontAwesome: {
      close: ' mdi mdi-close',
      prev: ' mdi mdi-chevron-left',
      next: ' mdi mdi-chevron-right',
      prevYear: ' mdi mdi-chevron-double-left',
      nextYear: ' mdi mdi-chevron-double-right'
    },
    weekNumberCalculation: 'ISO',
    firstDay: 1,
    businessHours: {
      daysOfWeek: [1, 2, 3, 4, 5],
      startTime: working_start,
      endTime: working_end,
    },
    dayMaxEvents: true,
    weekends: false,
    events: "/calapi/calendar/" + user + "/",
//    eventClick: function (arg) {
//     if (confirm('Are you sure you want to delete this event?')) {
//        var dataString = 'event_id=' + arg.event.id;
//        $.ajax({
//          type: "POST",
//          url: "/calapi/calendar/" + user + "/delete",
//          data: dataString,
//          success: function (html) { notify(html, 'danger'); arg.event.remove(); }
//        });
//      }
//    },
    eventDidMount: function(arg) {
      var popover = new bootstrap.Popover(arg.el, {
        title: arg.event.extendedProps.description,
        content: arg.event.extendedProps.description,
        placement: 'left',
        trigger: 'hover',
        container: 'body'
      });
    },
    editable: true
  });
  myCalendar.render();
  $(".calalert").hide();
  document.getElementById('savecal').addEventListener('click', function() {
    let color=$('input[name=evcolor]:checked', '#effForm').val();
    let reqid=$("#reqid").val();
    let reqinfo=$("#reqinfo").val();
    let starttime=$("#datetimepick").val();
    let timeperiod=$("#timeperiod").val();
    $.ajax({
      type: "POST",
      url: "/calapi/add",
      data: 'evcolor='+color+'&starttime='+starttime+'&reqid='+reqid+'&reqinfo='+reqinfo+'&timeperiod='+timeperiod,
      cache: false,
      success: function (html) {
        FullCalendar.Calendar('refetchResources');
        $('#modeff').modal('hide');
        notify(html, "success");
      }
    });
  });
});