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
    
    $auth=Auth::singleton();
    if ($auth->getLogin()===false) {
        echo "access denied.";
    } else {
        $dungeonName=Gb_Request::getFormPost("dungeonname");
        $level=Gb_Request::getFormPost("levelnumber");
        $cells=Gb_Request::getFormPost("tileIds");
    
        $map=new Map($dungeonName, $level);
        $map->setCells($cells);
        echo "map saved.";
    }
} else {
    $dungeonName=$args->remove("dungeonname");
    $level=$args->remove("levelnumber");
    
    if ($dungeonName===null) { $dungeonName=$args->remove(); }
    if ($level===null) { $level=$args->remove(); }
    
    $map=new Map($dungeonName, $level);
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

    $url=$mvc->getUrl("edit"."/".$dungeonName."/"."@@@level@@@");
    $urlSave=$mvc->getUrl(array("edit","save"));
    $urlswitch=$mvc->getUrl("view"."/".$dungeonName."/".$level);
    $urllevelup=$urlleveldown="";
    if ($level>0) {
        $urllevelup=str_replace("@@@level@@@", $level-1, $url);
    }
    if ($level<99) {
        $urlleveldown=str_replace("@@@level@@@", $level+1, $url);
    }
    include("edit.phtml");
}
?>