<?php

/**
 * @copyright	Copyright (C) 2009-2017 GWE Systems Ltd. All rights reserved.
 */

if (isset($_SERVER["REQUEST_URI"])) {
    $x = str_replace("plugins/jevents/jevrsvppro/rsvppro/sendreminders.php", "", $_SERVER["REQUEST_URI"]);
    echo header("Location:".$x."index.php?option=com_jevents&typeaheadtask=gwejson&file=sendreminders&path=plugin&folder=jevents&plugin=jevrsvppro/rsvppro&json={}");
}    

exit();

/*
// This doesn't work because Joomla checks the source page URL and it isn't correct!
$_GET["option"]="com_jevents";
$_GET["typeaheadtask"]="gwejson";
$_GET["file"]="sendreminders";
$_GET["path"]="plugin";
$_GET["folder"]="jevents";
$_GET["plugin"]="jevrsvppro/rsvppro";
$_GET["json"]="{}";

$_SERVER["CONTEXT_DOCUMENT_ROOT"];

echo __DIR__."<Br/>";
echo __FILE__."<Br/>";
echo __DIR__ ."/../../../index.php";
if (file_exists(__DIR__ ."/../../../index.php")){
    require __DIR__ ."/../../../index.php";        
}
else if (isset($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {        
    include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/index.php";
}
else if (isset($_SERVER["REQUEST_URI"])) {
    $x = str_replace("plugins/jevents/jevrsvppro/rsvppro/sendreminders.php", "", $_SERVER["REQUEST_URI"]);
    echo header("Location:".$x."index.php?option=com_jevents&typeaheadtask=gwejson&file=sendreminders&path=plugin&folder=jevents&plugin=jevrsvppro/rsvppro&json={}");
}    

exit();
*/