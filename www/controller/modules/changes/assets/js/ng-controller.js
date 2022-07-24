var app = angular.module('ngApp', ['angularUtils.directives.dirPagination','ui.tinymce']);
app.config(['$compileProvider',
  function ($compileProvider) {
    $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|tel|file|blob):/);
  }]);
app.controller('ngCtrl', function ($scope, $http, $location, $window, $sce, $anchorScroll) {
  $scope.selectedid = [];
  $scope.chgprogress=0;
  $scope.contentLoaded = false;
  $scope.contentpjLoaded = false;
  $scope.textlimit = 20;
  $scope.parJson = function (json) {
    if (json) { return JSON.parse(json); }
  };
  $scope.renderHtml = function (htmlCode) {
    return $sce.trustAsHtml(htmlCode);
  };
  $scope.decode64 = function (str) {
    return atob(str);
  };
  $scope.tinyOpts = {
    plugins: 'link image code',
    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | code',
    height : "280",
    paste_data_images: true,
    forced_root_block: false,
    force_br_newlines: true
  };
  $scope.exportData = function (what) { alasql('SELECT * INTO XLSX("MidleoData_' + what + '.xlsx",{sheetid:"' + what + '",headers:true}) FROM ?', [$scope.names]); };
  $scope.redirect = function (url, refresh) {
    if (refresh || $scope.$$phase) {
      $window.location.href = url;
    } else {
      $location.path(url);
      $scope.$apply();
    }
  };
  $scope.getAllchanges = function () {
    if ($('#contloaded')[0]) { angular.element(document.querySelector('#contloaded')).removeClass('hide'); }
    $http({
      method: 'POST',
      data: {  },
      url: '/chgapi/list'
    }).then(function successCallback(response) {
      $scope.contentLoaded = true;
      if (response.data != "null") { $scope.names = response.data; } else { $scope.names = {}; }
    });
  };
  $scope.getAlltasks = function (chgid) {
    if ($('#contloaded')[0]) { angular.element(document.querySelector('#contloaded')).removeClass('hide'); }
    $http({
      method: 'POST',
      data: { 'chgid': chgid },
      url: '/chgapi/tasks'
    }).then(function successCallback(response) {
      $scope.contentLoaded = true;
      if (response.data != "null") { $scope.names = response.data; } else { $scope.names = {}; }
    });
  };
  $scope.taskrun = function(thischg,taskid,thisid,thiscase){
    $(".tsk" + thisid).html('<i class="mdi mdi-loading iconspin"></i>');
    $(".tsk" + thisid).prop("disabled", true);
    let confirm=false;
    if(thiscase=="delete"){
      Swal.fire({
        title: 'Delete this object?',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        customClass: {
          confirmButton: 'btn btn-danger btn-sm',
          cancelButton: 'btn btn-light btn-sm',
        }
      }).then((result) => {
        if (result.value) {
          confirm=true;
        }
      })
    } else {
      confirm=true;
    }
    if(confirm){
      $http({
        method: 'POST',
        data: { 'taskid': taskid, 'thisid': thisid, 'case': thiscase, 'chg': thischg },
        url: '/chgapi/taskdo'
      }).then(function successCallback(response) {
        notify("Success","success");
        $scope.getAlltasks(thischg);
        if(thiscase=="finish"){
          $scope.getProgress(thischg);
        }
      });
    }
  };
  $scope.showmod = function(thisinfo,taskid){
    $scope.info=thisinfo;
    $scope.taskid=taskid;
    $('#taskmodal').modal('show');
  };
  $scope.getProgress = function(thischg){
    $http({
      method: 'POST',
      data: { 'chgid': thischg },
      url: '/chgapi/getprogress'
    }).then(function successCallback(response) {
      if (response.data != "null") { $scope.chgprogress = response.data; } else { $scope.chgprogress = 0; }
    });
  };
  $scope.newchg = function(){
    if($("#chgname").val()){
      let chgnew={};
      chgnew.owner=$("#groupuserselected").val().split("#")[1];
      chgnew.proj=$("#appname").val();
      chgnew.chgname=$("#chgname").val();
      chgnew.info=$scope.info;
      chgnew.deadline=$("#chgdue").val();
      chgnew.priority=$("#chgpriority").val();
      chgnew.parentchg=$("#parentchg").val();
      $http({
        method: 'POST',
        data: { 'change': chgnew },
        url: '/chgapi/addchange'
      }).then(function successCallback(response) {
        $scope.getAllchanges();
        $scope.info="";
        $("#groupuserselected").val("");
        $("#groupuser").val("");
        $("#appname").val("");
        $("#chgname").val("");
        $("#chgdue").val("");
        $("#chgpriority").val("");
        $("#applauto").val("");
        $("#parentchg").val("");
        $('#chgmodal').modal('hide');
        notify("Change added","success");
      });
    } else {
      notify("Please fill all the fields","danger");
    }
  };
});