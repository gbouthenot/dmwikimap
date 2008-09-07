<?
error_reporting(E_ALL);

require_once("Zend/Loader.php");
Zend_Loader::registerAutoload();

require_once("Gb/Mvc.php");
require_once("Gb/Request.php");
require_once("Gb/Session.php");

Gb_Session::session_start();

$mvc=Gb_Mvc::singleton();

$mvc->start();
