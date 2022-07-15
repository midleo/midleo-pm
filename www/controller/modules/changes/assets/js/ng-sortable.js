var app = angular.module('ngApp', ['ui.sortable']);
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
  $scope.newtask = function(thisdata){


  };
  $scope.edittask = function(thischg,thisid){

    
  };
});