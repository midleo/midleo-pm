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
        global $priorityarr;
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
        echo '<link rel="stylesheet" type="text/css" href="/'.$website['corebase'].'assets/css/jquery-ui.min.css">';
        if ($thisarray["p1"] == "taskedit") { ?>
<link rel="stylesheet" type="text/css" href="/controller/modules/changes/assets/css/nestablemenu.css">
<?php } 
        if ($thisarray["p1"] == "timeline") { ?>
<link rel="stylesheet" type="text/css" href="/controller/modules/calendar/assets/css/fullcalendar-schedule.min.css">
<style type="text/css">.fc .fc-toolbar.fc-header-toolbar{margin-bottom:0px;border:1px solid var(--bs-gray-400);}</style>
        <?php }
        echo '</head><body class="fix-header card-no-border"><div id="main-wrapper">';
        $breadcrumb["text"] = "Change management";
        include $website['corebase'] . "public/modules/headcontent.php";
        echo '<div class="page-wrapper"><div class="container-fluid">';
        $brarr = array();
      if ($thisarray["p1"] == "tasks") {
        $tmp["chgstatus"]=gTable::get("changes","taskcurr,chgstatus"," where chgnum='".htmlspecialchars($thisarray["p2"])."'")["chgstatus"];
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
            "link"=>"javascript:void(0)",
            "nglink" => "getAlltasks('".$thisarray["p2"]."');getProgress('".$thisarray["p2"]."')",
            "icon" => "mdi-refresh",
            "active" => false,
        ));
        array_push($brarr, array(
            "title" => "Timeline",
            "link" => "/changes/timeline/".$thisarray["p2"],
            "icon" => "mdi-chart-timeline",
            "active" => false,
        ));
        array_push($brarr, array(
            "title" => "Back to changes",
            "link" => "/changes",
            "icon" => "mdi-arrow-left",
            "active" => false,
        ));
      }
      if ($thisarray["p1"] == "timeline") {
        array_push($brarr, array(
            "title" => "Edit tasks",
            "link" => "/changes/taskedit/".$thisarray["p2"],
            "icon" => "mdi-format-list-checks",
            "active" => false,
        ));
        array_push($brarr, array(
            "title" => "Back to tasks",
            "link" => "/changes/tasks/".$thisarray["p2"],
            "icon" => "mdi-arrow-left",
            "active" => false,
        ));
        array_push($brarr, array(
            "title" => "Back to changes",
            "link" => "/changes",
            "icon" => "mdi-arrow-left",
            "active" => false,
        ));
      }
      if ($thisarray["p1"] == "taskedit") {
        array_push($brarr, array(
            "title" => "Define new task",
            "link" => "#",
            "onclick" => "$('#updtask').hide();$('#newtask').show();",
            "nglink" => "task=[];",
            "modal" => true,
            "mtarget" => "#taskmodal",
            "icon" => "mdi-plus",
            "active" => false,
        ));
        array_push($brarr, array(
            "title" => "Save tasks",
            "link"=>"javascript:void(0)",
            "icon" => "mdi-content-save",
            "nglink" => "saveTasks('".$thisarray["p2"]."')",
            "active" => false,
        ));
        array_push($brarr, array(
            "title" => "Back to task list",
            "link" => "/changes/tasks/".$thisarray["p2"],
            "icon" => "mdi-arrow-left",
            "active" => false,
        ));

      } if (!($thisarray["p1"])) {
        if (sessionClass::checkAcc($acclist, "chgm")) {
            array_push($brarr, array(
                "title" => "Describe a change",
                "link" => "#",
                "modal" => true,
                "mtarget" => "#chgmodal",
                "icon" => "mdi-plus",
                "active" => false,
            ));
        } 
      }
    
      ?>
