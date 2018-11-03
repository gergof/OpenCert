<?php
function hasGroup($group){
    if(isset($_SESSION["groups"])){
        return in_array($group, $_SESSION["groups"]);
    }
    else{
        if($group=="guest"){
            return true;
        }
        else{
            return false;
        }
    }
}