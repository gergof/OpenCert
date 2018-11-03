<?php
namespace LoginMaster;

require_once("config.php");
require_once("interfaces.php");
require_once("utils.php");

class LoginMaster{
    public function __construct($config, $eventHandler=defaultHandler, $passwordEngine=defaultPasswordEngine, $twoFactor=defaultTwoFactor){
        $this->config=$config;
        $this->eventHandler=$eventHandler;
        $this->passwordEngine=$passwordEngine;
        $this->twoFactor=$twoFactor;
    }

    private $config;
    private $eventHandler;
    private $passwordEngine;
    private $twoFactor;


    ///
    ///Constants
    ///
    const NOUSER=1;
    const LOGIN_FAILED=0;
    const CAPTCHA_FAILED=-1;
    const BANNED=-2;
    const LOGIN_OK=1;
    const FORGET_DONE=10;
    const LOGOUT_DONE=11;


    ///
    ///Public functions
    ///

    //Init
    public function init(){
        session_set_cookie_params($this->config->getSessionLifetime());
        return session_start();
    }

    //Prepare for login
    public function loginPrepare(){
        $this->passFailedAttempts();
    }

    public function login($uname, $passwd, $remember=false){
        global $lm_force_captcha;

        if($this->passFailedAttempts()){
            //not banned
            if(isset($lm_force_captcha)){
                //need to check captcha
                if(!isset($_POST["g-recaptcha-response"])){
                    //we don't have a captcha response
                    $captcha_failed=true;
                }
                else{
                    //we have a captcha response
                    if(!validateCaptcha($this->config->getCaptchaSecretkey(), $_POST["g-recaptcha-response"])){
                        //captcha failed
                        $captcha_failed=true;
                    }
                }
            }

            //check if we passed the occasional captcha check
            if(isset($captcha_failed)){
                $this->addHistory(self::NOUSER, self::LOGIN_FAILED);
                $this->eventHandler->handle(self::CAPTCHA_FAILED);
                return;
            }
            else{
                //captcha OK

                //check for autologin
                if($this->config->getRememberEnable()){
                    if($this->isRememberingUser() && $this->twoFactor->challange($this->isRememberingUser())){
                        //remembering user. Allow login
                        $this->permitAccess($this->isRememberingUser());
                    }
                }

                //normal login
                //prepare query
                $sql=$this->config->getPDO()->prepare("SELECT COUNT(id) AS count, id, password FROM users WHERE ".$this->config->getUsernameField()."=:identifier and id<>1");
                $sql->execute(array(":identifier"=>$uname));
                $res=$sql->fetch(\PDO::FETCH_ASSOC);

                //check if user exists
                if($res["count"]==0){
                    //user doesn't exist
                    $this->addHistory(self::NOUSER, self::LOGIN_FAILED);
                    $this->eventHandler->handle(self::LOGIN_FAILED);
                    return;
                }
                else{
                    //verify password for the defined passwordEngine and twoFactor engine
                    if($this->passwordEngine->verify($passwd, $res["password"]) && $this->twoFactor->challange($res["id"])){
                        //verification passed

                        //save user if he wants to remember
                        if($this->config->getRememberEnable()){
                            if($remember){
                                $this->rememberUser($res["id"]);
                            }
                        }

                        //permit login
                        $this->permitAccess($res["id"]);
                        return;
                    }
                    else{
                        //verification failed
                        $this->addHistory($res["id"], self::LOGIN_FAILED);
                        $this->eventHandler->handle(self::LOGIN_FAILED);
                        return;
                    }
                }
            }
        }
    }

    //log user out
    public function logout(){
        $_SESSION=array();
        session_destroy();
        setcookie("lm_login_random", null, -1);
        $this->eventHandler->handle(self::LOGOUT_DONE);
    }

    //check if the user is logged in
    public function validateLogin(){
        if(!isset($_SESSION["lm_id"]) || !isset($_COOKIE["lm_login_random"])){
            return false;
        }
        else{
            $sql=$this->config->getPDO()->prepare("SELECT auth_token FROM login_history WHERE user=:id and success=1 ORDER BY id DESC LIMIT 1");
            $sql->execute(array(":id"=>$_SESSION["lm_id"]));
            $res=$sql->fetch(\PDO::FETCH_ASSOC);

            if($res["auth_token"]==$this->getSessionKey()){
                return true;
            }
            else{
                return false;
            }
        }
    }

    //is remembering user (returns the user ID if remembering found)
    public function isRememberingUser(){
        if(!$this->config->getRememberEnable()){
            //remembering disabled
            return null;
        }

        if(is_null($this->getRememberKey())){
            return null;
        }

        $sql=$this->config->getPDO()->prepare("SELECT COUNT(id) AS count, user FROM login_remember WHERE remember_token=:token and until>:until");
        $sql->execute(array(":token"=>$this->getRememberKey(), ":until"=>date("Y-m-d H:i:s")));
        $res=$sql->fetch(\PDO::FETCH_ASSOC);

        if($res["count"]==0){
            $this->addHistory(self::NOUSER, self::LOGIN_FAILED);
            return null;
        }
        else{
            return res["user"];
        }
    }

