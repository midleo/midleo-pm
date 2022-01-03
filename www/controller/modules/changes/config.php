<?php
$modulelist["changes"]["name"] = "Changes module";
include_once "api.php";
class ClassMPM_changes
{
    public static function getPage($thisarray)
    {
        global $website;
        global $maindir;
        global $installedapp;
        global $env;
        global $page;
        global $maindir;
        $year = date("Y");
        if ($installedapp != "yes") {header("Location: /install");}
        sessionClass::page_protect(base64_encode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $err = array();
        $msg = array();
        $pdo = pdodb::connect();
        $data = sessionClass::getSessUserData();foreach ($data as $key => $val) {${$key} = $val;}
        if (!is_array($website)) {$website = json_decode($website, true);}
        include $website['corebase'] . "public/modules/css.php";
        if ($thisarray["p1"] == "taskedit") { ?>
<link rel="stylesheet" type="text/css" href="/controller/modules/changes/assets/css/nestablemenu.css">
<?php } 
        echo '</head><body class="fix-header card-no-border"><div id="main-wrapper">';
        $breadcrumb["text"] = "Change management";
        include $website['corebase'] . "public/modules/headcontent.php";
        echo '<div class="page-wrapper"><div class="container-fluid">';
        $brarr = array();
        if (sessionClass::checkAcc($acclist, "chgm")) {
          array_push($brarr, array(
              "title" => "Describe a change",
              "link" => "/changes/new",
              "icon" => "mdi-plus",
              "active" => false,
          ));
          
      } 
      if ($thisarray["p1"] == "tasks") {
        $tmp["numtasks"]=gTable::countAll("changes_tasks"," where chgnum='".htmlspecialchars($thisarray["p2"])."'");
        $tmp["curtask"]=gTable::get("changes","taskcurr,chgstatus"," where chgnum='".htmlspecialchars($thisarray["p2"])."'")["taskcurr"];
        $tmp["chgstatus"]=gTable::get("changes","taskcurr,chgstatus"," where chgnum='".htmlspecialchars($thisarray["p2"])."'")["chgstatus"];
        $percent=round((intval($tmp["curtask"]) / intval($tmp["numtasks"])) * 100);
        if($tmp["chgstatus"]==0){
            array_push($brarr, array(
                "title" => "Edit tasks",
                "link" => "/changes/taskedit/".$thisarray["p2"],
                "icon" => "mdi-format-list-checks",
                "active" => false,
            ));
        }
        array_push($brarr, array(
            "title" => "Refresh",
            "link"=>"#",
            "nglink" => "getAlltasks('".$thisarray["p2"]."')",
            "icon" => "mdi-refresh",
            "active" => false,
        ));
      }
      if ($thisarray["p1"] == "taskedit") {
        array_push($brarr, array(
            "title" => "Add new task",
            "link" => "",
            "icon" => "mdi-plus",
            "active" => false,
        ));

      }
    
      ?>
<div class="row pt-3" id="ngApp" ng-app="ngApp" ng-controller="ngCtrl">
    <div class="col-lg-2">
        <?php include "public/modules/sidebar.php";?>
    </div>

    <div class="col-lg-8">
        <?php if ($thisarray["p1"] == "new") { ?>



        <?php } else if ($thisarray["p1"] == "edit") { ?>



        <?php } else if ($thisarray["p1"] == "taskedit") { ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="p-0" ng-init="getAlltasks('<?php echo $thisarray["p2"];?>')">
                <div class="alert alert-light mb-0" ng-hide="contentLoaded">Loading...</div>
                    <ul ui-sortable="sortTasks" ng-model="names" class="p-0 mb-0 list" >
                        <li ng-repeat="item in names | filter:search | orderBy:'value'" class="item p-0" >
                            <table class="table table-vmiddle table-hover stylish-table mb-0">
                                <tbody>
                                    <tr>
                                        <td style="width:30px;"><i class="mdi mdi-menu-swap-outline"></i></td>
                                        <td class="text-center" style="width:80px;"><span
                                                class="badge badge-{{ item.taskstatusbut }}">{{ item.taskstatusname }}</span>
                                        </td>
                                        <td class="text-center" style="width:80px;"><a
                                                href="/browse/user/{{ item.owner }}">{{ item.owner }}</a></td>
                                        <td class="text-center" style="width:50px;">{{ item.appid }}</td>
                                        <td class="text-left" ng-bind-html="renderHtml(item.text)">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php } else if ($thisarray["p1"] == "tasks") { ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-vmiddle table-hover stylish-table mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:80px">Status</th>
                                    <th class="text-center" style="width:120px;">Owner</th>
                                    <th class="text-center" style="width:80px;">Application</th>

                                    <th class="text-left">Task</th>
                                    <th class="text-center" style="width:80px">Info</th>
                                    <th class="text-center" style="width:80px;">Action</th>
                                    <th class="text-center" style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody ng-init="getAlltasks('<?php echo $thisarray["p2"];?>')">
                                <tr ng-hide="contentLoaded">
                                    <td colspan="8" style="text-align:center;font-size:1.1em;"><i
                                            class="mdi mdi-loading iconspin"></i>&nbsp;Loading...</td>
                                </tr>
                                <tr id="contloaded"
                                    dir-paginate="d in names | filter:search | orderBy:'nestid' | itemsPerPage:10"
                                    ng-class="d.reqactive==1 ? 'hide active' : 'hide none'" pagination-id="prodx">
                                    <td class="text-center {{ d.taskfinished }}"><span
                                            class="badge badge-{{ d.taskstatusbut }}">{{ d.taskstatusname }}</span></td>
                                    <td class="text-center {{ d.taskfinished }}"><a
                                            href="/browse/user/{{ d.owner }}">{{ d.owner }}</a></td>
                                    <td class="text-center {{ d.taskfinished }}">{{ d.appid }}</td>
                                    <td class="text-left {{ d.taskfinished }}" ng-bind-html="renderHtml(d.taskname)">
                                    </td>
                                    <td class="text-center {{ d.taskfinished }}"><button class="btn btn-light btn-sm"
                                            ng-show="d.hasacc" ng-click="showmod(d.taskinfo,d.id)">Show</button></td>
                                    <td class="text-center {{ d.taskfinished }}"><button
                                            class="bnt btn-info btn-sm tsk{{d.nestid}}"
                                            ng-show="d.taskbutshow && d.hasacc"
                                            ng-click="taskrun('<?php echo $thisarray["p2"];?>',d.nestid,d.taskbutname|lowercase)">{{ d.taskbutname }}
                                            </buton>
                                    </td>
                                    <td class="text-center {{ d.taskfinished }}"><a href="" class="bnt btn-light btn-sm"
                                            ng-show="d.taskdel && d.hasacc"
                                            ng-click="taskrun('<?php echo $thisarray["p2"];?>',d.nestid,'delete')"><i
                                                class="mdi mdi-close"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <dir-pagination-controls pagination-id="prodx" boundary-links="true"
                            on-page-change="pageChangeHandler(newPageNumber)"
                            template-url="/<?php echo $website['corebase'];?>assets/templ/pagination.tpl.html">
                        </dir-pagination-controls>
                        <!-- Modal -->
                        <div class="modal fade" id="taskmodal" tabindex="-1" aria-labelledby="modlbl"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modlbl">Task info</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <textarea ng-model="info" ui-tinymce="tinyOpts"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="waves-effect waves-light btn btn-light btn-sm"
                                            ng-click="updtask(taskid,'<?php echo $thisarray["p2"];?>')"><i
                                                class="mdi mdi-content-save"></i>&nbsp;Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal -->
                    </div>
                </div>
            </div>
        </div>
        <?php } else { ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-vmiddle table-hover stylish-table mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:40px;"></th>
                                    <th class="text-center" style="width:40px"></th>
                                    <th class="text-center" style="width:120px;">Owner</th>
                                    <th class="text-left">Name</th>
                                    <th class="text-center" style="width:80px">Priority</th>
                                    <th class="text-center" style="width:80px">Status</th>
                                    <th class="text-center" style="width:80px;">Created</th>
                                    <th class="text-center" style="width:80px;">Due date</th>
                                </tr>
                            </thead>
                            <tbody ng-init="getAllchanges()">
                                <tr ng-hide="contentLoaded">
                                    <td colspan="8" style="text-align:center;font-size:1.1em;"><i
                                            class="mdi mdi-loading iconspin"></i>&nbsp;Loading...</td>
                                </tr>
                                <tr id="contloaded"
                                    dir-paginate="d in names | filter:search | orderBy:'deadline' | orderBy:'-priorityval' | itemsPerPage:10"
                                    pagination-id="prodx">
                                    <td class="text-center">
                                        <a href="/changes/edit/{{ d.chgnum }}" class="bnt btn-light btn-sm"><i
                                                class="mdi mdi-pencil"></i></a>
                                    </td>
                                    <td class="text-center"><a href="/changes/tasks/{{ d.chgnum }}"
                                            target="_parent">{{ d.chgnum }}</a></td>
                                    <td class="text-center"><a href="/browse/user/{{ d.owner }}">{{ d.owner }}</a></td>
                                    <td class="text-left">
                                        {{ d.name | limitTo:2*textlimit }}{{d.name.length > 2*textlimit ? '...' : ''}}
                                    </td>
                                    <td class="text-center"><span class="badge badge-{{ d.priority.butcolor }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ d.priority.info }}">{{ d.priority.name }}</span></td>
                                    <td class="text-center"><span
                                            class="badge badge-{{ d.statusbut }}">{{ d.statusn }}</span></td>
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
        <?php }  ?>
    </div>
    <div class="col-md-2">
        <?php if ($thisarray["p1"] != "new") { include $website['corebase'] . "public/modules/filterbar.php"; } ?>
        <?php include $website['corebase'] . "public/modules/breadcrumbin.php";?>
        <?php if ($thisarray["p1"] == "tasks") { ?>
        <div class="mt-2 p-2 bg-light br-4">
            <h4><i class="mdi mdi-progress-clock"></i>&nbsp;Progress</h4><br>
            <div class="progress">
                <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $percent;?>%;"
                    aria-valuenow="<?php echo $percent;?>" aria-valuemin="0" aria-valuemax="100"><?php echo $percent;?>%
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<?php
include $website['corebase'] . "public/modules/footer.php";
echo "</div></div>";
include $website['corebase'] . "public/modules/js.php"; 
if ($thisarray["p1"] == "taskedit") { ?>
<script type="text/javascript" src="/controller/modules/changes/assets/js/sortable.js"></script>
<script type="text/javascript" src="/controller/modules/changes/assets/js/ng-sortable.js"></script>
<?php } else { ?>
<script src="/<?php echo $website['corebase']; ?>assets/js/dirPagination.js" type="text/javascript"></script>
<script type="text/javascript" src="/controller/modules/changes/assets/js/ng-controller.js"></script>
<?php } ?>
<script type="text/javascript" src="/<?php echo $website['corebase']; ?>assets/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="/<?php echo $website['corebase']; ?>assets/js/tinymce/mentions.min.js"></script>
<script type="text/javascript" src="/<?php echo $website['corebase']; ?>assets/js/tinymce/angular.tinymce.min.js">
</script>
<script src="/<?php echo $website['corebase']; ?>assets/js/tagsinput.min.js" type="text/javascript"></script>
<?php include $website['corebase'] . "public/modules/template_end.php";
echo '</body></html>';
    }
}