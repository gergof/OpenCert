<?php
namespace LightFrame;

//check if we already have a config file included
if(!isset($config)){
    require_once("../config/config.php");
}

use function \LightFrame\Utils\setError;

function errorPage($errcode){
    setError($errcode);
    if(file_exists(__DIR__."/parts/error/".$errcode.".php")){
        include("parts/error/".$errcode.".php");
    }
    else{
        echo "<b>Error!</b> code: ".$errcode;
    }
}

function loadPart($view, $sub=null, $require=true){
    //globalize the vars here you want to make available for parts/submodules
    global $config, $lang, $langstr, $db, $lm;

    $view=$view==""?"index":$view;

    if($sub!=null && $sub!=""){
        if(!file_exists(__DIR__."/parts/".$view."/".$sub.".php")){
            if($require){
                errorPage(404);
            }
        }
        else{
            include("parts/".$view."/".$sub.".php");
        }
    }
    else{
        if(!file_exists(__DIR__."/parts/".$view.".php")){
            if($require){
                errorPage(404);
            }
        }
        else{
            include("parts/".$view.".php");
        }
    }
}

//load from request
if(isset($_GET["load"])){
    if(isset($_GET["sub"])){
        loadPart($_GET["load"], $_GET["sub"]);
    }
    else{
        loadPart($_GET["load"]);
    }
}