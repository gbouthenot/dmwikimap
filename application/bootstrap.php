<?
error_reporting(E_ALL);

require_once("Zend/Loader/Autoloader.php");
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Gb_');

Gb_Session::session_start();

Gb_Response::$nologo=true;

$mvc=Gb_Mvc::singleton();
Gb_Util::startup(array($mvc, "start"));

if (Gb_Util::$debug) {
    Gb_Response::send_footer();
}
