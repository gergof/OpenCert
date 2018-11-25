<?php
namespace LightFrame;

//check if we already have a config file included
if(!isset($config)){
    require_once("../config/config.php");
}

function errorPage($errcode){
    if(file_exists(__DIR__."/parts/error/".$errcode.".php")){
        include("parts/error/".$errcode.".php");
    }
    else{
        echo "<b>Error!</b> code: ".$errcode;
    }
}

function RBAC_check($view){
    if($view=="index"){
        return true;
    }
    if($view=="users"){
        if(!hasGroup("admin")){
            return false;
        }
    }
    if($view=="files"){
        if(!hasGroup("admin")){
            return false;
        }
    }
    if($view=="groups"){
        if(!hasGroup(array("admin", "manager"))){
            return false;
        }
    }
    if($view=="news"){
        if(!hasGroup(array("admin", "manager"))){
            return false;
        }
    }
    if($view=="organizations"){
        if(!hasGroup(array("admin", "manager"))){
            return false;
        }
    }
    if($view=="exams"){
        if(!hasGroup(array("admin", "exam_editor", "variant_editor"))){
            return false;
        }
    }
    if($view=="myorg"){
        return true;
    }
    if($view=="examinations"){
        if(!hasGroup(array("admin", "evaluator", "inspector"))){
            return false;
        }
    }
    if($view=="certificates"){
        if(!hasGroup(array("admin", "evaluator", "inspector"))){
            return false;
        }
    }
    if($view=="profile"){
        return true;
    }
    if($view=="login"){
        if(isset($_SESSION["id"])){
            return false;
        }
    }
    if($view=="about"){
        return true;
    }
    return true;
}

function loadPart($view, $sub=null, $require=true){
    //globalize the vars here you want to make available for parts/submodules
    global $config, $lang, $langstr, $db, $lm;

    $view=$view==""?"index":$view;

    if(!RBAC_check($view)){
        errorPage(403);
        die();
    }

    if($sub!=null && $sub!="" && !is_numeric($sub)){
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