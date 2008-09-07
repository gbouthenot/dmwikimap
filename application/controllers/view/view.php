<?php

// parameters supplied by Gb_Mvc:
// $mvcController
// $mvcRootUrl
// $mvcHref
// $mvcArgs


require_once("models/Map.php");

$mvc=Gb_Mvc::singleton();
$args=$mvc->getArgs();

$mapid=$args->remove("mapid");
if (isset($mapid)) {
    $map=new Map($mapid);
    $urlswitch=$mvc->getUrl("edit"."/"."mapid"."/".$map->getMapId());
} else {
    $dungeonName=$args->remove("dungeonname");
    $level=$args->remove("levelnumber");
    
    if ($dungeonName===null) { $dungeonName=$args->remove(); }
    if ($level===null) { $level=$args->remove(); }
    
    $map=new Map($dungeonName, $level);
    $urlswitch=$mvc->getUrl("edit"."/".$map->getDungeonName()."/".$map->getLevelNumber());
    }

$comments=$map->getComments();

$dungeonName=$map->getDungeonName();
$level=$map->getLevelNumber();
$mapSize=$map->getSize();
$tileIds=$map->getCells();
$mapWidth =$map->getWidth();
$mapHeight=$map->getHeight();
$aNotes=$map->getNotes();
$userComment=$map->getMapUserComment();
$userDatemodif=$map->getMapUserDatemodif();
$userName=$map->getMapUserName();
$aVersions=$map->getVersions();
$mapName = ucfirst($dungeonName)." / Level ".$level;
$mapId=$map->getMapId();


$tileSize=array(16, 16);
$tileCount = 10;                    // number of 'different' tiles
$tilePrefix = "tileimages/";
$tilePostfix = ".gif";

$url=$mvc->getUrl("view"."/".$dungeonName."/"."@@@level@@@");
$urllevelup=$urlleveldown="";
$urlVersion=$mvc->getUrl("view"."/"."mapid"."/"."@@@mapid@@@");
if ($level>0) {
    $urllevelup=str_replace("@@@level@@@", $level-1, $url);
}
if ($level<99) {
    $urlleveldown=str_replace("@@@level@@@", $level+1, $url);
}
include("view.phtml");

?>