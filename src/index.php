<?php
require_once("config/config.php");
require_once("modules/loader.php");

use function \LightFrame\loadPart;

$view=isset($_GET["view"])?$_GET["view"]:"";
$sub=isset($_GET["sub"])?$_GET["sub"]:"";

if($lm->validateLogin()){
    //logged in
    if(isset($_GET["logout"])){
        $lm->logout();
    }
}
else{
    $lm->loginPrepare();
    if(isset($_POST["username"]) && isset($_POST["password"])){
        $lm->login($_POST["username"], $_POST["password"], isset($_POST["remember"]));
    }
    if(isset($_GET["autologin"])){
        $lm->login("", "");
    }
    if(isset($_GET["forgetuser"])){
        $lm->forgetUser();
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo (isset($extend_title)?$extend_title." :: ":"").($sub!=""?$lang[$sub]." :: ":"").($view!=""?$lang[$view]." :: ":"").$lang["site_title"] ?></title>
        <meta charset="UTF-8"/>
        <!-- link icon -->
        <link rel="icon" href="./res/icon.png"/>
        <!-- import main script -->
        <script src="./script/bundle.js"></script>
        <!-- cookie consent -->
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css"/>
        <script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js"></script>
        <script>
            window.addEventListener("load", function(){
                window.cookieconsent.initialise({
                    "palette": {
                        "popup": {
                            "background": "#000000"
                        },
                        "button": {
                            "background": "#F1D600"
                        }
                    },
                    "content": {
                        "message": "<?php echo $lang["cookie_message"] ?>",
                        "dismiss": "<?php echo $lang["cookie_dismiss"] ?>"
                    }
                });
            });
        </script>
        <!-- fontawesome -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
        <!-- reCaptcha -->
        <script src="https://www.google.com/recaptcha/api.js"></script>
    </head>
    <body>
        <div id="messageContainer" class="message__container">
            <!-- messages go here -->
        </div>
        <div id="header" class="header">
            <img class="header__logo" alt="logo" src="./res/logo.png"/>
            <p class="header__title"><?php echo $lang["site_title"] ?></p>
            <div class="header__languageSelector">
                <span><?php echo $lang["language"].": " ?></span>
                <select id="languageSelector" class="header__languageSelector__select" onchange="ui.main.changeLanguage()">
                    <?php
                    foreach($config["language"]["available"] as $l){
                        echo "<option value=\"".$l."\">".$lang[$l]."</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="header__break"></div>
        <div id="content">
            <div class="menu">
                <div class="menu__item" onclick="ui.main.route('')">
                    <span><?php echo $lang["index"] ?></span>
                </div>
                <?php if($lm->validateLogin()): ?>
                    <?php if(hasGroup("admin")): ?>
                        <div class="menu__item" onclick="ui.main.route('users')">
                            <span><?php echo $lang["users"] ?></span>
                        </div>
                        <div class="menu__item" onclick="ui.main.route('files')">
                            <span><?php echo $lang["files"] ?></span>
                        </div>
                    <?php endif; if(hasGroup("admin") || hasGroup("manager")): ?>
                        <div class="menu__item" onclick="ui.main.route('groups')">
                            <span><?php echo $lang["groups"] ?></span>
                        </div>
                        <div class="menu__item" onclick="ui.main.route('news')">
                            <span><?php echo $lang["news"] ?></span>
                        </div>
                        <div class="menu__item" onclick="ui.main.route('organizations')">
                            <span><?php echo $lang["organizations"] ?></span>
                        </div>
                    <?php endif; if(hasGroup("admin") || hasGroup("exam_editor") || hasGroup("variant_editor")): ?>
                        <div class="menu__item" onclick="ui.main.route('exams')">
                            <span><?php echo $lang["exams"] ?></span>
                        </div>
                    <?php endif ?>
                    <div class="menu__item" onclick="ui.main.route('myorg')">
                        <span><?php echo $lang["myorg"] ?></span>
                    </div>
                    <?php if(hasGroup("admin") || hasGroup("evaluator") || hasGroup("inspector")): ?>
                        <div class="menu__item" onclick="ui.main.route('examinations')">
                            <span><?php echo $lang["examinations"] ?></span>
                        </div>
                        <div class="menu__item" onclick="ui.main.route('certificates')">
                            <span><?php echo $lang["certificates"] ?></span>
                        </div>
                    <?php endif ?>
                    <div class="menu__item" onclick="ui.main.route('profile')">
                        <span><?php echo $lang["profile"] ?></span>
                    </div>
                    <div class="menu__item" onclick="window.location='./?logout'">
                        <span><?php echo $lang["logout"] ?></span>
                    </div>
                <?php else: ?>
                    <div class="menu__item" onclick="ui.main.route('login')">
                        <span><?php echo $lang["login"] ?></span>
                    </div>
                <?php endif ?>
                <div class="menu__item" onclick="ui.main.route('about')">
                    <span><?php echo $lang["about"] ?></span>
                </div>
            </div>
            <div id="module" class="module">
                <?php loadPart($view, $sub) ?>
            </div>
        </div>
        <div id="footer" class="footer">
            <p>&copy; Copyright <?php echo $config["org"]["name"]." ".date("Y") ?></p>
            <p>
                <span>Powered by: <a href="https://github.com/gergof/OpenCert">OpenCert</a></span>
                <br/>
                <span>Created by: Fándly Gergő Zoltán (<a href="mailto:contact@systemtest.tk">contact@systemtest.tk</a>, <a href="https://www.systemtest.tk">Systemtest.tk</a>, <a href="https://github.com/gergof">GitHub</a>)</span>
                <br/>
                <span>Licensed under <a href="https://www.gnu.org/licenses/gpl-3.0.html">GPLv3</a> | <a href="https://github.com/gergof/OpenCert/issues">Issues</a></span>
            </p>
        </div>
    </body>
</html>