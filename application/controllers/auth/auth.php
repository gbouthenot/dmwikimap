<?php

require_once("Gb/Form2.php");
require_once("Gb/Session.php");
require_once("models/Auth.php");

$mvc=Gb_Mvc::singleton();
$auth=Auth::singleton();

$action=$mvc->getArgs()->get();

if ($action=="logout") {
    $auth->logout();
}

$form=new Gb_Form2();
$form
    ->append(new Gb_Form_Elem_Text("login",    array("fMandatory"=>true)))
    ->append(new Gb_Form_Elem_Password("password", array("fMandatory"=>true)))
;

$form->load();
$message="";

if ($form->isPost() && $action==="login" && $form->validate()===true)
{
    $login=$auth->login($form->getElem("login")->value(), $form->getElem("password")->value());
    if ($login === false) {
        $message="Wrong creditentials.<br>Try to log in the wiki first.";
    }
}

$login=$auth->getLogin();
if ($login===false) {
    $urlLogin=$mvc->getUrl(array("auth", "login"));
    include("notlogged.phtml");
} else {
    $urlLogout=$mvc->getUrl(array("auth", "logout"));
    include("logged.phtml");
}



