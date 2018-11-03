<?php
namespace LightFrame\Utils;

//enum for processing strings
const STR_SAME=0;
const STR_LOWERCASE=1;
const STR_REMACCENT=2;
const STR_REMACCENTLOWER=3;

//enum for generating random strings
const RAND_SMALL=0;
const RAND_LARGE=1;
const RAND_SPECIAL=2;

//config
const MSG_COOKIE_LIFETIME=3;



///
///Message transfer
///

//set message
function setMessage($code, $error=false){
    global $msg_code, $msg_code_error;

    if($error){
        if(isset($msg_code_error)){
            array_push($msg_code_error, $code);
        }
        else{
            $msg_code_error=array($code);
        }

        setcookie("msg_code_error", serialize($msg_code_error), time()+MSG_COOKIE_LIFETIME);
    }
    else{
        if(isset($msg_code)){
            array_push($msg_code, $code);
        }
        else{
            $msg_code=array($code);
        }

        setcookie("msg_code", serialize($msg_code), time()+MSG_COOKIE_LIFETIME);
    }
}
function setError($code){
    setMessage($code, true);
}

//do we have a message?
function isMessage($error=false){
    global $msg_code, $msg_code_error;

    if($error){
        return isset($msg_code_error) || isset($_COOKIE["msg_code_error"]);
    }
    else{
        return isset($msg_code) || isset($_COOKIE["msg_code"]);
    }
}
function isError(){
    return isMessage(true);
}

//get the message array
function getMessageArray($error=false){
    global $msg_code, $msg_code_error;

    if($error){
        if(isError()){
            if(isset($msg_code_error)){
                return $msg_code_error;
            }
            else{
                return unserialize($_COOKIE["msg_code_error"]);
            }
        }
        else{
            return null;
        }
    }
    else{
        if(isMessage()){
            if(isset($msg_code)){
                return $msg_code;
            }
            else{
                return unserialize($_COOKIE["msg_code"]);
            }
        }
        else{
            return null;
        }
    }
}
function getErrorArray(){
    return getMessageArray(true);
}

//clear message
function clearMessage($error=false){
    global $msg_code, $msg_code_error;

    if($error){
        if(isset($msg_code_error)){
            unset($msg_code_error);
        }
        setcookie("msg_code_error", null, -1);
    }
    else{
        if(isset($msg_code)){
            unset($msg_code);
        }
        setcookie("msg_code", null, -1);
    }
}
function clearError(){
    clearMessage(true);
}



///
///Safely reload page, without form resubmission
///
function safeReload(){
    header("Location: ".explode("?", $_SERVER["REQUEST_URI"][0]));
}



///
///Generate a random string
///
function randomString($length=8, $charset=RAND_SMALL){
    $chs="";
    switch($charset){
        case RAND_SMALL:
            $chs="0123456789abcdefghijklmnopqrstuvwxyz";
            break;
        case RAND_LARGE:
            $chs="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            break;
        case RAND_SPECIAL:
            $chs="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+=.?<>/";
            break;
        default:
            return null;
    }

    $chslen=strlen($chs);
    $str="";
    for($i=0; $i<$chslen; $i++){
        $str.=$chs[rand(0, $chslen-1)];
    }

    return $str;
}



///
///Get string between two delimiters
///
function getStringBetween($string, $start, $end){
    $string=" ".$string;
    $ini=strpos($string, $start);
    if($ini==0){
        return "";
    }
    $ini+=strlen($start);
    $len=strpos($string, $end, $ini)-$ini;
    return substr($string, $ini, $len);
}



///
///Process a string
///
function processString($string, $dep){
    global $accentConvert;
    switch($dep){
        case STR_SAME:
            return $string;
        case STR_LOWERCASE:
            return strtolower($string);
        case STR_REMACCENT:
            return strtr($string, $accentConvert);
        case STR_REMACCENTLOWER:
            return strtolower(strtr($string, $accentConvert));
        default:
            return null;
    }
}
//Table to use when converting accents
$accentConvert=array(
        // Decompositions for Latin-1 Supplement
        chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
        chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
        chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
        chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
        chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
        chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
        chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
        chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
        chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
        chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
        chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
        chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
        chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
        chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
        chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
        chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
        chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
        chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
        chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
        chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
        chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
        chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
        chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
        chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
        chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
        chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
        chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
        chr(195).chr(191) => 'y',
        // Decompositions for Latin Extended-A
        chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
        chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
        chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
        chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
        chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
        chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
        chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
        chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
        chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
        chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
        chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
        chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
        chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
        chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
        chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
        chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
        chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
        chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
        chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
        chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
        chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
        chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
        chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
        chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
        chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
        chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
        chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
        chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
        chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
        chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
        chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
        chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
        chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
        chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
        chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
        chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
        chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
        chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
        chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
        chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
        chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
        chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
        chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
        chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
        chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
        chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
        chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
        chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
        chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
        chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
        chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
        chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
        chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
        chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
        chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
        chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
        chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
        chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
        chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
        chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
        chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
        chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
        chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
        chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
    );