<?php

// parameters supplied by Gb_Mvc:
// $mvcController
// $mvcRootUrl
// $mvcHref
// $mvcArgs


require_once("models/Map.php");
require_once("models/Hints.php");

$mvc=Gb_Mvc::singleton();
$args=$mvc->getArgs();

$mapid=$args->remove("mapid");
if (isset($mapid)) {
    $map=new Map($mapid);
} else {
    $dungeonName=$args->remove("dungeonname");
    $level=$args->remove("levelnumber");
    
    if ($dungeonName===null) { $dungeonName=$args->remove(); }
    if ($level===null) { $level=$args->remove(); }
    
    $map=new Map($dungeonName, $level);
}

$comments=$map->getComments();

$dungeonName=$map->getDungeonName();
$level=$map->getLevelNumber();
$mapSize=$map->getSize();
$tileIds=$map->getCells();
$mapWidth =$map->getWidth();
$mapHeight=$map->getHeight();
$aNotes=$map->getNotes();
$aVersions=$map->getVersions();
$mapName = ucfirst($dungeonName)." / Level ".$level;
$mapId=$map->getMapId();
$urleditnotes="http://dmwiki.atomas.com/w/index.php?title=$dungeonName/Levels_notes&action=edit&section=".($level+2);
$Hints = new Hints("../data/hints/" . strtolower($dungeonName) . ".txt");


$tileSize=array(16, 16);
$tileCount = 12;                    // number of 'different' tiles

$url=$mvc->getUrl("view"."/".$dungeonName."/"."@@@level@@@");
$urllevelup=$urlleveldown="";
$urlVersion=$mvc->getUrl("view"."/"."mapid"."/"."@@@mapid@@@");
$urlBase   =$mvc->getUrl();
if ($level>0) {
    $urllevelup=str_replace("@@@level@@@", $level-1, $url);
}
if ($level<99) {
    $urlleveldown=str_replace("@@@level@@@", $level+1, $url);
}
include("view.phtml");
