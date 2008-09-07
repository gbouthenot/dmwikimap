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
$auth=Auth::singleton();
$args=$mvc->getArgs();

$action=$args->get();
if ($action=="save") {
    $args->remove();
    
    if ($auth->getLogin()===false) {
        echo "access denied: not logged in.";
    } else {
        $dungeonName=Gb_Request::getFormPost("dungeonname");
        $level=Gb_Request::getFormPost("levelnumber");
        $cells=Gb_Request::getFormPost("tileIds");
        $comment=Gb_Request::getFormPost("comment");
        
        $map=new Map($dungeonName, $level);
        $map->setCells($cells, $comment);
        $body="map saved.";
        $url=$mvc->getUrl(array("edit", $map->getDungeonName(), $map->getLevelNumber()));
        include("controllers/shared/javascriptDelayedRefresh.phtml");
        
    }
} elseif ($action=="delete") {
    $args->remove();
    
    if ($auth->getLogin()===false) {
        echo "access denied: not logged in.";
    } else {
        $mapid=Gb_Request::getFormPost("mapid");
        
        $map=new Map($mapid);
        if ($auth->getId() == $map->getMapUserId()) {
            $map->delete();
            $body="map deleted.";
            $url=$mvc->getUrl(array("edit", $map->getDungeonName(), $map->getLevelNumber()));
            include("controllers/shared/javascriptDelayedRefresh.phtml");
        } else {
            echo "cannot delete others map.";
        }
    }
} else {
    $mapid=$args->remove("mapid");
    if (isset($mapid)) {
        $map=new Map($mapid);
        $urlswitch=$mvc->getUrl("view"."/"."mapid"."/".$map->getMapId());
        
    } else {
        $dungeonName=$args->remove("dungeonname");
        $level=$args->remove("levelnumber");
        
        if ($dungeonName===null) { $dungeonName=$args->remove(); }
        if ($level===null) { $level=$args->remove(); }
        
        $map=new Map($dungeonName, $level);
        $urlswitch=$mvc->getUrl("view"."/".$map->getDungeonName()."/".$map->getLevelNumber());
    }
    
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
    
    $mapName = ucfirst($dungeonName)." / Level ".$level;
    $mapId=$map->getMapId();
    
    $fShowDelete=false;
    if ($mapId && $map->getMapUserId()==$auth->getId()) {
        $fShowDelete=true;
    }
    
    // always shows delete
    $fShowDelete=true;
    
    $tileSize=array(16, 16);
    $tileCount = 10;                    // number of 'different' tiles
    $tilePrefix = "tileimages/";
    $tilePostfix = ".gif";

    $url=$mvc->getUrl("edit"."/".$dungeonName."/"."@@@level@@@");
    $urlSave=$mvc->getUrl(array("edit","save"));
    $urlDelete=$mvc->getUrl(array("edit","delete"));
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