<div class="row pt-3" id="ngApp" ng-app="ngApp" ng-controller="ngCtrl">
    <div class="col-lg-2 bg-white leftsidebar">
        <?php include "public/modules/sidebar.php";?>
    </div>

    <div class="col-lg-8">
        <?php if ($thisarray["p1"] == "taskedit") { ?>
        <div class="card">
            <div class="card-body p-0">
                <input id="chgid" style="display:none;" value="<?php echo $thisarray["p2"];?>">
                <div class="p-0" ng-init="getAlltasks('<?php echo $thisarray["p2"];?>')">
                    <div class="alert alert-light mb-0" ng-hide="contentLoaded">Loading...</div>
                    <ul ui-sortable="sortTasks" ng-model="names" class="p-0 mb-0 list" id="sortable">
                        <li ng-repeat="item in names | filter:search" class="item p-0" id="{{item.uid}}">
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
                                        <td class="text-left" ng-bind-html="renderHtml(item.taskname)">
                                        </td>
                                        <td style="width:100px;">
                                            <div class="text-start d-grid gap-2 d-md-block">
                                                <button type="button"
                                                    ng-click="edittask('<?php echo $thisarray["p2"];?>',item.id)"
                                                    class="btn waves-effect btn-light btn-sm"><i
                                                        class="mdi mdi-pencil"></i></button>
                                                <button type="button"
                                                    ng-click="taskrun('<?php echo $thisarray["p2"];?>',item.nestid,item.id,'delete')"
                                                    class="btn waves-effect btn-light btn-sm"><i
                                                        class="mdi mdi-close"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="taskmodal" tabindex="-1" aria-labelledby="modlbl" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modlbl">Define the task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" id="applauto" ng-model="task.appid" class="form-control" required
                                placeholder="Project/Client" />
                            <input type="text" id="appname" name="appname" style="display:none;" />
                        </div>
                        <div class="form-group">
                            <input type="text" id="groupauto" ng-model="task.groupid" class="form-control" required
                                placeholder="Responsible group" />
                            <input type="text" id="groupname" name="groupname" style="display:none;" />
                            <input type="text" id="groupemail" name="groupemail" style="display:none;" />
                        </div>
                        <div class="form-group">
                            <input type="text" id="groupuser" ng-model="task.owner" class="form-control" required
                                placeholder="Person that will execute the task" />
                            <input type="text" id="groupuserselected" name="groupuserselected" style="display:none;" />
                        </div>
                        <div class="form-group">
                            <input type="text" id="taskname" ng-model="task.taskname" class="form-control" required placeholder="Task name" />
                        </div>
                        <div class="form-group">
                            <textarea ng-model="task.taskinfo" ui-tinymce="tinyOpts"
                                placeholder="Information about the task (will be visible only by the person that will execute it)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="waves-effect waves-light btn btn-light btn-sm"
                            style="display:none;" id="updtask" ng-click="updtask()"><i
                                class="mdi mdi-content-save"></i>&nbsp;Update</button>
                        <button type="button" id="newtask" class="waves-effect waves-light btn btn-light btn-sm"
                            ng-click="newtask('<?php echo $thisarray["p2"];?>')"><i
                                class="mdi mdi-content-save"></i>&nbsp;Create</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <?php } else if ($thisarray["p1"] == "timeline") {  
            $sql="select started,finished from changes where chgnum=?";
            $q = $pdo->prepare($sql);
            $q->execute(array(htmlspecialchars($thisarray["p2"])));
            if ($zobj = $q->fetch(PDO::FETCH_ASSOC)) {
            ?>
             
            <input id="thischange" value="<?php echo $thisarray["p2"];?>" style="display:none;">
            <input type='hidden' id='working_start' value="<?php echo date('Y-m-d\TH:i:s',strtotime($zobj["started"]));?>">
           <input type='hidden' id='working_end' value="<?php echo date('Y-m-d\TH:i:s',strtotime($zobj["finished"]));?>">
            <div id='calendar' class="card"></div>
            
            <?php } ?>
            
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
                                </tr>
                            </thead>
                            <tbody ng-init="getAlltasks('<?php echo $thisarray["p2"];?>')">
                                <tr ng-hide="contentLoaded">
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                </tr>
                                <tr id="contloaded" dir-paginate="d in names | filter:search | itemsPerPage:10"
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
                                            class="btn btn-info btn-sm tsk{{d.id}}" ng-show="d.taskbutshow && d.hasacc"
                                            ng-click="taskrun('<?php echo $thisarray["p2"];?>',d.nestid,d.id,d.taskbutname|lowercase)">{{ d.taskbutname }}
                                            </buton>
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
                                    <th class="text-center" style="width:100px;">Project</th>
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
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
                                <td class="text-center placeholder-glow"><small class="placeholder col-12"></small></td>
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
                                    <td class="text-center"><a href="/env/apps/?app={{ d.proj }}"
                                            target="_parent">{{ d.proj }}</a></td>
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
        <!-- Modal -->
        <div class="modal fade" id="chgmodal" tabindex="-1" aria-labelledby="modlbl" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modlbl">Define a change</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" id="applauto" class="form-control" required
                                placeholder="Project/Client" />
                            <input type="text" id="appname" name="appname" style="display:none;" />
                        </div>
                        <div class="form-group">
                            <input type="text" id="groupuser" class="form-control" required
                                placeholder="Change manager" />
                            <input type="text" id="groupuserselected" name="groupuserselected" style="display:none;" />
                        </div>
                        <div class="form-group">
                            <input type="text" id="chgname" class="form-control" required placeholder="Change name" />
                        </div>
                        <div class="form-group">
                            <textarea ng-model="info" ui-tinymce="tinyOpts"
                                placeholder="Information about the change"></textarea>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <select name="chgpriority" id="chgpriority" class="form-control">
                                    <?php foreach($priorityarr as $key=>$val){?><option value="<?php echo $key;?>">
                                        <?php echo $val["name"];?> - <?php echo $val["info"];?></option><?php } ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="chgdue" data-toggle="datetimepicker" data-target="#chgdue"
                                    class="form-control date-picker-unl" required placeholder="Due date" />
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" id="parentchg" class="form-control" placeholder="Change number in case you want to copy all tasks from" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="waves-effect waves-light btn btn-light btn-sm"
                            ng-click="newchg()"><i
                                class="mdi mdi-content-save"></i>&nbsp;Create</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <?php }  ?>
    </div>
    <div class="col-md-2">
        <?php if ($thisarray["p1"] != "new") { include $website['corebase'] . "public/modules/filterbar.php"; } ?>
        <?php include $website['corebase'] . "public/modules/breadcrumbin.php";?>
        <?php if ($thisarray["p1"] == "tasks") { ?>
        <div class="mt-2 p-2 bg-light br-4">
            <h4><i class="mdi mdi-progress-clock"></i>&nbsp;Progress</h4><br>
            <div class="progress" ng-init="getProgress('<?php echo $thisarray["p2"];?>')">
                <div class="progress-bar bg-info" role="progressbar" style="width: {{ chgprogress }}%;"
                    aria-valuenow="{{ chgprogress }}" aria-valuemin="0" aria-valuemax="100">{{ chgprogress }}%
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
if ($thisarray["p1"] == "timeline") { ?> 
<script type="text/javascript" src="/controller/modules/calendar/assets/js/fullcalendar-scheduler.min.js"></script>
<script type="text/javascript" src="/controller/modules/calendar/assets/js/mcalendar-scheduler.js"></script>   
<?php }
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