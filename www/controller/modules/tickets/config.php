<?php
$modulelist["requests"]["name"]="Request system";
include "functions.php";
include_once "api.php";
class Class_ticketinfo{
  public static function getPage($thisarray){
    global $installedapp;
    global $website;
    global $maindir;
    global $page;
    global $typeq;
    global $env;
    global $typereq;
    global $modulelist;
    global $priorityarr;
    include "pages/info.php";
  }
}
class Class_reqsearch{
  public static function getPage($thisarray){
    global $installedapp;
    global $website;
    global $maindir;
    global $page;
    global $typeq;
    global $typereq;
    global $modulelist;
    include "pages/search.php";
  }
}
class Class_reqchat{
  public static function getPage($thisarray){
    global $installedapp;
    global $website;
    global $maindir;
    global $page;
    global $typeq;
    global $typereq;
    global $modulelist;
    include "pages/chat.php";
  }
}
class Class_reqcomm{
  public static function getPage($thisarray){
    global $installedapp;
    global $website;
    global $maindir;
    global $page;
    global $typeq;
    global $typereq;
    global $modulelist;
    include "pages/comments.php";
  }
}
class Class_reqtasks{
  public static function getPage($thisarray){
    global $installedapp;
    global $website;
    global $maindir;
    global $page;
    global $typeq;
    global $typereq;
    global $modulelist;
    include "pages/subtasks.php";
  }
}
class Class_tickets{
  public static function getPage($thisarray){
    global $installedapp;
    global $website;
    global $maindir;
    global $page;
    global $env;
    global $typeq;
    global $typereq;
    global $modulelist;
    if($installedapp!="yes"){ header("Location: /install"); }
    sessionClass::page_protect(base64_encode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));
    $err = array();
    $msg = array();
    $pdo = pdodb::connect();
    $data=sessionClass::getSessUserData(); foreach($data as $key=>$val){  ${$key}=$val; } 
    if (!sessionClass::checkAcc($acclist, "requests")) { header("Location: /cp/?"); }
    include $website['corebase']."public/modules/css.php";
    if (!empty($env)) {    $menudataenv = json_decode($env, true);   } else {    $menudataenv = array();   }
   if(isset($_POST['updreq'])){ $q=requestFunctions::updreq($wid,$_SESSION['user']);$err=$q["err"];  $msg=$q["msg"]; }
   if(isset($_POST['assign'])){  $q=requestFunctions::assign($wid,htmlspecialchars($_POST["reqid"])); $err=$q["err"];  $msg=$q["msg"];  }
   if(isset($_POST['savereqdata'])){ $q=requestFunctions::savereqdata($thisarray["p3"],$thisarray["p2"],$_SESSION["user"],$wid); $err=$q["err"];  $msg=$q["msg"];  }
   if($thisarray["p1"]=="type"){ echo '<link rel="stylesheet" type="text/css" href="/'.$website['corebase'].'assets/css/jquery-ui.min.css">';   }
   if($thisarray["p1"]=="log" || $thisarray["p1"]=="files"){ ?>
<link rel="stylesheet" type="text/css"
    href="/<?php echo $website['corebase'];?>assets/js/datatables/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css"
    href="/<?php echo $website['corebase'];?>assets/js/datatables/responsive.dataTables.min.css">
<?php }  
   echo '</head><body class="fix-header card-no-border"><div id="main-wrapper">';
   if(!empty($thisarray["p1"])){  $breadcrumb["link"]="/tickets"; $breadcrumb["text"]="Requests"; } else {
    if(!empty($thisarray["p1"])){
      if($thisarray["p1"]=="type"){ $breadcrumb["link"]="/ticketinfo/".$thisarray["p3"]; $breadcrumb["text"]=$thisarray["p3"]; } 
      else if($thisarray["p1"]=="log" || $thisarray["p1"]=="files"){ $breadcrumb["link"]="/ticketinfo/".$thisarray["p2"]; $breadcrumb["text"]=$thisarray["p2"]; } 
      else { $breadcrumb["link"]="/ticketinfo/".$thisarray["p1"]; $breadcrumb["text"]=$thisarray["p1"]; }
    } else {  $breadcrumb["link"]="/tickets"; $breadcrumb["text"]="Requests"; }
  }
    include $website['corebase']."public/modules/headcontent.php";
    echo '<div class="page-wrapper"><div class="container-fluid">';
    if (sessionClass::checkAcc($acclist, "smanagementadm")) { 
      $brarr=array(
        array(
          "title"=>"Service catalog",
          "link"=>"/smanagement",
          "icon"=>"mdi-format-list-checks",
          "active"=>($page=="smanagement")?"active":"",
        )
      );
      if (sessionClass::checkAcc($acclist, "requests")) {
        array_push($brarr,array(
          "title"=>"Tickets",
          "link"=>"/tickets",
          "icon"=>"mdi-swap-horizontal-bold",
          "active"=>($page=="tickets")?"active":"",
        ));
      }
      if (sessionClass::checkAcc($acclist, "pjm,pja,pjv")) {
        array_push($brarr,array(
            "title"=>"Projects",
            "link"=>"/projects",
            "icon"=>"mdi-bulletin-board",
            "active"=>($page=="projects")?"active":"",
          ));
        array_push($brarr,array(
            "title"=>"Project Templates",
            "link"=>"/pjtemplates",
            "icon"=>"mdi-cards",
            "active"=>($page=="pjtemplates")?"active":"",
        ));
      }
      array_push($brarr,array(
        "title"=>"Service types",
        "link"=>"/smanagement//types",
        "icon"=>"mdi-head-cog-outline",
        "active"=>($thisarray["p2"]=="types")?"active":"",
      ));
    }
    ?>
<div class="row pt-3">
    <div class="col-lg-2">
        <?php include "public/modules/sidebar.php"; ?>
    </div>
    <div class="col-lg-8">
        <?php if(!empty($thisarray["p1"])){ include "pages/".$thisarray["p1"].".php";} else {  ?>



        <div id="ngApp" ng-app="ngApp" ng-controller="ngCtrl">
        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <div class="row">
                                                <div class="col-md-6 col-xl-3">
                                                    <div class="py-1">
                                                        <i class="mdi mdi-tag-outline mdi-24px"></i>
                                                        <h3>25563</h3>
                                                        <p class="text-uppercase mb-1 font-13 fw-medium">Total tickets</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-xl-3">
                                                    <div class="py-1">
                                                        <i class="mdi mdi-account-clock-outline mdi-24px"></i>
                                                        <h3 class="text-warning">6952</h3>
                                                        <p class="text-uppercase mb-1 font-13 fw-medium">Pending Tickets</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-xl-3">
                                                    <div class="py-1">
                                                        <i class="mdi mdi-cloud-check mdi-24px"></i>
                                                        <h3 class="text-success">18361</h3>
                                                        <p class="text-uppercase mb-1 font-13 fw-medium">Closed Tickets</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-xl-3">
                                                    <div class="py-1">
                                                        <i class="mdi mdi-archive-cancel-outline mdi-24px"></i>
                                                        <h3 class="text-danger">250</h3>
                                                        <p class="text-uppercase mb-1 font-13 fw-medium">Deleted Tickets</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            <div class="row">
                <div class="col-md-4 position-relative">
                    <input type="text" ng-model="search" class="form-control topsearch" placeholder="Find a request">
                    <span class="searchicon"><i class="mdi mdi-magnify"></i>
                </div>
                <div class="col-md-8 text-end">
                    <button type="button" ng-click="getAllreq('','','own')" class="btn btn-light">Mine</button>
                    <button type="button" ng-click="getAllreq('<?php echo $ugr;?>','<?php echo $thisarray["p2"];?>')"
                        class="btn btn-light">All</button>
                    <a class="btn btn-light waves-effect" href="/reqsearch">
                        <i class="mdi mdi-magnify"></i>&nbsp;Advanced search
                    </a>
                </div>
            </div><br>
            
            <div class="card">
                <div class="card-body p-0">

                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-vmiddle table-hover stylish-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:40px"></th>
                                        <th class="text-center" style="width:80px;"><i class="mdi mdi-cards"></i></th>

                                        <th class="text-center" style="width:120px;">Requested by</th>
                                        <th class="text-center">Subject</th>
                                        <th class="text-center" style="width:80px">Priority</th>
                                        <th class="text-center" style="width:80px">Status</th>
                                        <th class="text-center" style="width:80px;">Assignee</th>
                                        <th class="text-center" style="width:80px;">Created</th>
                                        <th class="text-center" style="width:80px;">Due date</th>
                                    </tr>
                                </thead>
                                <tbody ng-init="getAllreq('<?php echo $ugr;?>','<?php echo $thisarray["p2"];?>')">
                                    <tr ng-hide="contentLoaded">
                                        <td colspan="10" style="text-align:center;font-size:1.1em;"><i
                                                class="mdi mdi-loading iconspin"></i>&nbsp;Loading...</td>
                                    </tr>
                                    <tr id="contloaded"
                                        dir-paginate="d in names | filter:search | orderBy:'deadline' | orderBy:'status':reverse| orderBy:'-priorityval' | itemsPerPage:10"
                                        ng-class="d.reqactive==1 ? 'hide active' : 'hide none'" pagination-id="prodx">
                                        <td class="text-center"><i class="{{ d.statusicon }}"></i></td>
                                        <td class="text-center"><a href="/ticketinfo/{{ d.sname }}"
                                                target="_parent">{{ d.sname }}</a></td>
                                        <!--<td class="text-center"><a href="/view/{{ d.sname }}" target="_parent">{{ d.name | limitTo:2*textlimit }}{{d.name.length > 2*textlimit ? '...' : ''}}</a></td>-->
                                        <td class="text-center"><a
                                                href="/browse/user/{{ d.requser }}">{{ d.requser }}</a></td>
                                        <td class="text-center">
                                            {{ d.name | limitTo:2*textlimit }}{{d.name.length > 2*textlimit ? '...' : ''}}
                                        </td>
                                        <td class="text-center"><span class="badge badge-{{ d.priority.butcolor }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top"
                                                title="{{ d.priority.info }}">{{ d.priority.name }}</span></td>
                                        <td class="text-center"><span
                                                class="badge badge-{{ d.statusinfo }}">{{ d.statusinfotxt }}</span></td>
                                        <td class="text-center"><a href="/browse/user/{{ d.assigned }}"
                                                ng-show="d.assigned!='done' && d.assigned!='canceled' && d.assigned!='Not yet'">{{ d.assigned }}</a>
                                            <span
                                                ng-hide="d.assigned!='done' && d.assigned!='canceled' && d.assigned!='Not yet'">
                                                {{ d.assigned }}</span>
                                        </td>
                                        <td class="text-center">{{ d.created }}</td>
                                        <td class="text-center">{{ d.deadline }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <dir-pagination-controls pagination-id="prodx" boundary-links="true"
                                on-page-change="pageChangeHandler(newPageNumber)"
                                template-url="/<?php echo $website['corebase'];?>assets/templ/pagination.tpl.html">
                            </dir-pagination-controls>




                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    <div class="col-md-2">
        <?php include $website['corebase']."public/modules/breadcrumbin.php"; ?>
        </div>
        </div>

        <?php }
  echo "</div>";
     include $website['corebase']."public/modules/footer.php";
     echo "</div></div>";
     include $website['corebase']."public/modules/js.php"; 
     if($thisarray["p1"]=="files" || $thisarray["p1"]=="type"){  echo '<script src="/'.$website['corebase'].'assets/js/tagsinput.min.js" type="text/javascript"></script>'; }
    if($thisarray["p1"]=="log" || $thisarray["p1"]=="files"){?>
        <script src="/<?php echo $website['corebase'];?>assets/js/datatables/jquery.dataTables.min.js"></script>
        <script src="/<?php echo $website['corebase'];?>assets/js/datatables/dataTables.responsive.min.js"></script>
        <script src="/<?php echo $website['corebase'];?>assets/js/datatables/dataTables.buttons.min.js"></script>
        <script src="/<?php echo $website['corebase'];?>assets/js/datatables/buttons.flash.min.js"></script>
        <script src="/<?php echo $website['corebase'];?>assets/js/datatables/jszip.min.js"></script>
        <script src="/<?php echo $website['corebase'];?>assets/js/datatables/pdfmake.min.js"></script>
        <script src="/<?php echo $website['corebase'];?>assets/js/datatables/vfs_fonts.js"></script>
        <script src="/<?php echo $website['corebase'];?>assets/js/datatables/buttons.html5.min.js"></script>
        <script src="/<?php echo $website['corebase'];?>assets/js/datatables/buttons.print.min.js"></script>
        <script>
        let dtable = $('#data-table').DataTable({
            "oLanguage": {
                "sSearch": ""
            },
            dom: 'Bfrtip',
            buttons: ['csv', 'excel', 'pdf', 'print']
        });
        $('.dtfilter').keyup(function() {
            dtable.search($(this).val()).draw();
        });
        </script>

        <?php } else {?>
        <script src="/<?php echo $website['corebase'];?>assets/js/dirPagination.js"></script>
        <script type="text/javascript" src="/controller/modules/tickets/assets/js/ng-controller.js"></script>
        <?php } ?>
        <?php if($thisarray["p1"]=="mq"){?>
        <script src="/<?php echo $website['corebase'];?>assets/js/alasql.min.js"></script>
        <script src="/<?php echo $website['corebase'];?>assets/js/xlsx.core.min.js"></script>
        <?php }
    include $website['corebase']."public/modules/template_end.php";
    echo '</body></html>';
  }
}