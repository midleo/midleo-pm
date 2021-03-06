<?php 
$modulelist["calendar"]["name"] = "Calendar module - time management";
$modulelist["calendar"]["css"][] = "/controller/modules/calendar/assets/css/fullcalendar.min.css";
$modulelist["calendar"]["css"][] = "/controller/modules/calendar/assets/css/mcalendar.css";
$modulelist["calendar"]["js"][] = "/controller/modules/calendar/assets/js/fullcalendar.min.js";
$modulelist["calendar"]["js"][] = "/controller/modules/calendar/assets/js/mcalendar.js";
$modulelist["calendar"]["js"][] = "/controller/modules/calendar/assets/js/locales-all.min.js";
include "api.php";
class calendarClass
{
    public static function showCal($thisarr,$breadcrumb,$thisarray,$arrcolor)
    {
        global $website;
        $pdo = pdodb::connect();
        $hours = "";
        $brarr=$thisarr;
        array_push($brarr,array(
            "title"=>"Add hours",
            "link"=>"#modeff",
            "modal"=>true,
            "icon"=>"mdi-plus",
            "active"=>false,
          )); 
        for ($x = 1; $x <= 8; $x++) {$hours .= '<option value="' . $x . '">' . $x . ' Hours</option>';}
        ?>
<div class="row pt-3">
    <div class="col-lg-2 bg-white leftsidebar">
        <?php include "public/modules/sidebar.php"; ?>
    </div>
    <div class="col-lg-8">
        <div id='calendar' class="card">
            <div class="text-info text-center calalert p-2"><i class="mdi mdi-loading iconspin"></i>&nbsp;Loading...
            </div>
        </div>
        <input id="username" style="display:none" value="<?php echo $_SESSION["user"]; ?>">
    </div>
    <div class="col-md-2">
        <?php include $website['corebase']."public/modules/breadcrumbin.php"; ?>
        <!-- Modal -->
        <div class="modal" id="modeff" tabindex="-1" aria-labelledby="modefflbl" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form name="form" id="effForm" action="" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modefflbl">Add new efforts</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group showselect">
                            <label class="control-label">Ticket ID or Name</label>
                                <input type="text" id="reqeffauto" class="form-control"
                                    required /> <input type="text" id="reqid" name="reqid" style="display:none;" />
                            </div>
                            <div class="form-group showinput" style="display:none;">
                                <label class="control-label">Effort information</label>
                                <div class="row">
                                    <div class="col-md-9"><input name="reqinfo" id="reqinfo" class="form-control" type="text"></div>
                                    <div class="col-md-3"><button type="button"
                                            onclick="ShowHide('showinput','showselect')" class="btn btn-light btn-sm"><i
                                                class="mdi mdi-close"></i></button></div>
                                </div>
                            </div>
                            <div class="form-group">
                            <label class="control-label">Start time</label>
                                <input name="starttime" class="form-control date-time-picker-cal" id="datetimepick"
                                    data-toggle="datetimepicker" data-target="#datetimepick"
                                    type="text" required>
                            </div>
                            <div class="form-group">
                            <label class="control-label">Duration</label>
                                <select class="form-control" name="timeperiod" id="timeperiod" required>
                                    <?php echo $hours; ?>
                                </select>
                            </div>
                            <div class="form-group" >
                            <label class="control-label">Event color</label>
                            <div style="display:block;" class="pt-2" id="styleradio">
<?php $i=0;
foreach($arrcolor as $key=>$val){ $i++;?>
                <input name="evcolor" type="radio" id="r<?php echo $val;?>" class="radio-<?php echo $val;?>" value="<?php echo $val;?>" <?php echo $i==1?"checked":"";?> />
                <label for="r<?php echo $val;?>"></label>
<?php } ?>
</div>
</div>
<input type="text" id="eventid" name="eventid" style="display:none;" />
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="butdelev" name="delcal" class="btn btn-sm btn-danger waves-effect"
                            style="display:none;"><i
                                class="mdi mdi-close"></i>&nbsp;Delete</button>&nbsp;
                            <button type="button" id="savecal" name="savecal" class="btn btn-sm btn-info waves-effect"><i
                                    class="mdi mdi-plus"></i>&nbsp;Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal -->
    </div>
</div>
<?php
unset($hours);
        pdodb::disconnect();
    }
}
class Class_calendar
{
    public static function getPage($thisarray)
    {
        global $installedapp;
        global $modulelist;
        global $website;
        global $page;
        global $maindir;
        if ($installedapp != "yes") {header("Location: /install");}
        sessionClass::page_protect(base64_encode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $err = array();
        $msg = array();
        $pdo = pdodb::connect();
        $data = sessionClass::getSessUserData();foreach ($data as $key => $val) {${$key} = $val;}
        $arrcolor=array("40BC86","F31D2F","FCB410","B17E22","F24D16","2980B9","3498DB","0918EC","199EC7","03A2FD","7b68ee","BF4ACC","074354","181D21");
        include $website['corebase']."public/modules/css.php";
        echo '<link rel="stylesheet" type="text/css" href="/'.$website['corebase'].'assets/css/jquery-ui.min.css">';
        foreach ($modulelist["calendar"]["css"] as $csskey => $csslink) {
            if (!empty($csslink)) {?>
                <link rel="stylesheet" type="text/css" href="<?php echo $csslink; ?>"><?php }
        }
        echo '<style type="text/css">';
        foreach($arrcolor as $key=>$val){ echo '#styleradio [type="radio"].radio-'.$val.' + label:after{background-color:#'.$val.';border-color:#'.$val.';animation:ripple .2s linear forwards}'; }
        echo '</style>';
        echo '</head><body class="card-no-border"> <div id="main-wrapper">';
        $breadcrumb["text"] = "Calendar";
        include $website['corebase']."public/modules/headcontent.php";
        echo '<div class="page-wrapper"><div class="container-fluid">';
        $brarr = array(
            array(
                "title" => "View/Edit your tasks",
                "link" => "/tasks",
                "icon" => "mdi-calendar-check-outline",
                "active" => ($page == "tasks") ? "active" : "",
            ),
            array(
                "title" => "View your timesheets",
                "link" => "/timesheets",
                "icon" => "mdi-timetable",
                "active" => ($page == "timesheets") ? "active" : "",
            ),
        ); 
        calendarClass::showCal($brarr,$breadcrumb,$thisarray,$arrcolor);
        echo "<input type='hidden' id='working_start' value='{$website['working_start']}'>";
        echo "<input type='hidden' id='working_end' value='{$website['working_end']}'>";
        echo '</div>';
        include $website['corebase']."public/modules/footer.php";
        include $website['corebase']."public/modules/js.php";
        foreach ($modulelist["calendar"]["js"] as $jskey => $jslink) {
            if (!empty($jslink)) { ?><script type="text/javascript" src="<?php echo $jslink; ?>"></script><?php }
        }
        include $website['corebase']."public/modules/template_end.php";
        echo '</body></html>';
    }
}
class Class_timesheets
{
    public static function getPage($thisarray)
    {
        global $installedapp;
        global $modulelist;
        global $website;
        global $page;
        global $maindir;
        if ($installedapp != "yes") {header("Location: /install");}
        sessionClass::page_protect(base64_encode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $err = array();
        $msg = array();
        $pdo = pdodb::connect();
        $data = sessionClass::getSessUserData();foreach ($data as $key => $val) {${$key} = $val;}
        $month = date("m");
        $year = date("Y");
        if (!empty($_POST["thismonth"])) {$month = htmlspecialchars($_POST["thismonth"]);}
        if (!empty($_POST["thisyear"])) {$year = htmlspecialchars($_POST["thisyear"]);}
        $month = str_pad($month, 2, 0, STR_PAD_LEFT);
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        include $website['corebase']."public/modules/css.php";
        echo '<link rel="stylesheet" type="text/css" href="/'.$website['corebase'].'assets/js/datatables/dataTables.bootstrap5.min.css">';
        echo '<link rel="stylesheet" type="text/css" href="/'.$website['corebase'].'assets/js/datatables/fixedColumns.bootstrap5.min.css">';
        echo '</head><body class="card-no-border"> <div id="main-wrapper">';
        $breadcrumb["text"] = "Timesheets";
        include $website['corebase']."public/modules/headcontent.php";
        echo '<div class="page-wrapper"><div class="container-fluid">';
        $brarr = array(
            array(
                "title" => "View/Edit calendar",
                "link" => "/calendar",
                "icon" => "mdi-calendar-month-outline",
                "active" => ($page == "calendar") ? "active" : "",
            ),
            array(
                "title" => "View/Edit your tasks",
                "link" => "/tasks",
                "icon" => "mdi-calendar-check-outline",
                "active" => ($page == "tasks") ? "active" : "",
            ),
        );
        
        echo '<div class="row pt-3"><div class="col-2 bg-white leftsidebar">';?>
<?php include "public/modules/sidebar.php"; ?>
</div>
<div class="col-lg-8">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <form method="post" action="" class="form-material">
                    <div class="row">
                        <div class="col-md-5">
                            <select class="form-control topsearch" name="thisyear">
                                <?php echo (!empty($year) ? '<option value="' . $year . '">' . $year . '</option>' : '<option value="">Year</option>'); ?>
                                <?php for ($i = -5; $i < 6; $i++) {?><option
                                    value="<?php echo date('Y', strtotime('+' . $i . ' year')); ?>">
                                    <?php echo date('Y', strtotime('+' . $i . ' year')); ?></option><?php }?>
                                <option value="">This year</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <select class="form-control topsearch" name="thismonth" onchange="this.form.submit()">
                                <?php echo (!empty($month) ? '<option value="' . $month . '">' . date('F', strtotime('01.' . $month . '.' . $year)) . '</option>' : '<option value="">Month</option>'); ?>
                                <?php for ($i = 1; $i < 13; $i++) {?><option
                                    value="<?php echo str_pad($i, 2, 0, STR_PAD_LEFT); ?>">
                                    <?php echo date('F', strtotime('01.' . $i . '.' . $year)); ?></option><?php }?>
                                <option value="">This month</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body p-0">

            <?php
$q = gTable::countAll("calendar", " where mainuser='" . $_SESSION['user'] . "' and month(date_start)='" . $month . "' and year(date_start)='" . $year . "'");
        if ($q > 0) {
            $q = gTable::read("calendar", "*", " where mainuser='" . $_SESSION['user'] . "' and month(date_start)='" . $month . "' and year(date_start)='" . $year . "'");
            if ($zobj = $q->fetchAll()) {$proj = array();
                $sumproj = array();
                $sumprojmonth = array();
                foreach ($zobj as $val) {
                    $proj[$val["subj_id"]]["data"][date("Y-m-d", strtotime($val["date_start"]))][] = $val["time_period"];
                    $proj[$val["subj_id"]]["name"] = $val["subject"];
                    $sumproj[date("Y-m-d", strtotime($val["date_start"]))][] = $val["time_period"];
                    $sumprojmonth[] = $val["time_period"];
                }
            }?>

            <table id="dttimesh" class="table nowrap tablenohover" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-end">About</th>
                        <th class="text-end">ID</th>
                        <th class="text-center no-sort"><i class="mdi mdi-sigma"></i><br>sum</th>
                        <?php
for ($j = 1; $j <= $days; $j++) {$j = str_pad($j, 2, 0, STR_PAD_LEFT);
                echo "<th class='text-center no-sort " . (CallFunct::isWeekend($year . "-" . $month . "-" . $j) ? "bg-light" : "") . "'>$j<br>" . date("D", strtotime($year . "-" . $month . "-" . $j)) . "</th>";
            }?>

                </thead>
                <tbody>

                    <?php
foreach ($proj as $key => $val) {$sum = 0;foreach ($val["data"] as $keyin => $valin) {$sum += array_sum($valin);}?>
                    <tr>
                        <td class="text-end"><?php echo $val["name"]; ?></td>
                        <td class="text-end"><?php echo $key; ?></td>
                        <td class="text-center"><?php echo $sum; ?>h</td>
                        <?php
for ($j = 1; $j <= $days; $j++) {$j = str_pad($j, 2, 0, STR_PAD_LEFT);
                if (is_array($val["data"][$year . "-" . $month . "-" . $j])) {
                    echo "<td valign='middle' class='text-center " . (CallFunct::isWeekend($year . "-" . $month . "-" . $j) ? "bg-light" : "") . "'>" . (array_sum($val["data"][$year . "-" . $month . "-" . $j]) > 0 ? array_sum($val["data"][$year . "-" . $month . "-" . $j]) . "h" : "") . "</td>";
                } else {
                    echo "<td class='text-center " . (CallFunct::isWeekend($year . "-" . $month . "-" . $j) ? "bg-light" : "") . "'></td>";
                }
            }
                ?>
                    </tr>
                    <?php }?>


                </tbody>
            </table>


            <?php
echo '<div class="p-2"><br><div class="text-info">Summary for: <br><br>Year: <b>' . $year . '</b><br>Month: <b>' . date('F', strtotime('01.' . $month . '.' . $year)) . '</b><br><br>Total working period: <b>' . CallFunct::secondsToTime(array_sum($sumprojmonth) * 3600) . '</b></div></div>';
        } else {
            echo '<div class="p-2"><br><div class="text">No info found for:<br><br>Year: <b>' . $year . '</b><br>Month: <b>' . date('F', strtotime('01.' . $month . '.' . $year)) . '</b></div>';
        }
        echo '</div></div></div><div class="col-md-2">';
        include $website['corebase']."public/modules/breadcrumbin.php";
        echo '</div></div></div></div>';
        include $website['corebase']."public/modules/footer.php";
        echo '</div></div>';
        include $website['corebase']."public/modules/js.php"; ?>
            <script src="/<?php echo $website['corebase'];?>assets/js/datatables/jquery.dataTables.min.js"></script>
            <script src="/<?php echo $website['corebase'];?>assets/js/datatables/dataTables.bootstrap5.min.js"></script>
            <script src="/<?php echo $website['corebase'];?>assets/js/datatables/dataTables.fixedColumns.min.js">
            </script>
            <script src="/<?php echo $website['corebase'];?>assets/js/datatables/dataTables.buttons.min.js"></script>
            <script src="/<?php echo $website['corebase'];?>assets/js/datatables/buttons.flash.min.js"></script>
            <script src="/<?php echo $website['corebase'];?>assets/js/datatables/jszip.min.js"></script>
            <script src="/<?php echo $website['corebase'];?>assets/js/datatables/buttons.html5.min.js"></script>
            <script src="/<?php echo $website['corebase'];?>assets/js/datatables/buttons.print.min.js"></script>
            <script>
            $('#dttimesh').DataTable({
                "oLanguage": {
                    "sSearch": "",
                    "sSearchPlaceholder": "Search",
                    "sFilterInput": "form-control"
                },
                dom: 'Bfrtip',
                //   scrollY:        "300px",
                scrollX: true,
                scrollCollapse: true,
                paging: false,
                fixedColumns: {
                    leftColumns: 3
                },
                buttons: [
                    'csv', 'excel', 'print'
                ],
                "order": [],
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }]
            });
            </script>
            <?php
include $website['corebase']."public/modules/template_end.php";
        echo '</body></html>';
    }
}
class Class_tasks
{
    public static function getPage($thisarray)
    {
        global $installedapp;
        global $website;
        global $page;
        global $modulelist;
        global $maindir;
        if ($installedapp != "yes") {header("Location: /install");}
        sessionClass::page_protect(base64_encode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $err = array();
        $msg = array();
        $pdo = pdodb::connect();
        $data = sessionClass::getSessUserData();foreach ($data as $key => $val) {${$key} = $val;}
        if (isset($_POST["savetask"])) {
            $sql = "insert into tasks(mainuser,taskinfo) values(?,?)";
            $q = $pdo->prepare($sql);
            if ($q->execute(array($_SESSION["user"], htmlspecialchars($_POST["taskname"])))) {
                $msg[] = "You have created a new task!";} else {
                $err[] = "Error creating the task";}
        }
        include $website['corebase']."public/modules/css.php";?>
            <link rel="stylesheet" type="text/css"
                href="/<?php echo $website['corebase'];?>assets/js/datatables/dataTables.bootstrap5.min.css">
            <link rel="stylesheet" type="text/css"
                href="/<?php echo $website['corebase'];?>assets/js/datatables/responsive.dataTables.min.css">
            </head>

            <body class="fix-header card-no-border">
                <div id="main-wrapper">
                    <?php $breadcrumb["text"] = "Task list";include $website['corebase']."public/modules/headcontent.php";?>
                    <div class="page-wrapper">
                        <div class="container-fluid">
                            <?php
$brarr = array(
            array(
                "title" => "View/Edit calendar",
                "link" => "/calendar",
                "icon" => "mdi-calendar-month-outline",
                "active" => ($page == "calendar") ? "active" : "",
            ),
            array(
                "title" => "View your timesheets",
                "link" => "/timesheets",
                "icon" => "mdi-timetable",
                "active" => ($page == "timesheets") ? "active" : "",
            ),
            array(
                "title" => "Open a new task",
                "link" => "#modal-cal",
                "modal"=>true,
                "icon" => "mdi-plus",
                "active" => true,
            ),
        );
        ?>

                            <div class="row pt-3">
                                <div class="col-lg-2 bg-white leftsidebar">
                                    <?php include "public/modules/sidebar.php"; ?>
                                </div>
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3 class="card-title">Task list</h3>
                                            <h6 class="card-subtitle"></h6><br>
                                            <?php
$sql = "select * from tasks where mainuser=? order by id desc limit 40";
        $q = $pdo->prepare($sql);
        $q->execute(array($_SESSION["user"]));
        if ($zobj = $q->fetchAll()) {?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <table id="data-my-tasks" class="table table-hover stylish-table "
                                                        aria-busy="false">
                                                        <thead>
                                                            <tr>
                                                                <th data-column-id="date" data-width="150px">Start Date
                                                                </th>
                                                                <th data-column-id="taskstatus" data-type="numeric"
                                                                    data-visible="false" data-sortable="false"
                                                                    data-visibleInSelection="false" data-width="0px">
                                                                    task
                                                                    status</th>
                                                                <th data-column-id="id" data-identifier="true"
                                                                    data-type="numeric" data-visible="false"
                                                                    data-visibleInSelection="false" data-width="0px">ID
                                                                </th>
                                                                <th data-column-id="state" data-width="100px">State</th>
                                                                <th data-column-id="info">Task</th>
                                                                <th data-column-id="commands" data-formatter="commands"
                                                                    data-sortable="false" data-align="center"
                                                                    data-width="100px">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody><?php
foreach ($zobj as $val) {
            ?><tr id="task<?php echo $val['id']; ?>">
                                                                <td><?php echo $val['date_start']; ?></td>
                                                                <td><?php echo $val['taskstate']; ?></td>
                                                                <td><?php echo $val['id']; ?></td>
                                                                <td><?php if ($val['taskstate'] == 1) {echo "Done";} else {echo "In progress";};?>
                                                                </td>
                                                                <td><?php echo $val['taskinfo']; ?></td>
                                                                <td></td>
                                                            </tr>
                                                            <?php }?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <?php } else {?>
                                            <div class="row"><br>
                                                <div class="col-md-6">
                                                    <div class="alert alert-light">You have no tasks assigned.</div>
                                                </div>
                                            </div>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>





                                <div class="col-md-2">
                                    <?php include $website['corebase']."public/modules/breadcrumbin.php"; ?>

                                    <div class="modal" id="modal-cal" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form name="form" action="" method="post">
                                                    <div class="modal-body form-horizontal form-material">
                                                        <div class="form-group row">
                                                            <label
                                                                class="form-control-label text-lg-right col-md-3">Info</label>
                                                            <div class="col-md-9"><input name="taskname"
                                                                    class="form-control" type="text" required></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-light btn-sm" type="submit"
                                                            name="savetask"><i
                                                                class="mdi mdi-content-save"></i>&nbsp;Add</button>&nbsp;
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            data-bs-dismiss="modal"><i
                                                                class="mdi mdi-close"></i>&nbsp;Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include $website['corebase']."public/modules/footer.php";
        echo '</div></div>';
        include $website['corebase']."public/modules/js.php";?>
                    <script src="/<?php echo $website['corebase'];?>assets/js/datatables/jquery.dataTables.min.js">
                    </script>
                    <script src="/<?php echo $website['corebase'];?>assets/js/datatables/dataTables.responsive.min.js">
                    </script>
                    <script src="/<?php echo $website['corebase'];?>assets/js/datatables/dataTables.buttons.min.js">
                    </script>
                    <script src="/<?php echo $website['corebase'];?>assets/js/datatables/buttons.flash.min.js"></script>
                    <script src="/<?php echo $website['corebase'];?>assets/js/datatables/jszip.min.js"></script>
                    <script src="/<?php echo $website['corebase'];?>assets/js/datatables/pdfmake.min.js"></script>
                    <script src="/<?php echo $website['corebase'];?>assets/js/datatables/vfs_fonts.js"></script>
                    <script src="/<?php echo $website['corebase'];?>assets/js/datatables/buttons.html5.min.js"></script>
                    <script src="/<?php echo $website['corebase'];?>assets/js/datatables/buttons.print.min.js"></script>
                    <script type="text/javascript">
                    $(document).ready(function() {
                        var table = $('#data-my-tasks').DataTable({
                            "oLanguage": {
                                "sSearch": ""
                            },
                            dom: 'Bfrtip',
                            //  responsive: true,
                            columnDefs: [{
                                targets: -1,
                                "data": null,
                                "defaultContent": " <div class=\"btn-group\" role=\"group\"><button type=\"button\"  class=\"btn waves-effect btn-sm btn-light command-update\" ><i class='mdi mdi-check'></i></button><button type=\"button\" class=\"btn waves-effect btn-light btn-sm command-delete\" ><i class='mdi mdi-close'></i></button></div>"
                            }],
                            buttons: [
                                'copy', 'csv', 'excel', 'pdf', 'print'
                            ]
                        });
                        $('.command-update').on('click', function() {
                            var data = table.row($(this).parents('tr')).data();
                            var dataString = 'id=' + data[2];
                            $.ajax({
                                type: "POST",
                                url: "/calapi/tasks/update",
                                data: dataString,
                                success: function(html) {
                                    notify('Task was changed to Done!', 'success');
                                }
                            });
                        });
                        $('.command-delete').on('click', function() {
                            var data = table.row($(this).parents('tr')).data();
                            var dataString = 'id=' + data[2];
                            $.ajax({
                                type: "POST",
                                url: "/calapi/tasks/delete",
                                data: dataString,
                                success: function(html) {
                                    $("#task" + data[2]).hide();
                                    notify('Task deleted!', 'error');
                                }
                            });
                        });

                    });
                    </script>

                    <?php
include $website['corebase']."public/modules/template_end.php";
        echo '</body></html>';
    }
}