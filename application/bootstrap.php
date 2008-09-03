<?
error_reporting(E_ALL);

require_once("Gb/Mvc.php");
require_once("Gb/Session.php");

Gb_Session::session_start();

$mvc=Gb_Mvc::singleton();

$mvc->start();
