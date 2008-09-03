<?php

require_once("Gb/Form.php");
require_once("Gb/Session.php");
require_once("models/Auth.php");

$mvc=Gb_Mvc::singleton();
$auth=Auth::singleton();

$action=$mvc->getArgs()->get();

if ($action=="logout") {
    $auth->logout();
}

$form=new Gb_Form();
$form
    ->addElement("login",    array("type"=>"TEXT",      "fMandatory"=>true))
    ->addElement("password", array("type"=>"PASSWORD",  "fMandatory"=>true))
;

$form->load();


if ($action==="login" && $form->validate()===true)
{
    $auth->login($_POST["GBFORM_login"], $_POST["GBFORM_password"]);
}

$login=$auth->getLogin();
if ($login===false) {
    $urlLogin=$mvc->getUrl(array("auth", "login"));
    include("notlogged.phtml");
} else {
    $urlLogout=$mvc->getUrl(array("auth", "logout"));
    include("logged.phtml");
}