    //forget user
    public function forgetUser(){
        $sql=$this->config->getPDO()->prepare("UPDATE login_remember SET until=0 WHERE remember_token=:token");
        $sql->execute(array(":token"=>$this->getRememberKey()));

        setcookie("lm_login_remember", null, -1);

        $this->eventHandler->handle(self::FORGET_DONE);
    }

    //print captcha on screen
    public function printCaptcha($dark=false){
        global $lm_force_captcha;
        if($this->config->getCaptchaEnable()){
            if(isset($lm_force_captcha)){
                echo "<div class=\"g-recaptcha\" data-sitekey=\"".$this->config->getCaptchaSitekey()."\"".($dark?" data-theme=\"dark\"":"")."></div>";
            }
        }
    }


    ///
    ///Private functions
    ///

    //generate a key for this session
    private function generateSessionKey(){
        $random=randomString();
        setcookie("lm_login_random", $random, time()+$this->config->getSessionLifetime());
        $hash=hash("sha256", $_SERVER["REMOTE_ADDR"]."*".$_SERVER["HTTP_USER_AGENT"]."*".$random);
        return $hash;
    }

    //get the session key
    private function getSessionKey(){
        if(!isset($_COOKIE["lm_login_random"])){
            return null;
        }
        $hash=hash("sha256", $_SERVER["REMOTE_ADDR"]."*".$_SERVER["HTTP_USER_AGENT"]."*".$_COOKIE["lm_login_random"]);
        return $hash;
    }

    //check if failed attempts still pass the limit and act accordingly
    private function passFailedAttempts(){
        if(!$this->config->getCaptchaEnable() && !$this->config->getBanEnable()){
            //every limit disabled. Nothing to do
            return true;
        }

        //check if already banned
        if($this->config->getBanEnable()){
            $sql=$this->config->getPDO()->prepare("SELECT COUNT(id) AS count FROM login_bans WHERE ip=:ip and until>:until");
            $sql->execute(array(":ip"=>$_SERVER["REMOTE_ADDR"], ":until"=>date("Y-m-d H:i:s")));
            $res=$sql->fetch(\PDO::FETCH_ASSOC);

            if($res["count"]!=0){
                //user banned
                $this->eventHandler->handle(self::BANNED);
                return false;
            }
        }

        //count failed attempts
        $sql=$this->config->getPDO()->prepare("SELECT COUNT(id) AS count FROM login_history WHERE ip=:ip and date>:date and success=0");
        $sql->execute(array(":ip"=>$_SERVER["REMOTE_ADDR"], ":date"=>date("Y-m-d H:i:s", time()-$this->config->getLookTime())));
        $fails=$sql->fetch(\PDO::FETCH_ASSOC)["count"];

        //check if we need to force captcha
        if($this->config->getCaptchaEnable() && $fails>=$this->config->getCaptchaAfter()){
            global $lm_force_captcha;
            $lm_force_captcha=true;
        }

        //check if we need to ban user
        if($this->config->getBanEnable() && $fails>=$this->config->getBanAfter()){
            $sql=$this->config->getPDO()->prepare("INSERT INTO login_bans (ip, until) VALUE (:ip, :until)");
            $sql->execute(array(":ip"=>$_SERVER["REMOTE_ADDR"], ":until"=>date("Y-m-d H:i:s", time()+$this->config->getBanTime())));

            $this->eventHandler->handle(self::BANNED);
            return false;
        }

        return true;
    }

    //append to login_history
    private function addHistory($uid, $success, $token=""){
        $sql=$this->config->getPDO()->prepare("INSERT INTO login_history (user, date, ip, auth_token, user_agent, success) VALUES (:user, :date, :ip, :auth_token, :user_agent, :success)");
        $sql->execute(array(":user"=>$uid, ":date"=>date("Y-m-d H:i:s"), ":ip"=>$_SERVER["REMOTE_ADDR"], ":auth_token"=>$token, ":user_agent"=>$_SERVER["HTTP_USER_AGENT"], ":success"=>$success));
    }

    //allow login
    private function permitAccess($uid){
        //generate session token
        $token=$this->generateSessionKey();
        $this->addHistory($uid, self::LOGIN_OK, $token);

        //initialize session
        $_SESSION=array();
        $_SESSION["lm_id"]=$uid;

        $this->eventHandler->handle(self::LOGIN_OK, $uid);
    }

    //generate remember token
    private function generateRememberKey(){
        $random=randomString();
        setcookie("lm_login_remember", $random, time()+(86000*$this->config->getRememberTime()));
        $hash=hash("sha256", $_SERVER["REMOTE_ADDR"]."*".$_SERVER["HTTP_USER_AGENT"]."*".$random);
        return $hash;
    }

    //get remember token
    private function getRememberKey(){
        if(!isset($_COOKIE["lm_login_remember"])){
            return null;
        }

        $hash=hash("sha256", $_SERVER["REMOTE_ADDR"]."*".$_SERVER["HTTP_USER_AGENT"]."*".$_COOKIE["lm_login_remember"]);
        return $hash;
    }

    //save user for 1-click login
    private function rememberUser($uid){
        $token=$this->generateRememberKey();

        $sql=$this->config->getPDO()->prepare("INSERT INTO login_remember (user, remember_token, until) VALUES (:user, :remember_token, :until)");
        $sql->execute(array(":user"=>$uid, ":remember_token"=>$token, ":until"=>date("Y-m-d H:i:s", time()+86000*$this->config->getRememberTime())));
    }
};