<?
error_reporting(E_ALL);
ini_set("display_errors", TRUE);

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
