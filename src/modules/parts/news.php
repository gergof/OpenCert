<?php

if(isset($_GET["news_count"])){
    if(!hasGroup(array("admin", "manager"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    if(hasGroup("admin")){
        $sql=$db->prepare("SELECT COUNT(id) AS count FROM news");
        $sql->execute();
    }
    else{
        $sql=$db->prepare("SELECT COUNT(id) AS count FROM news WHERE owner=:uid");
        $sql->execute(array(":uid"=>$_SESSION["id"]));
    }
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_GET["news"])){
    if(!hasGroup(array("admin", "manager"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    if(hasGroup("admin")){
        $sql=$db->prepare("SELECT n.id, n.title, n.publish, u.fullname AS owner FROM news AS n INNER JOIN users AS u ON (u.id=n.user) ORDER BY n.publish DESC");
        $sql->execute();
    }
    else{
        $sql=$db->prepare("SELECT n.id, n.title, n.publish, u.fullname AS owner FROM news AS n INNER JOIN users AS u ON (u.id=n.user) WHERE n.user=:uid ORDER BY n.publish DESC");
        $sql->execute(array(":uid"=>$_SESSION["id"]));
    }
    $res=$sql->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($res);
    die();
}

if(isset($_POST["new"])){
    if(!hasGroup(array("admin", "manager"))){
        \LightFrame\Utils\setError(403);
        die("restricted");
    }

    $data=json_decode($_POST["new"], true);

    if(!(isset($data["title"]) && isset($data["content"]))){
        \LightFrame\Utils\setError(207);
        die("error");
    }

    $sql=$db->prepare("INSERT INTO news (title, content, publish, user) VALUES (:title, :content, :publish, :user)");
    $sql->execute(array(":title"=>$data["title"], ":content"=>$data["content"], ":publish"=>date("Y-m-d H:i:s"), ":user"=>$_SESSION["id"]));
    $res=$sql->rowCount();

    if($res<1){
        \LightFrame\Utils\setError(500);
        die("error");
    }
    else{
        \LightFrame\Utils\setMessage(8);
        die("ok");
    }
}

?>

<span style="display: none" id="lang_createNew"><?php echo $lang["createnew"] ?></span>
<span style="display: none" id="lang_markdownTooltip"><?php echo $lang["markdown_tooltip"] ?></span>
<span style="display: none" id="lang_title"><?php echo $lang["title"] ?></span>
<span style="display: none" id="lang_content"><?php echo $lang["content"] ?></span>
<span style="display: none" id="lang_save"><?php echo $lang["save"] ?></span>
<span style="display: none" id="lang_cancel"><?php echo $lang["cancel"] ?></span>
<span style="display: none" id="lang_preview"><?php echo $lang["preview"] ?></span>
<span style="display: none" id="lang_close"><?php echo $lang["close"] ?></span>
<fancy-table id="newstable" data-countlabel="<?php echo $lang["count"].": " ?>" data-count="0" data-perpage="50" data-header='["<?php echo $lang["id"] ?>", "<?php echo $lang["title"] ?>", "<?php echo $lang["publishdate"] ?>", "<?php echo $lang["owner"] ?>"]' data-order='["id", "title", "publish", "owner"]' data-content="[]" data-requestpage="ui.news.getNews"/>
<br style="line-height: 2em"/>
<button type="button" class="button" onclick="ui.news.newNews()"><i class="fa fa-plus"/> <?php echo $lang["createnew"] ?></button>