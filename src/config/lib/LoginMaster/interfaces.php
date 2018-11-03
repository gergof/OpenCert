<?php
namespace LoginMaster;

interface Handler{
    public function handle($state, $target=0);
}

interface PasswordEngine{
    public function verify($input, $database);
}

interface TwoFactor{
    public function challange($userId);
}

class defaultHandler implements Handler{
    public function handle($state, $target=0){
        echo $state;
    }
}

class defaultPasswordEngine implements PasswordEngine{
    public function verify($input, $database){
        //DON'T USE THIS!!!
        if($input==$database){
            return true;
        }
        return false;
    }
}

class defaultTwoFactor implements TwoFactor{
    public function challange($userid){
        //two factor disabled
        return true;
    }
}