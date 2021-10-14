<?php

function iniGetDbparams()
{
    return array(
        "type"=>"Mysql",
        "host"=>"localhost",
        "user"=>"DBUSER",
        "pass"=>"DBPASS",
        "name"=>"DBNAME"
    );
}

function iniGetWikiApiUrl()
{
    return "https://dmwiki.atomas.com/w/api.php";
}
