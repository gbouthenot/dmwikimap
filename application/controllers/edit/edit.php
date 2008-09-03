<?
require_once("Gb/Session.php");
require_once("models/Auth.php");
require_once("models/Map.php");
$mvc=Gb_Mvc::singleton();

//select user_id, user_name from user where UPPER(user_name)=UPPER('gilles') and user_password=MD5(CONCAT(user_id, "-", MD5("********")));

// parameters supplied by Gb_Mvc:
// $mvcController
// $mvcRootUrl
// $mvcHref
// $mvcArgs


$mvc=Gb_Mvc::singleton();
$args=$mvc->getArgs();

$action=$args->get();
if ($action=="save") {
    $args->remove();
    echo "<pre>";
    print_r($_POST);
    exit(1);
}

$dungeonName=$args->remove("dungeonname");
$level=$args->remove("levelnumber");

if ($dungeonName===null) { $dungeonName=$args->remove(); }
if ($level===null) { $level=$args->remove(); }

$action=$args->remove();
if ($action=="save") {
    echo "<pre>";
    print_r($_POST);
    exit(1);
}


$map=new Map($dungeonName, $level);
$mapSize=$map->getSize();

$urlSave=$mvc->getUrl(array("edit","save"));


$tileSize=array(16, 16);
$tileCount = 10;                    // number of 'different' tiles
$tilePrefix = "tileimages/";
$tilePostfix = ".gif";
$mapId = ucfirst($map->getDungeonName())." / Level ".$map->getLevelNumber();
$tileIds=$map->getCells();
$mapWidth =$map->getWidth();
$mapHeight=$map->getHeight();
$aNotes=$map->getNotes();
$url=$mvcRootUrl.$mvcController."/".$dungeonName."/"."@@@level@@@";

$urllevelup=$urlleveldown="";
$level=$map->getLevelNumber();
if ($level>0) {
    $urllevelup=str_replace("@@@level@@@", $level-1, $url);
}
if ($level<31) {
    $urlleveldown=str_replace("@@@level@@@", $level+1, $url);
}
include("edit.phtml");

?>