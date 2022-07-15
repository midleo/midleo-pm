var app = angular.module('ngApp', ['ui.sortable','ui.tinymce']);
app.config(['$compileProvider',
  function ($compileProvider) {
    $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|tel|file|blob):/);
  }]);
app.controller('ngCtrl', function ($scope, $http, $sce) {
  'use strict';
  $scope.contentLoaded = false;
  $scope.contentpjLoaded = false;
  $scope.textlimit = 20;
  $scope.parJson = function (json) {
    if (json) { return JSON.parse(json); }
  };
  $scope.renderHtml = function (htmlCode) {
    return $sce.trustAsHtml(htmlCode);
  };
  $scope.tinyOpts = {
    plugins: 'link image code',
    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | code',
    height : "280",
    paste_data_images: true,
    forced_root_block: false,
    force_br_newlines: true
  };
  $scope.getAlltasks = function (chgid) {
    if ($('#contloaded')[0]) { angular.element(document.querySelector('#contloaded')).removeClass('hide'); }
    $scope.names = [];
    $http({
      method: 'POST',
      data: { 'chgid': chgid },
      url: '/chgapi/tasks'
    }).then(function successCallback(response) {
      $scope.contentLoaded = true;
      if (response.data != "null") {  $scope.names = response.data;  } 
    });
  };
  $scope.sortTasks = {
    'ui-floating': true,
//    stop: function(e, ui) {      
//      $scope.getAlltasks(chgid);
//    }
  };
  $scope.taskrun = function(thischg,taskid,thisid,thiscase){
    $(".tsk" + thisid).html('<i class="mdi mdi-loading iconspin"></i>');
    $(".tsk" + thisid).prop("disabled", true);
    if(thiscase=="delete"){
      Swal.fire({
        title: 'Delete this task?',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        customClass: {
          confirmButton: 'btn btn-danger btn-sm',
          cancelButton: 'btn btn-light btn-sm',
        }
      }).then((result) => {
        if (result.value) {
          $http({
            method: 'POST',
            data: { 'taskid': taskid, 'thisid': thisid, 'case': thiscase, 'chg': thischg },
            url: '/chgapi/taskdo'
          }).then(function successCallback(response) {
            notify("Success","success");
            $scope.getAlltasks(thischg);
          });
        }
      })
    } else {
      $http({
        method: 'POST',
        data: { 'taskid': taskid, 'thisid': thisid, 'case': thiscase, 'chg': thischg },
        url: '/chgapi/taskdo'
      }).then(function successCallback(response) {
        notify("Success","success");
        $scope.getAlltasks(thischg);
      });
    }
  };
  $scope.saveTasks = function (chgid){
    let dataToSend=window.JSON.stringify($("#sortable").sortable("toArray"));
    $http({
      method: 'POST',
      data: { 'chgid': chgid, 'object': dataToSend },
      url: '/chgapi/updtasks'
    }).then(function successCallback(response) {
      notify("Data updated","success");
    });
  };
  $scope.newtask = function(thischg){
    if($("#taskname").val()){
      let tasknew={};
      tasknew.owner=$("#groupuserselected").val().split("#")[1];
      tasknew.appid=$("#appname").val();
      tasknew.groupid=$("#groupname").val();
      tasknew.taskname=$("#taskname").val();
      tasknew.taskinfo=$scope.info;
 //     tasknew.nestid=9999;
 //     tasknew.id=9999;
      tasknew.taskstatus=0;
      tasknew.taskstatusname="New";
      tasknew.taskstatusbut="secondary";
      $http({
        method: 'POST',
        data: { 'chgid': thischg, 'task': tasknew },
        url: '/chgapi/addtask'
      }).then(function successCallback(response) {
        $scope.names.push(tasknew);
        $('#taskmodal').modal('hide');
        notify("Task added","success");
      });
    } else {
      notify("Please fill all the fields","danger");
    }
  };
  $scope.edittask = function(thischg,thisid){

    
  };
});