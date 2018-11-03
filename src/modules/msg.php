<?php
if(!isset($config)){
    require_once("../config/config.php");
}

use function \LightFrame\Utils\isMessage;
use function \LightFrame\Utils\getMessageArray;
use function \LightFrame\Utils\isError;
use function \LightFrame\Utils\getErrorArray;

if(isMessage()){
    foreach(getMessageArray() as $i){
        //costumize messages:
        echo "<div class=\"message\"><p>".$lang["message"][$i]."</p></div><br/>";
    }
}

if(isError()){
    foreach(getErrorArray() as $i){
        //costumize error messages:
        echo "<div class=\"message message__error\"><p>".$lang["message"][$i]."</p></div><br/>";
    }
}