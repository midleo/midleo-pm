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
    initialView: 'timeGridWeek',
    navLinks: true,
    locale: 'en',
    height: 'auto',
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
    slotMinTime: working_start,
    slotMaxTime: working_end,
    businessHours: {
      daysOfWeek: [1, 2, 3, 4, 5],
      startTime: working_start,
      endTime: working_end,
    },
    dayMaxEvents: true,
    weekends: false,
    events: {
      url: "/calapi/calendar/" + user + "/",
      method: 'POST',
      cache: false
    },
    eventClick: function (arg) { 
      var start = new Date(arg.event.start);
      var end = new Date(arg.event.end);
      $('#datetimepick').val(moment(start).format('YYYY-MM-DD HH:mm'));
      $('#datetimepickto').val(moment(end).format('YYYY-MM-DD HH:mm'));
      $('#clientauto').val(arg.event.extendedProps.clientname);
      $('#clientphone').val(arg.event.extendedProps.clientphone);
      $('#clientemail').val(arg.event.extendedProps.clientemail);
      $('#event_name').val(arg.event.extendedProps.description);
      $('#event').val(arg.event.extendedProps.event);
      $('#eventid').val(arg.event.id);
      $("#r" + arg.event.borderColor.replace('#','')).prop('checked', true);
      $("#butdelev").show();
      $("#modeff").modal('show');
    },
    editable: true
  });
  myCalendar.render();
  $(".calalert").hide();
  document.getElementById('butdelev').addEventListener('click', function() {
    let evid=$('#eventid').val();
    var dataString = 'event_id=' + evid;
    $.ajax({
      type: "POST",
      url: "/calapi/calendar/" + user + "/delete",
      data: dataString,
      success: function (html) { 
        notify(html, 'danger');   
        var event = myCalendar.getEventById(evid);
        event.remove();
        myCalendar.refetchEvents();
        $("#modeff").modal('hide');
      }
    });
  });
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