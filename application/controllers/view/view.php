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
$mapSize=$map->getSize();


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
include("view.phtml");

?>