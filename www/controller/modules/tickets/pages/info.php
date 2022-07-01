<?php
sessionClass::page_protect(base64_encode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
$pdo = pdodb::connect();
$msg = array();
$data = sessionClass::getSessUserData();foreach ($data as $key => $val) {${$key} = $val;}

if (isset($_POST['assign'])) {$q = requestFunctions::assign(htmlspecialchars($_POST['wfstep']), $thisarray["p1"]);
    $err = $q["err"];
    $msg = $q["msg"];}
if (isset($_POST['addchtask'])) {$q = requestFunctions::addchtask($thisarray["p1"], $usname);
    $err = $q["err"];
    $msg = $q["msg"];}
if (isset($_POST['addcomm'])) {$q = requestFunctions::addcomm(htmlspecialchars($_POST['wfstep']), $_POST['requpdinfo'], $thisarray["p1"], $usname);
    $err = $q["err"];
    $msg = $q["msg"];}
if (isset($_POST['sendback'])) {$q = requestFunctions::sendback($thisarray["p1"], htmlspecialchars($_POST['requser']));
    $err = $q["err"];
    $msg = $q["msg"];}

$q = gTable::read("requests", "*", " where sname='" . $thisarray["p1"] . "'");
$zobj = $q->fetch(PDO::FETCH_ASSOC);
$q = gTable::read("requests_deployments", "deployedin,reqid", " where reqid='" . $thisarray["p1"] . "'");
$zobjin = $q->fetch(PDO::FETCH_ASSOC);
$deployedin = $zobjin['deployedin'];
$deplinreq = $zobjin['deployedin'];

if (!empty($zobj["wid"])) {
    $sql = "select haveappr,haveconf," . (DBTYPE == 'oracle' ? "to_char(wdata) as wdata" : "wdata") . "," . (DBTYPE == 'oracle' ? "to_char(wgroups) as wgroups" : "wgroups") . " from config_workflows where wid=?";
    $qin = $pdo->prepare($sql);
    $qin->execute(array($zobj["wid"]));
    if ($zobjin = $qin->fetch(PDO::FETCH_ASSOC)) {
        $wfdata = !empty($zobjin["wdata"]) ? json_decode($zobjin["wdata"], true) : json_decode("[{}]", true);
        $wfdatalaststep = end(array_keys($wfdata["nodes"]));
        $wfdatagroups = !empty($zobjin["wgroups"]) ? json_decode($zobjin["wgroups"], true) : json_decode("[{}]", true);
        $wfdataha = $zobjin["haveappr"];
        $wfdatahc = $zobjin["haveconf"];
    }
} else {
    $wfdata = json_decode("[{}]", true);
}

if ($wfdataha != 1) {$zobj['projapproved'] = 1;}
if ($wfdatahc != 1) {$zobj['projconfirmed'] = 1;}

if (!empty($env)) {$menudataenv = json_decode($env, true);} else { $menudataenv = array();}

if (isset($_POST['sendnext'])) {$q = requestFunctions::sendnext(htmlspecialchars($_POST['wfstep']), htmlspecialchars($_POST['nextstep']), $_SESSION['user'], $thisarray["p1"], $usname, $wfdata, $wfdatagroups);
    $err = $q["err"];
    $msg = $q["msg"];}
if (isset($_POST['updreq'])) {$q = requestFunctions::updreq($usname, $thisarray["p1"], $wfdata);}
if (isset($_POST['donereq'])) {$q = requestFunctions::donereq(htmlspecialchars($_POST['wfstep']), $thisarray["p1"], $wfdata);
    $err = $q["err"];
    $msg = $q["msg"];}

include $website['corebase'] . "public/modules/css.php";?>
<link href="/<?php echo $website['corebase']; ?>assets/css/css-chart.css" rel="stylesheet">
</head>

<body class="fix-header card-no-border">
    <div id="main-wrapper">
        <?php if (!empty($thisarray["p1"])) {$breadcrumb["text"] = "Requests";
    $breadcrumb["link"] = "/tickets";
    $breadcrumb["text2"] = ($thisarray["p1"] == "type" ? $thisarray["p3"] : $thisarray["p1"]);
    $breadcrumb["link2"] = ($thisarray["p1"] == "type" ? "" : "/ticketinfo/" . $thisarray["p1"]);
} else { $breadcrumb["text"] = "Requests";
    $breadcrumb["link"] = "";}?>
        <?php include $website['corebase'] . "public/modules/headcontent.php";?>
        <div class="page-wrapper">
            <div class="container-fluid">




                <?php if (!is_array($zobj) and empty($zobj)) {?>
                <html>

                <head>
                    <script language="JavaScript">
                    function redirect() {
                        parent.location.href = "/tickets"
                    }
                    </script>
                </head>

                <body onLoad="redirect()"></body>

                </html>
                <?php }?>
                <form name="form" action="" enctype="multipart/form-data" method="post">
                    <div class="row pt-3" id="ngApp" ng-app="ngApp" ng-controller="ngCtrl">

                        <div class="col-lg-2 bg-white leftsidebar">
                            <?php include "public/modules/sidebar.php";?>
                        </div>
                        <div class="col-lg-7">
                            <div class="card ps-3">
                                <div class="col-lg-12 align-self-center">
                                    <div class="nav nav-tabs customtab align-middle"
                                        style="padding: 10px 0;display: inline-block;">
                                        <span>Status: </span>
                                        <span class="badge bg-warning">New</span>
                                        <span class="ms-3">Client: </span>
                                        <a
                                            href="/browse/user/<?php echo $zobj['requser']; ?>"><?php echo $zobj['requser']; ?></a>
                                        <span class="ms-3">Created: </span>
                                        <span
                                            class="text-muted"><?php echo date("d/m/Y", strtotime($zobj['created'])); ?></span>
                                        <?php if (!empty($zobj['assigned'])) {?>
                                        <span class="ms-3">Assigned to: </span>
                                        <a
                                            href="/browse/user/<?php echo $zobj['assigned']; ?>"><?php echo $zobj['assigned']; ?></a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header"><span class="h4"><?php echo $zobj['reqname']; ?></span>
                                    <div class="float-end">Priority: <span
                                            class="badge bg-<?php echo $priorityarr[$zobj['priority']]["butcolor"]; ?>"><?php echo $priorityarr[$zobj['priority']]["name"]; ?></span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php echo $zobj['info']; ?>
                                </div>
                                <div class="card-footer">
                                    <?php if (!empty($zobj['reqapp'])) {
    $q = gTable::read("config_app_codes", "appinfo", " where appcode='" . $zobj['reqapp'] . "'");
    if ($zobjin = $q->fetch(PDO::FETCH_ASSOC)) {?>Application: <?php echo $zobjin['appinfo']; ?><br>
                                    <?php }} ?>
                                    Ready until: <span
                                        class="badge bg-secondary"><?php echo $zobj['deadline']; ?></span> &nbsp;
                                    Production date: <span
                                        class="badge bg-secondary"><?php echo $zobj['deadlinedeployed']; ?></span>
                                </div>
                            </div>

                            <?php 
if (!empty($ugrarr)) {$checkgr = in_array($zobj["wfunit"], $ugrarr); $effunit = $zobj["wfunit"];} else { $checkgr = 1;}
if (($checkgr || $zobj["wfunit"] == $_SESSION["user"]) && array_key_exists($zobj["wid"], $widarrkeys)) {?>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <div class="btn-group  mb-2 mb-md-0 reqbtngroup" role="group"
                                        aria-label="Request buttons">
                                        <button type="button" data-bs-toggle="modal" href="#updmodal"
                                            class="btn btn-light btn-sm waves-effect"><i
                                                class="mdi mdi-comment-multiple-outline"></i>&nbsp;Comment</button>
                                        <?php if (empty($deplinreq) && $wfdata["nodes"][$zobj["wfstep"]][0]["elusrchtask"] == 1 && $zobj['assigned'] == $_SESSION['user']) {?><button
                                            type="button" data-bs-toggle="modal" href="#chmodal"
                                            class="btn btn-light btn-sm waves-effect"><i
                                                class="mdi mdi-account-switch"></i>&nbsp;Change
                                            task</button><?php }?>
                                        <?php if (empty($zobj['assigned'])) {?><button
                                            class="btn btn-primary btn-sm waves-effect" name="assign" type="submit"><i
                                                class="mdi mdi-account-check-outline"></i>&nbsp;Assign</button><?php }?>

                                        <?php if ($wfdatalaststep == $zobj["wfstep"] && $zobj['assigned'] == $_SESSION['user'] && $zobj['deployed'] != 1) {?><button
                                            name="donereq" type="submit" class="btn btn-primary btn-sm waves-effect"><i
                                                class="mdi mdi-check"></i>&nbsp;Finish</button><?php } else {?>
                                        <?php if (!empty($zobj["wfstep"]) && $zobj['assigned'] == $_SESSION['user']) {
    foreach ($wfdata["connections"][$zobj["wfstep"]] as $keyin => $valin) {?>
                                        <button type="button" ng-click="sendnext('<?php echo $valin["targetId"]; ?>')"
                                            class="btn btn-light btn-sm waves-effect"><i
                                                class="mdi mdi-send"></i>&nbsp;Next:
                                            <?php echo $wfdata["nodes"][$valin["targetId"]][0]["label"]; ?></button>
                                        <?php }
}?>
                                        <?php }?>
                                        <?php if ($wfdata["nodes"][$zobj["wfstep"]][0]["elusrconf"] == 1 && $zobj['projconfirmed'] != 1 && $zobj['assigned'] == $_SESSION['user']) {$tmp["confirmreq"] = true;?><button
                                            class="btn btn-light btn-sm confirmreq<?php echo $zobj['sname']; ?>"
                                            type="button"
                                            ng-click="confirmreq('<?php echo $zobj['sname']; ?>','<?php echo $_SESSION['user']; ?>','<?php echo $zobj["wfstep"]; ?>','<?php echo $usname; ?>','<?php echo $zobj['projnum']; ?>')"><i
                                                class="mdi mdi-check-decagram"></i>&nbsp;Confirm</button><?php }?>
                                        <?php if ($wfdata["nodes"][$zobj["wfstep"]][0]["elusrappr"] == 1 && $zobj['projapproved'] != 1 && $zobj['assigned'] == $_SESSION['user']) {$tmp["approvereq"] = true;?><button
                                            class="btn btn-light btn-sm approvereq<?php echo $zobj['sname']; ?>"
                                            type="button"
                                            ng-click="approvereq('<?php echo $zobj['sname']; ?>','<?php echo $_SESSION['user']; ?>','<?php echo $zobj["wfstep"]; ?>','<?php echo $usname; ?>','<?php echo $zobj['projnum']; ?>')"><i
                                                class="mdi mdi-check-decagram"></i>&nbsp;Approve</button><?php }?>
                                        <?php if ($zobj['assigned'] == $_SESSION['user']) {?><button
                                            class="btn btn-light btn-sm" name="updreq" type="submit"><i
                                                class="mdi mdi-content-save"></i>&nbsp;Save</button><?php }?>
                                        <input type="text" style="display:none;" name="nextstep"
                                            ng-model="proj.nextstep">
                                        <button type="submit" id="sendnext" name="sendnext"
                                            style="display:none;"></button>
                                    </div>
                                    <div class="btn-group  mb-2 mb-md-0 reqbtngroup" role="group"
                                        aria-label="Request buttons">
                                        <?php if ($zobj['assigned'] == $_SESSION['user']) {?><button
                                            class="btn btn-light btn-sm" name="sendback" type="submit"><i
                                                class="mdi mdi-account-arrow-left-outline"></i>&nbsp;Sent back
                                            to requestor</button><?php }?>
                                    </div>
                                </div>
                            </div><br>
                            <?php }?>
                            <div class="modal" id="updmodal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-body">

                                            <div class="form-group">
                                                <textarea rows="5" name="requpdinfo"
                                                    class="form-control textarea"></textarea>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-light btn-sm" name="addcomm" type="submit"><i
                                                    class="mdi mdi-content-save"></i>&nbsp;Save</button>&nbsp;
                                            <button type="button" class="btn btn-danger btn-sm"
                                                data-bs-dismiss="modal"><i
                                                    class="mdi mdi-check"></i>&nbsp;Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <input type="hidden" name="reqname" value="<?php echo $zobj['reqname']; ?>">
                            <input type="hidden" name="requser" value="<?php echo $zobj['requser']; ?>">
                            <input type="hidden" name="reqid" value="<?php echo $thisarray["p1"]; ?>">
                            <input type="hidden" name="wfstep" value="<?php echo $zobj["wfstep"]; ?>">
                            <input type="text" style="display:none;" name="reqapp"
                                value="<?php echo $zobj['reqapp']; ?>">
                        </div>
                        <div class="col-md-3">
                            <div class="card ps-2 pe-2">
                                <div class="col-lg-12 align-self-center">
                                    <div class="nav nav-tabs customtab align-middle"
                                        style="padding:1px 0;display: inline-block;">
                                        <span>Support team: </span>
                                        <?php
$q = gTable::read("config_workflows", "wgroups", " where wid='" . $zobj["wid"] . "'");
$zobjin = $q->fetch(PDO::FETCH_ASSOC);
$tmp["wgroups"] = array();
$tmp["wusers"] = array();
$tmp["requsers"] = array();
foreach (json_decode($zobjin["wgroups"], true) as $keyin => $valin) {
    if ($valin["type"] == "group") {$tmp["wgroups"][] = $keyin;}
    if ($valin["type"] == "user") {$tmp["wusers"][] = $keyin;}
}
;
if (count($tmp["wgroups"]) > 0) {
    $sqlin = "select users from user_groups where group_latname in (" . str_repeat('?,', count($tmp["wgroups"]) - 1) . '?)';
    $qin = $pdo->prepare($sqlin);
    $qin->execute($tmp["wgroups"]);
    $zobjin = $qin->fetchAll();
    foreach ($zobjin as $val) {
        foreach (json_decode($val["users"], true) as $keyin => $valin) {
            $tmp["wusers"][] = $keyin;
        }
    }
}
if (count($tmp["wusers"]) > 0) {
    $sqlin = "select mainuser, avatar, fullname from users where mainuser in (" . str_repeat('?,', count($tmp["wusers"]) - 1) . '?)';
    $qin = $pdo->prepare($sqlin);
    $qin->execute($tmp["wusers"]);
    $zobjin = $qin->fetchAll();
    foreach ($zobjin as $valin) {
        $tmp["requsers"][$valin["mainuser"]]["avatar"] = $valin["avatar"];
        $tmp["requsers"][$valin["mainuser"]]["fullname"] = $valin["fullname"];
    }
}
if (count($tmp["requsers"]) > 0) {
    foreach ($tmp["requsers"] as $valin) {
        echo '<a class="avatar rounded-circle me-1" alt="user" data-bs-toggle="tooltip" data-bs-placement="top"
        title="' . $valin["fullname"] . '"><img src="' . (!empty($valin["avatar"]) ? $valin["avatar"] : "/assets/images/avatar.svg") . '"
                  width="40"  ></a>';
    }
}
?>
                                    </div>
                                </div>
                            </div>


                            <?php include "reqnav.php";?>
                            <br><br>
                            <?php if ($wfdatahc == 1) {?>
                            <?php if ($zobj['projconfirmed'] == 1) {
    $q = gTable::read("requests_confirmation", "conffullname,confuser,confdate", " where reqid='" . $thisarray["p1"] . "'");
    $zobjin = $q->fetch(PDO::FETCH_ASSOC);
    ?>
                            <div class="alert alert-success"> <i class="mdi mdi-account-circle-outline"></i>
                                <b><a href="/browse/user/<?php echo $zobjin['confuser']; ?>"
                                        target="_blank"><?php echo $zobjin['conffullname']; ?></a></b> confirmed the
                                project
                                on
                                <?php echo date("d.m.Y", strtotime($zobjin['confdate'])); ?>
                            </div>
                            <?php } else {?>
                            <div class="alert alert-warning">Project is still not confirmed</div>
                            <?php }?>
                            <?php }?>
                            <?php if ($wfdataha == 1) {?>
                            <?php if ($zobj['projapproved'] == 1) {
    $q = gTable::read("requests_approval", "apprdate,appruser,apprfullname", " where reqid='" . $thisarray["p1"] . "'");
    $zobjin = $q->fetch(PDO::FETCH_ASSOC);
    ?>
                            <div class="alert alert-success"> <i class="mdi mdi-account-circle-outline"></i>
                                <b><a href="/browse/user/<?php echo $zobjin['appruser']; ?>"
                                        target="_blank"><?php echo $zobjin['apprfullname']; ?></a></b> approved the
                                project
                                on
                                <?php echo date("d.m.Y", strtotime($zobjin['apprdate'])); ?>
                            </div>
                            <?php } else {?>
                            <div class="alert alert-warning">Project is still not approved</div>
                            <?php }?>
                            <?php }?>

                            <?php
if ($wfdata["nodes"][$zobj["wfstep"]][0]["elusreff"] == 1 && $zobj['assigned'] == $_SESSION['user']) {
    $q = gTable::read("requests_efforts", "effdays", " where reqid='" . $thisarray["p1"] . "' and effuser='" . $_SESSION["user"] . "'");
    $zobjin = $q->fetch(PDO::FETCH_ASSOC);
    if ($zobjin["effdays"]) {
        $tempvar = $zobjin["effdays"];
        $q = gTable::read("calendar", "sum(time_period) as timeperiod", " where subj_id='" . $thisarray["p1"] . "' and mainuser='" . $_SESSION["user"] . "'");
        $zobjin = $q->fetch(PDO::FETCH_ASSOC);
        $temparr["percent"] = 0;
        $temparr["timeperiod"] = 0;
        if ($zobjin["timeperiod"]) {
            $temparr["timeperiod"] = round($zobjin["timeperiod"] / 8, 2);
            $temparr["percent"] = round($temparr["timeperiod"] / $tempvar * 100, 2);
        }
        ?>
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col pe-0 align-self-center">
                                        <h2 class="font-weight-light mb-0">Efforts</h2>
                                        <h6 class="text-muted"><?php echo $temparr["timeperiod"]; ?> of
                                            <?php echo $tempvar; ?>
                                            <?php echo $website["effort_unit"]; ?></h6>
                                    </div>
                                    <div class="col text-end align-self-center">
                                        <div data-label="<?php echo $temparr["percent"]; ?>%"
                                            class="css-bar mb-0 css-bar-info css-bar-<?php echo $temparr["percent"]; ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                            <?php }?>

                        </div>

                    </div>
                </form>
            </div>
        </div>
        <?php include $website['corebase'] . "public/modules/footer.php";?>
        <?php include $website['corebase'] . "public/modules/js.php";?>
        <script type="text/javascript" src="/controller/modules/tickets/assets/js/ng-controller.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            var sum = 0;
            $('.effgroup').each(function() {
                sum += parseFloat(this.value);
            });
            $("#budg_total").val(sum);
            if (sum > 0) {
                $('#effchanged').val('yes');
            };
            $(".effgroup").change(function() {
                $('#effchanged').val('yes');
            });
            $(".effgroup").keyup(function() {
                var sum = 0;
                $('.effgroup').each(function() {
                    sum += parseFloat(this.value);
                });
                $("#budg_total").val(sum);
            });
        });
        </script>
        <!-- <script type="text/javascript">
        var app = angular.module('ngApp', []);

        app.config(['$compileProvider',
            function($compileProvider) {
                $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|tel|file|blob):/);
            }
        ]);
        app.controller('ngCtrl', function($scope, $http) {
            $('.updinfo,.deplbut').hide();
            var efforts = $("#efforts").val();
            $scope.proj = {};
            $scope.proj.efforts = efforts;
            <?php if ($tmp["confirmreq"]) {?>
            $scope.confirmreq = function(reqid, user, wfstep, usfullname, projid) {
                $http({
                    method: 'POST',
                    data: {
                        'reqid': reqid,
                        'user': user,
                        'wfstep': wfstep,
                        'fullname': usfullname,
                        'project': projid
                    },
                    url: '/reqapi/confirmreq'
                }).then(function successCallback(response) {
                    notify(response.data, 'success');
                    $(".confirmreq" + reqid).hide();
                });
            };
        <?php }?>
        <?php if ($tmp["approvereq"]) {?>
            $scope.approvereq = function(reqid, user, wfstep, usfullname, projid) {
                $http({
                    method: 'POST',
                    data: {
                        'reqid': reqid,
                        'user': user,
                        'wfstep': wfstep,
                        'fullname': usfullname,
                        'project': projid
                    },
                    url: '/reqapi/approvereq'
                }).then(function successCallback(response) {
                    notify(response.data, 'success');
                    $(".approvereq" + reqid).hide();
                });
            };
            <?php }?>
            $scope.deploybut = function(reqid, user, usfullname, projid) {
                var env = $scope.deployin;
                var pkgname = $scope.pkgname;
                $http({
                    method: 'POST',
                    data: {
                        'reqid': reqid,
                        'user': user,
                        'fullname': usfullname,
                        'project': projid,
                        'pkgname': pkgname,
                        'env': env
                    },
                    url: '/reqapi/deployreq'
                }).then(function successCallback(response) {
                    notify(response.data, 'success');
                });
            };
            $scope.sendnext = function(nextid) {
                $scope.proj.nextstep = nextid;
                setTimeout(function() {
                    $("#sendnext").click();
                }, 1);
            }
        });
        </script>-->
        <?php include $website['corebase'] . "public/modules/template_end.php";?>
</body>

</html>