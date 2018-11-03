<?php

//includes
require_once("lib/utils.php");
require_once("lib/LoginMaster/LoginMaster.php");
require_once("lib/PasswordStorage.php");
require_once("lib/myUtils.php");


//parse config
$config=parse_ini_file("config.ini", true);


//parse user config if available
if(file_exists(__DIR__."/user.config.ini")){
    $usrconf=parse_ini_file("user.config.ini", true);
    //merge the two config files together
    $config=array_merge($config, $usrconf);
}


//regionalization
date_default_timezone_set($config["general"]["timezone"]);
mb_internal_encoding("UTF-8");


//parse language file
$langstr="";
if(isset($_GET["langstr"])){
    $langstr=$_GET["langstr"];
    setcookie("langstr", $langstr, 90*86000);
}
else if(isset($_COOKIE["langstr"])){
    $langstr=$_COOKIE["langstr"];
}
else{
    $langstr=$config["language"]["default"];
}

if(!in_array($langstr, $config["language"]["available"])){
    $langstr=$config["language"]["default"];
}

$lang=parse_ini_file("lang/".$langstr.".ini", false);


//set up DB with PDO
$db=new PDO($config["database"]["type"].":host=".$config["database"]["host"].";dbname=".$config["database"]["name"].";charset=utf8", $config["database"]["user"], $config["database"]["password"]);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


//set utf8 BOM in case we want to work with utf8 files accross big-endian/little-endian machines
$UTF8_BOM=chr(239).chr(187).chr(191);


//enable debug mode
if($config["general"]["debug"]){
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    ini_set("display_errors", true);
}


//set up LoginMaster
$lmconfig=new \LoginMaster\Config($db, $config["login"]["session_lifetime"], $config["login"]["captcha_enable"], $config["login"]["captcha_after"], $config["login"]["captcha_sitekey"], $config["login"]["captcha_secretkey"], $config["login"]["ban_enable"], $config["login"]["ban_after"], $config["login"]["ban_time"], $config["login"]["look_time"], $config["login"]["remember_enable"], $config["login"]["remember_time"], "username");
class lmHandler implements \LoginMaster\Handler{
    public function handle($state, $target=0){
        global $db;
        switch($state){
            case \LoginMaster\LoginMaster::LOGIN_FAILED:
                \LightFrame\Utils\setError(200);
                \LightFrame\Utils\safeReload();
                break;
            case \LoginMaster\LoginMaster::CAPTCHA_FAILED:
                \LightFrame\Utils\setError(201);
                \LightFrame\Utils\safeReload();
                break;
            case \LoginMaster\LoginMaster::BANNED:
                \LightFrame\Utils\setError(203);
                \LightFrame\Utils\safeReload();
                break;
            case \LoginMaster\LoginMaster::LOGIN_OK:
                //load info about user
                $sql=$db->prepare("SELECT id, username, fullname, rsakey FROM users WHERE id=:id");
                $sql->execute(array(":id"=>$target));
                $user=$sql->fetch(PDO::FETCH_ASSOC);

                $sql=$db->prepare("SELECT `group`, `primary` FROM group_members WHERE user=:id");
                $sql->execute(array(":id"=>$target));
                $group=$sql->fetchAll(PDO::FETCH_ASSOC);

                //setting session
                $_SESSION["id"]=$user["id"];
                $_SESSION["username"]=$user["username"];
                $_SESSION["fullname"]=$user["fullname"];
                $_SESSION["rsakey"]=$user["rsakey"];
                $_SESSION["groups"]=array();
                foreach($group as $g){
                    array_push($_SESSION["groups"], $g["group"]);
                    if($g["primary"]){
                        $_SESSION["primary_group"]=$g["group"];
                    }
                }

                //reload browser window
                header("Location: ./");

                break;
            case \LoginMaster\LoginMaster::LOGOUT_DONE:
                \LightFrame\Utils\setMessage(1);
                \LightFrame\Utils\safeReload();
                break;
            case \LoginMaster\LoginMaster::FORGET_DONE:
                \LightFrame\Utils\setMessage(2);
                \LightFrame\Utils\safeReload();
                break; 
        }
    }
};
class lmPasswordEngine implements \LoginMaster\PasswordEngine{
    public function verify($input, $database){
        if(PasswordStorage::verify_password($input, $database)){
            return true;
        }
        else{
            return false;
        }
    }
};
$lm=new \LoginMaster\LoginMaster($lmconfig, new lmHandler(), new lmPasswordEngine(), new \LoginMaster\defaultTwoFactor());
$lm->init();