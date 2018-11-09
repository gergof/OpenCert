<?php
function hasGroup($group){
    if(isset($_SESSION["groups"])){
        if(!is_array($group)){
            return in_array($group, $_SESSION["groups"]);
        }
        else{
            foreach($group as $g){
                if(in_array($g, $_SESSION["groups"])){
                    return true;
                }
            }
        }
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