<?php

//get the news from the DB
if(isset($_GET["news"])){
    //get the target groupt for the news
    $target="(?";
    $targetArr=array("guest");

    if(isset($_SESSION["groups"])){
        for($i=0; $i<count($_SESSION["groups"]); $i++){
            $target.=", ?";
        }
        $targetArr=array_merge($targetArr, $_SESSION["groups"]);
    }

    $target.=")";

    //get the offset
    $offset=$_GET["news"]*10;
    array_push($targetArr, $offset);

    //prepare SQL
    $sql=$db->prepare("SELECT DISTINCT nt.news AS id, n.title, n.content, n.publish, u.fullname AS user FROM news_target AS nt INNER JOIN news AS n ON (n.id=nt.news) INNER JOIN users AS u ON (u.id=n.user) WHERE nt.group IN ".$target." ORDER BY publish DESC LIMIT 10 OFFSET ?");
    $sql->execute($targetArr);
    $res=$sql->fetchAll();

    echo json_encode($sql->fetchAll());
    die();
}

?>

<h2><?php echo $lang["news"] ?></h2>
<span style="display: none" id="lang_loadmore"><?php echo $lang["loadmore"] ?></span>
<div id="news">
    <!-- news go here -->
</div>
<script>
    ui.index.getNews();
</script>