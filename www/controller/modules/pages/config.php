<?php
class ClassMPM_main extends Class_main
{
    public static function getPage()
    {
        global $installedapp;
        global $website;
        if ($installedapp != "yes") {header("Location: /install");}
        session_start();
        if ($_GET["p"] = "welcome") {
            ClassMPM_welcome::getPage();
        } elseif (isset($_SESSION['user_id']) && isset($_SESSION['user'])) {
            header("Location: /cp/?");
        }
        // else {  header("Location: /info/?");  }
        else {ClassMPM_welcome::getPage();}
    }
}
class ClassMPM_welcome extends Class_welcome
{
    public static function getPage()
    {
        global $installedapp;
        global $website;
        global $maindir;
        global $page;
        global $modulelist;
        if ($installedapp != "yes") {header("Location: /install");}
        session_start();
        $pdo = pdodb::connect();
        include $website['corebase']."public/modules/css.php";
        echo '<style type="text/css">.card-header + .card-body{padding-top:15px;}</style></head>';
        echo '<body class="fix-header card-no-border no-sidebar"><div id="main-wrapper">';
        include "public/modules/headcontentmain.php";
        echo '<div class="page-wrapper">'; ?>
<div class="container-fluid">

    <div class="row">
        sss
    </div>

    <?php
        echo '</div></div>';
        include $website['corebase']."public/modules/footer.php";
        include $website['corebase']."public/modules/js.php";
        include $website['corebase']."public/modules/template_end.php";
        if (!empty($text)) {unset($text);}
        echo '</body></html>';

    }
}