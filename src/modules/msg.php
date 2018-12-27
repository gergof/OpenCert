<?php
if(!isset($config)){
    require_once("../config/config.php");
}

use function \LightFrame\Utils\isMessage;
use function \LightFrame\Utils\getMessageArray;
use function \LightFrame\Utils\isError;
use function \LightFrame\Utils\getErrorArray;
use function \LightFrame\Utils\clearMessage;
use function \LightFrame\Utils\clearError;

$message=isMessage();
$error=isError();
$messageArray=getMessageArray();
$errorArray=getErrorArray();

clearMessage();
clearError();

if($message){
    foreach($messageArray as $i){
        //costumize messages:
        echo "<div class=\"message\" onclick=\"ui.main.disposeMessage(this)\"><p>".$lang["message"][$i]."</p></div>";
    }
}

if($error){
    foreach($errorArray as $i){
        //costumize error messages:
        echo "<div class=\"message message__error\" onclick=\"ui.main.disposeMessage(this)\"><p>".$lang["message"][$i]."</p></div>";
    }
}