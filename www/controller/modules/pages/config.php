<?php
class ClassMPM_cp extends Class_cp
{
    public static function getPage($thisarray)
    {
        global $installedapp;
        global $website;
        global $maindir;
        global $page;
        global $modulelist;
        if ($installedapp != "yes") {header("Location: /install");}
        sessionClass::page_protect(base64_encode("http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        $err = array();
        $msg = array();
        $pdo = pdodb::connect();
        include $website['corebase']."public/modules/css.php";
        $data = sessionClass::getSessUserData();foreach ($data as $key => $val) {${$key} = $val;}
        $breadcrumb["text"] = "Dashboard";
        $brarr = array();
        include $website['corebase']."public/modules/css.php";
        $breadcrumb["text"] = "Dashboard";
        $brarr = array();
        array_push($brarr, array(
            "title" => "Import documents",
            "link" => "/docimport",
            "icon" => "mdi-plus",
            "active" => false,
        ));
        array_push($brarr, array(
            "title" => "Create/edit articles",
            "link" => "/cpinfo",
            "icon" => "mdi-file-document-edit-outline",
            "active" => false,
        ));
        array_push($brarr, array(
            "title" => "LDAP configuration",
            "link" => "/appconfig/ldap",
            "icon" => "mdi-file-tree-outline",
            "active" => false,
        ), array(
            "title" => "External connections",
            "link" => "/appconfig/external",
            "icon" => "mdi-open-in-new",
            "active" => false,
        ), array(
            "title" => "Core Configuration",
            "link" => "/appconfig/main",
            "icon" => "mdi-application-cog-outline",
            "active" => false,
        ));
        $page = "dashboard";
        echo '</head><body class="fix-header card-no-border"><div id="main-wrapper">';
        include $website['corebase']."public/modules/headcontent.php";
        echo '<div class="page-wrapper"><div class="container-fluid">';
         ?>

<div class="row pt-3">
    <div class="col-lg-2">
        <?php include "public/modules/sidebar.php";?></div>
    <div class="col-lg-8">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Activity chart</h4>
                            </div>
                            <div class="card-body p-1">
                                <div class="chart-edge">
                                    <canvas id="line-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-0">
                        <table class="table table-vmiddle table-hover stylish-table mb-0">
                            <thead>
                                <tr>
                                    <th colspan="3">
                                        <h4>Recent activity</h4>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="display:none;">
                                    <td colspan="3"></td>
                                </tr>
                                <?php if (empty($_SESSION["userdata"]["apparr"])) {$_SESSION["userdata"]["apparr"] = array();
            $argapparr = 0;} else { $argapparr = 1;}
        if (empty($_SESSION["userdata"]["widarr"])) {$_SESSION["userdata"]["widarr"] = array();
            $argwidarr = 0;} else { $argwidarr = 1;}
        if ($_SESSION["user_level"] > 3) {
            if (!in_array("system", $_SESSION["userdata"]["apparr"])) {
                $_SESSION["userdata"]["apparr"][] = "system";
            }
        }
        if (empty($_SESSION["userdata"]["apparr"])) {
            $sql = "select * from tracking where whoid=? " . ($dbtype == "oracle" ? " and ROWNUM <= 5 order by id desc" : " order by id desc limit 5");
            $q = $pdo->prepare($sql);
            $q->execute(array($_SESSION["user"]));
        } else {
            $sql = "select * from tracking where whoid='" . $_SESSION["user"] . "' or appid in (" . str_repeat('?,', count($_SESSION["userdata"]["apparr"]) - $argapparr) . '?' . ") " . ($dbtype == "oracle" ? " and ROWNUM <= 5 order by id desc" : " order by id desc limit 5");
            $q = $pdo->prepare($sql);
            $q->execute($_SESSION["userdata"]["apparr"]);
        }
        if ($zobj = $q->fetchAll()) {
            foreach ($zobj as $val) {
                ?><tr>
                                    <td class="text-start"><a href="/browse/user/<?php echo $val['whoid']; ?>"
                                            target="_blank"><?php echo $val['who']; ?></a></td>
                                    <td class="text-start"><?php echo $val['what']; ?></td>
                                    <td class="text-end"><?php echo textClass::getTheDay($val['trackdate']); ?></td>
                                </tr>
                                <?php }} else {?>
                                <tr style="display:none;">
                                    <td colspan="3">No activity yet</td>
                                </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <?php include $website['corebase']."public/modules/breadcrumbin.php";?>
    </div>
</div>

    <?php
        echo '</div></div>';
        include $website['corebase']."public/modules/footer.php";
        include $website['corebase']."public/modules/js.php"; ?>
        <script src="/<?php echo $website['corebase'];?>assets/js/chart.min.js" type="text/javascript"></script>
<script type="text/javascript">
var thiscolor = "#000";
var thiscolorreq = "rgb(255, 54, 54)";
var chdata = [<?php $thismonth = date('Y-m-d', strtotime('today -1 week'));
        if (DBTYPE == "oracle") {
            $sql = "SELECT * FROM (SELECT days.day as thisdate, count(id) as num
               FROM
               (select trunc(sysdate) as day from dual
               union select trunc(sysdate) - interval '1' day from dual
               union select trunc(sysdate) - interval '2' day from dual
               union select trunc(sysdate) - interval '3' day from dual
               union select trunc(sysdate) - interval '4' day from dual
               union select trunc(sysdate) - interval '5' day from dual
               union select trunc(sysdate) - interval '6' day from dual
               union select trunc(sysdate) - interval '7' day from dual
               union select trunc(sysdate) - interval '8' day from dual
               union select trunc(sysdate) - interval '9' day from dual) days
               left join tracking
               on days.day = to_char(trackdate, 'DD.MON.YYYY')" .
                (empty($_SESSION["userdata"]["apparr"]) ? " and whoid=?" : " and appid in (" . str_repeat('?,', count($_SESSION["userdata"]["apparr"]) - $argapparr) . "?" . ")") . "
               group by days.day
               order by days.day desc) WHERE rownum <= 10";
        } else if (DBTYPE == "postgresql") {
            $sql = "SELECT days.day as thisdate, count(id) as num
                FROM
               (select CURRENT_DATE as day
               union select CURRENT_DATE - interval '1' day
               union select CURRENT_DATE - interval '2' day
               union select CURRENT_DATE - interval '3' day
               union select CURRENT_DATE - interval '4' day
               union select CURRENT_DATE - interval '5' day
               union select CURRENT_DATE - interval '6' day
               union select CURRENT_DATE - interval '7' day
               union select CURRENT_DATE - interval '8' day
               union select CURRENT_DATE - interval '9' day) days
               left join tracking
               on days.day = DATE(trackdate)" .
                (empty($_SESSION["userdata"]["apparr"]) ? " where whoid=?" : " where appid in (" . str_repeat('?,', count($_SESSION["userdata"]["apparr"]) - $argapparr) . "?" . ")") . "
               group by days.day
               order by days.day desc limit 10";
        } else {
            $sql = "SELECT days.day as thisdate, count(id) as num
               FROM
               (select curdate() as day
               union select curdate() - interval 1 day
               union select curdate() - interval 2 day
               union select curdate() - interval 3 day
               union select curdate() - interval 4 day
               union select curdate() - interval 5 day
               union select curdate() - interval 6 day
               union select curdate() - interval 7 day
               union select curdate() - interval 8 day
               union select curdate() - interval 9 day) days
               left join tracking
               on days.day = DATE(trackdate)" .
                (empty($_SESSION["userdata"]["apparr"]) ? " and whoid=?" : " and appid in (" . str_repeat('?,', count($_SESSION["userdata"]["apparr"]) - $argapparr) . "?" . ")") . "
               group by days.day
               order by days.day desc limit 10";
        }
        $q = $pdo->prepare($sql);
        if (empty($_SESSION["userdata"]["apparr"])) {
            $q->execute(array($_SESSION["user"]));
        } else {
            $q->execute($_SESSION["userdata"]["apparr"]);
        }
        $zobj = $q->fetchAll();
        foreach ($zobj as $val) {echo "{ x: new Date('" . $val['thisdate'] . "'),y: " . $val['num'] . "},";}?>];
var reqdata = [<?php $thismonth = date('Y-m-d', strtotime('today -1 week'));
        if (DBTYPE == "oracle") {
            $sql = "SELECT * FROM (SELECT days.day as thisdate, count(id) as num
               FROM
               (select trunc(sysdate) as day from dual
               union select trunc(sysdate) - interval '1' day from dual
               union select trunc(sysdate) - interval '2' day from dual
               union select trunc(sysdate) - interval '3' day from dual
               union select trunc(sysdate) - interval '4' day from dual
               union select trunc(sysdate) - interval '5' day from dual
               union select trunc(sysdate) - interval '6' day from dual
               union select trunc(sysdate) - interval '7' day from dual
               union select trunc(sysdate) - interval '8' day from dual
               union select trunc(sysdate) - interval '9' day from dual) days
               left join requests
               on days.day = to_char(created, 'DD.MON.YYYY')" .
                (empty($_SESSION["userdata"]["widarr"]) ? " and requser=?" : " and wid in (" . str_repeat('?,', count($_SESSION["userdata"]["widarr"]) - $argwidarr) . "?" . ")") . "
               group by days.day
               order by days.day desc) WHERE rownum <= 10";
        } else if (DBTYPE == "postgresql") {
            $sql = "SELECT days.day as thisdate, count(id) as num
                FROM
                (select CURRENT_DATE as day
                union select CURRENT_DATE - interval '1' day
                union select CURRENT_DATE - interval '2' day
                union select CURRENT_DATE - interval '3' day
                union select CURRENT_DATE - interval '4' day
                union select CURRENT_DATE - interval '5' day
                union select CURRENT_DATE - interval '6' day
                union select CURRENT_DATE - interval '7' day
                union select CURRENT_DATE - interval '8' day
                union select CURRENT_DATE - interval '9' day) days
                left join requests
                on days.day = DATE(created)" .
                (empty($_SESSION["userdata"]["widarr"]) ? " and requser=?" : " and wid in (" . str_repeat('?,', count($_SESSION["userdata"]["widarr"]) - $argwidarr) . "?" . ")") . "
                group by days.day
                order by days.day desc limit 10";
        } else {
            $sql = "SELECT days.day as thisdate, count(id) as num
               FROM
               (select curdate() as day
               union select curdate() - interval 1 day
               union select curdate() - interval 2 day
               union select curdate() - interval 3 day
               union select curdate() - interval 4 day
               union select curdate() - interval 5 day
               union select curdate() - interval 6 day
               union select curdate() - interval 7 day
               union select curdate() - interval 8 day
               union select curdate() - interval 9 day) days
               left join requests
               on days.day = DATE(created)" .
                (empty($_SESSION["userdata"]["widarr"]) ? " and requser=?" : " and wid in (" . str_repeat('?,', count($_SESSION["userdata"]["widarr"]) - $argwidarr) . "?" . ")") . "
               group by days.day
               order by days.day desc limit 10";
        }
        $q = $pdo->prepare($sql);
        if (empty($_SESSION["userdata"]["widarr"])) {
            $q->execute(array($_SESSION["user"]));
        } else {
            $q->execute($_SESSION["userdata"]["widarr"]);
        }
        $zobj = $q->fetchAll();
        foreach ($zobj as $val) {echo "{ x: new Date('" . $val['thisdate'] . "'),y: " . $val['num'] . "},";}?>];
var color = Chart.helpers.color;
var config = {
    type: 'line',
    data: {
        datasets: [{
            label: 'changes per day',
            backgroundColor: color("#fff").rgbString(),
            borderColor: thiscolor,
            fill: false,
            data: chdata,
            pointRadius: 5,
            pointHoverRadius: 6,
        }, {
            label: 'requests per day',
            backgroundColor: color("#fff").rgbString(),
            borderColor: thiscolorreq,
            fill: false,
            data: reqdata,
            borderDash: [6, 4],
            pointRadius: 5,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        title: {
            display: false,
            /*	text: 'changes per day'*/
        },
        scales: {
            xAxes: [{
                type: 'time',
                time: {
                    unit: 'day'
                },
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Date'
                },
                ticks: {
                    major: {
                        fontStyle: 'bold',
                        fontColor: '#FF0000'
                    }
                }
            }],
            yAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'number'
                }
            }]
        }
    }
};
window.onload = function() {
    var ctx = document.getElementById('line-chart').getContext('2d');
    window.myLine = new Chart(ctx, config);
};
</script>
      <?php  include $website['corebase']."public/modules/template_end.php";
        if (!empty($text)) {unset($text);}
        echo '</body></html>';

    }
}