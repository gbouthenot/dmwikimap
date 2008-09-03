<?php

require_once("Gb/Form.php");
require_once("Gb/Session.php");
require_once("models/Auth.php");

$mvc=Gb_Mvc::singleton();

$action=$mvc->getArgs()->get();

$urlLogin=$mvc->getUrl(array("auth", "login"));
$form=new Gb_Form();
$form
    ->addElement("login",    array("type"=>"TEXT",      "fMandatory"=>true))
    ->addElement("password", array("type"=>"PASSWORD",  "fMandatory"=>true))
;

$form->load();

if ($action==="login" && $form->validate()===true)
{
    echo "reception login ".$_POST["GBFORM_login"]." et password ".$_POST["GBFORM_password"]."<br />";
    Gb_Session::set("auth", array("login"=>"test"));
}

$auth=Gb_Session::get("auth");
if ($auth===false) {
    include("notlogged.phtml");
} elseif (is_array($auth) && isset($auth["login"])) {
    $login=$auth["login"];
    include("logged.phtml");
}



