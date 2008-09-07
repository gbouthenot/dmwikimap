<?php

// parameters supplied by Gb_Mvc:
// $mvcController
// $mvcRootUrl
// $mvcHref
// $mvcArgs


require_once("models/Map.php");

$mvc=Gb_Mvc::singleton();
$args=$mvc->getArgs();

$dungeonName=$args->remove("dungeonname");
$level=$args->remove("levelnumber");

if ($dungeonName===null) { $dungeonName=$args->remove(); }
if ($level===null) { $level=$args->remove(); }

$map=new Map($dungeonName, $level);

$comments=$map->getComments();

$dungeonName=$map->getDungeonName();
$level=$map->getLevelNumber();
$mapSize=$map->getSize();
$tileIds=$map->getCells();
$mapWidth =$map->getWidth();
$mapHeight=$map->getHeight();
$aNotes=$map->getNotes();
$mapId = ucfirst($dungeonName)." / Level ".$level;


$tileSize=array(16, 16);
$tileCount = 10;                    // number of 'different' tiles
$tilePrefix = "tileimages/";
$tilePostfix = ".gif";

$url=$mvc->getUrl("view"."/".$dungeonName."/"."@@@level@@@");
$urlswitch=$mvc->getUrl("edit"."/".$dungeonName."/".$level);
$urllevelup=$urlleveldown="";
if ($level>0) {
    $urllevelup=str_replace("@@@level@@@", $level-1, $url);
}
if ($level<99) {
    $urlleveldown=str_replace("@@@level@@@", $level+1, $url);
}
include("view.phtml");

?>