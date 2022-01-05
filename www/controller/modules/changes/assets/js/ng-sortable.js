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
      if (response.data != "null") { 
        //$scope.names = response.data; 
        angular.forEach(response.data, function (value, key) {
          $scope.names.push({
            text: value.taskname ,
            owner: value.owner ,
            appid: value.appid ,
            taskstatusbut: value.taskstatusbut,
            taskstatusname: value.taskstatusname,
            value: value.nestid
          });
        });
      
      } 
    });
  };
  
  $scope.sortTasks = {
    'ui-floating': true,
    stop: function(e, ui) {
      var dataToSend=window.JSON.stringify($("#sortable").sortable('serialize'));
      alert(dataToSend);
      //alert('Moved element: ' + ui.item.scope().item.value);
      
    }
  };
  
});