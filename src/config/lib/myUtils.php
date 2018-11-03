<?php
function hasGroup($group){
    return in_array($group, $_SESSION["groups"]);
}