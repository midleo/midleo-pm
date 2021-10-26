<?php
include "config.db.php";
include "config.vars.php";
$corebaseurl = "core/www/";
$maindir = dirname(dirname(__FILE__));
if (file_exists(dirname(dirname(__FILE__)) . "/core/www/controller/config.main.php")) {include dirname(dirname(__FILE__)) . "/core/www/controller/config.main.php";}
foreach (glob(dirname(__FILE__) . "/modules/*/config.php") as $filename) {if (file_exists($filename)) {include $filename;}}
$typereq = array(
    'queues' => "IBM MQ",
    'fte' => "IBM File Transfer",
    'flow' => "IBM Message flow",
    'tibco' => "Tibco objects",
    'tomcat' => "Apache Tomcat config",
    'ibmwas' => "IBM Websphere AS config",
    'general' => "General request",
    'dns' => "DNS request",
    'server' => "Server request",
    'vps' => "Virtual server request",
    'network' => "Network request",
    'general' => "General request",
);
$projcodes = array(
    '0' => array(
        "name" => "New",
        "badge" => "secondary",
        "color" => "#6c757d",
    ),
    '1' => array(
        "name" => "Pending",
        "badge" => "warning",
        "color" => "#ffc107",
    ),
    '2' => array(
        "name" => "Approved",
        "badge" => "success",
        "color" => "#0CC44F",
    ),
    '3' => array(
        "name" => "In progress",
        "badge" => "info",
        "color" => "#00AFFF",
    ),
    '4' => array(
        "name" => "Completed",
        "badge" => "light",
        "color" => "#dfdfdf",
    ),
    '5' => array(
        "name" => "Delay",
        "badge" => "danger",
        "color" => "#E81625",
    ),
);