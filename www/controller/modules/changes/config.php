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
              "active" => ($page == "changes") ? "active" : "",
          ));
          
      } 
      array_push($brarr, array(
        "title" => "View your tasks",
        "link" => "/changes/tasks",
        "icon" => "mdi-format-list-checks",
        "active" => ($page == "changes" && empty($thisarray["p1"])) ? "active" : "",
    ));
    
      ?>
<div class="row pt-3" id="ngApp" ng-app="ngApp" ng-controller="ngCtrl">
    <div class="col-lg-2">
        <?php include "public/modules/sidebar.php";?>
    </div>
    <?php if ($thisarray["p1"] != "tasks") { ?>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-vmiddle table-hover stylish-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width:40px"></th>
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
                                    <td colspan="7" style="text-align:center;font-size:1.1em;"><i
                                            class="mdi mdi-loading iconspin"></i>&nbsp;Loading...</td>
                                </tr>
                                <tr id="contloaded"
                                    dir-paginate="d in names | filter:search | orderBy:'deadline' | orderBy:'-priorityval' | itemsPerPage:10"
                                     pagination-id="prodx">
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

    </div>
    <div class="col-md-2">
        <?php if ($thisarray["p1"] != "new") { include $website['corebase'] . "public/modules/filterbar.php"; } ?>
        <?php include $website['corebase'] . "public/modules/breadcrumbin.php";?>
    </div>
    <?php } else { ?>
    <div class="col-lg-10">
    <div class="card">
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-vmiddle table-hover stylish-table mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:50px;">Number</th>
                                    <th class="text-center" style="width:120px;">Owner</th>
                                    <th class="text-center" style="width:80px;">Application</th>
                                    <th class="text-center" style="width:80px">Status</th>
                                    <th class="text-left">Task</th>
                                    <th class="text-center" style="width:80px">Info</th>
                                    <th class="text-center" style="width:80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody ng-init="getAlltasks('<?php echo $thisarray["p2"];?>')">
                                <tr ng-hide="contentLoaded">
                                    <td colspan="7" style="text-align:center;font-size:1.1em;"><i
                                            class="mdi mdi-loading iconspin"></i>&nbsp;Loading...</td>
                                </tr>
                                <tr id="contloaded"
                                    dir-paginate="d in names | filter:search | orderBy:'id' | itemsPerPage:10"
                                    ng-class="d.reqactive==1 ? 'hide active' : 'hide none'" pagination-id="prodx">
                                    <td class="text-center">{{ d.id }}</td>
                                    <td class="text-center"><a href="/browse/user/{{ d.owner }}">{{ d.owner }}</a></td>
                                    <td class="text-center">{{ d.appid }}</td>
                                    <td class="text-center"><span class="badge badge-{{ d.taskstatuscol }}">{{ d.taskstatus }}</span></td>
                                    <td class="text-left" ng-bind-html="renderHtml(d.taskname)"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center">{{ d.taskbutname }}</td>
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
    <?php } ?>
</div>

<?php
include $website['corebase'] . "public/modules/footer.php";
echo "</div></div>";
include $website['corebase'] . "public/modules/js.php"; ?>
<script type="text/javascript" src="/<?php echo $website['corebase']; ?>assets/js/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="/<?php echo $website['corebase']; ?>assets/js/tinymce/mentions.min.js"></script>
<script type="text/javascript" src="/<?php echo $website['corebase']; ?>assets/js/tinymce/angular.tinymce.min.js">
</script>
<script src="/<?php echo $website['corebase']; ?>assets/js/dirPagination.js" type="text/javascript"></script>
<script type="text/javascript" src="/controller/modules/changes/assets/js/ng-controller.js"></script>
<script src="/<?php echo $website['corebase']; ?>assets/js/tagsinput.min.js" type="text/javascript"></script>
<?php include $website['corebase'] . "public/modules/template_end.php";
echo '</body></html>';
    }
}