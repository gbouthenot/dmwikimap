<?
error_reporting(E_ALL);
ini_set("display_errors", TRUE);
header('Content-Type: text/html; charset=utf-8');

init_paths();

require_once("Zend/Loader/Autoloader.php");
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Gb_');

Gb_Log::$loglevel_file = Gb_Log::LOG_INFO;
//Gb_Log::$loglevel_file = Gb_Log::LOG_DEBUG;

Gb_Session::session_start();

Gb_Response::$nologo=true;
//echo get_include_path();echo "ok";
$mvc=Gb_Mvc::singleton();
Gb_Util::startup(array($mvc, "start"));

if (Gb_Util::$debug) {
    Gb_Response::send_footer();
}

function init_paths() {
    // ajoute application, application/controllers, application/lib et application/extlib
    //   à l'include path
    // positionne $GLOBAL["_ROOT"] sur le répertoire principal (qui contient
    //   application, public, var,...) ex "/home/gbouthen/web/eccand/"
    $DS = DIRECTORY_SEPARATOR;
    $PS = PATH_SEPARATOR;
    $dir = realpath( dirname(__FILE__) . $DS . "..") . $DS;
    $GLOBALS["_ROOT"]    = $dir;
//    chdir($dir);
    set_include_path(
        "${dir}application${PS}" .
        "${dir}application${DS}controllers${PS}" .
        "${dir}application${DS}lib${PS}" .
        "${dir}application${DS}extlib${PS}" .
        get_include_path()
    );
}
