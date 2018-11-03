<?php
namespace LoginMaster;

function randomString($length=32){
    $charset="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_-=+\?/.>,<";
    $charsetLength=strlen($charset);
    $string="";
    for($i=0; $i<$length; $i++){
        $string.=$charset[rand(0, $charsetLength-1)];
    }
    return $string;
}

function validateCaptcha($secretkey, $response){
    $verify=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretkey."&response=".$response);
    $data=json_decode($verify);
    if($data->success){
        return true;
    }
    else{
        return false;
    }
}