<?php
require_once("config/config.php");
require_once("modules/loader.php");

use function \LightFrame\loadPart;

$view=isset($_GET["view"])?$_GET["view"]:"";
$sub=isset($_GET["sub"])?$_GET["sub"]:"";

//uncomment these if you have LoginMaster installed
/*
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
*/

?>

<!DOCTYPE html>
<html>
    <head>
        <title><?php echo (isset($extend_title)?$extend_title." :: ":"").($sub!=""?$lang[$sub]." :: ":"").($view!=""?$lang[$view]." :: ":"").$lang["site_title"] ?></title>
        <meta charset="UTF-8"/>
        <!-- link icon -->
        <link rel="icon" href="./res/icon.png"/>
        <!-- import main CSS -->
        <link rel="stylesheet" href="./style/main.css"/>
        <!-- import main script -->
        <script src="./script/main.js"></script>
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
        <!-- reCaptcha -->
        <script src="https://www.google.com/recaptcha/api.js"></script>
    </head>
    <body>
        <div id="header" class="header">
            <img style="max-width: 5em; max-height: 5em" src="./res/logo.png" alt="logo"/>
            <h1>LightFrame</h1>
        </div>
        <div id="content">
            <p>A very basic PHP framework that is made to suit my needs.</p>
            <div id="module">
                <?php loadPart($view, $sub) ?>
            </div>
        </div>
        <div id="footer">
            <p>This site was made using LightFrame</p>
        </div>
    </body>
</html>