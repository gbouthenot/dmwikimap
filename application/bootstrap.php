<?
error_reporting(E_ALL);

require_once("Zend/Loader.php");
Zend_Loader::registerAutoload();

require_once("Gb/Mvc.php");
require_once("Gb/Request.php");
require_once("Gb/Response.php");
require_once("Gb/Session.php");
require_once("Gb/Util.php");

Gb_Session::session_start();

Gb_Response::$nologo=true;

$mvc=Gb_Mvc::singleton();
Gb_Util::startup(array($mvc, "start"));

if (Gb_Util::$debug) {
    Gb_Response::send_footer();
}
