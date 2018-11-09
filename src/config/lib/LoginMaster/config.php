<?php
namespace LoginMaster;

class Config{
    public function __construct($pdo, $sessionLifetime, $captchaEnable, $captchaAfter, $captchaSitekey, $captchaSecretkey, $banEnable, $banAfter, $banTime, $lookTime, $rememberEnable, $rememberTime, $usernameField){
        $this->pdo=$pdo;
        $this->sessionLifetime=$sessionLifetime;
        $this->captchaEnable=$captchaEnable;
        $this->captchaAfter=$captchaAfter;
        $this->captchaSitekey=$captchaSitekey;
        $this->captchaSecretkey=$captchaSecretkey;
        $this->banEnable=$banEnable;
        $this->banAfter=$banAfter;
        $this->banTime=$banTime;
        $this->lookTime=$lookTime;
        $this->rememberEnable=$rememberEnable;
        $this->rememberTime=$rememberTime;
        $this->usernameField=$usernameField;
    }

    private $pdo;
    private $sessionLifetime;
    private $captchaEnable;
    private $captchaAfter;
    private $captchaSitekey;
    private $captchaSecretkey;
    private $banEnable;
    private $banAfter;
    private $banTime;
    private $lookTime;
    private $rememberEnable;
    private $rememberTime;
    private $usernameField;

    public function getPDO() { return $this->pdo; }
    public function getSessionLifetime() { return $this->sessionLifetime; }
    public function getCaptchaEnable() { return $this->captchaEnable; }
    public function getCaptchaAfter() { return $this->captchaAfter; }
    public function getCaptchaSitekey() { return $this->captchaSitekey; }
    public function getCaptchaSecretkey() { return $this->captchaSecretkey; }
    public function getBanEnable() { return $this->banEnable; }
    public function getBanAfter() { return $this->banAfter; }
    public function getLookTime() { return $this->lookTime; }
    public function getRememberEnable() { return $this->rememberEnable; }
    public function getRememberTime() { return $this->rememberTime; }
    public function getUsernameField() { return $this->usernameField; }
};